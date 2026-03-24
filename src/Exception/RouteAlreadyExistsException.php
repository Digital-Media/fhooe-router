<?php

declare(strict_types=1);

namespace Fhooe\Router\Exception;

use LogicException;

/**
 * Exception thrown when attempting to add a route that already exists (configuration error).
 * @package Fhooe\Router\Exception
 * @author Wolfgang Hochleitner <wolfgang.hochleitner@fh-hagenberg.at>
 * @since 2.0.0
 */
class RouteAlreadyExistsException extends LogicException {}
