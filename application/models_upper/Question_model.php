<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Question model is used to get an access to all questions.
 * question_type is used give the related type for each question.
 * topic is used to get the related topic for each question.
 * 
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class question_model extends MY_Model
{
    /* SET MY_Model VARIABLES */
    protected $_table = 't_question';
    protected $primary_key = 'ID';
    protected $protected_attributes = ['ID'];
    protected $belongs_to = ['question_type' => ['primary_key' => 'FK_Question_Type',
                                                 'model' => 'question_type_model'],
                             'topic' => ['primary_key' => 'FK_Topic',
                                         'model' => 'topic_model']];
    protected $has_many = ['question_questionnaires' => ['primary_key' => 'FK_Question',
                                                         'model' => 'question_questionnaire_model']];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Select some random questions for the questionnaire
     * @param $idTopic = Id of topic
     * @param $nbQuestion = number of questions to return
     * @return an array with the questions
     */
    public function getRNDQuestions($idTopic, $nbQuestion)
    {
        $query = $this->_database
                        ->select($this->primary_key)
                        ->limit($nbQuestion)
                        ->where("FK_Topic = $idTopic AND Archive = 0")
                        ->order_by("RAND()")
                        ->get($this->_table);

        return $query->result_array();
    }

    public function getNbQuestionByTopic($idTopic)
    {
        $query = $this->_database
                        ->select($this->primary_key)
                        ->where("FK_Topic = $idTopic AND Archive = 0")
                        ->get($this->_table);

        return $query->num_rows();
    }
}