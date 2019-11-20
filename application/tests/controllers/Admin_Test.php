<?php
require_once(__DIR__.'/../../third_party/Test_Trait.php');
require_once(__DIR__.'/../../controllers/Admin.php');

/**
 * Class for tests for Admin controller
 * 
 * @author      Orif, section informatique (ViDi, MeSa, BuYa)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class Admin_Test extends TestCase {
    use Test_Trait;

    /**
     * List of dummy values
     *
     * @var array
     */
    private static $_dummy_values = [
        'user' => [
            'user' => 'dummy_admin_user',
            'user_alt' => 'user_admin_dummy',
            'user_type' => 1,
            'password' => 'dummy_password'
        ],
    ];
    /**
     * List of users created
     *
     * @var array
     */
    private static $dummy_ids = [
        'users' => []
    ];

    /*******************
     * START/END METHODS
     *******************/
    public function setUp()
    {
        $this->resetInstance();
        $this->_login_admin();
    }
    public function tearDown()
    {
        parent::tearDown();

        self::_dummy_users_reset();

        session_reset();
    }
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        self::_dummy_users_delete();
    }

    /*******
     * TESTS
     *******/
    /**
     * Test for `Admin::index`
     *
     * @dataProvider provider_index
     * 
     * @param callable $callback = method to call before starting
     * @param boolean $redirect = Whether a redirect should be expected
     * @param string $content = Content expected in the page
     * @return void
     */
    public function test_index(callable $callback, bool $redirect, string $content)
    {
        $this->_db_errors_save();

        $callback();
        $output = $this->request('GET', 'admin/index');
        if($redirect) {
            $this->assertRedirect('auth/login');
        } else {
            $this->assertContains($content, $output);
        }

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Admin::user_index`
     *
     * @dataProvider provider_index
     * 
     * @param callable $callback = method to call before starting
     * @param boolean $redirect = Whether a redirect should be expected
     * @param string $content = Content expected in the page
     * @return void
     */
    public function test_user_index(callable $callback, bool $redirect, string $content)
    {
        $this->_db_errors_save();

        $callback();
        $output = $this->request('GET', 'admin/user_index');
        if($redirect) {
            $this->assertRedirect('auth/login');
        } else {
            $this->assertContains($content, $output);
        }

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Admin::user_add`
     * 
     * @dataProvider provider_user_add
     *
     * @param string $user_id = ID of the user to use for user_add
     * @param string $content = Content expected in the page
     * @return void
     */
    public function test_user_add($user_id, string $content)
    {
        $this->_db_errors_save();

        $output = $this->request('GET', "admin/user_add/{$user_id}");
        $this->assertContains($content, $output);

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Admin::user_form`
     * 
     * @dataProvider provider_user_form_create
     * @dataProvider provider_user_form_update
     * @dataProvider provider_user_form_disactivate
     * @dataProvider provider_user_form_reactivate
     *
     * @param array $post_params = Parameters to pass to $_POST.
     * @param boolean $error_expected = Whether an error is expected
     * @param callable $callback = function to execute before the test
     * @return void
     */
    public function test_user_form(array $post_params, bool $error_expected, int $user_id = 0, ?string $action = NULL)
    {
        $this->_db_errors_save();

        if(!is_null($action)) {
            $this->CI->load->model('user_model');
            if($action === 'archive')
                $this->CI->user_model->update($user_id, ['Archive' => 1]);
            elseif($action === 'unarchive')
                $this->CI->user_model->update($user_id, ['Archive' => 0]);
        }

        $error_div = '<div class="alert alert-danger">';
        $output = (string)$this->request('POST', 'admin/user_form', $post_params);
        if($error_expected) {
            $this->assertContains($error_div, $output);
        } else {
            $this->assertFalse(strpos($output, $error_div));
        }

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Admin::user_delete($id, 0)`
     * 
     * @dataProvider provider_user_delete_confirm
     * 
     * @param integer $user_id = ID of the user
     * @return void
     */
    public function test_user_delete_confirm(int $user_id)
    {
        $this->resetInstance();
        $this->CI->load->model('user_model');

        $this->_db_errors_save();

        $user = $this->CI->user_model->with_deleted()->get($user_id);
        $redirect_expected = is_null($user);

        $output = $this->request('GET', "admin/user_delete/{$user_id}/0");
        if($redirect_expected) {
            $this->assertRedirect('admin/user_index');
        } else {
            $this->assertContains($user->User, $output);
        }

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Admin::user_delete($id, 1)`
     * 
     * @dataProvider provider_user_delete_deactivate
     *
     * @param integer $user_id = ID of the user
     * @return void
     */
    public function test_user_delete_deactivate(int $user_id)
    {
        $this->resetInstance();
        $this->CI->load->model('user_model');

        $this->_db_errors_save();

        $user = $this->CI->user_model->with_deleted()->get($user_id);
        $this->request('GET', "admin/user_delete/{$user_id}/1");
        $upuser = $this->CI->user_model->with_deleted()->get($user_id);

        if(!is_null($user)) {
            $this->assertTrue($upuser->Archive == 1);
        } else {
            $this->assertNull($upuser);
            // There is a chance it was created when it shouldn't have been,
            // so just add it to the list of dummy ids for the resets.
            // At the end, it will be removed
            if(!is_null($upuser)) self::$dummy_ids[] = $upuser->ID;
        }

        $this->assertRedirect('admin/user_index');

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Admin::user_delete($id, 2)`
     * 
     * @dataProvider provider_user_delete_delete
     *
     * @param integer $user_id = ID of the user
     * @return void
     */
    public function test_user_delete_delete(int $user_id)
    {
        $this->resetInstance();
        $this->CI->load->model('user_model');

        $this->_db_errors_save();

        $this->request('GET', "admin/user_delete/{$user_id}/2");
        $upuser = $this->CI->user_model->with_deleted()->get($user_id);

        $this->assertNull($upuser);
        $this->assertRedirect('admin/user_index');
        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
        if(!is_null($upuser)) self::$dummy_ids[] = $upuser->ID;
    }
    public function test_user_delete_nothing()
    {
        $this->resetInstance();

        $this->_db_errors_save();

        $this->request('GET', 'admin/user_delete/1/-1');
        $this->assertRedirect('admin/user_index');

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Admin::user_change_password`
     * 
     * @dataProvider provider_user_change_password
     *
     * @param integer $user_id = ID of the user
     * @return void
     */
    public function test_user_change_password(int $user_id)
    {
        $this->resetInstance();
        $this->CI->load->model('user_model');

        $this->_db_errors_save();

        $user = $this->CI->user_model->get($user_id);
        $redirect_expected = is_null($user);

        $output = $this->request('GET', "admin/user_change_password/{$user_id}");
        if($redirect_expected) {
            $this->assertRedirect('admin/user_index');
        } else {
            $this->assertContains($user->User, $output);
        }

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Admin::user_change_password_form`
     * 
     * @dataProvider provider_user_change_password_form
     *
     * @param array $post_params = Parameters to pass to $_POST
     * @param boolean $error_expected = Whether an error is expected
     * @param integer $user_id = ID of the user
     * @param string $password = Password of the user, only if the user exists
     * @return void
     */
    public function test_user_change_password_form(array $post_params, bool $error_expected, int $user_id, ?string $password)
    {
        $this->resetInstance();
        $error_div = '<div class="alert alert-danger">';

        $this->_db_errors_save();

        $output = (string)$this->request('POST', 'admin/user_change_password_form', $post_params);

        if($error_expected) {
            $this->assertContains($error_div, $output);
        } else {
            $this->CI->load->model('user_model');
            $user = $this->CI->user_model->get($user_id);
            $this->assertTrue(password_verify($password, $user->Password));
        }

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Admin::not_null_user`
     * 
     * @dataProvider provider_not_null_user
     *
     * @param integer $user_id = ID of the user
     * @param boolean $expected = Expected result of `not_null_user` with $user_id
     * @return void
     */
    public function test_not_null_user(int $user_id, bool $expected)
    {
        reset_instance();
        $controller = new Admin();
        $this->CI =& get_instance();

        $this->_db_errors_save();

        $this->assertSame($expected, $this->CI->not_null_user($user_id));

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Admin::cb_not_inactive_user`
     *
     * @dataProvider provider_not_inactive_user
     * 
     * @param integer $user_id = ID of the user
     * @param boolean $expected = Expected result of `cb_not_inactive_user`
     * @param callable $callback = Function to call with $user_id for configuration
     * @return void
     */
    public function test_not_inactive_user(int $user_id, bool $expected, callable $callback)
    {
        reset_instance();
        $controller = new Admin();
        $this->CI =& get_instance();

        $this->_db_errors_save();

        $callback($user_id);

        $this->assertSame($expected, $this->CI->cb_not_inactive_user(0, $user_id));

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Admin::cb_not_active_user`
     *
     * @dataProvider provider_not_active_user
     * 
     * @param integer $user_id = ID of the user
     * @param boolean $expected = Expected result of `cb_not_active_user`
     * @param callable $callback = Function to call with $user_id for configuration
     * @return void
     */
    public function test_not_active_user(int $user_id, bool $expected, callable $callback)
    {
        reset_instance();
        $controller = new Admin();
        $this->CI =& get_instance();

        $this->_db_errors_save();

        $callback($user_id);

        $this->assertSame($expected, $this->CI->cb_not_active_user(0, $user_id));

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Admin::cb_type_exists`
     *
     * @dataProvider provider_type_exists
     * 
     * @param integer $type_id = ID of the user_type
     * @param boolean $expected = Expected result of `cb_type_exists`
     * @return void
     */
    public function test_type_exists(int $type_id, bool $expected)
    {
        reset_instance();
        $controller = new Admin();
        $this->CI =& get_instance();

        $this->_db_errors_save();

        $this->assertSame($expected, $this->CI->cb_type_exists($type_id));

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }

    /***********
     * PROVIDERS
     ***********/
    /**
     * Provider for `test_index`
     *
     * @return array
     */
    public function provider_index() : array
    {
        $this->resetInstance();

        $data = [];

        $data['not_logged'] = [
            function() {session_reset();$_SESSION = [];},
            TRUE,
            ''
        ];

        $data['logged_user'] = [
            function() {$this->_login_user();},
            FALSE,
            $this->CI->lang->line('msg_err_access_denied_message')
        ];

        $data['logged_manager'] = [
            function() {$this->_login_manager();},
            FALSE,
            $this->CI->lang->line('msg_err_access_denied_message')
        ];

        $data['logged_admin'] = [
            function() {$this->_login_admin();},
            FALSE,
            $this->CI->lang->line('title_users')
        ];

        return $data;
    }
    /**
     * Provider for `test_user_add`
     *
     * @return array
     */
    public function provider_user_add() : array
    {
        $this->resetInstance();

        $data = [];

        $data['empty'] = [
            '',
            $this->CI->lang->line('title_user_new')
        ];

        $data['exists_no_error'] = [
            1,
            $this->CI->lang->line('title_user_update')
        ];

        $data['not_exists_no_error'] = [
            -1,
            $this->CI->lang->line('title_user_new')
        ];

        $data['not_id'] = [
            'id_here',
            $this->CI->lang->line('title_user_new')
        ];

        return $data;
    }
    /**
     * Provider for `test_user_form`, only creation parameters
     *
     * @return array
     */
    public function provider_user_form_create() : array
    {
        $this->resetInstance();
        $this->CI->load->model('user_type_model');
        $user_name = self::$_dummy_values['user']['user'];
        $user_type = self::$_dummy_values['user']['user_type'];
        $password = self::$_dummy_values['user']['password'];

        $data = [];

        $data['new_no_error'] = [
            [
                'id' => 0,
                'user_name' => $user_name,
                'user_usertype' => $user_type,
                'user_password' => $password,
                'user_password_again' => $password
            ],
            FALSE
        ];

        $short_user_name = substr($user_name, 0, USERNAME_MIN_LENGTH-1);
        $data['new_short_user_name'] = [
            [
                'id' => 0,
                'user_name' => $short_user_name,
                'user_usertype' => $user_type,
                'user_password' => $password,
                'user_password_again' => $password
            ],
            TRUE
        ];

        $repeat_count = ceil(USERNAME_MAX_LENGTH / strlen($user_name)) + 1;
        $long_user_name = str_repeat($user_name, $repeat_count);
        $data['new_long_user_name'] = [
            [
                'id' => 0,
                'user_name' => $long_user_name,
                'user_usertype' => $user_type,
                'user_password' => $password,
                'user_password_again' => $password
            ],
            TRUE
        ];

        $short_password = substr($password, 0, PASSWORD_MIN_LENGTH-1);
        $data['new_short_password'] = [
            [
                'id' => 0,
                'user_name' => $user_name,
                'user_usertype' => $user_type,
                'user_password' => $short_password,
                'user_password_again' => $short_password
            ],
            TRUE
        ];

        $repeat_count = ceil(PASSWORD_MAX_LENGTH / strlen($password));
        $long_password = str_repeat($password, $repeat_count);
        $data['new_long_password'] = [
            [
                'id' => 0,
                'user_name' => $user_name,
                'user_usertype' => $user_type,
                'user_password' => $long_password,
                'user_password_again' => $long_password
            ],
            TRUE
        ];

        $data['new_password_not_match'] = [
            [
                'id' => 0,
                'user_name' => $user_name,
                'user_usertype' => $user_type,
                'user_password' => $password,
                'user_password_again' => $password.'_wrong'
            ],
            TRUE
        ];

        $bad_user_type = $this->CI->user_type_model->get_next_id()+100;
        $data['new_user_type_not_exist'] = [
            [
                'id' => 0,
                'user_name' => $user_name,
                'user_usertype' => $bad_user_type,
                'user_password' => $password,
                'user_password_again' => $password
            ],
            TRUE
        ];

        return $data;
    }
    /**
     * Provider for `test_user_form`, only update parameters
     *
     * @return array
     */
    public function provider_user_form_update() : array
    {
        $user_id = self::_dummy_user_create();

        $this->resetInstance();
        $this->CI->load->model('user_type_model');
        $user_name = self::$_dummy_values['user']['user'];
        $user_type = self::$_dummy_values['user']['user_type'];
        $user_name_alt = self::$_dummy_values['user']['user_alt'];

        $data = [];

        $data['update_no_error'] = [
            [
                'save' => TRUE,
                'id' => $user_id,
                'user_name' => $user_name_alt,
                'user_usertype' => $user_type
            ],
            FALSE,
            $user_id
        ];

        $short_user_name = substr($user_name, 0, USERNAME_MIN_LENGTH-1);
        $data['update_short_user_name'] = [
            [
                'save' => TRUE,
                'id' => 0,
                'user_name' => $short_user_name,
                'user_usertype' => $user_type
            ],
            TRUE,
            $user_id
        ];

        $repeat_count = ceil(USERNAME_MAX_LENGTH / strlen($user_name)) + 1;
        $long_user_name = str_repeat($user_name, $repeat_count);
        $data['update_long_user_name'] = [
            [
                'save' => TRUE,
                'id' => 0,
                'user_name' => $long_user_name,
                'user_usertype' => $user_type
            ],
            TRUE,
            $user_id
        ];

        $bad_user_type = $this->CI->user_type_model->get_next_id()+100;
        $data['update_user_type_not_exist'] = [
            [
                'save' => TRUE,
                'id' => 0,
                'user_name' => $user_name,
                'user_usertype' => $bad_user_type
            ],
            TRUE,
            $user_id
        ];

        return $data;
    }
    /**
     * Provider for `test_user_form`, only disactivate parameters
     *
     * @return array
     */
    public function provider_user_form_disactivate() : array
    {
        $user_id = self::_dummy_user_create();

        $this->resetInstance();
        $user_name = self::$_dummy_values['user']['user'];
        $user_type = self::$_dummy_values['user']['user_type'];

        $data = [];

        $data['disactivate_no_error'] = [
            [
                'id' => $user_id,
                'user_name' => $user_name,
                'user_usertype' => $user_type,
                'disactivate' => TRUE
            ],
            FALSE,
            $user_id,
            'unarchive'
        ];

        $data['disactivate_double_disactivate'] = [
            [
                'id' => $user_id,
                'user_name' => $user_name,
                'user_usertype' => $user_type,
                'disactivate' => TRUE
            ],
            TRUE,
            $user_id,
            'archive'
        ];

        return $data;
    }
    /**
     * Provider for `test_user_form`, only reactivate parameters
     *
     * @return array
     */
    public function provider_user_form_reactivate() : array
    {
        $user_id = self::_dummy_user_create();

        $this->resetInstance();
        $user_name = self::$_dummy_values['user']['user'];
        $user_type = self::$_dummy_values['user']['user_type'];

        $data = [];

        $data['reactivate_no_error'] = [
            [
                'id' => $user_id,
                'user_name' => $user_name,
                'user_usertype' => $user_type,
                'reactivate' => TRUE
            ],
            FALSE,
            $user_id,
            'archive'
        ];

        $data['reactivate_double_reactivate'] = [
            [
                'id' => $user_id,
                'user_name' => $user_name,
                'user_usertype' => $user_type,
                'reactivate' => TRUE
            ],
            TRUE,
            $user_id,
            'unarchive'
        ];

        return $data;
    }
    /**
     * Provider for `test_user_delete_confirm`
     *
     * @return array
     */
    public function provider_user_delete_confirm() : array
    {
        $user_id = self::_dummy_user_create();

        $this->resetInstance();
        $this->CI->load->model('user_model');

        $data = [];

        $data['confirm_no_error'] = [$user_id];

        $data['confirm_negative'] = [-1];

        $data['confirm_not_exist'] = [$this->CI->user_model->get_next_id()+100];

        return $data;
    }
    /**
     * Provider for `test_user_delete_deactivate`
     *
     * @return array
     */
    public function provider_user_delete_deactivate() : array
    {
        $user_id = self::_dummy_user_create();

        $this->resetInstance();
        $this->CI->load->model('user_model');

        $data = [];

        $data['deactivate_no_error'] = [$user_id];

        $data['deactivate_negative'] = [-1];

        $data['deactivate_not_exist'] = [$this->CI->user_model->get_next_id()+100];

        return $data;
    }
    /**
     * Provider for `test_user_delete_delete`
     *
     * @return array
     */
    public function provider_user_delete_delete() : array
    {
        $user_id = self::_dummy_user_create();

        $this->resetInstance();
        $this->CI->load->model('user_model');

        $data = [];

        $data['delete_no_error'] = [$user_id];

        $data['delete_negative'] = [-1];

        $data['delete_not_exist'] = [$this->CI->user_model->get_next_id()+100];

        return $data;
    }
    /**
     * Provider for `test_user_change_password`
     *
     * @return array
     */
    public function provider_user_change_password() : array
    {
        $user_id = self::_dummy_user_create();

        $this->resetInstance();
        $this->CI->load->model('user_model');

        $data = [];

        $data['no_error'] = [$user_id];

        $data['negative'] = [-1];

        $data['not_exist'] = [$this->CI->user_model->get_next_id()+100];

        return $data;
    }
    /**
     * Provider for `test_user_change_password_form`
     *
     * @return array
     */
    public function provider_user_change_password_form() : array
    {
        $user_id = self::_dummy_user_create();
        $password = 'password_dummy';

        $this->resetInstance();
        $this->CI->load->model('user_model');

        $data = [];

        $data['no_error'] = [
            [
                'id' => $user_id,
                'user_password_new' => $password,
                'user_password_again' => $password
            ],
            FALSE,
            $user_id,
            $password
        ];

        $short_password = substr($password, 0, PASSWORD_MIN_LENGTH-1);
        $data['short_password'] = [
            [
                'id' => $user_id,
                'user_password_new' => $short_password,
                'user_password_again' => $short_password
            ],
            TRUE,
            $user_id,
            $short_password
        ];

        $repeat_count = ceil(PASSWORD_MAX_LENGTH / strlen($password)) + 1;
        $long_password = str_repeat($password, $repeat_count);
        $data['long_password'] = [
            [
                'id' => $user_id,
                'user_password_new' => $long_password,
                'user_password_again' => $long_password
            ],
            TRUE,
            $user_id,
            $long_password
        ];

        $bad_id = $this->CI->user_model->get_next_id()+100;
        $data['not_exist'] = [
            [
                'id' => $bad_id,
                'user_password_new' => $password,
                'user_password_again' => $password
            ],
            TRUE,
            $bad_id,
            $password
        ];

        return $data;
    }
    /**
     * Provider for `test_not_null_user`
     *
     * @return array
     */
    public function provider_not_null_user() : array
    {
        $user_id = self::_dummy_user_create();

        $this->resetInstance();
        $this->CI->load->model('user_model');

        $data = [];

        $data['no_error'] = [
            $user_id,
            TRUE
        ];

        $data['negative'] = [
            -1,
            FALSE
        ];

        $data['zero'] = [
            0,
            TRUE
        ];

        $data['not_exist'] = [
            $this->CI->user_model->get_next_id()+100,
            FALSE
        ];

        return $data;
    }
    /**
     * Provider for `test_not_inactive_user`
     *
     * @return array
     */
    public function provider_not_inactive_user() : array
    {
        $user_id = self::_dummy_user_create();

        $this->resetInstance();
        $this->CI->load->model('user_model');

        $data = [];

        $data['active'] = [
            $user_id,
            TRUE,
            function($user_id) {
                $CI =& get_instance();
                $CI->load->model('user_model');
                $CI->user_model->update($user_id, ['Archive' => 0]);
            }
        ];

        $data['inactive'] = [
            $user_id,
            FALSE,
            function($user_id) {
                $CI =& get_instance();
                $CI->load->model('user_model');
                $CI->user_model->update($user_id, ['Archive' => 1]);
            }
        ];

        $data['negative'] = [
            -1,
            FALSE,
            function() { }
        ];

        $data['not_exist'] = [
            $this->CI->user_model->get_next_id()+100,
            FALSE,
            function() { }
        ];

        return $data;
    }
    /**
     * Provider for `test_not_active_user`
     *
     * @return array
     */
    public function provider_not_active_user() : array
    {
        $user_id = self::_dummy_user_create();

        $this->resetInstance();
        $this->CI->load->model('user_model');

        $data = [];

        $data['inactive'] = [
            $user_id,
            TRUE,
            function($user_id) {
                $CI =& get_instance();
                $CI->load->model('user_model');
                $CI->user_model->update($user_id, ['Archive' => 1]);
            }
        ];

        $data['active'] = [
            $user_id,
            FALSE,
            function($user_id) {
                $CI =& get_instance();
                $CI->load->model('user_model');
                $CI->user_model->update($user_id, ['Archive' => 0]);
            }
        ];

        $data['negative'] = [
            -1,
            FALSE,
            function() { }
        ];

        $data['not_exist'] = [
            $this->CI->user_model->get_next_id()+1,
            FALSE,
            function() { }
        ];

        return $data;
    }
    /**
     * Provider for `test_type_exists`
     *
     * @return array
     */
    public function provider_type_exists() : array
    {
        $this->resetInstance();
        $this->CI->load->model('user_type_model');

        $data = [];

        $data['no_error'] = [
            $this->CI->user_type_model->get_all()[0]->ID,
            TRUE
        ];

        $data['negative'] = [
            -1,
            FALSE
        ];

        $data['not_exist'] = [
            $this->CI->user_type_model->get_next_id()+1,
            FALSE
        ];

        return $data;
    }

    /**************
     * MISC METHODS
     **************/
    /**
     * Logs in as an admin user
     *
     * @return void
     */
    private function _login_admin()
    {
        session_reset();
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = '';
        $_SESSION['user_access'] = ACCESS_LVL_ADMIN;
        $_SESSION['logged_in'] = TRUE;
    }
    /**
     * Logs in as a manager user
     *
     * @return void
     */
    private function _login_manager()
    {
        session_reset();
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = '';
        $_SESSION['user_access'] = ACCESS_LVL_MANAGER;
        $_SESSION['logged_in'] = TRUE;
    }
    /**
     * Logs in as a user user
     *
     * @return void
     */
    private function _login_user()
    {
        session_reset();
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = '';
        $_SESSION['user_access'] = ACCESS_LVL_USER;
        $_SESSION['logged_in'] = TRUE;
    }

    /**
     * Creates a dummy user according to dummy values.
     * 
     * It's also recorded in the list of users to delete later.
     *
     * @return integer = ID of the dummy user
     */
    private static function _dummy_user_create() : int
    {
		reset_instance();
		CIPHPUnitTest::createCodeIgniterInstance();
        $CI =& get_instance();
        $CI->load->model('user_model');

        $dummy_user_values = self::$_dummy_values['user'];
        $dummy_user = array(
            'User' => $dummy_user_values['user'],
            'FK_User_Type' => $dummy_user_values['user_type'],
            'Password' => password_hash($dummy_user_values['password'], PASSWORD_HASH_ALGORITHM),
            'Archive' => 0
        );

        return self::$dummy_ids[] = $CI->user_model->insert($dummy_user);
    }
    /**
     * Resets all the saved dummy users
     *
     * @return void
     */
    private static function _dummy_users_reset()
    {
		reset_instance();
		CIPHPUnitTest::createCodeIgniterInstance();
        $CI =& get_instance();
        $CI->load->model('user_model');

        $dummy_user_values = self::$_dummy_values['user'];
        $dummy_user = array(
            'User' => $dummy_user_values['user'],
            'FK_User_Type' => $dummy_user_values['user_type'],
            'Password' => password_hash($dummy_user_values['password'], PASSWORD_HASH_ALGORITHM),
            'Archive' => 0
        );
        $CI->user_model->update_many(self::$dummy_ids['users'], $dummy_user);
    }
    /**
     * Deletes all the dummy users
     *
     * @return void
     */
    private static function _dummy_users_delete()
    {
		reset_instance();
		CIPHPUnitTest::createCodeIgniterInstance();
        $CI =& get_instance();
        $CI->load->model('user_model');

        $users = $CI->user_model->with_deleted()->get_many_by(['User' => self::$_dummy_values['user']['user']]);
        foreach($users as $user) {
            $CI->user_model->delete($user->ID, TRUE);
        }

        $users = $CI->user_model->with_deleted()->get_many_by(['User' => self::$_dummy_values['user']['user_alt']]);
        foreach($users as $user) {
            $CI->user_model->delete($user->ID, TRUE);
        }

        self::$dummy_ids['users'] = [];
    }
}
