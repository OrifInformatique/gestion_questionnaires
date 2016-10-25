<?php
/**
 * Created by PhpStorm.
 * User: UlSi
 * Date: 13.09.2016
 * Time: 11:57
 */
class fk_topic_model extends MY_Model
{
    protected $_table = 't_topic';
    protected $primary_key = 'ID';
    protected $protected_attributes = 'ID';
    protected $belong_to = ['FK_Parent_Topic'];
    protected $has_many = ['FK_Question'];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
}