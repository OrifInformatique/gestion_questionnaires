<?php
/**
 * Created by PhpStorm.
 * User: UlSi
 * Date: 13.09.2016
 * Time: 14:37
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

    public function list_questions()
    {
        $output['questions'] = $this->question_model->with_all()->get_all();
        $output['topics'] = $this->topic_model->get_all();
        $this->display_view('questions/list_questions', $output);
    }

    public function delete($id = 0)
    {
        if($id != 0)
        {
            $this->question_model->delete($id);

            $this->list_questions();
        }
    }

    public function form_update()
    {
        
    }

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