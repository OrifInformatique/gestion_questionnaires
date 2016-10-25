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
        $this->load->model('login_model');
        $this->load->helper(array('form', 'url'));

        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');
    }

    public function index($error = false){

        $output['error'] = $error;
        $this->load->view("common/header");
        $this->load->view("login/login", $output);
        $this->load->view("common/footer");
    }

    public function login(){
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        if ($this->form_validation->run() == true) {

            $login = $this->login_model->get_all();

            foreach ($login as $log)
            {
                if(password_verify($password, $log->Password) && $username == $log->User)
                {
                    $this->session->logged_in = true;
                    redirect('Question/acceuil');
                }
            }
            if(!isset($_SESSION['logged_in'])){
                $this->index(true);
            }

        }else{
            $this->index();
        }
    }

    public function unlog(){
        session_destroy();
        redirect('Auth/index');
    }

}