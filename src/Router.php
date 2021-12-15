<?php

declare(strict_types=1);

namespace Fhooe\Router;

use Closure;
use Fhooe\Router\Exception\HandlerNotSetException;
use InvalidArgumentException;

/**
 * A simple object-oriented Router for educational purposes.
 *
 * This routing class can be used in two ways:
 * 1. Instantiate it, set routes with callbacks and run it.
 * 2. Use the static getRoute() methode to just retrieve the protocol and route and perform the logic yourself.
 * @package Fhooe\Router
 * @author Wolfgang Hochleitner <wolfgang.hochleitner@fh-hagenberg.at>
 * @author Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @since 0.1.0
 */
class Router
{
    /**
     * @var array<string> The supported HTTP methods for this router.
     */
    private const METHODS = [
        "GET",
        "POST"
    ];

    /**
     * @var array<array<Closure|string>> All routes and their associated callbacks.
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
     * Creates a new Router. The list of routes is initially empty, so is the supplied 404 callback.
     */
    public function __construct()
    {
        $this->routes = [];
        $this->noRouteCallback = null;
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
        } else {
            throw new HandlerNotSetException("404 Handler not set.");
        }
    }

    /**
     * Handles a single route. The functions first matches the current request's method with the one of the route.
     * If there is a match, the URI pattern is compared. In case of a match, the associated callback is invoked.
     * @param array<Closure|string> $route The route to handle.
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
     * Returns the current route. The route is a combination of protocol and request URI. If a base path is specified,
     * it is removed from the request URI before the route is returned.
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
     * Return the correct URL for a given route. If a base Path is set, it is appended to account for projects in
     * subdirectories.
     * @param string $route The full route specification consisting of protocol and URL.
     * @return string The correct URL for a route.
     */
    public static function urlFor(string $route): string
    {
        $url = "";
        if ($spacePos = mb_strpos($route, " ")) {
            $url = mb_substr($route, $spacePos + 1);
        }

        if (self::$basePath) {
            $url = self::$basePath . $url;
        }

        return $url;
    }
}
