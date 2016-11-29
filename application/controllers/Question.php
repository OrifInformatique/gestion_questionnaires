<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Question controller 
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */

class Question extends MY_Controller
{
    /* MY_Controller variables definition */
    protected $access_level = "2";


    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('question_model', 'question_type_model', 'topic_model'));
        $this->load->helper('url');
        $this->load->helper('form');

    }

    /**
     * Display question list
     */
    public function list_questions()
    {
        $output['questions'] = $this->question_model->with_all()->get_all();
        $output['topics'] = $this->topic_model->get_all();
        $this->display_view('questions/list_questions', $output);
    }

    /**
     * @param int $id = id of the question
     * Delete selected question
     */
    public function delete($id = 0)
    {
        if($id != 0)
        {
            $this->question_model->delete($id);

            $this->list_questions();
        }
    }

    /**
     * Form validation to update question
     */
    public function form_update()
    {
        
    }

    /**
     * @param int $id = selected question id
     * @param bool $error = Optional error
     * Display the detailed view to update a question
     */
    public function update($id = 0, $error = false)
    {
        $output['error'] = $error;
        $output['id'] = $id;

        if($id != 0)
        {
            $output['question'] = $this->question_model->get_by('ID = ' . $id);
            $output['question_types'] = $this->question_type_model->get_all();
            
            $this->display_view('questions/update_question', $output);
        }
        else{

        }
    }
}