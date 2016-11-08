<?php
/**
 * Created by PhpStorm.
 * User: UlSi
 * Date: 01.11.2016
 * Time: 14:20
 */

class user_type_model extends MY_Model
{
    protected $_table = 't_user';
    protected $primary_key = 'id';
    protected $protected_attributes = ['id'];
    protected $has_many = ['user_type'];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
}