<?php
require "../vendor/autoload.php";

session_start();

use Router\Router;
use Demo\Demo;
use Exercises\Imprint;
use Exercises\Contact;
use Exercises\Login;
use Exercises\Register;
use Demo\FAdemo;
use Demo\Solution;
use View\View;

/**
 * Activate Debugging-Messages here for easier testing
 */
const DEBUG = true;
if (DEBUG) {
    echo "<br>WARNING: Debugging is enabled. Set DEBUG to false for production use in " . __FILE__;
    echo "<br>Connect via SSH and send tail -f /var/log/apache2/error.log";
    echo " to see errors not displayed in Browser<br><br>";
    error_reporting(E_ALL);
    ini_set('html_errors', '1');
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
}

/**
 * @var string DATA_DIRECTORY Sets the directory where the meta data (JSON files) for users is stored.
 */
const DATA_DIRECTORY = "../data/";

// Login Handling

/**
 * @var string IS_LOGGED_IN is set in SESSION-Array, if user is logged in successfully.
 */
const IS_LOGGED_IN = "isloggedin";

$route = Router::getRoute("/code/fhooe-router/public");
switch ($route) {
    case 'GET /' :
        $view = new View("index.html.twig");
        $view->display();
        break;
    case 'GET /demo' :
        $demo = new Demo("demo.html.twig");
        $demo->show();
        break;
    case 'POST /demo' :
        $demo = new Demo("demo.html.twig");
        $demo->normForm();
        break;
    case 'GET /imprint' :
        $imprint = new Imprint();
        $imprint->show();
        break;
    case 'GET /contact' :
        $contact = new Contact("contactMain.html.twig");
        $contact->show();
        break;
    case 'POST /contact' :
        $contact = new Contact("contactMain.html.twig");
        $contact->normForm();
        break;
    case 'GET /login' :
        $login = new Login("loginMain.html.twig");
        $login->show();
        break;
    case 'POST /login' :
        $login = new Login("loginMain.html.twig");
        $login->normForm();
        break;
    case 'GET /register' :
        $register = new Register("registerMain.html.twig");
        $register->show();
        break;
    case 'POST /register' :
        $register = new Register("registerMain.html.twig");
        $register->normForm();
        break;
    case 'GET /fademo' :
        $fademo = new FAdemo("fademoMain.html.twig");
        $fademo->show();
        break;
    case 'POST /fademo' :
        $fademo = new FAdemo("fademoMain.html.twig");
        $fademo->normForm();
        break;
    case 'GET /solution' :
        $solution = new Solution("solutionMain.html.twig");
        $solution->show();
        break;
    case 'POST /solution' :
        $solution = new Solution("solutionMain.html.twig");
        $solution->normForm();
        break;
    default:
        http_response_code(404);
        $template = file("../templates/404.html.twig");
        foreach ($template as $line)  {
            echo $line;
        }
        break;
}
