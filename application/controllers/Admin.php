<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Admin controller
 *
 * @author      Orif, section informatique (UlSi, ViDi, MeDa)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class Admin extends MY_Controller {
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->config->load('auth/MY_auth_config');
        $this->lang->load('auth/MY_auth');
        $this->access_level = $this->config->item('access_lvl_admin');

        $this->load->model(['user_model', 'user_type_model']);

        $this->load->library('form_validation');
        $this->form_validation->CI =& $this;
    }

    /**
     * Redirects to the user index
     */
    public function index(...$args) {
        $this->user_index(...$args);
    }

    /*************************
     * Users-related functions
     *************************/

    /**
     * Displays the list of users
     */
    public function user_index() {
        $output = array(
            'title' => $this->lang->line('title_users'),
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
            'title' => $this->lang->line('title_user_new'),
            'user' => $this->user_model->with_deleted()->get($userId) ?? NULL,
            'user_types' => $this->user_type_model->dropdown('name')
        );
        $this->display_view('admin/user/add', $output);
    }

    /**
     * Checks the user input and updates/inserts it into the database
     */
    public function user_form() {
        $userId = $this->input->post('id');

        $this->load->config('auth/MY_auth_config');

        $this->form_validation->set_rules('id', $this->lang->line('id'), 'callback_not_null_user');
        $this->form_validation->set_rules('user_name', $this->lang->line('user_name'), [
            'required', 'trim',
            'min_length['.$this->config->item('username_min_length').']',
            'max_length['.$this->config->item('username_max_length').']'
        ]);

        $this->form_validation->set_rules('user_usertype', $this->lang->line('user_usertype'), [
            'required','callback_cb_type_exists'
        ]);

        $this->form_validation->set_rules(
            'disactivate', $this->lang->line('btn_desactivate'),
            "callback_cb_not_inactive_user[{$userId}]",
            $this->lang->line('msg_err_user_already_inactivate')
        );
        $this->form_validation->set_rules(
            'reactivate', $this->lang->line('btn_reactivate'),
            "callback_cb_not_active_user[{$userId}]",
            $this->lang->line('msg_err_user_already_reactivate')
        );
        
        if($userId == 0) {
            $this->form_validation->set_rules('user_password', $this->lang->line('user_password'), [
                'required','trim',
                'min_length['.$this->config->item('password_min_length').']',
                'max_length['.$this->config->item('password_max_length').']'
            ]);
            $this->form_validation->set_rules('user_password_again', $this->lang->line('user_password_again'), [
                'required','trim','matches[user_password]',
                'min_length['.$this->config->item('password_min_length').']',
                'max_length['.$this->config->item('password_max_length').']'
            ]);
        }

        if($this->form_validation->run()) {
            $user = array(
                'username' => $this->input->post('user_name'),
                'fk_user_type' => $this->input->post('user_usertype')
            );
            if($userId > 0) {
                if(isset($_POST['save'])) {
                    $this->user_model->update($userId, $user);
                } elseif(isset($_POST['disactivate'])) {
                    $this->user_model->delete($userId);
                    $this->user_add($userId);
                    return;
                } elseif(isset($_POST['reactivate'])) {
                    $this->user_model->update($userId, array('archive' => 0));
                    $this->user_add($userId);
                    return;
                }
            } else {
                $password = $this->input->post('user_password');
                $user['Password'] = password_hash($password, PASSWORD_HASH_ALGORITHM);
                $this->user_model->insert($user);
            }
            redirect('admin/user_index');
        } else {
            $this->user_add($userId);
        }
    }

    /**
     * Deletes a user
     *
     * @param int $userId = The id of the user to delete
     * @param int $action = Display confirmation or deactivate
     */
    public function user_delete($userId, $action = 0) {
        $user = $this->user_model->with_deleted()->get($userId);
        if(is_null($user)) return redirect('admin/user_index');

        switch($action) {
            case 0: // Display confirmation
                $output = get_object_vars($user);
                $output['title'] = $this->lang->line('delete_user');
                $this->display_view('admin/user/delete', $output);
                break;
            case 1: // Deactivate user
                $this->user_model->delete($userId);
                redirect('admin/user_index');
            case 2: // Delete user
                $this->user_model->delete($userId, TRUE);
                redirect('Admin/user_index');
                break;
            default: // Do nothing
                redirect('admin/user_index');
        }
    }

    /**
     * Displays a page to change the user's password
     *
     * @param integer $userId = The id of the user to modify
     */
    public function user_change_password($userId, bool $preventRedirect = FALSE) {
        $user = $this->user_model->with_deleted()->get($userId);
        if(is_null($user) && !$preventRedirect || $userId == 0) redirect('admin/user_index');

        $output = array(
            'user' => $user,
            'title' => $this->lang->line('page_password_change')
        );

        $this->display_view('admin/user/change_password', $output);
    }

    /**
     * Changes a user's password
     */
    public function user_change_password_form() {
        $userId = $this->input->post('id');

        $this->load->config('auth/MY_auth_config');

        $this->form_validation->set_rules(
            'id', $this->lang->line('id'),
            'callback_not_null_user',
            $this->lang->line('msg_err_user_not_exist')
        );
        $this->form_validation->set_rules('user_password_new', $this->lang->line('field_new_password'), [
            'trim', 'required',
            'min_length['.$this->config->item('password_min_length').']',
            'max_length['.$this->config->item('password_max_length').']'
        ]);
        $this->form_validation->set_rules('user_password_again', $this->lang->line('field_password_confirm'), [
            'trim', 'required',
            'min_length['.$this->config->item('password_min_length').']',
            'max_length['.$this->config->item('password_max_length').']',
            'matches[user_password_new]'
        ]);

        if($this->form_validation->run()) {
            $new_password = $this->input->post('user_password_new');
            $new_password = password_hash($new_password, $this->config->item('password_hash_algorithm'));
            $this->user_model->update($userId, ['password' => $new_password]);
            redirect('admin/user_index');
        } else {
            $this->user_change_password($userId, TRUE);
        }
    }

    /**
     * Checks that the provided id is linked to an user
     *
     * @param int $id = ID of the user to check
     * @return bool
     */
    public function not_null_user($id){
        return $id == 0 || !is_null($this->user_model->with_deleted()->get($id));
    }

    /**
     * Checks that user is not active
     *
     * @param any $disactivate = Value of $_POST['disactivate']
     * @param int $userId = ID of the user
     * @return boolean
     */
    public function cb_not_inactive_user($disactivate, $userId) : bool
    {
        if(is_null($disactivate)) return TRUE;
        $user = $this->user_model->with_deleted()->get($userId);
        if(is_null($user)) return FALSE;
        return $user->archive == 0;
    }

    /**
     * Checks that user is already active
     *
     * @param any $reactivate = Value of $_POST['reactivate']
     * @param int $userId = ID of the user
     * @return boolean
     */
    public function cb_not_active_user($reactivate, $userId) : bool
    {
        if(is_null($reactivate)) return TRUE;
        $user = $this->user_model->with_deleted()->get($userId);
        if(is_null($user)) return FALSE;
        return $user->archive == 1;
    }

    public function cb_type_exists($user_type_id) : bool
    {
        return !is_null($this->user_type_model->get($user_type_id));
    }
}
