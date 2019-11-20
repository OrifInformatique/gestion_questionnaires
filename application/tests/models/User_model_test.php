<?php
require_once(__DIR__.'/../../third_party/Test_Trait.php');

/**
 * Class for tests for user model
 * 
 * @author      Orif, section informatique (ViDi, MeSa, BuYa)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class User_model_test extends TestCase {
    use Test_Trait;

    /**
     * List of dummy values
     *
     * @var array
     */
    private static $_dummy_values = [
        'user' => 'dummy_user',
        'user_type' => 1,
        'password' => 'dummy_password'
    ];
    /**
     * List of users created
     *
     * @var array
     */
    private static $user_ids = [];

    public function setUp()
    {
        $this->resetInstance();
        $this->CI->load->model('user_model');
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
     * Test for `user_model::check_password`
     * 
     * @dataProvider provider_check_password
     *
     * @param string $username = Username to use
     * @param string $password = Password to use
     * @param bool $shouldwork = Whether or not it should work
     * @return void
     */
    public function test_check_password($username, $password, $shouldwork)
    {
        $this->_db_errors_save();

        $this->assertEquals(
            $shouldwork,
            $this->CI->user_model->check_password($username, $password)
        );

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }

    /****************
     * DATA PROVIDERS
     ****************/
    /**
     * Data provider for `test_check_password`
     *
     * @return array
     */
    public function provider_check_password() : array
    {
        // Load the model
        $this->resetInstance();
        $this->CI->load->model('user_model');

        // Create a basic dummy user
        $username = self::$_dummy_values['user'];
        $password = self::$_dummy_values['password'];

        $dummy_user = array(
            'User' => $username,
            'FK_User_Type' => self::$_dummy_values['user_type'],
            'Password' => password_hash($password, PASSWORD_HASH_ALGORITHM)
        );
        self::$user_ids[] = $this->CI->user_model->insert($dummy_user);

        // Prepare some data for testing
        $data = [];

        $data['correct_password'] = [
            $username,
            $password,
            TRUE
        ];

        $data['bad_password'] = [
            $username,
            $password.'_wrong',
            FALSE
        ];

        $data['not_exist'] = [
            (function() use ($username) {
                $loop = 0;
                while(!is_null($this->CI->user_model->get_by('User', $username.$loop))) {
                    $loop++;
                }
                return $username.$loop;
            })(),
            $password,
            FALSE
        ];

        return $data;
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

        $users = $CI->user_model->with_deleted()->get_many_by(['User' => self::$_dummy_values['user']]);
        foreach($users as $user) {
            $CI->user_model->delete($user->ID, TRUE);
        }
        $users = $CI->user_model->with_deleted()->get_many_by(['User' => 'user_dummy']);
        foreach($users as $user) {
            $CI->user_model->delete($user->ID, TRUE);
        }

        self::$user_ids['users'] = [];
    }
}