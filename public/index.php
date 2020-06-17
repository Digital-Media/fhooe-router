<?php
require "../vendor/autoload.php";

use Router\Router;
use Demo\Demo;
use Exercises\Login;
use Exercises\Register;

$route = Router::getRoute();
switch ($route) {
    case 'GET /' :
        $template = file("../templates/index.html.twig");
        foreach ($template as $line)  {
            echo $line;
        }
        break;
    case 'GET /demo' :
        $demo = new Demo("demo.html.twig");
        $demo->show();
        break;
    case 'POST /demo' :
        $demo = new Demo("demo.html.twig");
        $demo->normForm();
        break;
    case 'GET /login' :
        $login = new Login("login.html.twig");
        $login->show();
        break;
    case 'POST /login' :
        $login = new Login("login.html.twig");
        $login->normForm();
        break;
    case 'GET /register' :
        $register = new Register("register.html.twig");
        $register->show();
        break;
    case 'POST /register' :
        $register = new Register("register.html.twig");
        $register->normForm();
        break;
    default:
        http_response_code(404);
        $template = file("../templates/404.html.twig");
        foreach ($template as $line)  {
            echo $line;
        }
        break;
}
