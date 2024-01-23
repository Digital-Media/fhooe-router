<?php

/**
 * Tests for object instantiation.
 */

use Fhooe\Router\Router;

/**
 * Creates a Router object and checks if it is an instance of the Router class.
 */
it("creates a Router object", function () {
    expect(new Router())->toBeInstanceOf(Router::class);
});
