<?php
require "../vendor/autoload.php";

use Demo\Demo;
use Router\Router;

$route = Router::getRoute();
switch ($route) {
    case 'GET /' :
        $demo = new Demo("demo.html.twig");
        $demo->show();
        break;
    case 'POST /' :
        $demo = new Demo("demo.html.twig");
        $demo->normForm();
        break;
    case '' :
        $demo = new Demo("demo.html.twig");
        $demo->show();
        break;
    case 'GET /about' :
        $template = file("../templates/about.html.twig");
        foreach ($template as $line)  {
            echo $line;
        }
        break;
    default:
        http_response_code(404);
        $template = file("../templates/404.html.twig");
        foreach ($template as $line)  {
            echo $line;
        }
        break;
}
