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

/**
 * Test handling URL parameters in routes
 */
it("handles URL parameters correctly", function () {
    $_SERVER["REQUEST_URI"] = "/user/123";
    
    $this->router->addRoute(HttpMethod::GET, "/user/{id}", function ($id) {
        echo "User ID: $id";
    });

    ob_start();
    $this->router->run();
    $output = ob_get_contents();
    ob_end_clean();

    expect($output)->toBe("User ID: 123");
});

/**
 * Test handling multiple URL parameters
 */
it("handles multiple URL parameters correctly", function () {
    $_SERVER["REQUEST_URI"] = "/post/123/comment/456";
    
    $this->router->addRoute(HttpMethod::GET, "/post/{postId}/comment/{commentId}", function ($postId, $commentId) {
        echo "Post: $postId, Comment: $commentId";
    });

    ob_start();
    $this->router->run();
    $output = ob_get_contents();
    ob_end_clean();

    expect($output)->toBe("Post: 123, Comment: 456");
});

/**
 * Test handling trailing slashes
 */
it("treats routes with and without trailing slash as different", function () {
    $_SERVER["REQUEST_URI"] = "/test/";
    
    $this->router->set404Callback(function () {
        echo "404";
    });
    
    // Route ohne trailing slash
    $this->router->addRoute(HttpMethod::GET, "/test", function () {
        echo "test without slash";
    });

    ob_start();
    $this->router->run();
    $output = ob_get_contents();
    ob_end_clean();

    expect($output)->toBe("404");

    // Route mit trailing slash
    $this->router->addRoute(HttpMethod::GET, "/test/", function () {
        echo "test with slash";
    });

    ob_start();
    $this->router->run();
    $output = ob_get_contents();
    ob_end_clean();

    expect($output)->toBe("test with slash");
});

/**
 * Test handling query parameters
 */
it("handles query parameters correctly", function () {
    $_SERVER["REQUEST_URI"] = "/test?param=value";
    
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
 * Test handling missing REQUEST_URI
 */
it("returns root path when REQUEST_URI is not set", function () {
    unset($_SERVER["REQUEST_URI"]);
    
    expect($this->router->getUri())->toBe("/");
});

/**
 * Test successful POST request
 */
it("handles POST request correctly", function () {
    $_SERVER["REQUEST_METHOD"] = "POST";
    $_SERVER["REQUEST_URI"] = "/submit";
    
    $this->router->addRoute(HttpMethod::POST, "/submit", function () {
        echo "submitted";
    });

    ob_start();
    $this->router->run();
    $output = ob_get_contents();
    ob_end_clean();

    expect($output)->toBe("submitted");
});

/**
 * Test handling optional parts in routes
 */
it("handles optional parts in routes correctly", function () {
    $_SERVER["REQUEST_URI"] = "/test";
    
    $this->router->addRoute(HttpMethod::GET, "/test[/]", function () {
        echo "test with optional slash";
    });

    ob_start();
    $this->router->run();
    $output = ob_get_contents();
    ob_end_clean();

    expect($output)->toBe("test with optional slash");

    // Test with trailing slash
    $_SERVER["REQUEST_URI"] = "/test/";
    
    ob_start();
    $this->router->run();
    $output = ob_get_contents();
    ob_end_clean();

    expect($output)->toBe("test with optional slash");
});
