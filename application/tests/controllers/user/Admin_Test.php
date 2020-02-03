<?php
// Load Admin controller for callback tests
require_once(__DIR__.'/../../../modules/user/controllers/Admin.php');

/**
 * Class for tests for Admin controller
 * 
 * @author      Orif, section informatique (ViDi, MeSa, BuYa)
 * @link        https://github.com/OrifInformatique
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 * 
 * @todo Write tests for `Admin::cb_unique_user`
 */
class Admin_Test extends TestCase {
    /**
     * Stores the dummy values for dummy entries
     *
     * @var array
     */
    private static $_dummy_values = [
        'user' => [
            'name' => 'admin_dummy_user',
			'name_alt' => 'admin_user_dummy',
			'name_unique' => 'admin_user_unique',
            'pass' => 'dummy_password',
            'pass_alt' => 'password_dummy',
            'type' => NULL
        ]
    ];

    /**
     * Stores the current dummy ids
     * 
     * Only use these through reference
     *
     * @var array
     */
    private static $_dummy_ids = [
        'user' => NULL
	];

    /**
     * Saves the previous database errors
     *
     * @var array
     */
    private static $_old_db_errors = [];

    /*******************
     * START/END METHODS
     *******************/
    /**
     * Called before testing begins
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $CI =& self::_get_ci_instance();
        $CI->load->database();
        $CI->load->model('user/user_type_model');

        self::$_dummy_values['user']['type'] = $CI->user_type_model->get_all()[0]->id;

        // Save most recent db errors, for the first use
        self::_db_errors_save();
    }
    /**
     * Called before a test
     *
     * @return void
     */
    public function setUp()
    {
        $this->resetInstance();
        // Required to load the correct url_helper
        $this->CI->load->helper('url');
        // Tests cannot work without this
        self::_login_as_admin();
        // Make sure everything exists before testing
        self::_dummy_user_create();

        // Load Admin for future use
        // It can't be put in a static function
        $this->class_map['Admin'] = Admin::class;
    }
    /**
     * Called after a test
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        self::_logout();
        // Reset dummy user, in case it was updated
        self::_dummy_user_reset();
    }
    /**
     * Called after testing ends
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        // Delete all dummies
        self::_dummy_users_wipe();
    }

    /*******
     * TESTS
     *******/
    /**
     * Test for `Admin::list_user` without being logged
     *
     * @return void
     */
    public function test_list_user_not_logged()
    {
        self::_logout();

        $this->_db_errors_save();

        $this->request('GET', 'user/admin/list_user');

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );

