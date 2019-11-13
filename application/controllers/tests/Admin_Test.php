<?php

include(__DIR__.'/../../core/MY_Test_Trait.php');
include(__DIR__.'/../Admin.php');

class Admin_Test extends Admin {
    use MY_Test_Trait;

    /**
     * Stores the tests' results
     *
     * @var array
     */
    private $_test_results = [
        'user' => [
            'create' => [],
            'update' => [],
            'delete' => [],
            'change_pwd' => []
        ]
    ];

    /**
     * Contains the list of tests to run
     *
     * @var array
     */
    private $_tests = [
        'user_create', 'user_update', 'user_delete', 'user_pwd_change'
    ];

    /**
     * Contains the base values for dummies
     *
     * @var array
     */
    protected $_dummy_values = [
        'user' => [
            'user' => 'dummy_user',
            'password' => 'dummy_password'
        ]
    ];

    /**
     * Does the testing
     *
     * @return void
     */
    public function test()
    {
        if(in_array('user_create', $this->_tests)) {
            $this->test_user_add_no_errors();
            $this->test_user_add_short_name();
            $this->test_user_add_long_name();
            $this->test_user_add_short_pwd();
            $this->test_user_add_long_pwd();
            $this->test_user_add_pwd_not_match();
        }

        if(in_array('user_update', $this->_tests)) {
            $dummy_user_id = $this->_dummy_user_create();

            $this->test_user_update_empty_update($dummy_user_id);
            $this->_dummy_user_reset($dummy_user_id);

            $this->test_user_update_no_error($dummy_user_id);
            $this->_dummy_user_reset($dummy_user_id);

            $this->test_user_update_not_exist();
            $this->_dummy_user_reset($dummy_user_id);

            $this->test_user_update_disactivate($dummy_user_id);
            $this->_dummy_user_reset($dummy_user_id);

            $this->test_user_update_double_disactivate($dummy_user_id);
            $this->_dummy_user_reset($dummy_user_id);

            $this->test_user_update_disactivate_not_exist();
            $this->_dummy_user_reset($dummy_user_id);

            $this->test_user_update_reactivate($dummy_user_id);
            $this->_dummy_user_reset($dummy_user_id);

            $this->test_user_update_double_reactivate($dummy_user_id);
            $this->_dummy_user_reset($dummy_user_id);

            $this->test_user_update_reactivate_not_exist();
            $this->_dummy_user_reset($dummy_user_id);

            $this->user_model->delete($dummy_user_id, TRUE);
        }

        if(in_array('user_delete', $this->_tests)) {
            $dummy_user_id = $this->_dummy_user_create();

            $this->test_user_delete_no_delete($dummy_user_id);
            $this->_dummy_user_reset($dummy_user_id);

            $this->test_user_delete_not_exist();

            $this->test_user_delete_disactivate($dummy_user_id);
            $this->_dummy_user_reset($dummy_user_id);

            $this->test_user_delete_disactivate_not_exist();

            $this->test_user_delete_delete();

            $this->test_user_delete_delete_not_exist();

            $this->user_model->delete($dummy_user_id, TRUE);
        }

        if(in_array('user_pwd_change', $this->_tests)) {
            $dummy_user_id = $this->_dummy_user_create();

            $this->test_user_change_pwd_no_change($dummy_user_id);
            $this->_dummy_user_reset($dummy_user_id);

            $this->test_user_change_pwd_no_errors($dummy_user_id);
            $this->_dummy_user_reset($dummy_user_id);

            $this->test_user_change_pwd_not_match($dummy_user_id);
            $this->_dummy_user_reset($dummy_user_id);

            $this->test_user_change_pwd_not_exist();
            $this->_dummy_user_reset($dummy_user_id);

            $this->user_model->delete($dummy_user_id, TRUE);
        }

        // Display test results
        $output['test_results'] = $this->_test_results;
        parent::display_view('tests/tests_results_new.php', $output);
    }

