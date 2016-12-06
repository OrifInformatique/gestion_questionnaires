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
    public function index()
    {
        $output['questions'] = $this->question_model->with_all()->get_all();
        $output['topics'] = $this->topic_model->get_all();
        $this->display_view('questions/index', $output);
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

            $this->index();
        }
    }

    /**
     * Form validation to update question
     */
    public function form_update()
    {
        $id = $this->input->post('id');

        if($this->form_validation->run() == true){

            $this->index();
        }else{;
            $this->update($id, 1);
        }
    }

    /**
     * @param int $id = selected question id
     * @param int $error = Type of error :
     * 0 = no error
     * 1 = wrong identifiers
     * 2 = field(s) empty
     * Display the detailed view to update a question
     */
    public function update($id = 0, $error = 0)
    {
        $output['error'] = $error;
        $output['id'] = $id;

        if($id != 0)
        {
            $output['question'] = $this->question_model->get_by('ID = ' . $id);
            $output['question_types'] = $this->question_type_model->get_all();
            
            $this->display_view('questions/update', $output);
        }
        else{

        }
    }

    /**
     * Display form to add a question
     * Not build
     */
    public function add()
    {

    }
}