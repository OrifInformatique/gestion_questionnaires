<?php
/**
 * Created by PhpStorm.
 * User: UlSi
 * Date: 14.07.2016
 * Time: 09:18
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

    public function index(){
        $this->login();
    }

    public function login()
    {

        $username = $this->input->post('username');
        $password = $this->input->post('password');

        if ($this->form_validation->run() == true) {

        }

        $this->load->view("common/header");
        $this->load->view("login/login");
        $this->load->view("common/footer");
    }
}