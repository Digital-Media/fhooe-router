<?php

/**
 * Tests for the instantiated router.
 */

use Fhooe\Router\Exception\HandlerNotSetException;
use Fhooe\Router\Type\HttpMethod;
use Fhooe\Router\Router;

/**
 * Creates a Router object and defines the method GET and the route /test for testing purposes.
 * GET /test is considered the route that is called in the client.
 */
beforeEach(function () {
    $this->router = new Router();
    $_SERVER["REQUEST_METHOD"] = "GET";
    $_SERVER["REQUEST_URI"] = "/test";
});

/**
 * Test adding a correct and found route
 * Adds the route GET /test and runs it. Expects the output by that route callback.
 */
it("adds the GET route /test and runs it", function () {
    $this->router->addRoute(HttpMethod::GET, "/test", function () {
        echo "test";
    });

    ob_start();
    $this->router->run();
    $output = ob_get_contents();
    ob_end_clean();

    expect($output)->toBe("test");
});

/**
 * Test adding a correct and found route with a base path
 * Adds the route GET /test with the base path /some/basepath and runs it. Expects the output by that route callback.
 */
it("adds the GET route /test with a matching base path set and runs it", function () {
    $_SERVER["REQUEST_URI"] = "/some/basepath/test";

    $this->router->set404Callback(function () {
        echo "404";
    });
    $this->router->setBasePath("/some/basepath");

    $this->router->addRoute(HttpMethod::GET, "/test", function () {
        echo "test";
    });

    ob_start();
    $this->router->run();
    $output = ob_get_contents();
    ob_end_clean();

    expect($output)->toBe("test");
});

/**
 * Test adding a correct route but with an incorrect base path
 * Adds the route GET /test with the base path /some/basepath and runs it. Since the actual path is /some/path/test,
 * it expects the output by the 404 callback that is also set.
 */
it("adds the GET route /test with a mismatching base path set and runs it", function () {
    $_SERVER["REQUEST_URI"] = "/some/path/test";

    $this->router->set404Callback(function () {
        echo "404";
    });
    $this->router->setBasePath("/some/basepath");

    $this->router->addRoute(HttpMethod::GET, "/test", function () {
        echo "test";
    });

    ob_start();
    $this->router->run();
    $output = ob_get_contents();
    ob_end_clean();

    expect($output)->toBe("404");
});

/**
 * Test adding a correct route that is not found (method does not match but path would) without a 404 handler
 * Adds the route POST /test and runs it. Expects a HandlerNotSetException since a 404 callback is missing.
 */
it("adds the POST route /test and runs it without a 404 handler", function () {
    $this->router->addRoute(HttpMethod::POST, "/test", function () {
        echo "test";
    });

    expect(fn() => $this->router->run())->toThrow(HandlerNotSetException::class, "404 handler not set.");
});

/**
 * Test adding a correct route that is not found (method matches but path doesn't) without a 404 handler
 * Adds the route GET /other and runs it. Expects a HandlerNotSetException since a 404 callback is missing.
 */
it("adds the GET route /other and runs it without a 404 handler", function () {
    $this->router->addRoute(HttpMethod::GET, "/other", function () {
        echo "other";
    });

    expect(fn() => $this->router->run())->toThrow(HandlerNotSetException::class, "404 handler not set.");
});

/**
 * Test adding a correct route that is not found (method does not match but path would) with a 404 handler
 * Adds the route POST /test and runs it. Expects the output of the 404 handler.
 */
it("adds the POST route /test, sets a 404 handler and runs it", function () {
    $this->router->set404Callback(function () {
        echo "404";
    });

    $this->router->addRoute(HttpMethod::POST, "/test", function () {
        echo "test";
    });

    ob_start();
    $this->router->run();
    $output = ob_get_contents();
    ob_end_clean();

    expect($output)->toBe("404");
});

/**
 * Test adding a correct route that is not found (method matches but path doesn't) with a 404 handler
 * Adds the route GET /other and runs it. Expects the output of the 404 handler.
 */
it("adds the GET route /other, sets a 404 handler and runs it", function () {
    $this->router->set404Callback(function () {
        echo "404";
    });

    $this->router->addRoute(HttpMethod::GET, "/other", function () {
        echo "other";
    });

    ob_start();
    $this->router->run();
    $output = ob_get_contents();
    ob_end_clean();

    expect($output)->toBe("404");
});
