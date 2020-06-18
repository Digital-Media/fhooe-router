<?php
namespace Exercises;

use NormForm\AbstractNormForm;
use View\View;
use Demo\FileAccess;
use Demo\Utilities;
use Router\Router;

/**
 * The Login page of phpintro.
 *
 * This class enables users to log in to the system with a provided user name and password. Both items are match with
 * stored credentials. If they match, a Login hash is stored in the session that acts as a token for a successful Login.
 * Other pages can then use login.php to check for $_SESSION[IS_LOGGED_IN] before the site is initialized. If no
 * hash is present the Login system redirects and prevents accessing the page.
 *
 * @author  Wolfgang Hochleitner <wolfgang.hochleitner@fh-hagenberg.at>
 * @author  Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @version 2017
 */
final class Login extends AbstractNormForm
{
    /**
     * @var string USERNAME Form field constant that defines how the form field for holding the username is called
     * (id/name).
     */
    const USERNAME = "username";

    /**
     * @var string PASSWORD Form field constant that defines how the form field for holding the password is called
     * (id/name).
     */
    const PASSWORD = "password";

    /**
     * @var string USER_DATA_PATH The full path for the user meta data JSON file.
     */
    const USER_DATA_PATH = DATA_DIRECTORY . "userdata.json";

    /**
     * @var FileAccess $fileAccess The object handling all file access operations.
     */
    private $fileAccess;

    /**
     * Creates a new Login object based on AbstractNormForm. Takes a View object that holds the information about which
     * template will be shown and which parameters (e.g. for form fields) are passed on to the template.
     * The constructor needs initialize the object for file handling.
     *
     * @param $template string Holds the initial template name used for displaying the form.
     */
    public function __construct(string $template)
    {
        parent::__construct($template);

        // TODO: Create the FileAccess object and assign it to $fileAccess;
        // TODO: @see src/FAdemo.php for this

        //%%login/construct
    }

    /**
     * Validates user input after submitting login credentials. The function first has to check if both fields were
     * filled out and then checks the result of authenticateUser() to see if the credentials match others that are
     * already stored in the system.
     *
     * @return bool Returns true if no errors occurred and therefore no error messages were set, otherwise false.
     */
    protected function isValid(): bool
    {
        // TODO: The code for correct form validation goes here. Check for empty fields and correct authentication.
        // TODO: @see src/FAdemo.php for this

        //%%login/isValid
        //##%%
        $this->authenticateUser();
        //#%#%

        $this->templateParameters['errorMessages'] = $this->errorMessages;
        return (count($this->errorMessages) === 0);
    }

    /**
     * This method is only called when the form input was validated successfully.
     * It stores the username in the session for further use (e.g. in the template).
     * It then forwards to the Register page.
     */
    protected function business(): void
    {
        // TODO: Save the username in $_SESSION. Replace John Doe with the username used to login
        $_SESSION['username'] = "John Doe";
        //%%login/business
        $_SESSION[IS_LOGGED_IN] = Utilities::generateLoginHash();
        // using the null coalesce operator
        $redirect = $_SESSION['redirect'] ?? $redirect = '/';
        // equivalent to: isset($_SESSION['redirect']) ? $redirect= $_SESSION['redirect'] : $redirect='Register.php';
        Router::redirectTo($redirect);
    }

    /**
     * Authenticates a user by matching the entered username and password with the stored records. If the username is
     * present and the entered password matches the stored password, a valid login is assumed and stored in $_SESSION
     *
     * In the file phpintro/data/userdata.json the BCRYPT algorithm ist used for hashing the password.
     * This was done in PHP 5.6 with password_hash(... , PASSWORD_DEFAULT)
     *
     * With PHP 7.3 the challenge is to update older hashes to the strongest hash, that is currently available.
     * Therefore password_get_info(), password_verify() and password_needs_rehash() are used to store
     * an argon2 hash in phpintro/data/userdata.json, after a successful login against the old password hash.
     *

     *
     * @return bool Returns true if the combination of username and password is valid, otherwise false.
     */
    private function authenticateUser(): bool
    {
        // TODO: Check if the provided user name and password combination is correct.
        // TODO: See src/FileAcess.php loadcontents and FAdemo.php for calling it
        // TODO: @see src/FAdemo.php for this
        // TODO: load whole file USER_DATA_PATH: user1 and user2 have password "geheim"
        // TODO: Step throw the array with foreach
        // TODO: Compare each username with the value in $_POST
        // TODO: Validate the password associated with the username with
        // TODO: PHP function password_verify() against the value in $_POST
        // TODO: return true or false, depending on result of verification

        //##%%
        return true;
        //#%#%

        //%%login/authenticateUser
    }
}
