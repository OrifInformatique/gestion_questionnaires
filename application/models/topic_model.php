<?php
/**
 * Created by PhpStorm.
 * User: UlSi
 * Date: 13.09.2016
 * Time: 11:57
 */
class topic_model extends MY_Model
{
    protected $_table = 't_topic';
    protected $primary_key = 'ID';
    protected $protected_attributes = 'ID';
    protected $belongs_to = ['Parent_Topic' => ['primary_key' => 'FK_Parent_Topic',
                                                'model' => 'topic']];
    protected $has_many = ['questions'];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
}