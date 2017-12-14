<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Question Type model is used to get different types for each question.
 * question is used to get related question type for each question.
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class question_type_model extends MY_Model
{
    /* SET MY_Model VARIABLES */
    protected $_table = 't_question_type';
    protected $primary_key = 'ID';
    protected $protected_attributes = ['ID'];
    protected $has_many = ['questions' => ['primary_key' => 'FK_Question_Type',
                                           'model' => 'question_model']];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
}