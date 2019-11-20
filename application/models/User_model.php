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
    /* SET MY_Model VARIABLES */
    protected $_table = 't_user';
    protected $primary_key = 'ID';
    protected $protected_attributes = ['ID'];
    protected $belongs_to = ['user_type'=> ['primary_key' => 'FK_User_Type',
                                            'model' => 'user_type_model']];
    protected $soft_delete = TRUE;
    protected $soft_delete_key = 'Archive';

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
        $user = $this->get_by('User', $username);

        if (!is_null($user) && $user->Archive == false) {
            // A corresponding active user has been found
            // Check password
            return password_verify($password, $user->Password);
        }
        else {
            // No corresponding active user
            return false;
        }
    }

    /**
     * Deletes the item permanently
     * 
     * @deprecated Use `user_model::delete($id, TRUE)` instead
     *
     * @param integer $id = The id of the item to delete
     * @return integer =
     */
    public function hard_delete($id) {
        trigger_error('Usage of deprecated hard_delete', E_USER_DEPRECATED);
        return $this->delete($id, TRUE);
    }
}