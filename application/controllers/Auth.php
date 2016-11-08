<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: UlSi
 * Date: 14.07.2016
 * Time: 09:18
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

    public function index($error = 0){

        $output['error'] = $error;
        echo '7TQAKVm';
        $this->display_view("login/login", $output);
    }

    public function login(){
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        if ($this->form_validation->run() == true) {

            if($this->user_model->check_password($username, $password))
            {
                $user = $this->user_model->get_by('user', $username);;
                $this->session->user_id = $user->id;
                $this->session->username = $user->user;
                $this->session->user_access = (int)$user->user_type;
                $this->session->logged_in = true;
                redirect('Questionnaire/questionnaires_list');
            }

            if(!isset($_SESSION['logged_in'])){
                $this->index(1);
            }

        }else{
            $this->index(2);
        }
    }

    public function unlog(){
        session_destroy();
        redirect('Auth/index');
    }

}