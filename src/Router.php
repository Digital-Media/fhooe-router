<?php

declare(strict_types=1);

namespace Fhooe\Router;

use Closure;
use Fhooe\Router\Exception\HandlerNotSetException;
use Fhooe\Router\Exception\RouteAlreadyExistsException;
use Fhooe\Router\Type\HttpMethod;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * A simple object-oriented router for educational purposes that can handle GET and POST requests.
 *
 * This routing class can be used in two ways:
 * 1. Instantiate it, set routes with callbacks and run it (recommended).
 * 2. Use the static getRoute() method to just retrieve the HTTP method and route and perform the logic yourself.
 * @package Fhooe\Router
 * @author Wolfgang Hochleitner <wolfgang.hochleitner@fh-hagenberg.at>
 * @since 0.1.0
 */
class Router
{
    /**
     * @var array<array{method: HttpMethod, pattern: string, callback: Closure}> All routes (methods and patterns)
     * and their associated callbacks.
     */
    private array $routes;

    /**
     * @var Closure|null The 404 callback when no suitable other route is found.
     */
    private ?Closure $noRouteCallback;

    /**
     * @var string The base path that is considered when this application is not in the server's document root.
     */
    private string $basePath;

    /**
     * @var LoggerInterface The logger instance used for logging router events.
     */
    private LoggerInterface $logger;

