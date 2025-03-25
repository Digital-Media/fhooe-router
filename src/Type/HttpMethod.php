<?php

declare(strict_types=1);

namespace Fhooe\Router\Type;

/**
 * The supported HTTP methods for the router.
 * @package Fhooe\Router\Type   
 * @author Wolfgang Hochleitner <wolfgang.hochleitner@fh-hagenberg.at>
 * @since 1.0.0
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
