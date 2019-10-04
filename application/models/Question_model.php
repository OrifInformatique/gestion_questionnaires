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
                                                         'model' => 'question_questionnaire_model'],
                           'cloze_text' => ['primary_key' => 'FK_Question',
                                            'model' => 'cloze_text_model']];
    protected $soft_delete = TRUE;
    protected $soft_delete_key = 'Archive';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Select some random questions for the questionnaire
     * @param int $idTopic = Id of topic
     * @param int $nbQuestion = number of questions to return
     * @return array an array with the questions
     */
    public function getRNDQuestions($idTopic, $nbQuestion)
    {
        if($nbQuestion <= 0) return [];

        return $this->limit($nbQuestion)->
            as_array()->
            order_by('RAND()')->
            get_many_by("FK_Topic = {$idTopic} AND Archive = 0");
    }

    /**
     * Gets the amount of questions in a topic
     *
     * @param int $idTopic = ID of the topic
     * @return int = Amount of questions linked to the topic
     */
    public function getNbQuestionByTopic($idTopic)
    {
        return $this->count_by("FK_Topic = {$idTopic} AND Archive = 0");
    }
}