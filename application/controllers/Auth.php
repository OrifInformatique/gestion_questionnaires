<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Authentication controller
 * 
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */

class Auth extends MY_Controller {
    /* MY_Controller variables definition */
    protected $access_level = "*";

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('user_model');
        $this->load->model('user_type_model');
        $this->load->helper(array('form', 'url'));
    }

    /**
     * Display the login page
     *
     * @param int $error = Type of error :  0 = no error
     *                                      1 = wrong identifiers
     *                                      2 = field(s) empty
     */
    public function index($error = 0){
        $outputs['error'] = $error;
        $outputs['title'] = $this->lang->line('page_login');
        $this->display_view("login/login", $outputs);
    }

    /**
     * Validate the login informaions and create session variables.
     * If necessary, redirect to the login page.
     */
    public function login(){
        $this->form_validation->set_rules('username', strtolower($this->lang->line('field_username')),
                                          'trim|required|min_length['.USERNAME_MIN_LENGTH.']');
        $this->form_validation->set_rules('password', strtolower($this->lang->line('field_password')),
                                          'trim|required|min_length['.PASSWORD_MIN_LENGTH.']');

        $username = $this->input->post('username');
        $password = $this->input->post('password');

        if ($this->form_validation->run() == true) {

            if($this->user_model->check_password($username, $password)) {
                $user = $this->user_model->with('user_type')->get_by('User', $username);

                $this->session->user_id = $user->ID;
                $this->session->username = $user->User;
                $this->session->user_access = $user->user_type->access_level;
                $this->session->logged_in = true;

                redirect('Home');
            }
            else {
                // Login informations error : display login page again, with error message
                $this->index(1);
            }

        }
        else {
            // Validation error : display login page again, with error messages
            $this->index(2);
        }
    }

    /**
     * Destroy the session and redirect to login page
     */
    public function unlog(){
        session_destroy();
        redirect('Auth');
    }
}