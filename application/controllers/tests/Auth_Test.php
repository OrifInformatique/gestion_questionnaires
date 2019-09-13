<?php

include(__DIR__.'/../Auth.php');

/**
 * Exists only to rewrite the `redirect` function from codeigniter.
 *
 * @param string $uri
 * @param string $method
 * @param integer $code
 * @return void
 */
function redirect($uri = '', $method = 'auto', $code = NULL) { }

/**
 * Test class for Auth controller
 * 
 * @author      Orif, section informatique (ViDi, MeSa, BuYa)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class Auth_Test extends Auth {
    /**
     * Stores the tests' results
     *
     * @var array
     */
    private $_test_results = [
        'auth' => [
            'login' => [],
            'logout' => [],
            'change_pwd' => [],
            'check_pwd' => []
        ]
    ];

    /**
     * Contains the list of tests to run
     *
     * @var array
     */
    private $_tests = [
        'login', 'logout', 'change_pwd',
        'check_pwd'
    ];

    /**
     * Last saved database error
     *
     * @var object
     */
    private $_old_db_error;

    /**
     * Saves the state of the user's login
     *
     * @var array
     */
    private $_user_login_state = [];

    /**
     * Saves the dummy values
     *
     * @var array
     */
    private $_dummy_values = [
        'user' => 'dummy_user',
        'password' => 'test_password'
    ];

    protected $access_level = '*';

    public function __construct()
    {
        parent::__construct();

        $this->_user_login_state_save();
    }

    public function test()
    {
        // Prepare a dummy user for the tests
        $dummy_id = $this->_dummy_create();
        $can_login = $this->_dummy_login($dummy_id);

        // These tests depend on the ability to log in
        if($can_login) {
            if(array_search('login', $this->_tests) !== FALSE) {
                $this->test_login_no_login();
                $this->_dummy_reset($dummy_id);
    
                $this->test_login_no_errors($dummy_id);
                $this->_dummy_reset($dummy_id);
    
                $this->test_login_bad_password($dummy_id);
                $this->_dummy_reset($dummy_id);
    
                $this->test_login_bad_username($dummy_id);
                $this->_dummy_reset($dummy_id);
            }
    
            if(array_search('logout', $this->_tests) !== FALSE) {
                $this->test_logout_no_errors($dummy_id);
                $this->_dummy_reset($dummy_id);

                $this->test_logout_not_logged_in();
                $this->_dummy_reset($dummy_id);
            }

            if(array_search('change_pwd', $this->_tests) !== FALSE) {
                // Being logged in is required for change password
                $this->_dummy_login($dummy_id);
                $this->test_change_password_no_change();
                $this->_dummy_reset($dummy_id);

                $this->_dummy_login($dummy_id);
                $this->test_change_password_no_error($dummy_id);
                $this->_dummy_reset($dummy_id);

                $this->_dummy_login($dummy_id);
                $this->test_change_password_not_match_confirm($dummy_id);
                $this->_dummy_reset($dummy_id);

                $this->_dummy_login($dummy_id);
                $this->test_change_password_not_match_old($dummy_id);
                $this->_dummy_reset($dummy_id);
            }
        } else {
            // Cannot log in, display aborted
            $this->_test_results['auth']['login']['aborted'] =
            $this->_test_results['auth']['logout']['aborted'] =
            $this->_test_results['auth']['change_pwd']['aborted'] = (object) array(
                'errors' => TRUE,
                'errors_expected' => FALSE,
                'db_update' => FALSE,
                'db_update_expected' => FALSE
            );
            log_message('error', 'Could not log in dummy user, there is a problem in auth');
        }

        // If these tests fail, there is a major problem
        if(array_search('check_pwd', $this->_tests) !== FALSE) {
            $this->test_check_password_no_error($dummy_id);
            $this->_dummy_reset($dummy_id);

            $this->test_check_password_error($dummy_id);
            $this->_dummy_reset($dummy_id);
        }

        // Delete dummy user, as the tests are finished
        $this->_dummy_delete($dummy_id);

        // Display test results
        $this->_user_login_state_restore();
        $output['test_results'] = $this->_test_results;
        parent::display_view('tests/tests_results.php', $output);
    }

    /******************
     * LOGGING IN TESTS
     * Check $_SESSION for update
     ******************/
    /**
     * Test for user login without a button being pressed
     *
     * @return void
     */
    private function test_login_no_login()
    {
        $this->_db_error_save();
        $this->login();

        $this->_test_results['auth']['login']['no_login'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_error_diff() ||
                !is_null($this->session->flashdata('message-danger')),
            'errors_expected' => FALSE,
            'db_update' => $this->_db_error_diff(),
            'db_update_expected' => FALSE
        );
    }
    /**
     * Test for user login with the button being pressed
     *
     * @param integer $dummy_id = ID of the dummy user
     * @return void
     */
    private function test_login_no_errors(int $dummy_id)
    {
        $username = $this->_dummy_values['user'];
        $password = $this->_dummy_values['password'];

        $this->_db_error_save();
        $this->_send_form_request('login', [
            'btn_login' => 1,
            'username' => $username,
            'password' => $password
        ]);

        $this->_test_results['auth']['login']['no_errors'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_error_diff() ||
                $_SESSION['user_id'] != $dummy_id || !is_null($this->session->flashdata('message-danger')),
            'errors_expected' => FALSE,
            'db_update' => $this->_db_error_diff(),
            'db_update_expected' => FALSE
        );
    }
    /**
     * Test for failed user login due to bad password
     *
     * @param integer $dummy_id = ID of the dummy user
     * @return void
     */
    private function test_login_bad_password(int $dummy_id)
    {
        $username = $this->_dummy_values['user'];
        $password = $this->_dummy_values['password'].'_wrong';

        $this->_db_error_save();
        $this->_send_form_request('login', [
            'btn_login' => 1,
            'username' => $username,
            'password' => $password
        ]);

        $this->_test_results['auth']['login']['no_errors'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_error_diff() ||
                ($_SESSION['user_id'] ?? -1) == $dummy_id || !is_null($this->session->flashdata('message-danger')),
            'errors_expected' => TRUE,
            'db_update' => $this->_db_error_diff(),
            'db_update_expected' => FALSE
        );
    }
    /**
     * Test for failed user login due to bad username
     *
     * @param integer $dummy_id = ID of the dummy user
     * @return void
     */
    private function test_login_bad_username(int $dummy_id)
    {
        $username = $original_username = $this->_dummy_values['user'];
        $loop = 1;
        // Make sure that username does not exist
        while(!is_null($this->user_model->get_by('User', $username))) {
            $username = $original_username.$loop;
            $loop++;
        }
        $password = $this->_dummy_values['password'];

        $this->_db_error_save();
        $this->_send_form_request('login', [
            'btn_login' => 1,
            'username' => $username,
            'password' => $password
        ]);

        $this->_test_results['auth']['login']['no_errors'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_error_diff() ||
            ($_SESSION['user_id'] ?? -1) || !is_null($this->session->flashdata('message-danger')),
            'errors_expected' => TRUE,
            'db_update' => $this->_db_error_diff(),
            'db_update_expected' => FALSE
        );
    }

    /*******************
     * LOGGING OUT TESTS
     * Check $_SESSION for update
     *******************/
    /**
     * Test for logging out
     *
     * @param integer $dummy_id = ID of the dummy user
     * @return void
     */
    private function test_logout_no_errors(int $dummy_id)
    {
        $this->_dummy_login($dummy_id);

        $this->_db_error_save();
        $this->logout();

        $this->_test_results['auth']['logout']['no_errors'] = (object) array(
            'errors' => $_SESSION['logged_in'] ?? FALSE,
            'errors_expected' => FALSE,
            'db_update' => $this->_db_error_diff(),
            'db_update_expected' => FALSE
        );
    }
    /**
     * Test for logging out without being logged in before
     *
     * @return void
     */
    private function test_logout_not_logged_in()
    {
        $this->_db_error_save();
        $this->logout();

        $this->_test_results['auth']['logout']['not_logged_in'] = (object) array(
            'errors' => $_SESSION['logged_in'] ?? FALSE,
            'errors_expected' => FALSE,
            'db_update' => $this->_db_error_diff(),
            'db_update_expected' => FALSE
        );
    }

    /*************************
     * CHANGING PASSWORD TESTS
     *************************/
    /**
     * Test for user password change without any change being done
     *
     * @return void
     */
    private function test_change_password_no_change()
    {
        $this->_db_error_save();
        $this->_send_form_request('change_password');

        $this->_test_results['auth']['change_pwd']['no_change'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_error_diff(),
            'errors_expected' => FALSE,
            'db_update' => $this->_db_error_diff(),
            'db_update_expected' => FALSE
        );
    }
    /**
     * Test for user password change without any error
     *
     * @param integer $dummy_id = ID of the dummy user
     * @return void
     */
    private function test_change_password_no_error(int $dummy_id)
    {
        $old_password = $this->_dummy_values['password'];
        $new_password = 'password_test';

        $this->_db_error_save();
        $this->_send_form_request('change_password', [
            'btn_change_password' => 1,
            'old_password' => $old_password,
            'new_password' => $new_password,
            'confirm_password' => $new_password
        ]);

        $this->_test_results['auth']['change_pwd']['no_error'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_error_diff(),
            'errors_expected' => FALSE,
            'db_update' => password_verify($new_password, $this->user_model->get($dummy_id)->Password),
            'db_update_expected' => TRUE
        );
    }
    /**
     * Test for failed user password change due to wrong new password confirmation
     *
     * @param integer $dummy_id = ID of the dummy user
     * @return void
     */
    private function test_change_password_not_match_confirm(int $dummy_id)
    {
        $old_password = $this->_dummy_values['password'];
        $new_password = 'password_test';

        $this->_db_error_save();
        $this->_send_form_request('change_password', [
            'btn_change_password' => 1,
            'old_password' => $old_password,
            'new_password' => $new_password,
            'confirm_password' => $new_password.'_wrong'
        ]);

        $this->_test_results['auth']['change_pwd']['not_match_confirm'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_error_diff(),
            'errors_expected' => TRUE,
            'db_update' => password_verify($new_password, $this->user_model->get($dummy_id)->Password),
            'db_update_expected' => FALSE
        );
    }
    /**
     * Test for failed user password change due to input password not matching
     * the previous one.
     *
     * @param integer $dummy_id = ID of the dummy user
     * @return void
     */
    private function test_change_password_not_match_old(int $dummy_id)
    {
        $old_password = $this->_dummy_values['password'].'_wrong';
        $new_password = 'password_test';

        $this->_db_error_save();
        $this->_send_form_request('change_password', [
            'btn_change_password' => 1,
            'old_password' => $old_password,
            'new_password' => $new_password,
            'confirm_password' => $new_password.'_wrong'
        ]);

        $this->_test_results['auth']['change_pwd']['not_match_old'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_error_diff(),
            'errors_expected' => TRUE,
            'db_update' => password_verify($new_password, $this->user_model->get($dummy_id)->Password),
            'db_update_expected' => FALSE
        );
    }

    /*****************************
     * OLD PASSWORD CHECKING TESTS
     *****************************/
    /**
     * Test for password check
     *
     * @param integer $dummy_id = ID of the dummy user
     * @return void
     */
    private function test_check_password_no_error(int $dummy_id)
    {
        $user = $this->user_model->get($dummy_id);
        $db_password = $user->Password;
        $password = $this->_dummy_values['password'];
        $username = $user->User;

        $this->_db_error_save();
        $result = $this->old_password_check($password, $username);

        $this->_test_results['auth']['check_pwd']['no_error'] = (object) array(
            'errors' => $this->_db_error_diff() || ($result != password_verify($password, $db_password)),
            'errors_expected' => FALSE,
            'db_update' => $this->_db_error_diff(),
            'db_update_expected' => FALSE
        );
    }
    /**
     * Test for failed password check due to wrong password
     *
     * @param integer $dummy_id = ID of the dummy user
     * @return void
     */
    private function test_check_password_error(int $dummy_id)
    {
        $user = $this->user_model->get($dummy_id);
        $db_password = $user->Password;
        $password = $this->_dummy_values['password'].'_wrong';
        $username = $user->User;

        $this->_db_error_save();
        $result = $this->old_password_check($password, $username);

        $this->_test_results['auth']['check_pwd']['error'] = (object) array(
            'errors' => $this->_db_error_diff() || ($result != password_verify($password, $db_password)),
            'errors_expected' => FALSE,
            'db_update' => $this->_db_error_diff(),
            'db_update_expected' => FALSE
        );
    }

    /**************
     * MISC METHODS
     **************/
    /**
     * Sends a request to the form_validation.
     *
     * @param string $form_validation = The name of the function to call
     * @param array $data = The data to store in $_POST
     * @return void
     */
    private function _send_form_request(string $form_validation, array $data = [])
    {
        if(!method_exists($this, $form_validation)) {
            return;
        }

        $this->form_validation->reset_validation();
        foreach($data as $key => $value) {
            $_POST[$key] = $value;
        }
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $this->{$form_validation}();
    }

    /**
     * Saves the most recent database error.
     *
     * @return void
     */
    private function _db_error_save()
    {
        $this->_old_db_error = $this->user_model->_database->error();
    }
    /**
     * Compares the saved error and the most recent error.
     *
     * @return boolean = True if the 2 errors are different
     */
    private function _db_error_diff() : bool
    {
        return $this->_old_db_error != $this->user_model->_database->error();
    }

    /**
     * Saves the current user's login state
     *
     * @return void
     */
    private function _user_login_state_save()
    {
        $this->_user_login_state['user_id'] = $_SESSION['user_id'] ?? 0;
        $this->_user_login_state['username'] = $_SESSION['username'] ?? '';
        $this->_user_login_state['user_access'] = $_SESSION['user_access'] ?? 0;
        $this->_user_login_state['logged_in'] = $_SESSION['logged_in'] ?? FALSE;
    }
    /**
     * Restores the current user's login state
     *
     * @return void
     */
    private function _user_login_state_restore()
    {
        $_SESSION['user_id'] = $this->_user_login_state['user_id'];
        $_SESSION['username'] = $this->_user_login_state['username'];
        $_SESSION['user_access'] = $this->_user_login_state['user_access'];
        $_SESSION['logged_in'] = $this->_user_login_state['logged_in'];
    }

    /**
     * Creates a dummy user with a dummy password
     *
     * @return integer = ID of the dummy user
     */
    private function _dummy_create() : int
    {
        $username = $this->_dummy_values['user'];
        $password = $this->_dummy_values['password'];

        $user = array(
            'User' =>$username,
            'Password' => password_hash($password, PASSWORD_HASH_ALGORITHM),
            'FK_User_Type' => 1
        );
        return $this->user_model->insert($user);
    }
    /**
     * Resets the dummy user
     *
     * @param int $dummy_id = ID of the dummy user
     * @return void
     */
    private function _dummy_reset(int $dummy_id)
    {
        $user = array(
            'User' => 'dummy_user',
            'Password' => password_hash('test_password', PASSWORD_HASH_ALGORITHM),
            'FK_User_Type' => 1
        );
        $this->user_model->update($dummy_id, $user);
        switch(session_status()) {
            case 2:
                session_reset();
                break;
            case 1:
                session_start();
                break;
        }
    }
    /**
     * Logs in the dummy
     *
     * @param int $dummy_id = ID of the dummy
     * @return bool = Whether or not it succeeded
     */
    private function _dummy_login(int $dummy_id) : bool
    {
        $username = $this->_dummy_values['user'];
        $password = $this->_dummy_values['password'];

        $this->_send_form_request('login', [
            'btn_login' => 1,
            'username' => $username,
            'password' => $password
        ]);

        return $_SESSION['user_id'] == $dummy_id;
    }
    /**
     * Deletes the dummy user
     *
     * @param integer $dummy_id = ID of the dummy user
     * @return boolean
     */
    private function _dummy_delete(int $dummy_id)
    {
        if(session_status() == PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        // Delete the dummy user instead of archiving it
        $db =& $this->user_model->_database;
        $db->where('ID', $dummy_id);
        $db->delete('t_user');
    }

    /***********
     * OVERRIDES
     ***********/
    /**
     * Prevents view displaying, call `parent::display_view` instead
     *
     * @param mixed $view_parts
     * @param mixed|null $data
     * @return void
     */
    public function display_view($view_parts, $data = NULL) { }
}