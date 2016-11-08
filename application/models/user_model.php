<?php
/**
 * Created by PhpStorm.
 * User: UlSi
 * Date: 11.10.2016
 * Time: 13:26
 */
class user_model extends MY_Model
{
    protected $_table = 't_user';
    protected $primary_key = 'id';
    protected $protected_attributes = ['id'];
    protected $belongs_to = ['user_type'];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function check_password($username, $password)
    {
        $user = $this->get_by('user', $username);

        if (!is_null($user) && password_verify($password, $user->password)) {
            return true;
        }else{
            return false;
        }
    }
}