        $this->assertRedirect('user/auth/login');
    }
    /**
     * Test for `Admin::list_user`
     * 
     * @dataProvider provider_list_user
     *
     * @param string $with_deleted = Value to pass to $with_deleted
     * @param integer $expected_count = Amount of users expected
     * @return void
     */
    public function test_list_user(string $with_deleted, int $expected_count)
    {
        $this->_db_errors_save();

        $output = $this->request('GET', 'user/admin/list_user/'.$with_deleted);

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
		);

        // Each user is in a <tr>, and so are the headers
        $actual_count = substr_count($output, '<tr>')-1;

        $this->assertEquals($expected_count, $actual_count);
    }
    /**
     * Test for `Admin::save_user` without $_POST data
     * 
     * @dataProvider provider_save_user
     *
     * @param string $user_id = ID of the user to modify
     * @param string $expected_title = Expected title of the page
     * @return void
     */
    public function test_save_user(string $user_id, string $expected_title, callable $setup)
    {
        $setup($user_id);
        $this->CI->lang->load('MY_application');
        $expected_title = '<title>'.$this->CI->lang->line('page_prefix').' - '.$expected_title.'</title>';

        // First request always fails, so make sure it's not first
        $this->request('GET', 'user/admin/save_user');

        $this->_db_errors_save();
        $output = $this->request('GET', 'user/admin/save_user/'.$user_id);

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );

        $this->assertTrue(strpos($output, $expected_title) !== FALSE);
    }
    /**
     * Test for `Admin::save_user` with $_POST data
     * 
     * @dataProvider provider_save_user_post
     *
     * @param array $post_params = Parameters to pass to $_POST
     * @param boolean $error_expected = Whether or not an error is expected in `validation_errors()`
     * @param boolean $redirect_expected = Whether or not a redirect is expected
     * @return void
     */
    public function test_save_user_post(array $post_params, bool $error_expected, bool $redirect_expected)
    {
        $this->_db_errors_save();

        $this->request('POST', 'user/admin/save_user', $post_params);

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );

        if ($error_expected) {
            $this->assertNotEmpty(validation_errors());
        } else {
            $this->assertEmpty(validation_errors());
        }

        if ($redirect_expected) {
            $this->assertRedirect('user/admin/list_user');
        }
    }
    /**
     * Test for `Admin::delete_user`
     * 
     * @dataProvider provider_delete_user
     *
     * @param string $user_id = ID of the user to "delete"
     * @param string $action = Value to pass to $action
     * @param array $status = Status of the user
     * @param boolean $redirect_expected = Whether a redirect is expected
     * @return void
     */
    public function test_delete_user(string $user_id, string $action, array $status, bool $redirect_expected)
    {
        $this->CI->load->model('user/user_model');

        $user = $this->CI->user_model->with_deleted()->get($user_id);
        if ($status['deleted']['pre']) {
            $this->assertNull($user);
        } else {
            $this->assertTrue((int)$user->archive == (int)$status['archived']['pre']);
        }

        $this->_db_errors_save();

        $this->request('GET', "user/admin/delete_user/{$user_id}/{$action}");

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );

        $user = $this->CI->user_model->with_deleted()->get($user_id);
        if ($status['deleted']['post']) {
            $this->assertNull($user);
        } else {
            $this->assertTrue((int)$user->archive == (int)$status['archived']['post']);
        }
        if ($redirect_expected) {
            $this->assertRedirect('user/admin/list_user');
        }
    }
    /**
     * Test for `Admin::reactivate_user`
     * 
     * @dataProvider provider_reactivate_user
     *
     * @param integer $user_id = ID of the user to reactivate
     * @param boolean $redirect_to_index = Whether the method redirects to index or to add
     * @return void
     */
    public function test_reactivate_user(int $user_id, bool $redirect_to_index)
    {
        $this->_db_errors_save();

        $this->request('GET', "user/admin/reactivate_user/{$user_id}");

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );

        $target = 'user/admin/';
        if ($redirect_to_index) {
            $target .= 'list_user';
        } else {
            $target .= "save_user/{$user_id}";
        }

        $this->assertRedirect($target);
    }
    /**
     * Test for `Admin::password_change_user` without $_POST data
     * 
     * @dataProvider provider_password_change_user
     *
     * @param integer $user_id = ID of the user
     * @param boolean $redirect_expected = Whether a redirect is expected
     * @return void
     */
    public function test_password_change_user(int $user_id, bool $redirect_expected)
    {
        $this->CI->lang->load('MY_application');

        $this->_db_errors_save();

        $output = $this->request('GET', "user/admin/password_change_user/{$user_id}");

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );

        if ($redirect_expected) {
            $this->assertRedirect('user/admin/list_user');
        } else {
            $this->assertContains($this->CI->lang->line('title_user_password_reset'), $output);
        }
    }
    /**
     * Test for `Admin::password_change_user` with $_POST data
     * 
     * @dataProvider provider_password_change_user_post
     *
     * @param array $post_params = Parameters to pass to $_POST
     * @param boolean $error_expected = Whether an error is expected
     * @param boolean $redirect_expected = Whether a redirect is expected
     * @return void
     */
    public function test_password_change_user_post(array $post_params, bool $error_expected, bool $redirect_expected)
    {
        $this->_db_errors_save();

        $this->request('POST', "user/admin/password_change_user/{$post_params['id']}", $post_params);

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );

        if ($error_expected) {
            $this->assertNotEmpty(validation_errors());
        }
        if ($redirect_expected) {
            $this->assertRedirect('user/admin/list_user');
        }
    }
    /**
     * Test for `Admin::cb_not_null_user`
     * 
     * @dataProvider provider_not_null_user
     *
     * @param integer $user_id = ID of the user to test
     * @param boolean $expected_result = Expected result from the call
     * @return void
     */
    public function test_not_null_user(int $user_id, bool $expected_result)
    {
        $this->assertSame($expected_result, $this->Admin->cb_not_null_user($user_id));
    }
    /**
     * Test for `Admin::cb_not_null_user_type`
     * 
     * @dataProvider provider_not_null_user_type
     *
     * @param integer $user_type = Type of the user to test
     * @param boolean $expected_result = Expected result from the call
     * @return void
     */
    public function test_not_null_user_type(int $user_type, bool $expected_result)
    {
        $this->assertSame($expected_result, $this->Admin->cb_not_null_user_type($user_type));
    }

    /***********
     * PROVIDERS
     ***********/
	/**
	 * Provider for `test_list_user`
	 *
	 * @return array
	 */
	public function provider_list_user() : array
	{
        self::_dummy_user_create();
        $this->resetInstance();
        $this->CI->load->database();
        $this->CI->load->model('user/user_model');

        $data = [];

        $data['none_hide'] = [
            '',
            $this->CI->user_model->count_by(['archive' => 0])
        ];

        $data['hide'] = [
            '0',
            $this->CI->user_model->count_by(['archive' => 0])
        ];

        $data['show'] = [
            '1',
            $this->CI->user_model->with_deleted()->count_all()
        ];

        return $data;
	}
    /**
     * Provider for `test_save_user`
     *
     * @return array
     */
    public function provider_save_user() : array
    {
        $this->resetInstance();
        $user_id =& self::_dummy_user_create();
        $this->CI->lang->load('../../modules/user/language/french/MY_user');

        $data = [];

        $data['new_none'] = [
            '',
            $this->CI->lang->line('title_user_new'),
            function() { }
        ];

        $data['new_0'] = [
            '0',
            $this->CI->lang->line('title_user_new'),
            function() { }
        ];

        $data['update_active'] = [
            (string)$user_id,
            $this->CI->lang->line('title_user_update'),
            function($user_id) {
                $CI =& self::_get_ci_instance();
                $CI->load->model('user/user_model');

                $CI->user_model->update($user_id, ['archive' => 1]);
            }
        ];

        return $data;
    }
    /**
     * Provider for `test_save_user_post`
     *
     * @return array
     */
    public function provider_save_user_post() : array
    {
        $this->resetInstance();
        $this->CI->load->model(['user/user_model', 'user/user_type_model']);
        $this->CI->load->config('../modules/user/config/MY_user_config');

        $user_id =& self::_dummy_user_create();
		$user_name = self::$_dummy_values['user']['name_alt'];
		$user_name_unique = self::$_dummy_values['user']['name_unique'];
        $user_type = self::$_dummy_values['user']['type'];
        $user_pass = self::$_dummy_values['user']['pass'];

        $data = [];

        $data['no_error_add'] = [
            [
                'id' => 0,
                'user_name' => $user_name_unique,
                'user_usertype' => $user_type,
                'user_password' => $user_pass,
                'user_password_again' => $user_pass
            ],
            FALSE,
            TRUE
        ];

        $data['no_error_update'] = [
            [
                'save' => 1,
                'id' => &$user_id,
                'user_name' => $user_name,
                'user_usertype' => $user_type
            ],
            FALSE,
            TRUE
        ];

        $bad_id = $this->CI->user_model->get_next_id()+100;
        $data['error_not_exist'] = [
            [
                'id' => $bad_id,
                'user_name' => $user_name,
                'user_usertype' => $user_type
            ],
            TRUE,
            FALSE
        ];
        
        $data['error_no_name'] = [
            [
                'id' => &$user_id,
                'user_usertype' => $user_type
            ],
            TRUE,
            FALSE
        ];

        $name_short = substr($user_name, 0, $this->CI->config->item('username_min_length')-1);
        $data['error_name_short'] = [
            [
                'id' => &$user_id,
                'user_name' => $name_short,
                'user_usertype' => $user_type
            ],
            TRUE,
            FALSE
        ];

        $repeat_count = ceil($this->CI->config->item('username_max_length') / strlen($user_name)) + 1;
        $name_long = str_repeat($user_name, $repeat_count);
        $data['error_name_long'] = [
            [
                'id' => &$user_id,
                'user_name' => $name_long,
                'user_usertype' => $user_type
            ],
            TRUE,
            FALSE
        ];

        $pass_short = substr($user_pass, 0, $this->CI->config->item('password_min_length')-1);
        $data['error_pass_short'] = [
            [
                'id' => 0,
                'user_name' => $user_name,
                'user_usertype' => $user_type,
                'user_password' => $pass_short,
                'user_password_again' => $pass_short
            ],
            TRUE,
            FALSE
        ];

        $repeat_count = ceil($this->CI->config->item('password_max_length') / strlen($user_pass)) + 1;
        $pass_long = str_repeat($user_pass, $repeat_count);
        $data['error_pass_long'] = [
            [
                'id' => 0,
                'user_name' => $user_name,
                'user_usertype' => $user_type,
                'user_password' => $pass_long,
                'user_password_again' => $pass_long
            ],
            TRUE,
            FALSE
        ];

        $data['error_no_type'] = [
            [
                'id' => &$user_id,
                'user_name' => $user_name
            ],
            TRUE,
            FALSE
        ];

        $bad_type = $this->CI->user_type_model->get_next_id()+100;
        $data['error_type_not_exist'] = [
            [
                'id' => &$user_id,
                'user_name' => $user_name,
                'user_usertype' => $bad_type
            ],
            TRUE,
            FALSE
        ];

        $data['error_no_password'] = [
            [
                'id' => 0,
                'user_name' => $user_name,
                'user_usertype' => $user_type
            ],
            TRUE,
            FALSE
        ];

        $data['error_passwords_not_match'] = [
            [
                'id' => 0,
                'user_name' => $user_name,
                'user_usertype' => $user_type,
                'user_password' => $user_pass,
                'user_password_again' => $user_pass.'_wrong'
            ],
            TRUE,
            FALSE
        ];

        return $data;
    }
    /**
     * Provider for `test_delete_user`
     *
     * @return array
     */
    public function provider_delete_user() : array
    {
        $this->resetInstance();
        $this->CI->load->model('user/user_model');
        $user_id =& self::_dummy_user_create();

        $data = [];

        $bad_id = $this->CI->user_model->get_next_id()+100;
        $data['not_exist'] = [
            (string)$bad_id,
            '',
            [
                'deleted' => [
                    'pre' => TRUE,
                    'post' => TRUE
                ]
            ],
            TRUE
        ];

        $data['display'] = [
            &$user_id,
            '',
            [
                'deleted' => [
                    'pre' => FALSE,
                    'post' => FALSE
                ],
                'archived' => [
                    'pre' => FALSE,
                    'post' => FALSE
                ]
            ],
            FALSE
        ];

        $data['deactivate'] = [
            &$user_id,
            '1',
            [
                'deleted' => [
                    'pre' => FALSE,
                    'post' => FALSE
                ],
                'archived' => [
                    'pre' => FALSE,
                    'post' => TRUE
                ]
            ],
            TRUE
        ];

        $data['delete'] = [
            &$user_id,
            '2',
            [
                'deleted' => [
                    'pre' => FALSE,
                    'post' => TRUE
                ],
                'archived' => [
                    'pre' => FALSE
                ]
            ],
            TRUE
        ];

        $data['unknown'] = [
            &$user_id,
            '-1',
            [
                'deleted' => [
                    'pre' => FALSE,
                    'post' => FALSE
                ],
                'archived' => [
                    'pre' => FALSE,
                    'post' => FALSE
                ]
            ],
            TRUE
        ];

        return $data;
    }
    /**
     * Provider for `test_reactivate_user`
     *
     * @return array
     */
    public function provider_reactivate_user() : array
    {
        $this->resetInstance();
        $this->CI->load->model('user/user_model');
        $user_id =& self::_dummy_user_create();

        $data = [];

        $data['no_error'] = [
            &$user_id,
            FALSE
        ];

        $bad_id = $this->CI->user_model->get_next_id()+100;
        $data['not_exist'] = [
            $bad_id,
            TRUE
        ];

        return $data;
    }
    /**
     * Provider for `test_password_change_user`
     *
     * @return array
     */
    public function provider_password_change_user() : array
    {
        $this->resetInstance();
        $this->CI->load->model('user/user_model');
        $user_id =& self::_dummy_user_create();

        $data = [];

        $bad_id = $this->CI->user_model->get_next_id()+100;
        $data['not_exist'] = [
            $bad_id,
            TRUE
        ];

        $data['no_error'] = [
            &$user_id,
            FALSE
        ];

        return $data;
    }
    /**
     * Provider for `test_password_change_user_post`
     *
     * @return array
     */
    public function provider_password_change_user_post() : array
    {
        $this->resetInstance();
        $this->CI->load->model('user/user_model');
        $this->CI->load->config('../modules/user/config/MY_user_config');

        $user_id =& self::_dummy_user_create();
        $user_pass = self::$_dummy_values['user']['pass_alt'];

        $data = [];

        $data['no_error'] = [
            [
                'id' => &$user_id,
                'user_password_new' => $user_pass,
                'user_password_again' => $user_pass
            ],
            FALSE,
            TRUE
        ];

        $bad_id = $this->CI->user_model->get_next_id()+100;
        $data['not_exist'] = [
            [
                'id' => $bad_id,
                'user_password_new' => $user_pass,
                'user_password_again' => $user_pass
            ],
            TRUE,
            TRUE
        ];

        $data['passwords_not_match'] = [
            [
                'id' => &$user_id,
                'user_password_new' => $user_pass,
                'user_password_again' => $user_pass.'_wrong'
            ],
            TRUE,
            FALSE
        ];
        
        $pass_short = substr($user_pass, 0, $this->CI->config->item('password_min_length')-1);
        $data['password_short'] = [
            [
                'id' => &$user_id,
                'user_password_new' => $pass_short,
                'user_password_again' => $pass_short
            ],
            TRUE,
            FALSE
        ];

        $repeat_count = ceil($this->CI->config->item('password_max_length') / strlen($user_pass)) + 1;
        $pass_long = str_repeat($user_pass, $repeat_count);
        $data['password_long'] = [
            [
                'id' => &$user_id,
                'user_password_new' => $pass_long,
                'user_password_again' => $pass_long
            ],
            TRUE,
            FALSE
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
        $this->resetInstance();
        $this->CI->load->model('user/user_model');
        $user_id =& self::_dummy_user_create();

        $data = [];

        $data['exists'] = [
            &$user_id,
            TRUE
        ];

        $bad_id = $this->CI->user_model->get_next_id()+100;
        $data['not_exist'] = [
            $bad_id,
            FALSE
        ];

        $data['zero'] = [
            0,
            TRUE
        ];

        return $data;
    }
    /**
     * Provider for `test_not_null_user_type`
     *
     * @return array
     */
    public function provider_not_null_user_type() : array
    {
        $this->resetInstance();
        $this->CI->load->model('user/user_type_model');
        $user_type = self::$_dummy_values['user']['type'];

        $data = [];

        $data['exists'] = [
            $user_type,
            TRUE
        ];

        $bad_type = $this->CI->user_type_model->get_next_id()+100;
        $data['not_exist'] = [
            $bad_type,
            FALSE
        ];

        return $data;
    }

    /**************
     * MISC METHODS
     **************/
    /**
     * Tricks the server to think there is an admin logged in
     *
     * @return void
     */
    private static function _login_as_admin()
    {
        $_SESSION['logged_in'] = TRUE;
        // We can't know the current configuration for admin access, so we max it
		$_SESSION['user_access'] = PHP_INT_MAX;
		$_SESSION['user_id'] = 0;
    }
    /**
     * Tricks the server to think there is nobody logged in
     *
     * @return void
     */
    private static function _logout()
    {
        $_SESSION = [];
        session_reset();
        session_unset();
    }

    /**
     * Gets the CI instance and creates it if required
     *
     * @return CI_Controller
     */
    private static function &_get_ci_instance()
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        return $CI;
    }

    /**
     * Creates a dummy user
     *
     * @return integer = ID of the dummy user
     */
    private static function &_dummy_user_create() : int
    {
        // Make sure CI is initialized
        $CI =& self::_get_ci_instance();
        $CI->load->model(['user/user_model', 'user/user_type_model']);

        // Only create user if it does not exist
        $user = $CI->user_model->with_deleted()->get(self::$_dummy_ids['user']);
        if (is_null($user)) {
            // Load auth config, for password encryption
            $CI->load->config('../modules/user/config/MY_user_config');
            $dummy_values =& self::$_dummy_values['user'];

            // While we're at it, make sure the user type exists
            if (is_null($CI->user_type_model->get($dummy_values['type']))) {
                $dummy_values['type'] = $CI->user_type_model->get_all()[0]->id;
            }

            // Create the user
            $user = array(
                'fk_user_type' => $dummy_values['type'],
                'username' => $dummy_values['name'],
                'password' => password_hash($dummy_values['pass'], $CI->config->item('password_hash_algorithm'))
            );

            self::$_dummy_ids['user'] = $CI->user_model->insert($user);
        }

        return self::$_dummy_ids['user'];
    }
    /**
     * Resets the current dummy user
     *
     * @return void
     */
    private static function _dummy_user_reset()
    {
        $CI =& self::_get_ci_instance();
        $CI->load->model('user/user_model');

        $user = $CI->user_model->with_deleted()->get(self::$_dummy_ids['user']);
        if (is_null($user)) {
            // Can't reset what does not exist
            self::_dummy_user_create();
        } else {
            $CI->load->config('../modules/user/config/MY_user_config');
            $dummy_values =& self::$_dummy_values['user'];

            // Create the user
            $user = array(
                'fk_user_type' => $dummy_values['type'],
                'username' => $dummy_values['name'],
                'password' => password_hash($dummy_values['pass'], $CI->config->item('password_hash_algorithm')),
                'archive' => 0
            );

            $CI->user_model->update(self::$_dummy_ids['user'], $user);
        }
    }
    /**
     * Deletes all possible dummy users.
     *
     * @return void
     */
    private static function _dummy_users_wipe()
    {
        $CI =& self::_get_ci_instance();
        $CI->load->model('user/user_model');

        $dummy_values = [
            self::$_dummy_values['user']['name'],
			self::$_dummy_values['user']['name_alt'],
			self::$_dummy_values['user']['name_unique'],
            ''
        ];

        foreach ($dummy_values as $value) {
            // Fetch users
            $users = $CI->user_model->with_deleted()->get_many_by(['username' => $value]);
            foreach ($users as $user) {
                // Delete user
                $CI->user_model->delete($user->id, TRUE);
            }
        }
    }

    /**
     * Saves most recent database errors.
     * 
     * Uses user_model and user_type_model
     *
     * @return void
     */
    private static function _db_errors_save()
    {
        $CI =& self::_get_ci_instance();
        $CI->load->model([
            'user/user_model',
            'user/user_type_model'
        ]);

        self::$_old_db_errors['user_model'] = $CI->user_model->_database->error();
        self::$_old_db_errors['user_type_model'] = $CI->user_type_model->_database->error();
    }
    /**
     * Compares the saved database errors to the most recent ones.
     *
     * @return boolean = FALSE if the error is the same as before
     */
    private static function _db_errors_diff() : bool
    {
        $CI =& self::_get_ci_instance();
        $CI->load->model([
            'user/user_model',
            'user/user_type_model'
        ]);

        return self::$_old_db_errors['user_model'] != $CI->user_model->_database->error() ||
            self::$_old_db_errors['user_type_model'] != $CI->user_type_model->_database->error();
    }
}
