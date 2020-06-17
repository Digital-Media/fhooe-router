<?php

namespace Router;

class Router
{
    /**
     * Checks if the current request was an initial one (thus using GET) or a recurring one after a form submission
     * (where POST was used).
     * @return bool Returns true if a form was submitted or false if it was an initial call.
     */
    public static function getRoute(): string
    {

        $routingParams['method'] = strip_tags($_SERVER["REQUEST_METHOD"]);
        $routingParams['route'] = strip_tags($_SERVER['REQUEST_URI']);
        return $routingParams['method'] . " " . $routingParams['route'];
    }
    /**
     * Performs a generic redirect using header(). GET-Parameters may optionally be supplied as an associative array.
     * @param string $location The target location for the redirect.
     * @param array $queryParameters GET-Parameters for HTTP-Request
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
