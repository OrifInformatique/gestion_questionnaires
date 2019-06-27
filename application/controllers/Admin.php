<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Admin controller
 *
 * @author      Orif, section informatique (UlSi, ViDi, MeDa)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class Admin extends MY_Controller {
    /* MY_Controller variables definition */
    protected $access_level = ACCESS_LVL_ADMIN;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();

        $this->load->model(['user_model', 'user_type_model']);
        $this->load->helper(['url','form']);
        $this->load->library('form_validation');
    }

    /**
     * Redirects to the user index
     */
    public function index() {
        $this->user_index();
    }

    /*************************
     * Users-related functions
     *************************/

    /**
     * Displays the list of users
     */
    public function user_index() {
        $output = array(
            'users' => $this->user_model->with_deleted()->get_all(),
            'user_types' => $this->user_type_model->dropdown('name')
        );
        $this->display_view('admin/user/index', $output);
    }

    /**
     * Adds or modify a user depending on the id
     *
     * @param integer $userId = The id of the user to modify. Leave blank for a new user.
     */
    public function user_add($userId = 0) {
        $output = array(
            'user' => $this->user_model->get($userId) ?? NULL,
            'user_types' => $this->user_type_model->dropdown('name')
        );
        $this->display_view('admin/user/add', $output);
    }

    /**
     * Checks the user input and updates/inserts it into the database
     */
    public function user_form() {
        $userId = $this->input->post('id');

        $this->form_validation->set_rules('user_name', $this->lang->line('user_name'), [
            'required', 'trim',
            'min_length['.USERNAME_MIN_LENGTH.']',
            'max_length['.USERNAME_MAX_LENGTH.']'
        ]);
        $this->form_validation->set_rules('user_usertype', $this->lang->line('user_usertype'), 'required');
        if($userId === 0) {
            $this->form_validation->set_rules('user_password', $this->lang->line('user_password'), [
                'required','trim',
                'min_length['.PASSWORD_MIN_LENGTH.']',
                'max_length['.PASSWORD_MAX_LENGTH.']'
            ]);
            $this->form_validation->set_rules('user_password_again', $this->lang->line('user_password_again'), [
                'required','trim','matches[user_password]',
                'min_length['.PASSWORD_MIN_LENGTH.']',
                'max_length['.PASSWORD_MAX_LENGTH.']'
            ]);
        }

        if($this->form_validation->run()) {
            $user = array(
                'User' => $this->input->post('user_name'),
                'FK_User_Type' => $this->input->post('user_usertype')
            );
            if($userId > 0) {
                $this->user_model->update($userId, $user);
            } else {
                $password = $this->input->post('user_password');
                $user['Password'] = password_hash($password, PASSWORD_HASH_ALGORITHM);
                $this->user_model->insert($user);
            }
            redirect('Admin/user_index');
        } else {
            $this->user_add($userId);
        }
    }

    /**
     * Deletes a user
     *
     * @param int $userId = The id of the user to delete
     * @param int $action = Display confirmation or desactivate
     */
    public function user_delete($userId, $action = 0) {
        $user = $this->user_model->get($userId);
        if(is_null($user)) redirect('Admin/user_index');

        switch($action) {
            case 0:
                $output = get_object_vars($user);
                $this->display_view('admin/user/delete', $output);
                break;
            case 1:
                $this->user_model->delete($userId);
                redirect('Admin/user_index');
                break;
            default:
                redirect('Admin/user_index');
        }
        if(is_null($action)) {
            $output = get_object_vars($user);
            $this->display_view('admin/user/delete', $output);
        } else {
            $this->user_model->delete($userId);
            redirect('Admin/user_index');
        }
    }

    /**
     * Displays a page to change the user's password
     *
     * @param integer $userId = The id of the user to modify
     */
    public function user_change_password($userId) {
        $user = $this->user_model->get($userId);
        if(is_null($user)) redirect('Admin/user_index');

        $output = array(
            'user' => $user
        );

        $this->display_view('admin/user/change_password', $output);
    }

    /**
     * Changes a user's password
     */
    public function user_change_password_form() {
        $userId = $this->input->post('id');
        $force = (bool) $this->input->post('password_force');
        $user = $this->user_model->get($userId);

        if(!$force) {
            $this->form_validation->set_rules('user_password_old', $this->lang->line('field_old_password'), [
                'trim', 'required',
                'min_length['.PASSWORD_MIN_LENGTH.']',
                'max_length['.PASSWORD_MAX_LENGTH.']',
                'callback_old_password_check['.$user->User.']'
            ]);
        }
        $this->form_validation->set_rules('user_password_new', $this->lang->line('field_new_password'), [
            'trim', 'required',
            'min_length['.PASSWORD_MIN_LENGTH.']',
            'max_length['.PASSWORD_MAX_LENGTH.']'
        ]);
        $this->form_validation->set_rules('user_password_again', $this->lang->line('field_password_confirm'), [
            'trim', 'required',
            'min_length['.PASSWORD_MIN_LENGTH.']',
            'max_length['.PASSWORD_MAX_LENGTH.']',
            'matches[user_password_new]'
        ]);

        if($this->form_validation->run()) {
            $new_password = $this->input->post('field_new_password');
            $new_password = password_hash($new_password, PASSWORD_HASH_ALGORITHM);
            //$this->user_model->update($userId, ['Password' => $new_password]);
            //redirect('Admin/user_index');
        } else {
            $this->user_change_password($userId);
        }
    }

    /**
     * Callback method for change_password validation rule
     *
     * @param string $pwd = The previous password
     * @param string $user = The username
     * @return boolean = Whether or not the combination is correct
     */
    public function old_password_check($pwd, $user){
        return $this->user_model->check_password($user, $pwd);
    }
}