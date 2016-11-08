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

    }
    
    public function questionnaires_list(){
        
        $outputs['questionnaires'] = $this->questionnaire_model->get_all();
        $this->display_view('questionnaires/questionnaires_list', $outputs);
    }
    
}