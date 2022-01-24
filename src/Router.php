<?php

declare(strict_types=1);

namespace Fhooe\Router;

use Closure;
use Fhooe\Router\Exception\HandlerNotSetException;
use InvalidArgumentException;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

/**
 * A simple object-oriented Router for educational purposes.
 *
 * This routing class can be used in two ways:
 * 1. Instantiate it, set routes with callbacks and run it.
 * 2. Use the static getRoute() method to just retrieve the HTTP method and route and perform the logic yourself.
 * @package Fhooe\Router
 * @author Wolfgang Hochleitner <wolfgang.hochleitner@fh-hagenberg.at>
 * @author Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @since 0.1.0
 */
class Router
{
    use LoggerAwareTrait;

    /**
     * @var array<string> The supported HTTP methods for this router.
     */
    private const METHODS = [
        "GET",
        "POST"
    ];

    /**
     * @var array<array{method: string, pattern: string, callback: Closure}> All routes and their associated callbacks.
     */
    private array $routes;

    /**
     * @var Closure|null The 404 callback when no suitable other route is found.
     */
    private ?Closure $noRouteCallback;

    /**
     * @var string|null The base path that is considered when this application is not in the server's document root.
     */
    private static ?string $basePath = null;

    /**
     * Creates a new Router. The list of routes is initially empty, so is the supplied 404 callback. The logger instance
     * is also empty but can be added at any time.
     */
    public function __construct()
    {
        $this->routes = [];
        $this->noRouteCallback = null;
        $this->logger = new NullLogger();
    }

    /**
     * Sets the base path if the application is not in the server's document root.
     * @param string $basePath The base path. Specify without a trailing slash.
     */
    public function setBasePath(string $basePath): void
    {
        self::$basePath = $basePath;
    }

    /**
     * Adds a route, consisting of a method and a URI pattern together with its callback handler.
     * @param string $method The HTTP method of this route.
     * @param string $pattern The routing pattern.
     * @param Closure $callback The callback that is called when the route matches.
     */
    public function addRoute(string $method, string $pattern, Closure $callback): void
    {
        if (in_array($method, self::METHODS)) {
            $this->routes[] = [
                "method" => $method,
                "pattern" => $pattern,
                "callback" => $callback
            ];
            $this->logger->info("Route added: " . $method . " " . $pattern);
        } else {
            throw new InvalidArgumentException("Method must be one of the following: " . implode("|", self::METHODS));
        }
    }

    /**
     * Shorthand method for adding a new GET route.
     * @param string $pattern The routing pattern.
     * @param Closure $callback The callback that is called when the route matches.
     */
    public function get(string $pattern, Closure $callback): void
    {
        $this->addRoute("GET", $pattern, $callback);
    }

    /**
     * Shorthand method for adding a new POST route.
     * @param string $pattern The routing pattern.
     * @param Closure $callback The callback that is called when the route matches.
     */
    public function post(string $pattern, Closure $callback): void
    {
        $this->addRoute("POST", $pattern, $callback);
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
            throw new HandlerNotSetException("404 Handler not set.");
        }
    }

    /**
     * Handles a single route. The method first matches the current request's method with the one of the route.
     * If there is a match, the URI pattern is compared. In case of a match, the associated callback is invoked.
     * @param array{method: string, pattern: string, callback: Closure} $route The route to handle.
     * @return bool Returns true, if there was a match and the route was handled, otherwise false.
     */
    private function handle(array $route): bool
    {
        $method = $_SERVER["REQUEST_METHOD"];

        if ($route["method"] === $method) {
            $uri = $this->getUri();

            if ($route["pattern"] === $uri) {
                if (is_callable($route["callback"])) {
                    $route["callback"]();
                    $this->logger->info(
                        "Route match found: " . $route["method"] . " " . $route["pattern"] . ". Callback executed."
                    );
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
    private function getUri(): string
    {
        $uri = rawurldecode($_SERVER["REQUEST_URI"]);

        // Remove the base path if there is one
        if (self::$basePath) {
            $uri = str_replace(self::$basePath, "", $uri);
        }

        // Remove potential URI parameters (everything after ?) and return
        $trimmedUri = strtok($uri, "?");

        /* Since strtok can return false (if $uri was an empty string, which it should never be because
           $_SERVER["REQUEST_URI"] should always have a value), return $uri if that was ever the case in order to have a
           consistent string return value. */
        return $trimmedUri ?: $uri;
    }

    /**
     * Static router method. This simply returns the current route. The route is a combination of method and request
     * URI. If a base path is specified, it is removed from the request URI before the route is returned.
     * When using the static routing method, all logic handling the route has to be done separately.
     * @param string|null $basePath The base path that is to be removed from the route when the application is not in
     * the server's document root but in a subdirectory. Specify without a trailing slash.
     * @return string The current route.
     */
    public static function getRoute(?string $basePath = null): string
    {
        $routingParams["method"] = strip_tags($_SERVER["REQUEST_METHOD"]);
        $routingParams["route"] = strip_tags($_SERVER["REQUEST_URI"]);

        if ($basePath) {
            self::$basePath = $basePath;
            $routingParams["route"] = str_replace($basePath, "", $routingParams["route"]);
        }

        return $routingParams["method"] . " " . $routingParams["route"];
    }

    /**
     * Returns the full URL for a given route. If a base path is set, it is prepended to account for projects in
     * subdirectories.
     * @param string $pattern The pattern of a route, has to start with a slash ("/").
     * @return string The full URL for a route for this application.
     */
    public static function urlFor(string $pattern): string
    {
        // If we're in the document root, the URL is already our pattern.
        $url = $pattern;

        // If there's a base path (not in the document root) then we prepend it
        if (self::$basePath) {
            $url = self::$basePath . $url;
        }

        return $url;
    }

    /**
     * Returns the base path if the application is not in the server's document root. If no base path is set, an empty
     * string is returned.
     * @return string The base path without a trailing slash or an empty string if no base path is set.
     */
    public static function getBasePath(): string
    {
        return self::$basePath ?? "";
    }

    /**
     * Performs a generic redirect to a full URL using header(). GET-Parameters may optionally be supplied as an
     * associative array.
     * @param string $url The target URL for the redirect.
     * @param array<string>|null $queryParameters Optional GET parameters to be appended to the URL.
     * @return void Returns nothing.
     */
    public static function redirect(string $url, ?array $queryParameters = null): void
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
     * @return void Returns nothing.
     */
    public static function redirectTo(string $pattern): void
    {
        self::redirect(self::urlFor($pattern));
    }
}
