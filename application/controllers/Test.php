<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Test controller used to test some parts of the applications.
 * CAN BE REMOVED WHEN APPLICATION IS FINISHED
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */

class Test extends MY_Controller
{
    /* MY_Controller variables definition */
    protected $access_level = "*";


    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('question_model', 'question_type_model', 'topic_model',
                                 'questionnaire_model', 'question_questionnaire_model',
                                 'user_model', 'user_type_model'));
    }

    /**
     * Test question_model
     */
    public function question()
    {
        $question = $this->question_model->get(2);
        var_dump($question);

        $question = $this->question_model->with('topic')->get(2);
        var_dump($question);

        $question = $this->question_model->with_all()->get(2);
        var_dump($question);

        $rndQuestions = $this->question_model->getRNDQuestions(1, 2);
        var_dump($rndQuestions);
    }

    /**
     * Test question_questionnaire_model
     */
    public function question_questionnaire()
    {
        $question_questionnaire = $this->question_questionnaire_model->get(2);
        var_dump($question_questionnaire);

        $question_questionnaire = $this->question_questionnaire_model->with('question')->get(2);
        var_dump($question_questionnaire);

        $question_questionnaire = $this->question_questionnaire_model->with_all()->get(2);
        var_dump($question_questionnaire);

        $question_questionnaire = $this->question_questionnaire_model->get_all();
        var_dump($question_questionnaire);
    }

    /**
     * Test question_type_model
     */
    public function question_type()
    {
        $question_type = $this->question_type_model->get(2);
        var_dump($question_type);

        $question_type = $this->question_type_model->with('questions')->get(2);
        var_dump($question_type);

        $question_type = $this->question_type_model->get_all();
        var_dump($question_type);
    }

    /**
     * Test questionnaire_model
     */
    public function questionnaire()
    {
        $questionnaire = $this->questionnaire_model->get(3);
        var_dump($questionnaire);

        $questionnaire = $this->questionnaire_model->with('question_questionnaires')->get(3);
        var_dump($questionnaire);

        $questionnaire = $this->questionnaire_model->get_all();
        var_dump($questionnaire);
    }

    /**
     * Test topic_model
     */
    public function topic()
    {
        $topic = $this->topic_model->get(1);
        var_dump($topic);

        $topic = $this->topic_model->with('questions')->get(1);
        var_dump($topic);

        $topic = $this->topic_model->with_all()->get(2);
        var_dump($topic);

        $topic = $this->topic_model->with_all()->get(3);
        var_dump($topic);

        $topic = $this->topic_model->get_all();
        var_dump($topic);
    }

    /**
     * Test user_model
     */
    public function user()
    {
        $user = $this->user_model->get(1);
        var_dump($user);

        $user = $this->user_model->with('user_type')->get(1);
        var_dump($user);

        $user = $this->user_model->get_all();
        var_dump($user);

        $user = $this->user_model->with('user_type')->get_by('User', 'user1');
        var_dump($user);
    }
}