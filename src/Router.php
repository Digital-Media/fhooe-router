<?php

namespace Fhooe\Router;

use Closure;
use InvalidArgumentException;

class Router
{
    private const METHODS = [
        "GET",
        "POST"
    ];

    private array $routes;

    private ?Closure $noRouteCallback;

    /**
     * @var string|null The base path that is considered when this application is not in the server's document root.
     */
    private static ?string $basePath = null;

    public function __construct()
    {
        $this->routes = [];
        $this->noRouteCallback = null;
    }

    public function setBasePath(string $basePath): void
    {
        self::$basePath = $basePath;
    }

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

    public function get(string $pattern, Closure $callback): void
    {
        $this->addRoute("GET", $pattern, $callback);
    }

    public function post(string $pattern, Closure $callback): void
    {
        $this->addRoute("POST", $pattern, $callback);
    }

    public function set404(Closure $callback)
    {
        if (get_class($callback) === Closure::class) {
            $this->noRouteCallback = $callback;
        } else {
            throw new InvalidArgumentException("Callback has to be an anonymous function of type Closure.");
        }
    }

    public function run(): void
    {
        $routeHandled = false;
        foreach ($this->routes as $route) {
            if ($this->handle($route)) {
                return;
            }
        }

        // If no route was handled, call the 404 callback
        ($this->noRouteCallback)();
    }

    private function handle(array $route): bool
    {
        $method = $_SERVER["REQUEST_METHOD"];

        if ($route["method"] === $method) {
            $uri = $this->getUri();

            if ($route["pattern"] === $uri) {
                $route["callback"]();
                return true;
            }
            return false;
        }
        return false;
    }

    private function getUri(): string
    {
        $uri = rawurldecode($_SERVER["REQUEST_URI"]);

        // Remove the base path if there is one
        if (self::$basePath) {
            $uri = str_replace(self::$basePath, "", $uri);
        }

        // Remove potential URI parameters (everything after ?) and return
        return strtok($uri, "?");
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

    /**
     * Performs a generic redirect using header(). GET-Parameters may optionally be supplied as an associative array.
     * @param string $location The target location for the redirect.
     * @param array|null $queryParameters GET-Parameters for HTTP-Request
     */
    public static function redirectTo(string $location, array $queryParameters = null): void
    {
        if (isset($queryParameters)) {
            header("Location: $location" . "?" . http_build_query($queryParameters));
        } else {
            header("Location: $location");
        }
        exit();
    }
}
