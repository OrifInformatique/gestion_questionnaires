<?php
/**
 * Created by PhpStorm.
 * User: UlSi
 * Date: 08.11.2016
 * Time: 15:47
 */
class questionnaire_model extends MY_Model
{
    protected $_table = 't_questionnaire';
    protected $primary_key = 'ID';
    protected $protected_attributes = ['ID'];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
}