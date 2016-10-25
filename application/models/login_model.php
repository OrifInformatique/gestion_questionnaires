<?php
/**
 * Created by PhpStorm.
 * User: UlSi
 * Date: 11.10.2016
 * Time: 13:26
 */
class login_model extends MY_Model
{
    protected $_table = 't_user';
    protected $primary_key = 'ID';
    protected $protected_attributes = 'ID';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
}