    /**
     * Creates a new Router. The list of routes is initially empty, so is the supplied 404 callback.
     * @param LoggerInterface|null $logger Optional logger instance. If not provided, a NullLogger will be used.
     */
    public function __construct(?LoggerInterface $logger = null)
    {
        $this->basePath = "";
        $this->routes = [];
        $this->noRouteCallback = null;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * Sets the base path if the application is not in the server's document root.
     * @param string $basePath The base path. Specify without a trailing slash.
     */
    public function setBasePath(string $basePath): void
    {
        $this->basePath = $basePath;
        $this->logger->info("Base path set to: {basePath}", ["basePath" => $basePath]);
    }

    /**
     * Adds a route, consisting of a method and a URI pattern together with its callback handler.
     * @param HttpMethod $method The HTTP method of this route.
     * Only GET and POST (as specified in the HttpMethod enum) are supported.
     * @param string $pattern The routing pattern. Parameters in curly braces (e.g. {id}) must match
     * the parameter names in the callback function. Optional parts can be specified in square brackets (e.g. [/]).
     * @param Closure $callback The callback that is called when the route matches. Parameter names
     * must match the names in the route pattern to avoid PHP errors (e.g. function($id) for pattern {id}).
     * @throws RouteAlreadyExistsException If a route with the same method and pattern already exists.
     */
    public function addRoute(HttpMethod $method, string $pattern, Closure $callback): void
    {
        // Check for existing route with same method and pattern
        foreach ($this->routes as $route) {
            if ($route["method"] === $method && $route["pattern"] === $pattern) {
                throw new RouteAlreadyExistsException(
                    "A route with method {$method->name} and pattern '$pattern' already exists."
                );
            }
        }

        $this->routes[] = [
            "method" => $method,
            "pattern" => $pattern,
            "callback" => $callback
        ];
        $this->logger->info(
            "Route added: {method} {pattern}",
            [
                "method" => $method->name,
                "pattern" => $pattern
            ]
        );
    }

    /**
     * Shorthand method for adding a new GET route.
     * @param string $pattern The routing pattern. Parameters in curly braces (e.g. {id}) must match
     * the parameter names in the callback function. Optional parts can be specified in square brackets (e.g. [/]).
     * @param Closure $callback The callback that is called when the route matches. Parameter names
     * must match the names in the route pattern to avoid PHP errors (e.g. function($id) for pattern {id}).
     * @throws RouteAlreadyExistsException If a route with the same method and pattern already exists.
     */
    public function get(string $pattern, Closure $callback): void
    {
        $this->addRoute(HttpMethod::GET, $pattern, $callback);
    }

    /**
     * Shorthand method for adding a new POST route.
     * @param string $pattern The routing pattern. Parameters in curly braces (e.g. {id}) must match
     * the parameter names in the callback function. Optional parts can be specified in square brackets (e.g. [/]).
     * @param Closure $callback The callback that is called when the route matches. Parameter names
     * must match the names in the route pattern to avoid PHP errors (e.g. function($id) for pattern {id}).
     * @throws RouteAlreadyExistsException If a route with the same method and pattern already exists.
     */
    public function post(string $pattern, Closure $callback): void
    {
        $this->addRoute(HttpMethod::POST, $pattern, $callback);
    }

    /**
     * Sets the callback for the case that no route matches, a.k.a. 404.
     * @param Closure $callback The 404 callback that is called when no route matches.
     */
    public function set404Callback(Closure $callback): void
    {
        $this->noRouteCallback = $callback;
        $this->logger->info("404 callback set.");
    }

    /**
     * Execute the router. This loops over all the routes that have been added and invokes the associated callback if
     * the method and pattern match. If there is no match, the 404 callback is invoked.
     */
    public function run(): void
    {
        foreach ($this->routes as $route) {
            if ($this->handle($route)) {
                return;
            }
        }

        // If no route was handled, call the 404 callback
        http_response_code(404);
        if ($this->noRouteCallback) {
            ($this->noRouteCallback)();
            $this->logger->info("No route match found. 404 callback executed.");
        } else {
            throw new HandlerNotSetException("404 handler not set.");
        }
    }

    /**
     * Handles a single route. The method first matches the current request's method with the one of the route.
     * If there is a match, the URI pattern is compared. In case of a match, the associated callback is invoked.
     * @param array{method: HttpMethod, pattern: string, callback: Closure} $route The route to handle.
     * @return bool Returns true, if there was a match and the route was handled, otherwise false.
     */
    private function handle(array $route): bool
    {
        $method = $_SERVER["REQUEST_METHOD"];

        if ($route["method"]->name === $method) {
            $uri = $this->getUri();

            // Convert route pattern to regex pattern
            // First handle optional parts in square brackets
            $pattern = preg_replace('/\[(.*?)\]/', '(?:$1)?', $route["pattern"]);
            // Then handle parameters in curly braces
            $pattern = preg_replace('/\{([^}]+)\}/', '(?P<$1>[^/]+)', $pattern);
            // Finally escape forward slashes
            $pattern = str_replace('/', '\/', $pattern);
            $pattern = '/^' . $pattern . '$/';

            if (preg_match($pattern, $uri, $matches)) {
                if (is_callable($route["callback"])) {
                    // Extract named parameters
                    $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                    
                    // Log the route match before executing the callback
                    $this->logger->info(
                        "Route match found: {method} {pattern} (called URL: {uri}). Callback parameters: {params}",
                        [
                            "method" => $route["method"]->name,
                            "pattern" => $route["pattern"],
                            "uri" => $uri,
                            "params" => implode(", ", array_map(fn($key, $value) => "$key => $value", array_keys($params), $params))
                        ]
                    );
                    
                    // Execute callback with parameters
                    ($route["callback"])(...$params);
                    return true;
                }
            }
            return false;
        }
        return false;
    }

    /**
     * Returns the URI of the current request. If a base path is specified, it is removed first. Also, potential
     * parameters are filtered out so that a comparison with a route pattern is possible.
     * @return string The current URI.
     */
    public function getUri(): string
    {
        $uri = rawurldecode($_SERVER["REQUEST_URI"]);

        // Remove the base path if there is one
        if ($this->basePath) {
            $uri = str_replace($this->basePath, "", $uri);
        }

        // Remove potential URI parameters (everything after ?) and return
        $trimmedUri = strtok($uri, "?");

        /* Since strtok can return false (if $uri was an empty string, which it should never be because
           $_SERVER["REQUEST_URI"] should always have a value), return $uri if that was ever the case in order to have a
           consistent string return value. */
        return $trimmedUri ?: $uri;
    }

    /**
     * Returns the full URL for a given route. If a base path is set, it is prepended to account for projects in
     * subdirectories.
     * @param string $pattern The pattern of a route, has to start with a slash ("/").
     * @return string The full URL for a route for this application.
     */
    public function urlFor(string $pattern): string
    {
        // If we're in the document root, the URL is already our pattern.
        $url = $pattern;

        // If there's a base path (not in the document root) then we prepend it
        if ($this->basePath) {
            $url = $this->basePath . $url;
        }

        return $url;
    }

    /**
     * Returns the base path if the application is not in the server's document root. If no base path is set, an empty
     * string is returned.
     * @return string The base path without a trailing slash or an empty string if no base path is set.
     */
    public function getBasePath(): string
    {
        return $this->basePath ?? "";
    }

    /**
     * Performs a generic redirect to a full URL using header(). GET parameters may optionally be supplied as an
     * associative array.
     * @param string $url The target URL for the redirect.
     * @param array<string>|null $queryParameters Optional GET parameters to be appended to the URL.
     * @return never Never returns due to the redirect.
     */
    public function redirect(string $url, ?array $queryParameters = null): never
    {
        // Set response code 302 for a generic redirect.
        http_response_code(302);
        if (isset($queryParameters)) {
            header("Location: $url" . "?" . http_build_query($queryParameters));
        } else {
            header("Location: $url");
        }
        exit();
    }

    /**
     * Perform a generic redirect to a route pattern. This pattern will then be converted to a full URL and the redirect
     * will be performed.
     * @param string $pattern The route pattern. Has to start with a slash ("/").
     * @return never Never returns due to the redirect.
     */
    public function redirectTo(string $pattern): never
    {
        $this->redirect($this->urlFor($pattern));
    }

    /**
     * Static router method. This simply returns the current route. The route is a combination of method and request
     * URI. If a base path is specified, it is removed from the request URI before the route is returned.
     * When using the static routing method, all logic handling the route has to be done separately.
     * @param string $basePath The base path that is to be removed from the route when the application is not in
     * the server's document root but in a subdirectory. Specify without a trailing slash.
     * @return string The current route.
     */
    public static function getRoute(string $basePath = ""): string
    {
        $uri = rawurldecode($_SERVER["REQUEST_URI"]);

        // Remove the base path if there is one
        if ($basePath) {
            $uri = str_replace($basePath, "", $uri);
        }

        // Remove potential URI parameters (everything after ?) and return
        $trimmedUri = strtok($uri, "?");

        /* Since strtok can return false (if $uri was an empty string, which it should never be because
           $_SERVER["REQUEST_URI"] should always have a value), return $uri if that was ever the case in order to have a
           consistent string return value. */
        return $_SERVER["REQUEST_METHOD"] . " " . ($trimmedUri ?: $uri);
    }
}
