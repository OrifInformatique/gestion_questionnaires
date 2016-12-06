<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Questionnaire controller
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */

class Questionnaire extends MY_Controller{

    /* MY_Controller variables definition */
    protected $access_level = "2";

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('questionnaire_model');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('title','Title','required');

    }

    /**
     * Display questionnaire list
     */
    public function index(){
        $outputs['questionnaires'] = $this->questionnaire_model->get_all();
        $this->display_view('questionnaires/index', $outputs);
    }

    /**
     * @param int $id = id of the selected questionnaire
     * @param int $error = Type of error :
     * 0 = no error
     * 1 = wrong identifiers
     * 2 = field(s) empty
     * Display the update questionnaire view
     */
    public function update($id = 0, $error = 0){
        $outputs['error'] = $error;
        if($id != 0){
            $outputs["id"] = $id;
            $this->display_view("questionnaires/update", $outputs);
        }else{
            $this->index();
        }
    }

    /**
     * Form validation to update a questionnaire
     */
    public function form_update(){

        $id = $this->input->post('id');
        $title = array('Questionnaire_Name' => $this->input->post('title'));

        if($this->form_validation->run() == true){

            $this->questionnaire_model->update($id, $title);
            $this->index();
        }else{;
            $this->update($id, true);
        }
    }

    /**
     * @param int $id = id of the selected questionnaire
     * Delete selected questionnaire and redirect to questionnaire list
     */
    public function delete($id = 0){
        if($id != 0){
            $this->questionnaire_model->delete($id);
        }
        $this->index();
    }

    /**
     * not build
     * To add a new questionnaire
     */
    public function add(){

    }
}