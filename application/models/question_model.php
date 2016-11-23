<?php
/**
 * Created by PhpStorm.
 * User: UlSi
 * Date: 13.09.2016
 * Time: 11:09
 */
class question_model extends MY_Model
{
    protected $_table = 't_question';
    protected $primary_key = 'ID';
    protected $protected_attributes = 'ID';
    protected $belongs_to = ['question_type' => ['primary_key' => 'FK_Question_Type',
                                                    'model' => 'question_type'],
                             'topic' => ['primary_key' => 'FK_Topic',
                                         'model' => 'topic']];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
}