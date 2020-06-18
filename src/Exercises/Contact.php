<?php
namespace Exercises;

use NormForm\AbstractNormForm;
use View\View;
use Demo\Utilities;

/*
 * the object-oriented and template based Contact implements a contact form.
 * *
 * @author Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @package phpintro
 * @version 2017
 */
final class Contact extends AbstractNormForm
{
    /**
     * Contact Constructor.
     *
     * Uses Class View included by AbstractNormForm to define,
     * which template to use and the names of the HTML input fields
     * Calls the constructor of class AbstractNormform.
     * @param $template string Holds the initial template name used for displaying the form.
     */
    public function __construct(string $template)
    {
        parent::__construct($template);
    }

    /**
     * Validates the input after sending the form.
     *
     * Examples for REGEX to validate input can be found in src/Utilities/Utilities.php
     *
     * Abstract methods of class AbstractNormForm have to be implemented here
     *
     * @return bool true, if $errorMessages is empty. Else false
     */
    protected function isValid(): bool
    {
        // TODO Add your own solution here. Keep code that ist already there.
        // TODO Sometimes it will be part of your solution. Sometimes you will have to discard it.
        // TODO Decide before you finish your work
        // TODO @see src/NormFormSkeleton/NormFormDemo.php and change the code,
        // TODO to match the requirements of templates/contactMain.html.twig
        //%%contact/isValid
        // TODO keep the next two lines
        $this->templateParameters['errorMessages'] = $this->errorMessages;

        return (count($this->errorMessages) === 0);
    }

    /**
     * processes data sent via form
     * shows a status message, when processing data succeeded.
     *
     * abstract methods of AbstractNormForm have to be implemented here
     */
    protected function business(): void
    {
        // TODO Add your own solution here. Keep code that ist already there.
        // TODO Sometimes it will be part of your solution. Sometimes you will have to discard it.
        // TODO Decide before you finish your work
        // TODO @see src/NormFormSkeleton/NormFormDemo.php
        //%%contact/business
    }
}
