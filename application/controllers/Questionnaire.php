<?php
/**
 * Created by PhpStorm.
 * User: UlSi
 * Date: 08.11.2016
 * Time: 11:01
 */

class Questionnaire extends MY_Controller{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('questionnaire_model');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('title','Title','required');

    }
    
    public function questionnaires_list(){
        
        $outputs['questionnaires'] = $this->questionnaire_model->get_all();
        $outputs['onglet'] = 'Questionnaire';
        $this->display_view('questionnaires/questionnaires_list', $outputs);
    }

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

    public function delete($id = 0){
        if($id != 0){
            $this->questionnaire_model->delete($id);
        }
        $this->questionnaires_list();
    }

    public function add(){

    }
    
}