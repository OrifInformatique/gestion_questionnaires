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
        $this->load->model('question_model');
        $this->load->helper('url');
        $this->load->helper('form');

    }

    public function list_questions()
    {
        $output['questions'] = $this->question_model->get_all();
        $this->display_view('questions/list_questions', $output);
    }

    public function delete($id = 0)
    {
        if($id != 0)
        {
            $this->question_model->delete($id);

            redirect('index.php/Question/questions_list');
        }
    }

    public function detail($id = 0)
    {
        if($id != 0)
        {
            $output['question'] = $this->question_model->get(8);
            
            $this->load->view('common/header');
            $this->load->view('questions/detail', $output);
            $this->load->view('common/footer');
            
            //$this->question_model->update($id);

            //redirect('index.php/Question/questions_list');
        }
    }
}