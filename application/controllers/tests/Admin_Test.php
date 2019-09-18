<?php

include(__DIR__.'/../Admin.php');

/**
 * Exists only to rewrite the `redirect` function from codeigniter.
 *
 * @param string $uri
 * @param string $method
 * @param integer $code
 * @return void
 */
function redirect($uri = '', $method = 'auto', $code = NULL) { }

class Admin_Test extends Admin {
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
     * Last saved database error
     *
     * @var object
     */
    private $_old_db_error;

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
        if(array_search('user_create', $this->_tests) !== FALSE) {
            $this->test_user_add_no_errors();
            $this->test_user_add_short_name();
            $this->test_user_add_long_name();
            $this->test_user_add_short_pwd();
            $this->test_user_add_long_pwd();
            $this->test_user_add_pwd_not_match();
        }

        if(array_search('user_update', $this->_tests) !== FALSE) {
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

            $this->user_model->hard_delete($dummy_user_id);
        }

        if(array_search('user_delete', $this->_tests) !== FALSE) {
            $dummy_user_id = $this->_dummy_user_create();

            $this->test_user_delete_no_delete($dummy_user_id);
            $this->_dummy_user_reset($dummy_user_id);

            $this->test_user_delete_not_exist();

            $this->test_user_delete_disactivate($dummy_user_id);
            $this->_dummy_user_reset($dummy_user_id);

            $this->test_user_delete_disactivate_not_exist();

            $this->test_user_delete_delete();

            $this->test_user_delete_delete_not_exist();

            $this->user_model->hard_delete($dummy_user_id);
        }

        if(array_search('user_pwd_change', $this->_tests) !== FALSE) {
            $dummy_user_id = $this->_dummy_user_create();

            $this->test_user_change_pwd_no_change($dummy_user_id);
            $this->_dummy_user_reset($dummy_user_id);

            $this->test_user_change_pwd_no_errors($dummy_user_id);
            $this->_dummy_user_reset($dummy_user_id);

            $this->test_user_change_pwd_not_match($dummy_user_id);
            $this->_dummy_user_reset($dummy_user_id);

            $this->test_user_change_pwd_not_exist();
            $this->_dummy_user_reset($dummy_user_id);

            $this->user_model->hard_delete($dummy_user_id);
        }

        // Display test results
        $output['test_results'] = $this->_test_results;
        parent::display_view('tests/tests_results.php', $output);
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

        $this->_test_results['user']['create']['no_errors'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_errors_diff(),
            'errors_expected' => FALSE,
            'db_update' => !is_null($user),
            'db_update_expected' => TRUE
        );

