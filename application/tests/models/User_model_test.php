<?php

class User_model_test extends TestCase {
    /**
     * List of user ids to remove afterward
     *
     * @var array
     */
    private static $user_ids = [];

    public function setUp()
    {
        $this->resetInstance();
        $this->CI->load->model('user_model');
    }
    public function tearDown()
    {
        parent::tearDown();

        $this->resetInstance();
        $this->CI->load->model('user_model');

        foreach(self::$user_ids as $user_id) {
            $this->CI->user_model->hard_delete($user_id);
            unset(self::$user_ids[array_search($user_id, self::$user_ids)]);
        }
    }

    /*******
     * TESTS
     *******/

    /**
     * Test for `user_model::hard_delete`
     * 
     * @dataProvider provider_hard_delete
     * 
     * @param int $user_id = ID of the user to test
     * @param bool $deleted_pre = Whether or not it exists before hard_delete
     * @param bool $deleted_past = Whether or not it exists after hard_delete
     */
    public function test_hard_delete($user_id, $deleted_pre, $deleted_past)
    {
        $user = $this->CI->user_model->get($user_id);

        $this->assertEquals($deleted_pre, is_null($user));

        $this->CI->user_model->hard_delete($user_id);

        $user = $this->CI->user_model->get($user_id);

        $this->assertEquals($deleted_past, is_null($user));
    }

    /**
     * Test for `user_model::check_password`
     * 
     * @dataProvider provider_check_password
     *
     * @param string $username = username
     * @param string $password = password
     * @param bool $shouldwork = Whether or not it should work
     * @return void
     */
    public function test_check_password($username, $password, $shouldwork)
    {
        $this->assertEquals(
            $shouldwork,
            $this->CI->user_model->check_password($username, $password)
        );
    }

    /****************
     * DATA PROVIDERS
     ****************/

    /**
     * Data provider for `test_hard_delete`
     *
     * @return array
     */
    public function provider_hard_delete() : array
    {
        // Load the model
        $this->resetInstance();
        $this->CI->load->model('user_model');

        // Create a basic dummy user
        $dummy_user = array(
            'User' => 'dummy_user',
            'FK_User_Type' => 1,
            'Password' => password_hash('dummy_password', PASSWORD_HASH_ALGORITHM)
        );

        // Prepare some data for testing
        $data = [];

        $user_id = $this->CI->user_model->insert($dummy_user);
        $data['existing_user'] = [
            $user_id,
            FALSE,
            TRUE
        ];
        self::$user_ids[] = $user_id;

        $data['user_not_exist'] = [
            $this->CI->user_model->get_next_id()+1,
            TRUE,
            TRUE
        ];

        return $data;
    }

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
        $password = 'dummy_password';
        $username = 'dummy_user';

        $dummy_user = array(
            'User' => $username,
            'FK_User_Type' => 1,
            'Password' => password_hash($password, PASSWORD_HASH_ALGORITHM)
        );
        $this->CI->user_model->insert($dummy_user);

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

        return $data;
    }
}