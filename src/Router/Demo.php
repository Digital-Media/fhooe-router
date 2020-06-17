<?php

namespace Router;

/**
 * A demo implementation for a HTML form
 *
 * @package FhooeRouter
 * @author Wolfgang Hochleitner <wolfgang.hochleitner@fh-hagenberg.at>
 * @author Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @version 0.0.1
 */
class Demo
{

    /**
     * Constructor for creating a new object. Use this to perform initializations of properties you need throughout your
     * application, otherwise leave it as is. Do not remove the call to the parent constructor.
     * @param $template string Holds the initial template name used for displaying the form.
     */

    public function __construct($template)
    {
        $this->currentView = new View($template);
        $this->errorMessages = [];
        $this->statusMessage = "";
        $this->templateParameters = [];
    }

    /**
     * Validates the form submission. The criteria for this example are non-empty fields for first and last name.
     * These are checked using isEmptyPostField() in two separate if-clauses.
     * If a criterion is violated, an entry in errorMessages is created.
     * The array holding these error messages is then added to the parameters of the current view. If no error
     * messages where created, validation is seen as successful.
     *
     * @return bool Returns true if validation was successful, otherwise false.
     */
    protected function isValid(): bool
    {
        $params = $this->getBodyParams();
        if ($this->isEmptyPostField('firstname')) {
            $this->errorMessages['firstname'] = "First name is required.";
        }
        if ($this->isEmptyPostField('lastname')) {
            $this->errorMessages['lastname'] = "Last name is required.";
        }
        $this->templateParameters['firstname'] = $params['firstname'];
        $this->templateParameters['lastname'] = $params['lastname'];
        $this->templateParameters['errorMessages'] = $this->errorMessages;

        return (count($this->errorMessages) === 0);
    }

    /**
     * Business logic method used to process the data that was used after a successful validation. In this example the
     * received data is stored in result and passed on to the view. In more complex scenarios this would be the
     * place to add things to a database or perform other tasks before displaying the data.
     */
    protected function business(): void
    {
        $this->templateParameters['firstname'] = "";
        $this->templateParameters['lastname'] = "";
        $this->templateParameters['result'] = $_POST;
        $this->templateParameters['statusMessage'] = "Processing successful!";
    }

    /**
     * Used to display output. The currently used object of type View is used to display the content by calling
     * the display() method. Depending on the type of View object, a certain template engine will be used to
     * render the output. The view object will handle passing on the parameters to the template engine.
     */
    public function show(): void
    {
        $this->currentView->display($this->templateParameters);
    }

    /**
     * Checks if the current request was an initial one (thus using GET) or a recurring one after a form submission
     * (where POST was used).
     * @return bool Returns true if a form was submitted or false if it was an initial call.
     */
    public static function getRoute(): array
    {

        $method = strip_tags($_SERVER["REQUEST_METHOD"]);
        switch ($method) {
            case "POST":
                $route['method'] = "POST";
                $route['route'] = strip_tags($_POST['route']);
                break;
            case "GET":
                $route['method'] = "GET";
                isset($_GET['route']) ? $route['route'] = strip_tags($_GET['route']) : $route['route'] = "normform" ;
                break;
        }
        return $route;
    }

    /**
     * Convenience method to check if a form field is empty, thus contains only an empty string. This is preferred to
     * PHP's own empty() method which also defines inputs such as "0" as empty.
     * @param string $index The index in the super global $_POST array.
     * @return bool Returns true if the form field is empty, otherwise false.
     */
    protected function isEmptyPostField(string $index): bool
    {
        return (!isset($_POST[$index]) || strlen(trim($_POST[$index])) === 0);
    }

    /**
     * Returns the supplied parameters.
     * @return array The parameters.
     */
    protected function getBodyParams(): array
    {
        return $this->params = $_POST;
    }

    /**
     * Performs a generic redirect using header(). GET-Parameters may optionally be supplied as an associative array.
     * @param string $location The target location for the redirect.
     * @param array $queryParameters GET-Parameters for HTTP-Request
     */
    public static function redirectTo(string $location, array $queryParameters = null): void
    {
        if (isset($queryParameters)) {
            header("Location: $location" . "?" . http_build_query($queryParameters));
        } else {
            header("Location: $location");
        }
        exit();
    }
}