        if(!is_null($user)) {
            $this->user_model->hard_delete($user->ID);
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

        $this->_test_results['user']['create']['short_name'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_errors_diff(),
            'errors_expected' => TRUE,
            'db_update' => !is_null($user),
            'db_update_expected' => FALSE
        );

        if(!is_null($user)) {
            $this->user_model->hard_delete($user->ID);
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

        $this->_test_results['user']['create']['long_name'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_errors_diff(),
            'errors_expected' => TRUE,
            'db_update' => !is_null($user),
            'db_update_expected' => FALSE
        );

        if(!is_null($user)) {
            $this->user_model->hard_delete($user->ID);
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

        $this->_test_results['user']['create']['short_pwd'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_errors_diff(),
            'errors_expected' => TRUE,
            'db_update' => !is_null($user),
            'db_update_expected' => FALSE
        );

        if(!is_null($user)) {
            $this->user_model->hard_delete($user->ID);
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

        $this->_test_results['user']['create']['long_pwd'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_errors_diff(),
            'errors_expected' => TRUE,
            'db_update' => !is_null($user),
            'db_update_expected' => FALSE
        );

        if(!is_null($user)) {
            $this->user_model->hard_delete($user->ID);
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

        $this->_test_results['user']['create']['pwd_not_match'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_errors_diff(),
            'errors_expected' => TRUE,
            'db_update' => !is_null($user),
            'db_update_expected' => FALSE
        );

        if(!is_null($user)) {
            $this->user_model->hard_delete($user->ID);
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

        $user = $this->user_model->get_by('User', $username);

        $this->_test_results['user']['update']['empty_update'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_errors_diff(),
            'errors_expected' => FALSE,
            'db_update' => !is_null($user),
            'db_update_expected' => FALSE
        );
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

        $user = $this->user_model->get_by('User', $username);

        $this->_test_results['user']['update']['no_error'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_errors_diff(),
            'errors_expected' => FALSE,
            'db_update' => !is_null($user),
            'db_update_expected' => TRUE
        );
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

        $this->_test_results['user']['update']['not_exist'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_errors_diff(),
            'errors_expected' => TRUE,
            'db_update' => !is_null($user),
            'db_update_expected' => FALSE
        );

        // In case the user is created by accident, delete it
        if(!is_null($user)) {
            $this->user_model->hard_delete($user->ID);
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

        $user = $this->user_model->get($dummy_user_id);

        $this->_test_results['user']['update']['disactivate'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_errors_diff(),
            'errors_expected' => FALSE,
            'db_update' => $user->Archive == 1,
            'db_update_expected' => TRUE
        );
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

        $user = $this->user_model->get($dummy_user_id);

        $this->_test_results['user']['update']['double_disactivate'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_errors_diff(),
            'errors_expected' => TRUE,
            'db_update' => $user->Archive == 1,
            'db_update_expected' => TRUE
        );
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

        $this->_test_results['user']['update']['double_disactivate'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_errors_diff(),
            'errors_expected' => TRUE,
            'db_update' => !is_null($user),
            'db_update_expected' => FALSE
        );

        // In case the user is created by accident, delete it
        if(!is_null($user)) {
            $this->user_model->hard_delete($user->ID);
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

        $user = $this->user_model->get($dummy_user_id);

        $this->_test_results['user']['update']['reactivate'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_errors_diff(),
            'errors_expected' => FALSE,
            'db_update' => $user->Archive == 0,
            'db_update_expected' => TRUE
        );
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

        $this->_test_results['user']['update']['double_reactivate'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_errors_diff(),
            'errors_expected' => TRUE,
            'db_update' => $user->Archive == 0,
            'db_update_expected' => TRUE
        );
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

        $this->_test_results['user']['update']['double_disactivate'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_errors_diff(),
            'errors_expected' => TRUE,
            'db_update' => !is_null($user),
            'db_update_expected' => FALSE
        );

        // In case the user is created by accident, delete it
        if(!is_null($user)) {
            $this->user_model->hard_delete($user->ID);
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

        $this->_test_results['user']['delete']['no_delete'] = (object) array(
            'errors' => $this->_db_errors_diff(),
            'errors_expected' => FALSE,
            'db_update' => $user->Archive == 1,
            'db_update_expected' => FALSE
        );
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

        $this->_test_results['user']['delete']['not_exist'] = (object) array(
            'errors' => $this->_db_errors_diff(),
            'errors_expected' => FALSE,
            'db_update' => !is_null($user),
            'db_update_expected' => FALSE
        );

        if(!is_null($user)) {
            $this->user_model->hard_delete($user->ID);
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

        $this->_test_results['user']['delete']['disactivate'] = (object) array(
            'errors' => $this->_db_errors_diff(),
            'errors_expected' => FALSE,
            'db_update' => $user->Archive == 1,
            'db_update_expected' => FALSE
        );
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

        $this->_test_results['user']['delete']['disactivate_not_exist'] = (object) array(
            'errors' => $this->_db_errors_diff(),
            'errors_expected' => FALSE,
            'db_update' => !is_null($user),
            'db_update_expected' => FALSE
        );

        if(!is_null($user)) {
            $this->user_model->hard_delete($user->ID);
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

        $this->_test_results['user']['delete']['delete'] = (object) array(
            'errors' => $this->_db_errors_diff(),
            'errors_expected' => FALSE,
            'db_update' => !is_null($user_deleted) == is_null($user_not_deleted),
            'db_update_expected' => TRUE
        );

        if(!is_null($user_deleted)) {
            $this->user_model->hard_delete($dummy_user_id);
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

        $this->_test_results['user']['delete']['delete_not_exist'] = (object) array(
            'errors' => $this->_db_errors_diff(),
            'errors_expected' => FALSE,
            'db_update' => is_null($user_deleted) != is_null($user_not_deleted),
            'db_update_expected' => FALSE
        );

        if(!is_null($user_deleted)) {
            $this->user_model->hard_delete($bad_id);
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
        $this->_send_form_request('change_password', [
            'id' => $dummy_user_id
        ]);

        $this->_test_results['user']['change_pwd']['no_change'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_errors_diff(),
            'errors_expected' => FALSE,
            'db_update' => $this->_db_errors_diff(),
            'db_update_expected' => FALSE
        );
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

        $this->_test_results['user']['change_pwd']['no_errors'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_errors_diff(),
            'errors_expected' => FALSE,
            'db_update' => password_verify($user_password_new, $user->Password),
            'db_update_expected' => TRUE
        );
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

        $this->_test_results['user']['change_pwd']['not_match'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_errors_diff(),
            'errors_expected' => TRUE,
            'db_update' => password_verify($user_password_new, $user->Password),
            'db_update_expected' => FALSE
        );
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

        $this->_test_results['user']['change_pwd']['not_exist'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_errors_diff(),
            'errors_expected' => TRUE,
            'db_update' => !is_null($user),
            'db_update_expected' => FALSE
        );

        if(!is_null($user)) {
            $this->user_model->hard_delete($user->ID);
        }
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
     * Saves the most recent database errors.
     *
     * @return void
     */
    private function _db_errors_save()
    {
        $this->_old_db_error = array(
            'user_error' => $this->user_model->_database->error(),
            'user_type_error' => $this->user_type_model->_database->error(),
        );
    }
    /**
     * Compares the saved errors and the most recent errors.
     *
     * @return boolean = True if the 2 errors are different
     */
    private function _db_errors_diff() : bool
    {
        $user_err_diff = $this->_old_db_error['user_error'] != $this->user_model->_database->error();
        $user_type_err_diff = $this->_old_db_error['user_type_error'] != $this->user_type_model->_database->error();
        return $user_err_diff || $user_type_err_diff;
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