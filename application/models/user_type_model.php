<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * User model is used to give access to the application.
 * user_type is used to give different access rights (defining an access level).
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class user_type_model extends MY_Model
{
    protected $_table = 't_user_type';
    protected $primary_key = 'id';
    protected $protected_attributes = ['id'];
    public $has_many = ['user'];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
}