<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Authentication controller
 * 
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */

class Auth extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('user_model');
        $this->load->model('user_type_model');
        $this->load->helper(array('form', 'url'));

        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');
    }

    /**
     * @param int $error = Type of error :
     * 0 = no error
     * 1 = wrong identifiers
     * 2 = field(s) empty
     * Display the login view
     */
    public function index($error = 0){

        $output['error'] = $error;
        $this->display_view("login/login", $output);
    }

    /**
     * Form validation to login
     * Redirect if necessary on the login page or on the main site
     */
    public function login(){
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        if ($this->form_validation->run() == true) {

            if($this->user_model->check_password($username, $password))
            {
                $user = $this->user_model->get_by('user', $username);
                $this->session->user_id = $user->id;
                $this->session->username = $user->user;
                $this->session->user_access = 
                    $this->user_type_model->get($user->user_type)->access_level;
                $this->session->logged_in = true;
                switch($this->session->user_access){
                    case 1 :
                        $this->unlog();
                        break;
                    case 2 :
                        redirect('Questionnaire/questionnaires_list');
                        break;
                }
                redirect('Questionnaire/questionnaires_list');
            }

            if(!isset($_SESSION['logged_in'])){
                $this->index(1);
            }

        }else{
            $this->index(2);
        }
    }

    /**
     * Destroy the session and redirect to login page
     */
    public function unlog(){
        session_destroy();
        redirect('Auth/index');
    }

}