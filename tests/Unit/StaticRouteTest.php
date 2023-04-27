<?php

use Fhooe\Router\Router;

/**
 * Defines the method GET and the route /test for testing purposes.
 * GET /test is considered the route that is called in the client.
 */
beforeEach(function () {
    $_SERVER["REQUEST_METHOD"] = "GET";
    $_SERVER["REQUEST_URI"] = "/test";
});

/**
 * Test getting the current route with a base path
 * Expects the route GET /some/basepath/test
 */
it("gets the current route with a base path set", function () {
    $_SERVER["REQUEST_URI"] = "/some/basepath/test";

    $route = Router::getRoute("/some/basepath");

    expect($route)->toBe("GET /test");
});

/**
 * Test getting the current route with a base path
 * Expects the route GET /test
 */
it("gets the current route without a base path", function () {
    $route = Router::getRoute();

    expect($route)->toBe("GET /test");
});

/**
 * Test getting the full URL when a base path is not set
 * Expects the full URL /test
 */
it("gets the full URL from a route pattern without a base path", function () {
    // Get the route and make sure no base path is set
    Router::getRoute();

    $url = Router::urlFor("/test");

    expect($url)->toBe("/test");
});

/**
 * Test getting the full URL when a base path is set.
 * Expects the full URL /some/basepath/test
 */
it("gets the full URL from a route pattern with a base path", function () {
    $_SERVER["REQUEST_URI"] = "/some/basepath/test";

    // Get the route and specify a base path on the way
    Router::getRoute("/some/basepath");

    $url = Router::urlFor("/test");

    expect($url)->toBe("/some/basepath/test");
});
