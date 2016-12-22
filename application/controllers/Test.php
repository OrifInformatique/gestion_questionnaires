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
        $this->load->model(array('question_model', 'question_type_model', 'topic_model', 'question_questionnaire_model'));
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
}