    /*********************
     * USER CREATION TESTS
     *********************/
    /**
     * Test for user creation
     *
     * @return void
     */
    private function test_user_add_no_errors()
    {
        $username = 'test_user_no_errors';
        $password = 'test_pwd_no_errors';

        $this->_db_errors_save();
        $this->_send_form_request('user_form', [
            'id' => 0,
            'user_name' => $username,
            'user_usertype' => 1,
            'user_password' => $password,
            'user_password_again' => $password
        ]);

        $user = $this->user_model->get_by('User', $username);

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
        if(is_null($user)) {
            $result->success = FALSE;
            $result->errors[] = "Expected user {$user->ID} to exist, but it does not";
        }

        $this->_test_results['user']['create']['no_errors'] = $result;

        if(!is_null($user)) {
            $this->user_model->delete($user->ID, TRUE);
        }
    }
    /**
     * Test for failed user creation due to name being too short
     *
     * @return void
     */
    private function test_user_add_short_name()
    {
        $username = 'test_user_short_name';
        $password = 'test_pwd_short_name';
        // Make sure it is too short
        $username = substr($username, 0, USERNAME_MIN_LENGTH - 1);

        $this->_db_errors_save();
        $this->_send_form_request('user_form', [
            'id' => 0,
            'user_name' => $username,
            'user_usertype' => 1,
            'user_password' => $password,
            'user_password_again' => $password
        ]);

        $user = $this->user_model->get_by('User', $username);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to contain an error, but it is empty';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!is_null($user)) {
            $result->success = FALSE;
            $result->errors[] = "Expected user {$user->ID} to not exist, but it does";
        }

        $this->_test_results['user']['create']['short_name'] = $result;

