<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Topic controller
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */

class Topic extends MY_Controller
{
    /* MY_Controller variables definition */
    protected $access_level = "2";

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('topic_model');
        $this->load->helper(array('form', 'url'));
    }

    /**
     * Display the topic view
     */
    public function index()
    {
        $output['topics'] = $this->topic_model->get_all();
        $this->display_view("topics/index", $output);
    }

    /**
     * @param int $id = id of the selected topic
     * @param int $error = Type of error :
     * 0 = no error
     * 1 = wrong identifiers
     * 2 = field(s) empty
     * Display the update topic view
     */
    public function update($id = 0, $error = 0){
        $outputs['error'] = $error;
        if($id != 0){
            $outputs["id"] = $id;
            $this->display_view("topic/update", $outputs);
        }else{
            $this->index();
        }
    }

    /**
     * Form validation to update a topic
     */
    public function form_update(){

        $id = $this->input->post('id');

        if($this->form_validation->run() == true){
            
            $this->index();
        }else{;
            $this->update($id, 1);
        }
    }

    /**
     * @param int $id = id of the selected questionnaire
     * Delete selected topic and redirect to topic list
     */
    public function delete($id = 0){
        if($id != 0){
            $this->topic_model->delete($id);
        }
        $this->index();
    }

    /**
     * not build
     * To add a new topic
     */
    public function add(){

    }
}