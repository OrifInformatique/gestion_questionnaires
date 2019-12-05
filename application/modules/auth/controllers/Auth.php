<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Authentication System
 *
 * @author      Orif (ViDi)
 * @link        https://github.com/OrifInformatique
 * @copyright   Copyright (c), Orif (https://www.orif.ch)
 * @version     2.0
 */
class Auth extends MY_Controller
{
    /* MY_Controller variables definition */
    protected $access_level = "*";

    /**
    * Constructor
    */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('user_type_model');
    }

    /**
     * Login and create session variables
     */
    public function login ()
    {
        // Store the redirection URL in a session variable
        if (!is_null($this->input->post('after_login_redirect'))) {
            $_SESSION['after_login_redirect'] = $this->input->post('after_login_redirect');
        }
        // If no redirection URL is provided or the redirection URL is the
        // login form, redirect to site's root after login
        if (!isset($_SESSION['after_login_redirect'])
                || $_SESSION['after_login_redirect'] == current_url()) {

            $_SESSION['after_login_redirect'] = base_url();
        }

        // Check if the form has been submitted, else just display the form
        if (!is_null($this->input->post('btn_login'))) {
            // Define fields validation rules
            $validation_rules = array(
                array(
                    'field' => 'username',
                    'label' => 'lang:field_username',
                    'rules' => 'trim|required|'
                             . 'min_length['.$this->config->item('username_min_length').']|'
                             . 'max_length['.$this->config->item('username_max_length').']'
                ),
                array(
                    'field' => 'password',
                    'label' => 'lang:field_password',
                    'rules' => 'trim|required|'
                             . 'min_length['.$this->config->item('password_min_length').']|'
                             . 'max_length['.$this->config->item('password_max_length').']'
                )
            );
            $this->form_validation->set_rules($validation_rules);

            // Check fields validation rules
            if ($this->form_validation->run() == true) {
                $username = $this->input->post('username');
                $password = $this->input->post('password');
                
                if ($this->user_model->check_password($username, $password)) {
                    // Login success
                    $user = $this->user_model->with('user_type')
                                             ->get_by('username', $username);

                    // Set session variables
                    $_SESSION['user_id'] = (int)$user->id;
                    $_SESSION['username'] = (string)$user->username;
                    $_SESSION['user_access'] = (int)$user->user_type->access_level;
                    $_SESSION['logged_in'] = (bool)true;

                    // Send the user to the redirection URL
                    redirect($_SESSION['after_login_redirect']);

                } else {
                    // Login failed
                    $this->session->set_flashdata('message-danger', lang('msg_err_invalid_password'));
                }               
            }
        }

        // Display login page
        $output = array('title' => lang('page_login'));
        $this->display_view('auth/login_form', $output);
    }

    /**
     * Logout and destroy session
     */
    public function logout()
    {
        // Restart session with empty parameters
        $_SESSION = [];
        session_reset();
        session_unset();

        redirect(base_url());
    }
}