        if(!is_null($user)) {
            $this->user_model->delete($user->ID, TRUE);
        }
    }
    /**
     * Test for failed user creation due to name being too long
     *
     * @return void
     */
    private function test_user_add_long_name()
    {
        $username_long = 'test_user_long_name';
        $password = 'test_pwd_long_name';
        // Make sure it is too long
        $repeat_count = ceil(USERNAME_MAX_LENGTH / strlen($username_long)) + 1;
        $username = str_repeat($username_long, $repeat_count);

        $this->_db_errors_save();
        $this->_send_form_request('user_form', [
            'id' => 0,
            'user_name' => $username,
            'user_usertype' => 1,
            'user_password' => $password,
            'user_password_again' => $password
        ]);

        $user = $this->user_model->get_by('User', $username);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to contain an error, but it is empty';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!is_null($user)) {
            $result->success = FALSE;
            $result->errors[] = "Expected user {$user->ID} to not exist, but it does";
        }

        $this->_test_results['user']['create']['long_name'] = $result;

        if(!is_null($user)) {
            $this->user_model->delete($user->ID, TRUE);
        }
    }
    /**
     * Test for failed user creation due to password being too short
     *
     * @return void
     */
    private function test_user_add_short_pwd()
    {
        $username = 'test_user_short_pwd';
        $password = 'test_pwd_short_pwd';
        // Make sure it is too short
        $password = substr($password, 0, PASSWORD_MIN_LENGTH - 1);

        $this->_db_errors_save();
        $this->_send_form_request('user_form', [
            'id' => 0,
            'user_name' => $username,
            'user_usertype' => 1,
            'user_password' => $password,
            'user_password_again' => $password
        ]);

        $user = $this->user_model->get_by('User', $username);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to contain an error, but it is empty';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!is_null($user)) {
            $result->success = FALSE;
            $result->errors[] = "Expected user {$user->ID} to not exist, but it does";
        }

        $this->_test_results['user']['create']['short_pwd'] = $result;

        if(!is_null($user)) {
            $this->user_model->delete($user->ID, TRUE);
        }
    }
    /**
     * Test for failed user creation due to password being too long
     *
     * @return void
     */
    private function test_user_add_long_pwd()
    {
        $username = 'test_user_long_pwd';
        $password_long = 'test_pwd_long_pwd';
        // Make sure it is too long
        $repeat_count = ceil(PASSWORD_MAX_LENGTH / strlen($password_long)) + 1;
        $password = str_repeat($password_long, $repeat_count);

        $this->_db_errors_save();
        $this->_send_form_request('user_form', [
            'id' => 0,
            'user_name' => $username,
            'user_usertype' => 1,
            'user_password' => $password,
            'user_password_again' => $password
        ]);

        $user = $this->user_model->get_by('User', $username);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to contain an error, but it is empty';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!is_null($user)) {
            $result->success = FALSE;
            $result->errors[] = "Expected user {$user->ID} to not exist, but it does";
        }

        $this->_test_results['user']['create']['long_pwd'] = $result;

        if(!is_null($user)) {
            $this->user_model->delete($user->ID, TRUE);
        }
    }
    /**
     * Test for failed user creation due to passwords not matching
     *
     * @return void
     */
    private function test_user_add_pwd_not_match()
    {
        $username = 'test_user_pwd_not_match';
        $password = 'test_pwd_pwd_not_match';

        $this->_db_errors_save();
        $this->_send_form_request('user_form', [
            'id' => 0,
            'user_name' => $username,
            'user_usertype' => 1,
            'user_password' => $password,
            'user_password_again' => $password.'_wrong'
        ]);

        $user = $this->user_model->get_by('User', $username);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to contain an error, but it is empty';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!is_null($user)) {
            $result->success = FALSE;
            $result->errors[] = "Expected user {$user->ID} to not exist, but it does";
        }

        $this->_test_results['user']['create']['pwd_not_match'] = $result;

        if(!is_null($user)) {
            $this->user_model->delete($user->ID, TRUE);
        }
    }

    /*********************
     * USER UPDATING TESTS
     *********************/
    /**
     * Test for user updating doing nothing
     *
     * @param integer $dummy_user_id = ID of the dummy user
     * @return void
     */
    private function test_user_update_empty_update(int $dummy_user_id)
    {
        $username = 'test_user_update_empty_update';

        $this->_db_errors_save();
        $this->_send_form_request('user_form', [
            'id' => $dummy_user_id,
            'user_name' => $username,
            'user_usertype' => 1
        ]);

        $user = $this->user_model->get($dummy_user_id);

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
        if($user->User === $username) {
            $result->success = FALSE;
            $result->errors[] = "Expected user {$user->ID} to not be updated, but it is";
        }

        $this->_test_results['user']['update']['empty_update'] = $result;
    }
    /**
     * Test for user updating without any problem
     *
     * @param integer $dummy_user_id = ID of the dummy user
     * @return void
     */
    private function test_user_update_no_error(int $dummy_user_id)
    {
        $username = 'test_user_update_no_error';

        $this->_db_errors_save();
        $this->_send_form_request('user_form', [
            'id' => $dummy_user_id,
            'user_name' => $username,
            'user_usertype' => 1,
            'save' => 1
        ]);

        $user = $this->user_model->get($dummy_user_id);

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
        if($user->User !== $username) {
            $result->success = FALSE;
            $result->errors[] = "Expected user {$dummy_user_id} to be updated, but it is not";
        }

        $this->_test_results['user']['update']['no_error'] = $result;
    }
    /**
     * Test for failed user updating due to non-existant user
     *
     * @return void
     */
    private function test_user_update_not_exist()
    {
        $username = 'test_user_update_not_exist';
        $bad_id = $this->user_model->get_next_id()+1;

        $this->_db_errors_save();
        $this->_send_form_request('user_form', [
            'id' => $bad_id,
            'user_name' => $username,
            'user_usertype' => 1,
            'disactivate' => 1
        ]);

        $user = $this->user_model->get_by('User', $username);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = "<code>validation_errors()</code> was expected to contain errors, but it does not";
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!is_null($user)) {
            $result->success = FALSE;
            $result->errors[] = "Expected user {$user->ID} to not exist, but it does";
        }

        $this->_test_results['user']['update']['not_exist'] = $result;

        // In case the user is created by accident, delete it
        if(!is_null($user)) {
            $this->user_model->delete($user->ID, TRUE);
        }
    }
    /**
     * Test for user disactivating without any problem
     *
     * @param integer $dummy_user_id = ID of the dummy user
     * @return void
     */
    private function test_user_update_disactivate(int $dummy_user_id)
    {
        $username = 'test_user_disactivate';

        $this->_db_errors_save();
        $this->_send_form_request('user_form', [
            'id' => $dummy_user_id,
            'user_name' => $username,
            'user_usertype' => 1,
            'disactivate' => 1
        ]);

        $user = $this->user_model->with_deleted()->get($dummy_user_id);

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
        if($user->Archive != 1) {
            $result->success = FALSE;
            $result->errors[] = "Expected user {$dummy_user_id} to be archived, but it is not";
        }

        $this->_test_results['user']['update']['disactivate'] = $result;
    }
    /**
     * Test for failed user disactivating due to already being disactivated
     *
     * @param integer $dummy_user_id = ID of the dummy user
     * @return void
     */
    private function test_user_update_double_disactivate(int $dummy_user_id)
    {
        $username = 'test_user_double_disactivate';

        $this->user_model->update($dummy_user_id, ['Archive' => 1]);

        $this->_db_errors_save();
        $this->_send_form_request('user_form', [
            'id' => $dummy_user_id,
            'user_name' => $username,
            'user_usertype' => 1,
            'disactivate' => 1
        ]);

        $user = $this->user_model->with_deleted()->get($dummy_user_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to contain an error, but it is empty';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($user->Archive != 1) {
            $result->success = FALSE;
            $result->errors[] = "Expected user {$dummy_user_id} to be archived, but it is not";
        }

        $this->_test_results['user']['update']['double_disactivate'] = $result;
    }
    /**
     * Test for failed user disactivating due to non-existant user
     *
     * @return void
     */
    private function test_user_update_disactivate_not_exist()
    {
        $username = 'test_user_disactivate_not_exist';
        $bad_id = $this->user_model->get_next_id()+1;

        $this->_db_errors_save();
        $this->_send_form_request('user_form', [
            'id' => $bad_id,
            'user_name' => $username,
            'user_usertype' => 1,
            'disactivate' => 1
        ]);

        $user = $this->user_model->get_by('User', $username);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to contain an error, but it is empty';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!is_null($user)) {
            $result->success = FALSE;
            $result->errors[] = "Expected user {$user->ID} to not exist, but it does";
        }

        $this->_test_results['user']['update']['disactivate_not_exist'] = $result;

        // In case the user is created by accident, delete it
        if(!is_null($user)) {
            $this->user_model->delete($user->ID, TRUE);
        }
    }
    /**
     * Test for user reactivating with no problem
     *
     * @param integer $dummy_user_id = ID of the dummy user
     * @return void
     */
    private function test_user_update_reactivate(int $dummy_user_id)
    {
        $username = 'test_user_reactivate';

        $this->user_model->update($dummy_user_id, ['Archive' => 1]);

        $this->_db_errors_save();
        $this->_send_form_request('user_form', [
            'id' => $dummy_user_id,
            'user_name' => $username,
            'user_usertype' => 1,
            'reactivate' => 1
        ]);

        $user = $this->user_model->with_deleted()->get($dummy_user_id);

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
        if($user->Archive != 0) {
            $result->success = FALSE;
            $result->errors[] = "Expected user {$dummy_user_id} to not be archived, but it is";
        }

        $this->_test_results['user']['update']['reactivate'] = $result;
    }
    /**
     * Test for failed user reactivating due to already being active
     *
     * @param integer $dummy_user_id = ID of the dummy user
     * @return void
     */
    private function test_user_update_double_reactivate(int $dummy_user_id)
    {
        $username = 'test_user_double_reactivate';

        $this->user_model->update($dummy_user_id, ['Archive' => 0]);

        $this->_db_errors_save();
        $this->_send_form_request('user_form', [
            'id' => $dummy_user_id,
            'user_name' => $username,
            'user_usertype' => 1,
            'reactivate' => 1
        ]);

        $user = $this->user_model->get($dummy_user_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to contain an error, but it is empty';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($user->Archive != 0) {
            $result->success = FALSE;
            $result->errors[] = "Expected user {$dummy_user_id} to not be archived, but it is";
        }

        $this->_test_results['user']['update']['double_reactive'] = $result;
    }
    /**
     * Test for failed user reactivation due to user not existing
     *
     * @return void
     */
    private function test_user_update_reactivate_not_exist()
    {
        $username = 'test_user_reactivate_not_exist';
        $bad_id = $this->user_model->get_next_id()+1;

        $this->_db_errors_save();
        $this->_send_form_request('user_form', [
            'id' => $bad_id,
            'user_name' => $username,
            'user_usertype' => 1,
            'reactivate' => 1
        ]);

        $user = $this->user_model->get_by('User', $username);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to contain an error, but it is empty';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!is_null($user)) {
            $result->success = FALSE;
            $result->errors[] = "Expected user {$bad_id} to not exist, but it does";
        }

        $this->_test_results['user']['update']['reactivate_not_exist'] = $result;

        // In case the user is created by accident, delete it
        if(!is_null($user)) {
            $this->user_model->delete($user->ID, TRUE);
        }
    }

    /*********************
     * USER DELETION TESTS
     *********************/
    /**
     * Test for user deletion that does nothing
     *
     * @param integer $dummy_user_id = ID of the dummy user
     * @return void
     */
    private function test_user_delete_no_delete(int $dummy_user_id)
    {
        $this->_db_errors_save();
        $this->user_delete($dummy_user_id);

        $user = $this->user_model->get($dummy_user_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($user->Archive == 1) {
            $result->success = FALSE;
            $result->errors[] = "Expected user {$dummy_user_id} to not be archived, but it is";
        }

        $this->_test_results['user']['delete']['no_delete'] = $result;
    }
    /**
     * Test for failed user deletion due to user not existing
     *
     * @return void
     */
    private function test_user_delete_not_exist()
    {
        $bad_id = $this->user_model->get_next_id()+1;

        $this->_db_errors_save();
        $this->user_delete($bad_id);

        $user = $this->user_model->get($bad_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!is_null($user)) {
            $result->success = FALSE;
            $result->errors[] = "Expected user {$bad_id} to not exist, but it does";
        }

        $this->_test_results['user']['delete']['not_exist'] = $result;

        if(!is_null($user)) {
            $this->user_model->delete($user->ID, TRUE);
        }
    }
    /**
     * Test for user disactivation
     *
     * @param integer $dummy_user_id = ID of the dummy user
     * @return void
     */
    private function test_user_delete_disactivate(int $dummy_user_id)
    {
        $this->_db_errors_save();
        $this->user_delete($dummy_user_id, 1);

        $user = $this->user_model->get($dummy_user_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($user->Archive != 1) {
            $result->success = FALSE;
            $result->errors[] = "Expected user {$dummy_user_id} to be archived, but it is not";
        }

        $this->_test_results['user']['delete']['disactivate'] = $result;
    }
    /**
     * Test for failed user disactivation due to user not existing
     *
     * @return void
     */
    private function test_user_delete_disactivate_not_exist()
    {
        $bad_id = $this->user_model->get_next_id()+1;

        $this->_db_errors_save();
        $this->user_delete($bad_id, 1);

        $user = $this->user_model->get($bad_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!is_null($user)) {
            $result->success = FALSE;
            $result->errors[] = "Expected user {$bad_id} to not exist, but it does";
        }

        $this->_test_results['user']['delete']['disactivate_not_exist'] = $result;

        if(!is_null($user)) {
            $this->user_model->delete($user->ID, TRUE);
        }
    }
    /**
     * Test for user deletion.
     * Does not use the dummy user, but creates its own.
     *
     * @return void
     */
    private function test_user_delete_delete()
    {
        // Create local dummy user with no risk of deleting external dummy
        $dummy_user_id = $this->_dummy_user_create();

        $user_not_deleted = $this->user_model->get($dummy_user_id);

        $this->_db_errors_save();
        $this->user_delete($dummy_user_id, 2);

        $user_deleted = $this->user_model->get($dummy_user_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(is_null($user_not_deleted)) {
            $result->success = FALSE;
            $result->errors[] = 'Expected user to be deleted to exist, but it does not';
        }
        if(!is_null($user_deleted)) {
            $result->success = FALSE;
            $result->errors[] = 'Expected deleted user to not exist, but it does';
        }

        $this->_test_results['user']['delete']['delete'] = $result;

        if(!is_null($user_deleted)) {
            $this->user_model->delete($dummy_user_id, TRUE);
        }
    }
    /**
     * Test for failed user deletion due to user not existing
     *
     * @return void
     */
    private function test_user_delete_delete_not_exist()
    {
        $bad_id = $this->user_model->get_next_id()+1;

        $user_not_deleted = $this->user_model->get($bad_id);

        $this->_db_errors_save();
        $this->user_delete($bad_id, 2);

        $user_deleted = $this->user_model->get($bad_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!is_null($user_not_deleted)) {
            $result->success = FALSE;
            $result->errors[] = 'Expected non-existant user to be deleted to not exist, but it does';
        }
        if(!is_null($user_deleted)) {
            $result->success = FALSE;
            $result->errors[] = 'Expected deleted non-existant user to not exist, but it does';
        }

        if(!is_null($user_deleted)) {
            $this->user_model->delete($bad_id, TRUE);
        }
    }

    /******************************
     * USER PASSWORD UPDATING TESTS
     ******************************/
    /**
     * Test for failed user password changing due to no input
     *
     * @param integer $dummy_user_id = ID of the dummy user
     * @return void
     */
    private function test_user_change_pwd_no_change(int $dummy_user_id)
    {
        $this->_db_errors_save();
        $this->_send_form_request('user_change_password_form', [
            'id' => $dummy_user_id
        ]);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to contain an error, but it is empty';
        }

        $this->_test_results['user']['change_pwd']['no_change'] = $result;
    }
    /**
     * Test for user password changing
     *
     * @param integer $dummy_user_id = ID of the dummy user
     * @return void
     */
    private function test_user_change_pwd_no_errors(int $dummy_user_id)
    {
        $user_password_new = 'password_dummy';

        $this->_db_errors_save();
        $this->_send_form_request('user_change_password_form', [
            'id' => $dummy_user_id,
            'user_password_new' => $user_password_new,
            'user_password_again' => $user_password_new
        ]);

        $user = $this->user_model->get($dummy_user_id);

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
        if(!password_verify($user_password_new, $user->Password)) {
            $result->success = FALSE;
            $result->errors[] = 'Expected stored user password to be hash of new user password, but it is not';
        }

        $this->_test_results['user']['change_pwd']['no_errors'] = $result;
    }
    /**
     * Test for failed user password changing due to new passwords not matching.
     *
     * @param integer $dummy_user_id = ID of the dummy user
     * @return void
     */
    private function test_user_change_pwd_not_match(int $dummy_user_id)
    {
        $user_password_new = 'password_dummy_not_match';

        $this->_db_errors_save();
        $this->_send_form_request('user_change_password_form', [
            'id' => $dummy_user_id,
            'user_password_new' => $user_password_new,
            'user_password_again' => $user_password_new.'_wrong'
        ]);

        $user = $this->user_model->get($dummy_user_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to contain an error, but it is empty';
        }
        if(password_verify($user_password_new, $user->Password) ||
            password_verify($user_password_new.'_wrong', $user->Password)) {
            $result->success = FALSE;
            $result->errors[] = 'Expected stored user password to not be hash of new user password, but it is';
        }

        $this->_test_results['user']['change_pwd']['not_match'] = $result;
    }
    /**
     * Test for failed user password changing due to user not existing.
     *
     * @return void
     */
    private function test_user_change_pwd_not_exist()
    {
        $bad_id = $this->user_model->get_next_id()+1;
        $user_password_new = 'password_dummy_not_exist';

        $this->_db_errors_save();
        $this->_send_form_request('user_change_password_form', [
            'id' => $bad_id,
            'user_password_new' => $user_password_new,
            'user_password_again' => $user_password_new
        ]);

        $user = $this->user_model->get($bad_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to contain an error, but it is empty';
        }
        if(!is_null($user)) {
            $result->success = FALSE;
            $result->errors[] = "Expected user {$bad_id} to not exist, but it does";
        }

        $this->_test_results['user']['change_pwd']['not_exist'] = $result;

        if(!is_null($user)) {
            $this->user_model->delete($user->ID, TRUE);
        }
    }

    /*************************
     * DUMMIES-RELATED METHODS
     * Do not use with actual users!
     *************************/
    /**
     * Creates a new dummy user
     *
     * @return integer = ID of the dummy user
     */
    private function _dummy_user_create() : int
    {
        $username = $this->_dummy_values['user']['user'];
        $password = $this->_dummy_values['user']['password'];

        $user = array(
            'User' => $username,
            'Password' => password_hash($password, PASSWORD_HASH_ALGORITHM),
            'FK_User_Type' => 1
        );
        return $this->user_model->insert($user);
    }
    /**
     * Resets the dummy user
     *
     * @param integer $dummy_user_id = ID of the dummy user
     * @return void
     */
    private function _dummy_user_reset(int $dummy_user_id)
    {
        $username = $this->_dummy_values['user']['user'];
        $password = $this->_dummy_values['user']['password'];

        $user = array(
            'User' => $username,
            'Password' => password_hash($password, PASSWORD_HASH_ALGORITHM),
            'FK_User_Type' => 1,
            'Archive' => 0
        );
        $this->user_model->update($dummy_user_id, $user);
    }
    
}