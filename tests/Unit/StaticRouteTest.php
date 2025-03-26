<?php

/**
 * Tests for the static router.
 */

use Fhooe\Router\Router;
use Fhooe\Router\Type\HttpMethod;

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
 * Test getting the current route with different HTTP methods
 */
it("gets the current route with different HTTP methods", function () {
    foreach (HttpMethod::cases() as $method) {
        $_SERVER["REQUEST_METHOD"] = $method->name;
        $_SERVER["REQUEST_URI"] = "/test";
        
        $route = Router::getRoute();
        expect($route)->toBe("{$method->name} /test");
    }
});

/**
 * Test getting the current route with invalid URI
 */
it("handles invalid URI gracefully", function () {
    $_SERVER["REQUEST_URI"] = "";
    
    $route = Router::getRoute();
    expect($route)->toBe("GET /");
});

/**
 * Test getting the current route with query parameters
 */
it("strips query parameters from URI", function () {
    $_SERVER["REQUEST_URI"] = "/test?param=value";
    
    $route = Router::getRoute();
    expect($route)->toBe("GET /test");
});
