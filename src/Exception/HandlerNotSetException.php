<?php

namespace Fhooe\Router\Exception;

use LogicException;

/**
 * Exception that can be thrown when a callback for a certain action is not specified.
 * @package Fhooe\Router\Exception
 * @author Wolfgang Hochleitner <wolfgang.hochleitner@fh-hagenberg.at>
 * @author Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @since 0.1.0
 */
class HandlerNotSetException extends LogicException
{
}
