<?php

declare(strict_types=1);

namespace Fhooe\Router\Type;

/**
 * The supported HTTP methods for the router.
 */
enum HttpMethod
{
    /**
     * Represents a GET request.
     */
    case GET;
    /**
     * Represents a POST request.
     */
    case POST;
}
