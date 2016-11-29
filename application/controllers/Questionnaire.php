<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Questionnaire controller
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */

class Questionnaire extends MY_Controller{

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
    public function questionnaires_list(){
        
        $outputs['questionnaires'] = $this->questionnaire_model->get_all();
        $outputs['onglet'] = 'Questionnaire';
        $this->display_view('questionnaires/questionnaires_list', $outputs);
    }

    /**
     * @param int $id = id of the selected questionnaire
     * @param bool $error = Optional error -> Not Build
     */
    public function update($id = 0, $error = false){
        $outputs['error'] = $error;
        if($id != 0){
            $outputs["id"] = $id;
            $outputs['onglet'] = 'Questionnaire';
            $this->display_view("questionnaires/update_questionnaire", $outputs);
        }else{
            $this->questionnaires_list();
        }
    }

    /**
     * Form validation to update a questionnaire
     */
    public function form_update(){

        $id = intval($this->input->post('id'));
        $title = array('Questionnaire_Name' => $this->input->post('title'));

        if($this->form_validation->run() == true){

            $this->questionnaire_model->update($id, $title);
            $this->questionnaires_list();
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
        $this->questionnaires_list();
    }

    /**
     * not build
     * To add a new questionnaire
     */
    public function add(){

    }
    
}