<?php
/**
 * logout.php zerstört die Session
 *
 * In diesem File muss für die UE nichts geändert werden
 *
 * @author  Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @package onlineshop
 * @version 2018
 */

require "../vendor/autoload.php";

session_start();

use Router\Router;

$_SESSION = [];
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), "", time() - 86400, "/");
}
session_destroy();
Router::redirectTo('/');
