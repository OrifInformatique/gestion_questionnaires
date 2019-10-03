<?php

class User_model_test extends TestCase {

    public function setUp()
    {
        $this->resetInstance();
        $this->CI->load->model('user_model');
    }

    /**
     * @dataProvider _provide
     */
    public function test_test($dummy_id, $existsfirst, $existslast)
    {
    }

    public function _provide()
    {
        $data = [];

        $dummyUser = array(
            'FK_User_Type' => 1,
            'User' => 'dummy_user_1',
            'Password' => password_hash('dummy_password_1', PASSWORD_HASH_ALGORITHM)
        );

        $data[] = [
            $this->CI->user_model->insert($dummyUser),
            TRUE,
            FALSE
        ];
    }
}