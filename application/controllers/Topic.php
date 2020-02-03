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
    public function __construct()
    {
        $this->config->load(to_test_path('user/MY_user_config'));
        $this->access_level = $this->config->item('access_lvl_registered');
        parent::__construct();
        $this->load->model('topic_model');
        $this->load->helper('date');

        $this->form_validation->CI =& $this;
    }

    /**
     * Display the topic view
     */
    public function index($error = "")
    {
        $output = array(
            'title' => $this->lang->line('title_topic'),
            'modules' => $this->topic_model->get_many_by('FK_Parent_Topic IS NULL AND (Archive IS NULL OR Archive = 0)'),
            'topics' => $this->topic_model->get_many_by('FK_Parent_Topic IS NOT NULL AND (Archive IS NULL OR Archive = 0)'),
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
        $topic = $this->topic_model->get($id);
        if (!is_null($topic)){
            $outputs = array(
                'title' => $this->lang->line('title_module_update'),
                'error' => $error,
                'id' => $id,
                'title_topic' => $topic->Topic
            );
            $this->display_view("topics/update", $outputs);
        } else redirect('topic');
    }

    /**
     * Form validation to update a topic
     */
    public function form_update_topic(){
        $this->form_validation->set_rules('title', 'Title', 'required|max_length['.TOPIC_MAX_LENGTH.']');
        $this->form_validation->set_rules('id', 'Id', 'callback_cb_topic_exists', $this->lang->line('msg_err_topic_not_exist'));

        $id = $this->input->post('id');
        $title = array('Topic' => $this->input->post('title'));
        if($this->form_validation->run() == true){
            $this->topic_model->update($id, $title);
            redirect('topic');
        }else{
            $this->update_topic($id, 1);
        }
    }

    /**
     * @param int $id = id of the selected questionnaire
     * Delete selected topic and redirect to topic list
     */
    public function delete_topic($id = 0, $action = NULL){
        $this->load->model('question_model');
        $topic = $this->topic_model->get($id);
        if(is_null($topic)) redirect('topic');

        if (is_null($action)) {
            $count = $this->question_model->count_by('FK_Topic='.$id.' AND (Archive IS NULL OR Archive = 0)');
            $output['ID'] = $id;
            $output['Topic'] = $this->topic_model->get($id)->Topic;
            $output['title'] = $this->lang->line('delete_topic');
            if ($count != 0) {
                $output['questions'] = $count;
            }
            $this->display_view("topics/delete", $output);
        } else {
            $update = array('Archive' => 1);
            $this->topic_model->update($id, $update);
            $this->question_model->update_by('FK_Topic='.$id, $update);

            redirect('topic');
        }
    }

    /**
     * not build
     * To add a new topic
     */
    public function add_topic($preselect_module = NULL, $error = NULL){
        $output = array(
            'title' => $this->lang->line('title_topic_add'),
            'topics' => $this->topic_model->get_many_by('(FK_Parent_Topic IS NULL OR FK_Parent_Topic = 0) AND (Archive IS NULL OR Archive = 0)'),
            'error' => ($error == NULL ? NULL : true),
            'selected_module' => $preselect_module
        );
        $this->display_view("topics/add", $output);
    }

    /**
     * Form validation to update a topic (parent topic)
     */
    public function form_add_topic(){
        $datestring = '%Y-%m-%d %h:%i:%s';
        $time = time();

        $valid_modules_db = $this->topic_model->get_many_by('FK_Parent_Topic IS NULL AND (Archive IS NULL OR Archive = 0)');
        foreach($valid_modules_db as &$valid_module_db) {
            $valid_module_db = $valid_module_db->ID;
        }
        $valid_modules = implode(',', $valid_modules_db);

        $this->form_validation->set_rules('title', $this->lang->line('topic_title_error'), 'required|max_length['.TOPIC_MAX_LENGTH.']');
        $this->form_validation->set_rules('module_selected', $this->lang->line('topic_module_selected_error'), 'required|is_natural_no_zero|in_list['.$valid_modules.']');

        if($this->form_validation->run()){

            $title = array(
                'Topic' => $this->input->post('title'),
                'FK_Parent_Topic' => $this->input->post('module_selected'),
                'Creation_Date' => mdate($datestring, $time));

            $this->topic_model->insert($title);
            redirect('topic');
        }else{
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
        $topic = $this->topic_model->get($id);
        if (!is_null($topic)){
            $outputs = array(
                'title' => $this->lang->line('title_module_update'),
                'error' => $error,
                'id' => $id,
                'title_module' => $topic->Topic,
                'action' => 'update'
            );
            $this->display_view("modules/update", $outputs);
        } else redirect('topic');
    }

    /**
     * Form validate to update or add a module (parent topic)
     */
    public function form_validate_module(){
        $datestring = '%Y-%m-%d %h:%i:%s';
        $time = time();

        $this->form_validation->set_rules('title', 'Title', 'required|max_length['.TOPIC_MAX_LENGTH.']');
        $this->form_validation->set_rules('id', 'Id', 'callback_cb_topic_exists', $this->lang->line('msg_err_module_not_exist'));

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
            redirect('topic');
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
        $this->load->model('question_model');
        $topic = $this->topic_model->get($id);
        if(is_null($topic)) redirect('topic');

        if (is_null($action)) {
            $count = $this->topic_model->count_by('FK_Parent_Topic='.$id.' AND (Archive IS NULL OR Archive = 0)');
            $output['ID'] = $id;
            $output['Topic'] = $this->topic_model->get($id)->Topic;
            $output['title'] = $this->lang->line('delete_module');
            if ($count != 0) {
                $output['topics'] = $count;
            }
            $this->display_view("topics/delete", $output);
        } else {
            $update = array('Archive' => 1);
            $this->topic_model->update($id, $update);
            $topics = $this->topic_model->get_many_by('FK_Parent_Topic='.$id);
            foreach ($topics as $topic) {
                $questions = $this->question_model->get_many_by('FK_Topic='.$topic->ID);
                foreach ($questions as $question) {
                    $this->question_model->update($question->ID, $update);
                }
                $this->question_model->update($topic->ID, $update);
            }
            redirect('topic');
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
            'title' => $this->lang->line('title_module_add'),
            'error' => ($error == NULL ? NULL : true),
            'action' => 'add'
        );
        $this->display_view("modules/add", $outputs);
    }

    /**
     * Checks that the topic exists
     *
     * @param int $topicId = ID of the topic
     * @return boolean = Whether the topic exists
     */
    public function cb_topic_exists($topicId) : bool
    {
        return $topicId == 0 || !is_null($this->topic_model->with_deleted()->get($topicId));
    }
}
