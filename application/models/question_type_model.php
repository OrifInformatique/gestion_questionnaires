<?php
/**
 * Created by PhpStorm.
 * User: UlSi
 * Date: 13.09.2016
 * Time: 11:57
 */
class question_type_model extends MY_Model
{
    protected $_table = 't_question_type';
    protected $primary_key = 'ID';
    protected $protected_attributes = 'ID';
    protected $has_many = ['questions'];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
}