<?php

include(__DIR__.'/../../core/MY_Test_Trait.php');
include(__DIR__.'/../Auth.php');

/**
 * Test class for Auth controller
 * 
 * @author      Orif, section informatique (ViDi, MeSa, BuYa)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class Auth_Test extends Auth {
    use MY_Test_Trait;
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
        'login', 'logout', 'change_pwd', 'check_pwd'
    ];

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

    public function __construct()
    {
        parent::__construct();

        $this->_user_login_state_save();
    }

    public function __destruct()
    {
        if(method_exists(get_parent_class($this), '__destruct')) {
            parent::__destruct();
        }
        $this->_user_login_state_restore();
    }

    public function test()
    {
        // Prepare a dummy user for the tests
        $dummy_id = $this->_dummy_create();
        $can_login = $this->_dummy_login($dummy_id);

        // These tests depend on the ability to log in
        if($can_login) {
            if(in_array('login', $this->_tests)) {
                $this->test_login_no_login();
                $this->_dummy_reset($dummy_id);
    
                $this->test_login_no_error($dummy_id);
                $this->_dummy_reset($dummy_id);
    
                $this->test_login_bad_password($dummy_id);
                $this->_dummy_reset($dummy_id);
    
                $this->test_login_bad_username($dummy_id);
                $this->_dummy_reset($dummy_id);
            }
    
            if(in_array('logout', $this->_tests)) {
                $this->test_logout_no_error($dummy_id);
                $this->_dummy_reset($dummy_id);

                $this->test_logout_not_logged_in();
                $this->_dummy_reset($dummy_id);
            }

            if(in_array('change_pwd', $this->_tests)) {
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
                'success' => FALSE,
                'errors' => ['Could not log in dummy user, there is a problem in auth']
            );
        }

        // If these tests fail, there is a major problem
        if(in_array('check_pwd', $this->_tests)) {
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
        parent::display_view('tests/tests_results_new.php', $output);
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
        $this->_db_errors_save();
        $this->login();

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(!empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = "<code>validation_errors()</code> was expected to be empty, but contains an error";
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!is_null($this->session->flashdata('message-danger'))) {
            $result->success = FALSE;
            $result->errors[] = 'Expected no error in flashdata(\'message-danger\'), but there is one';
        }

        $this->_test_results['auth']['login']['no_login'] = $result;
    }
    /**
     * Test for user login with the button being pressed
     *
     * @param integer $dummy_id = ID of the dummy user
     * @return void
     */
    private function test_login_no_error(int $dummy_id)
    {
        $username = $this->_dummy_values['user'];
        $password = $this->_dummy_values['password'];

        $this->_db_errors_save();
        $this->_send_form_request('login', [
            'btn_login' => 1,
            'username' => $username,
            'password' => $password
        ]);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(!empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = "<code>validation_errors()</code> was expected to be empty, but contains an error";
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($_SESSION['user_id'] != $dummy_id) {
            $result->success = FALSE;
            $result->errors[] = "Expected '{$dummy_id}' in \$_SESSION['user_id'], found '{$_SESSION['user_id']}'";
        }
        if(!is_null($this->session->flashdata('message-danger'))) {
            $result->success = FALSE;
            $result->errors[] = 'Expected no error in flashdata(\'message-danger\'), but there is one';
        }

        $this->_test_results['auth']['login']['no_error'] = $result;
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

        $this->_db_errors_save();
        $this->_send_form_request('login', [
            'btn_login' => 1,
            'username' => $username,
            'password' => $password
        ]);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(!empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = "<code>validation_errors()</code> was expected to be empty, but contains an error";
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $dummy_id) {
            $result->success = FALSE;
            $result->errors[] = "Expected nothin in \$_SESSION['user_id'], found '{$_SESSION['user_id']}'";
        }
        if(is_null($this->session->flashdata('message-danger'))) {
            $result->success = FALSE;
            $result->errors[] = 'Expected an error in flashdata(\'message-danger\'), found none';
        }

        $this->_test_results['auth']['login']['bad_password'] = $result;
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

        $this->_db_errors_save();
        $this->_send_form_request('login', [
            'btn_login' => 1,
            'username' => $username,
            'password' => $password
        ]);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(!empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = "<code>validation_errors()</code> was expected to be empty, but contains an error";
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $dummy_id) {
            $result->success = FALSE;
            $result->errors[] = "Expected nothin in \$_SESSION['user_id'], found '{$_SESSION['user_id']}'";
        }
        if(is_null($this->session->flashdata('message-danger'))) {
            $result->success = FALSE;
            $result->errors[] = 'Expected an error in flashdata(\'message-danger\'), found none';
        }

        $this->_test_results['auth']['login']['bad_username'] = $result;
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
    private function test_logout_no_error(int $dummy_id)
    {
        $this->_dummy_login($dummy_id);

        $this->_db_errors_save();
        $this->logout();

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($_SESSION['logged_in']) {
            $result->success = FALSE;
            $result->errors[] = 'Expected $_SESSION[\'logged_in\'] to be FALSE, found TRUE';
        }

        $this->_test_results['auth']['logout']['no_error'] = $result;
    }
    /**
     * Test for logging out without being logged in before
     *
     * @return void
     */
    private function test_logout_not_logged_in()
    {
        $this->_db_errors_save();
        $this->logout();

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($_SESSION['logged_in']) {
            $result->success = FALSE;
            $result->errors[] = 'Expected $_SESSION[\'logged_in\'] to be FALSE, found TRUE';
        }

        $this->_test_results['auth']['logout']['not_logged_in'] = $result;
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
        $this->_db_errors_save();
        $this->_send_form_request('change_password');

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(!empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = "<code>validation_errors()</code> was expected to be empty, but contains an error";
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }

        $this->_test_results['auth']['change_pwd']['no_change'] = $result;
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

        $this->_db_errors_save();
        $this->_send_form_request('change_password', [
            'btn_change_password' => 1,
            'old_password' => $old_password,
            'new_password' => $new_password,
            'confirm_password' => $new_password
        ]);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(!empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = "<code>validation_errors()</code> was expected to be empty, but contains an error";
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!password_verify($new_password, $this->user_model->get($dummy_id)->Password)) {
            $result->success = FALSE;
            $result->errors[] = 'Expected stored user password to be hash of new user password, but it is not';
        }

        $this->_test_results['auth']['change_pwd']['no_error'] = $result;
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

        $this->_db_errors_save();
        $this->_send_form_request('change_password', [
            'btn_change_password' => 1,
            'old_password' => $old_password,
            'new_password' => $new_password,
            'confirm_password' => $new_password.'_wrong'
        ]);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = "<code>validation_errors()</code> was expected to contain an error, but it is empty";
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(password_verify($new_password, $this->user_model->get($dummy_id)->Password)) {
            $result->success = FALSE;
            $result->errors[] = 'Expected stored user password to not be hash of new password, but it is';
        }

        $this->_test_results['auth']['change_pwd']['not_match_confirm'] = $result;
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

        $this->_db_errors_save();
        $this->_send_form_request('change_password', [
            'btn_change_password' => 1,
            'old_password' => $old_password,
            'new_password' => $new_password,
            'confirm_password' => $new_password.'_wrong'
        ]);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = "<code>validation_errors()</code> was expected to contain an error, but it is empty";
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(password_verify($new_password, $this->user_model->get($dummy_id)->Password)) {
            $result->success = FALSE;
            $result->errors[] = 'Expected stored user password to not be hash of new password, but it is';
        }

        $this->_test_results['auth']['change_pwd']['not_match_old'] = $result;
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

        $this->_db_errors_save();
        $check_result = $this->old_password_check($password, $username);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($check_result != password_verify($password, $db_password)) {
            $result->success = FALSE;
            $result->errors[] = 'The result of <code>self::old_password_check()</code> does not match the result of <code>password_verify()</code>';
        }

        $this->_test_results['auth']['check_pwd']['no_error'] = $result;
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

        $this->_db_errors_save();
        $check_result = $this->old_password_check($password, $username);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($check_result != password_verify($password, $db_password)) {
            $result->success = FALSE;
            $result->errors[] = 'The result of <code>self::old_password_check()</code> does not match the result of <code>password_verify()</code>';
        }

        $this->_test_results['auth']['check_pwd']['error'] = $result;
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
        $username = $this->_dummy_values['user'];
        $password = $this->_dummy_values['password'];

        $user = array(
            'User' => $username,
            'Password' => password_hash($password, PASSWORD_HASH_ALGORITHM),
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

}