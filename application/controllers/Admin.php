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
                if(isset($_POST['save'])) {
                    $this->user_model->update($userId, $user);
                } elseif(isset($_POST['disactivate'])) {
                    $this->user_model->delete($userId);
                    $this->user_add($userId);
                    return;
                } elseif(isset($_POST['reactivate'])) {
                    $this->user_model->update($userId, array('Archive' => 0));
                    $this->user_add($userId);
                    return;
                }
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
     * @param int $action = Display confirmation or deactivate
     */
    public function user_delete($userId, $action = 0) {
        $user = $this->user_model->with_deleted()->get($userId);
        if(is_null($user)) redirect('Admin/user_index');

        switch($action) {
            case 0: // Display confirmation
                $output = get_object_vars($user);
                $this->display_view('admin/user/delete', $output);
                break;
            case 1: // Deactivate user
                $this->user_model->delete($userId);
                redirect('Admin/user_index');
                break;
            case 2: // Delete user
                $this->user_model->hard_delete($userId);
                redirect('Admin/user_index');
                break;
            default: // Do nothing
                redirect('Admin/user_index');
        }
    }

    /**
     * Displays a page to change the user's password
     *
     * @param integer $userId = The id of the user to modify
     */
    public function user_change_password($userId) {
        $user = $this->user_model->with_deleted()->get($userId);
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
        $user = $this->user_model->get($userId);

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
            $new_password = $this->input->post('user_password_new');
            $new_password = password_hash($new_password, PASSWORD_HASH_ALGORITHM);
            $this->user_model->update($userId, ['Password' => $new_password]);
            redirect('Admin/user_index');
        } else {
            $this->user_change_password($userId);
        }
    }
}