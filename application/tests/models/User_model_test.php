<?php

class User_model_test extends TestCase {
    public function setUp()
    {
        $this->resetInstance();
        $this->CI->load->model('user_model');
    }

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

    /****************
     * DATA PROVIDERS
     ****************/

    /**
     * Data provider for test_hard_delete
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

        $data['existing_user'] = [
            $this->CI->user_model->insert($dummy_user),
            FALSE,
            TRUE
        ];

        $data['user_not_exist'] = [
            $this->CI->user_model->get_next_id()+1,
            TRUE,
            TRUE
        ];

        return $data;
    }
}