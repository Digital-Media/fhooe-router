<?php

namespace Fhooe\Router;

class Router
{
    /**
     * @var string|null The base path that is considered when this application is not in the server's document root.
     */
    private static ?string $basePath = null;

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
    public static function urlFor(string $route): string {
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
