<?php

namespace View;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

/**
 * Encapsulates data for displaying a form or result of a form submission and uses the Twig template engine to render
 * its output.
 *
 * This class manages the parameters that are involved in the form (which should implement ParameterInterface) and
 * allows for a general PHP redirect. It initializes the Twig template engine and passes the stored parameters to it.
 * Twig is then used to render and display the form as specified in the main template. This view also passes on the
 * $_SERVER superglobal to the template (accessible as "_server"). Exceptions generated by Twig are shown as errors and
 * logged accordingly.
 *
 * @package Fhooe\NormForm\View
 * @author Wolfgang Hochleitner <wolfgang.hochleitner@fh-hagenberg.at>
 * @author Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @version 1.2.2
 */
class View
{
    /**
     * The name of the view (the template file that is to be rendered).
     *
     * @var string
     */
    private $templateName;

    /**
     * The relative path to the directory where the template files are stored.
     *
     * @var string
     */
    private $templateDirectory;

    /**
     * The relative path where cached/compiled templates are to be stored.
     *
     * @var string
     */
    private $templateCacheDirectory;

    /**
     * The Twig loader instance.
     *
     * @var FilesystemLoader
     */
    private $loader;

    /**
     * The main instance of the Twig template engine (environment).
     *
     * @var Environment
     */
    private $twig;

    /**
     * Creates a new view with the main template to be displayed, the path to the template and compiled templates
     * directory as well as parameters of the form. Also initializes the Twig template engine with caching and auto
     * reload enabled. Two global variables for $_SERVER and (if available) for $_SESSION are passed to the template for
     * easy access.
     * @param string $templateName The name of the template to be displayed.
     * @param string $templateDirectory The path where the template file is located (default is "templates").
     * @param string $templateCacheDirectory The path where cached template files are to be stored (default is
     * "templates_c").
     */
    public function __construct(
        string $templateName
    ) {
        $this->templateName = $templateName;
        $this->templateDirectory = '../templates';
        $this->templateCacheDirectory = '../templates_c';

        $this->loader = new FilesystemLoader($this->templateDirectory);
        $this->twig = new Environment($this->loader, [
            "cache" => $this->templateCacheDirectory,
            "auto_reload" => true
        ]);

        $this->twig->addGlobal("_server", $_SERVER);
        if (isset($_SESSION)) {
            $this->twig->addGlobal("_session", $_SESSION);
        }
    }

    /**
     * Returns the name of the main template that's being used for display.
     * @return string The template name.
     */
    public function getTemplateName(): string
    {
        return $this->templateName;
    }

    /**
     * Displays the current view. Iterates over all the parameters and stores them in a temporary, associative array.
     * Twig then displays the main template, using the array with the parameters.
     * Exceptions generated by Twig are shown as errors and logged accordingly for simplification.
     */
    public function display(array $templateParameters = []): void
    {
        try {
            $this->twig->display($this->templateName, $templateParameters);
        } catch (LoaderError $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        } catch (RuntimeError $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        } catch (SyntaxError $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
    }
}