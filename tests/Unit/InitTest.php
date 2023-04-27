<?php

use Fhooe\Router\Router;

it ("creates a Router object", function () {
    expect(new Router())->toBeInstanceOf(Router::class);
});
