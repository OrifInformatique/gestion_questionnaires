<?php
require_once(__DIR__.'/../../../third_party/Test_Trait.php');
require_once(__DIR__.'/../../../modules/user/controllers/Auth.php');

/**
 * Class for tests for Auth controller
 * 
 * @author      Orif, section informatique (ViDi, MeSa, BuYa)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class Auth_Test extends TestCase {
    use Test_Trait;

    /**
     * List of dummy values
     *
     * @var array
     */
    private static $_dummy_values = [
        'user' => 'dummy_auth_user',
        'user_type' => 1,
        'password' => 'dummy_password',
        'password_alt' => 'password_dummy'
    ];
    /**
     * List of users created
     *
     * @var array
     */
    private static $dummy_ids = [];

    /*******************
     * START/END METHODS
     *******************/
    public function setUp()
    {
        $this->resetInstance();
        $this->CI->load->helper('url');
        $this->CI->load->model('user/user_model');
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
     * Test for `Auth::login`
     * 
     * @dataProvider provider_login
     *
     * @param array $post_params = Parameters to pass to $_POST
     * @param boolean $redirect_expected = Whether a redirect is expected
     * @param string $redirect_target = Where the redirect is expected to lead
     * @return void
     */
    public function test_login(array $post_params, bool $redirect_expected, ?string $redirect_target)
    {
        $this->_login_as(0);

        $this->_db_errors_save();

        $this->request('POST', 'user/auth/login', $post_params);

        if($redirect_expected) {
            $this->assertRedirect($redirect_target);
        } else {
            $this->assertTrue(!empty(validation_errors().$this->CI->session->flashdata('message-danger')));
        }

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Auth::logout`
     *
     * @return void
     */
    public function test_logout()
    {
        $this->request('GET', 'user/auth/logout');

        $this->assertNull($_SESSION['user_access'] ?? NULL);
    }
    /**
     * Test for `Auth::change_password`
     * 
     * @dataProvider provider_change_password
     *
     * @param array $user_info = Information about the user (id and username)
     * @param array $post_params = Parameters to pass to $_POST
     * @param array $results = Expected results:
     *      - `redirect`: If true, checks for the redirect
     *      - else `errors`: If true, checks for errors
     * @return void
     */
    public function test_change_password(array $user_info, array $post_params, array $results)
    {
        $this->_login_as(self::$_dummy_values['user_type']);

        $this->_db_errors_save();

        $_SESSION['username'] = $user_info['username'];
        $_SESSION['user_id'] = $user_info['id'];

        $this->request('POST', 'user/auth/change_password', $post_params);

        if($results['redirect']) {
            $this->assertRedirect(base_url());
        } elseif($results['errors']) {
            $this->assertNotEmpty(validation_errors());
        }

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Auth::change_password` while unlogged
     *
     * @return void
     */
    public function test_change_password_unlogged()
    {
        $this->_login_as(0);

        $this->request('POST', 'user/auth/change_password', []);

        $this->assertRedirect('user/auth/login');
    }
    /**
     * Test for `Auth::cb_old_password_check`
     * 
     * @dataProvider provider_cb_old_password_check
     *
     * @param string $username = Username to use
     * @param string $password = Password to use
     * @param boolean $expected = Expected result
     * @return void
     */
    public function test_cb_old_password_check(string $username, string $password, bool $expected)
    {
        $this->resetInstance();
        $controller = new Auth();
        $this->CI =& $controller;

        $this->_db_errors_save();

        $result = $this->CI->cb_old_password_check($password, $username);

        $this->assertSame($expected, $result);
        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }

    /***********
     * PROVIDERS
     ***********/
    /**
     * Provider for `test_login`
     *
     * @return array
     */
    public function provider_login() : array
    {
        $CI =& get_instance();
        $CI->load->helper('url');

        self::_dummy_user_create();
        $username = self::$_dummy_values['user'];
        $password = self::$_dummy_values['password'];

        $data = [];

        $data['no_error'] = [
            [
                'after_login_redirect' => base_url(),
                'btn_login' => 1,
                'username' => $username,
                'password' => $password
            ],
            TRUE,
            base_url()
        ];

        $data['wrong_password'] = [
            [
                'btn_login' => 1,
                'username' => $username,
                'password' => $password.'_wrong'
            ],
            FALSE,
            ''
        ];

        return $data;
    }
    /**
     * Provider for `test_change_password`
     *
     * @return array
     */
    public function provider_change_password() : array
    {
        $CI =& get_instance();

        $user_id = self::_dummy_user_create();
        $username = self::$_dummy_values['user'];

        $password = self::$_dummy_values['password'];
        $new_password = self::$_dummy_values['password_alt'];

        $data = [];

        $data['display'] = [
            [
                'id' => $user_id,
                'username' => $username
            ],
            [],
            [
                'redirect' => FALSE,
                'errors' => FALSE
            ]
        ];

        $data['no_error'] = [
            [
                'id' => $user_id,
                'username' => $username
            ],
            [
                'old_password' => $password,
                'new_password' => $new_password,
                'confirm_password' => $new_password,
                'btn_change_password' => 1
            ],
            [
                'redirect' => TRUE,
                'errors' => FALSE
            ]
        ];

        $data['wrong_password'] = [
            [
                'id' => $user_id,
                'username' => $username
            ],
            [
                'old_password' => $password.'_wrong',
                'new_password' => $new_password,
                'confirm_password' => $new_password,
                'btn_change_password' => 1
            ],
            [
                'redirect' => FALSE,
                'errors' => TRUE
            ]
        ];

        $data['password_not_match'] = [
            [
                'id' => $user_id,
                'username' => $username
            ],
            [
                'old_password' => $password,
                'new_password' => $new_password,
                'confirm_password' => $new_password.'_wrong',
                'btn_change_password' => 1
            ],
            [
                'redirect' => FALSE,
                'errors' => TRUE
            ]
        ];

        $repeat_count = ceil($CI->config->item('password_max_length') / strlen($new_password))+1;
        $long_password = str_repeat($new_password, $repeat_count);
        $data['long_password'] = [
            [
                'id' => $user_id,
                'username' => $username,
                'new_password' => $new_password
            ],
            [
                'old_password' => $password,
                'new_password' => $long_password,
                'confirm_password' => $long_password,
                'btn_change_password' => 1
            ],
            [
                'redirect' => FALSE,
                'errors' => TRUE
            ]
        ];

        $short_password = substr($new_password, 0, $CI->config->item('password_min_length')-1);
        $data['short_password'] = [
            [
                'id' => $user_id,
                'username' => $username,
                'new_password' => $new_password
            ],
            [
                'old_password' => $password,
                'new_password' => $short_password,
                'confirm_password' => $short_password,
                'btn_change_password' => 1
            ],
            [
                'redirect' => FALSE,
                'errors' => TRUE
            ]
        ];

        return $data;
    }
    /**
     * Provider for `test_cb_old_password_check`
     *
     * @return array
     */
    public function provider_cb_old_password_check() : array
    {
        self::_dummy_user_create();
        $username = self::$_dummy_values['user'];
        $password = self::$_dummy_values['password'];

        $data = [];

        $data['no_error'] = [
            $username,
            $password,
            TRUE
        ];

        $data['wrong_password'] = [
            $username,
            $password.'_wrong',
            FALSE
        ];

        return $data;
    }

    /**************
     * MISC METHODS
     **************/
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
        $CI->load->model('user/user_model');
        $CI->config->load(to_test_path('user/MY_user_config'));

        $dummy_user = array(
            'Username' => self::$_dummy_values['user'],
            'FK_User_Type' => self::$_dummy_values['user_type'],
            'Password' => password_hash(self::$_dummy_values['password'], $CI->config->item('password_hash_algorithm')),
            'Archive' => 0
        );

        return self::$dummy_ids[] = (int)$CI->user_model->insert($dummy_user);
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
        $CI->load->model('user/user_model');
        $CI->config->load(to_test_path('user/MY_user_config'));

        $dummy_user = array(
            'Username' => self::$_dummy_values['user'],
            'FK_User_Type' => self::$_dummy_values['user_type'],
            'Password' => password_hash(self::$_dummy_values['password'], $CI->config->item('password_hash_algorithm')),
            'Archive' => 0
        );
        $CI->user_model->update_many(self::$dummy_ids, $dummy_user);
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
        $CI->load->model('user/user_model');

        $users = $CI->user_model->with_deleted()->get_many_by(['username' => self::$_dummy_values['user']]);
        foreach($users as $user) {
            $CI->user_model->delete($user->id, TRUE);
        }

        self::$dummy_ids = [];
    }
}