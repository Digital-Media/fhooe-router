<?php

namespace Demo;

use NormForm\AbstractNormForm;
use Demo\FileAccess;
use Demo\Utilities;

/**
 * The demo page for the class FileAccess.
 *
 * @author  Wolfgang Hochleitner <wolfgang.hochleitner@fh-hagenberg.at>
 * @author  Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @version 2017
 */
final class FAdemo extends AbstractNormForm
{
    /**
     * @var string USER_DATA_PATH The full path for the user meta data JSON file.
     */
    private const TEST_DATA_PATH = DATA_DIRECTORY . "testdata.json";

    /**
     * @var FileAccess $fileAccess The object handling all file access operations.
     */
    private $fileAccess;

    /**
     * Creates a new DEMO object based on AbstractNormForm and FileAccess.
     * Takes a View object that holds the information about which
     * template will be shown and which parameters (e.g. for form fields) are passed on to the template.
     *
     * @param $template string  The default View object with information on what will be displayed.
     */
    public function __construct(string $template)
    {
        // invoke parent constructor explicitly, cause it requires one parameter
        // this is not done implicitly while creating the object from this subclass
        parent::__construct($template);
        // creating the FileAccess object
        $this->fileAccess = new FileAccess();
        // filling the result array if data exist in the file TEST_DATA_PATH
        $this->templateParameters['result'] = $this->readText();
    }

    /**
     * Validates user input.
     *
     * @return bool Returns true if no errors occurred and therefore no error messages were set, otherwise false.
     */
    protected function isValid(): bool
    {
        $params = $this->getBodyParams();
        if ($this->isEmptyPostField('demo_field')) {
            $this->errorMessages['demo_field'] = "Please type some text.";
        }
        $this->templateParameters['demo_field'] = $params['demo_field'];
        $this->templateParameters['errorMessages'] = $this->errorMessages;
        return (count($this->errorMessages) === 0);
    }

    /**
     * This method is only called when the form input was validated successfully. It adds the newly added image,
     * creates a status message for showing success and updates the View object with the status message and the updated
     * array of images. The form fields for image title and author are updated with an empty parameter so that their
     * content is deleted.
     */
    protected function business(): void
    {
        $this->writeText();
        $this->templateParameters['demo_field'] = "";
        $this->templateParameters['result'] = $this->readText();
        $this->templateParameters['statusMessage'] = "Processing successful!";
    }

    protected function readText()
    {
        $fields = $this->fileAccess->loadContents(self::TEST_DATA_PATH);
        return $fields;
    }

    protected function writeText()
    {
        //don't need to sanitize with Twig
        //$demofield = Utilities::sanitizeFilter($_POST[self::DEMO_FIELD]);
        $demofield = $_POST['demo_field'];

        $fields = $this->fileAccess->loadContents(self::TEST_DATA_PATH);

        $fields[] = [
            "demo_field" => $demofield
        ];

        $this->fileAccess->storeContents(self::TEST_DATA_PATH, $fields);
    }
}
