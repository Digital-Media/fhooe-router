<?php

/**
 * Tests for object instantiation.
 */

use Fhooe\Router\Router;
use Psr\Log\LoggerInterface;

/**
 * Creates a Router object and checks if it is an instance of the Router class.
 */
it("creates a Router object", function () {
    expect(new Router())->toBeInstanceOf(Router::class);
});

/**
 * Creates a Router object with a base path and checks if it is set correctly.
 */
it("creates a Router object with a base path", function () {
    $router = new Router();
    $router->setBasePath("/api");
    expect($router->getBasePath())->toBe("/api");
});

/**
 * Creates a Router object with a logger and checks if it is set correctly.
 */
it("creates a Router object with a logger", function () {
    $logger = Mockery::mock(LoggerInterface::class);

    // We expect that the logger receives at least one info message
    $logger
        ->shouldReceive('info')
        ->with("Base path set to: {basePath}", ["basePath" => "/api"])
        ->atLeast()
        ->once();

    $router = new Router($logger);
    expect($router)->toBeInstanceOf(Router::class);

    // Führe eine Aktion aus, die Logging auslöst
    $router->setBasePath("/api");
});

/**
 * Creates a Router object without a logger and checks if it works correctly.
 */
it("creates a Router object without a logger", function () {
    $router = new Router();
    expect($router)
        ->toBeInstanceOf(Router::class)
        ->and($router->getBasePath())->toBe("");
});
