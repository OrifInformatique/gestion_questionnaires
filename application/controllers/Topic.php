<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Topic controller
 *
 * @author      Orif, section informatique (UlSi, ViDi, BuYa)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */

class Topic extends MY_Controller
{
    /* MY_Controller variables definition */
    protected $access_level = ACCESS_LVL_ADMIN;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('topic_model');
        $this->load->helper(array('form', 'url', 'date'));
    }

    /**
     * Display the topic view
     */
    public function index($error = "")
    {
        $output = array(
            'modules' => $this->topic_model->get_many_by('FK_Parent_Topic IS NULL'),
            'topics' => $this->topic_model->get_many_by('FK_Parent_Topic IS NOT NULL'),
            'error' => $error
        );

        $this->display_view("topics/index", $output);
    }

    /**
     * @param int $id = id of the selected topic
     * @param int $error = Type of error :
     * 0 = no error
     * 1 = wrong identifiers
     * 2 = field(s) empty
     * Display the update topic view
     */
    public function update_topic($id = 0, $error = 0){
        if($id != 0){
            $topic = $this->topic_model->get($id);
            if (!is_null($topic)){
                $outputs = array(
                    'error' => $error,
                    'id' => $id,
                    'title' => $topic->Topic
                );
                $this->display_view("topics/update", $outputs);
            } else show_error($this->lang->line('topic_error_404_message'), 404, $this->lang->line('topic_error_404_heading'));
        } else $this->index();
    }

    /**
     * Form validation to update a topic
     */
    public function form_update_topic(){
        $this->form_validation->set_rules('title', 'Title', 'required');

        $id = $this->input->post('id');
        $title = array('Topic' => $this->input->post('title'));
        if($this->form_validation->run() == true){
            $this->topic_model->update($id, $title);
            $this->index();
        }else{
            $this->update_topic($id, 1);
        }
    }

    /**
     * @param int $id = id of the selected questionnaire
     * Delete selected topic and redirect to topic list
     */
    public function delete_topic($id = 0, $action = NULL){

        if($id != 0){

            $topic = $this->topic_model->with("questions")->with("child_topics")->get($id);

            if (is_null($action)) {
                if ((count($topic->child_topics) > 0) OR (count($topic->questions) > 0)) {
                    $this->index($this->lang->line('del_topic_form_err'));
                } else {
                    $output = get_object_vars($this->topic_model->get($id));
                    $output["topics"] = $this->topic_model->get_all();
                    $this->display_view("topics/delete", $output);
                }
            } else {
                $this->topic_model->delete($id);
                $this->index();
            }
        } else {
            $this->index();
        }
    }

    /**
     * not build
     * To add a new topic
     */
    public function add_topic($preselect_module = NULL, $error = NULL){
        $output = array(
            'topics' => $this->topic_model->get_all(),
            'error' => ($error == NULL ? NULL : true),
            'selected_module' => $preselect_module
        );
        $this->display_view("topics/add", $output);
    }

    /**
     * Form validation to update a topic (parent topic)
     */
    public function form_add_topic(){
        define('TIMEZONE', 'Europe/Zurich');
        date_default_timezone_set(TIMEZONE);
        $datestring = '%Y-%m-%d %h:%i:%s';
        $time = time();

        $this->form_validation->set_rules('title', $this->lang->line('topic_title_error'), 'required');
        $this->form_validation->set_rules('module_selected', $this->lang->line('topic_module_selected_error'), 'required|is_natural_no_zero');

        $title = array(
            'Topic' => $this->input->post('title'),
            'FK_Parent_Topic' => $this->input->post('module_selected'),
            'Creation_Date' => mdate($datestring, $time));
        if($this->form_validation->run() == true){
            $this->topic_model->insert($title);
            $this->index();
        }else{;
            $this->add_topic(NULL, 1);
        }
    }

    /**
     * @param int $id = id of the selected module
     * @param int $error = Type of error :
     * 0 = no error
     * 1 = wrong identifiers
     * 2 = field(s) empty
     * Display the update module view
     */
    public function update_module($id = 0, $error = 0){
        if($id != 0){
            $topic = $this->topic_model->get($id);
            if (!is_null($topic)){
                $outputs = array(
                    'error' => $error,
                    'id' => $id,
                    'title' => $topic->Topic,
                    'action' => 'update'
                );

                $this->display_view("modules/update", $outputs);
            }
            else
                show_error($this->lang->line('module_error_404_message'), 404, $this->lang->line('module_error_404_heading'));

        }else{
            $this->index();
        }

    }

    /**
     * Form validate to update or add a module (parent topic)
     */
    public function form_validate_module(){

        define('TIMEZONE', 'Europe/Zurich');
        date_default_timezone_set(TIMEZONE);
        $datestring = '%Y-%m-%d %h:%i:%s';
        $time = time();

        $this->form_validation->set_rules('title', 'Title', 'required');

        $id = $this->input->post('id');
        $action = $this->input->post('action');
        if($this->form_validation->run() == true){
            if ($action == "update") {
                $title = array('Topic' => $this->input->post('title'));
                $this->topic_model->update($id, $title);
            } else {
                $title = array('Topic' => $this->input->post('title'),
                               'Creation_Date' => mdate($datestring, $time));
                $this->topic_model->insert($title);
            }
            $this->index();
        } else {
            if ($action == "update") {
                $this->update_module($id, 1);
            } else {
                $this->add_module(1);
            }
        }
    }

    /**
     * Delete selected module (parent topic) and redirect to module list
     *
     * @param int $id = id of the selected questionnaire
     * @param any $action = Set to not null to delete the module
     */
    public function delete_module($id = 0, $action = NULL){
        if ($id != 0) {
            if (is_null($action)) {
                $topic = $this->topic_model->with("questions")->with("child_topics")->get($id);
                if ((count($topic->child_topics) > 0) OR (count($topic->questions) > 0)) {
                    $this->index($this->lang->line('del_module_form_err'));
                } else {
                    $output = get_object_vars($this->topic_model->get($id));
                    $output["modules"] = $this->topic_model->get_all();
                    $this->display_view("modules/delete", $output);
                }
            } else {
                $this->topic_model->delete($id);
                $this->index();
            }
        } else {
          $this->index();
        }
    }

    /**
     * not build
     * To add a new module
     *
     * @param any $error = Whether or not there has been an error
     */
    public function add_module($error = NULL){
        $outputs = array(
            'error' => ($error == NULL ? NULL : true),
            'action' => 'add'
        );
        $this->display_view("modules/add", $outputs);
    }

}