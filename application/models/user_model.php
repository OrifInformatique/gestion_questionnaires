<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * User is used to give access to the application.
 * User type is used to give different access rights (defining an access level).
 * 
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class user_model extends MY_Model
{
    /* MY_Model variables definition */
    protected $_table = 't_user';
    protected $primary_key = 'id';
    protected $protected_attributes = ['id'];
    public $belongs_to = ['user_type'];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Check username and password for login
     *
     * @access public
     * @param $username
     * @param $password
     * @return bool true on success, false on failure
     */
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