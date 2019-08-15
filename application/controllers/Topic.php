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
    protected $access_level = ACCESS_LVL_MANAGER;

    public function __construct()
    {
        parent::__construct();
        $this->load->model(['topic_model', 'question_model']);
        $this->load->helper('date');
    }

    /**
     * Display the topic view
     */
    public function index()
    {
        $output = array(
            'modules' => $this->topic_model->get_many_by('FK_Parent_Topic IS NULL AND (Archive IS NULL OR Archive = 0)'),
            'topics' => $this->topic_model->get_many_by('FK_Parent_Topic IS NOT NULL AND (Archive IS NULL OR Archive = 0)')
        );

        $this->display_view("topics/index", $output);
    }

    /**
     * Display the update topic view
     *
     * @param int $id = id of the selected topic
     */
    public function update_topic($id = 0){
        if(!$id) redirect('topic');

        // Verify topic
        $topic = $this->topic_model->get($id);
        if(is_null($topic)) {
            $error = 'topic_error_404_';
            show_error($this->lang->line($error.'message'), 404, $this->lang->line($error.'heading'));
        }

        // Display result
        $outputs = array(
            'id' => $id,
            'title' => $topic->Topic
        );
        $this->display_view("topics/update", $outputs);
    }

    /**
     * Form validation for a topic
     */
    public function form_validate_topic() {
        $id = $this->input->post('id');
        $datestring = '%Y-%m-%d %h:%i:%s';
        $topic = array(
            'Topic' => $this->input->post('title'),
            'FK_Parent_Topic' => $this->input->post('module_selected'),
            'Creation_Date' => mdate($datestring, time())
        );

        // Set validation rules
        $this->form_validation->set_rules('title', $this->lang->line('topic_title_error'), 'required|max_length['.TOPIC_MAX_LENGTH.']');
        if(is_null($id)) {
            $this->form_validation->set_rules('module_selected', $this->lang->line('topic_module_selected_error'), 'required|is_natural_no_zero');
        }

        if($this->form_validation->run()) {
            if(is_null($id)) {
                $this->topic_model->insert($topic);
            } else {
                // Remove unchangeable values to prevent overwrite
                unset($topic['FK_Parent_Topic'], $topic['Creation_Date']);
                $this->topic_model->update($id, $topic);
            }
            redirect('topic');
        } else {
            if(is_null($id)) {
                $this->add_topic();
            } else {
                $this->update_topic($id);
            }
        }
    }

    /**
     * Delete selected topic and redirect to topic list
     * 
     * @param int $id = id of the selected questionnaire
     * @param any $action = set NULL to display confirmation, anything else to delete
     */
    public function delete_topic($id = 0, $action = NULL){
        if(!$id) redirect('topic');

        // Verify topic
        $topic = $this->topic_model->get($id);
        if(is_null($topic)) {
            $error = 'topic_error_404_';
            show_error($this->lang->line($error.'message'), 404, $this->lang->line($error.'heading'));
        }
        // Ask for confirmation
        elseif(is_null($action)) {
            $count = $this->question_model->count_by('FK_Topic='.$id.' AND (Archive IS NULL OR Archive = 0)');
            $output['ID'] = $id;
            $output['Topic'] = $this->topic_model->get($id)->Topic;
            if ($count != 0) {
                $output['questions'] = $count;
            }
            $this->display_view("topics/delete", $output);
        }
        // Delete topic
        else {
            $update = array('Archive' => 1);
            $this->topic_model->update($id, $update);
            $this->question_model->delete_by('FK_Topic='.$id);

            redirect('topic');
        }
    }

    /**
     * Displays a form to create a new topic
     * 
     * @param int $module = The id of the module to add it to
     */
    public function add_topic($module = 0){
        $topics = $this->topic_model->get_many_by('(FK_Parent_Topic IS NULL OR FK_Parent_Topic = 0) AND (Archive IS NULL OR Archive = 0)');
        $output = array(
            'topics' => [],
            'selected_module' => $module
        );

        foreach($topics as $topic) {
            $output['topics'][$topic->ID] = $topic->Topic;
        }

        $this->display_view("topics/add", $output);
    }

    /**
     * Display the update module view
     *
     * @param int $id = id of the selected module
     */
    public function update_module($id = 0){
        if(!$id) redirect('topic');

        // Verify module
        $module = $this->topic_model->get($id);
        if(is_null($module)) {
            $error = 'module_error_404_';
            show_error($this->lang->line($error.'message'), 404, $this->lang->line($error.'heading'));
        }

        // Display result
        $outputs = array(
            'id' => $id,
            'title' => $topic->Topic,
            'action' => 'update'
        );
        $this->display_view("modules/update", $outputs);
    }

    /**
     * Form validate to update or add a module (parent topic)
     */
    public function form_validate_module(){
        $this->form_validation->set_rules('title', 'Title', 'required|max_length['.TOPIC_MAX_LENGTH.']');

        $id = $this->input->post('id');
        $action = $this->input->post('action');
        if($this->form_validation->run()){
            if ($action == "update") {
                $title = array('Topic' => $this->input->post('title'));
                $this->topic_model->update($id, $title);
            } else {
                $title = array(
                    'Topic' => $this->input->post('title'),
                    // Make Creation_Date now
                    'Creation_Date' => mdate('%Y-%m-%d %h:%i:%s', time())
                );
                $this->topic_model->insert($title);
            }
            redirect('topic');
        } else {
            if ($action == "update") {
                $this->update_module($id);
            } else {
                $this->add_module();
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
        if (!$id) redirect('topic');

        // Verify module
        $module = $this->topic_model->get($id);
        if (is_null($module)) {
            $error = 'module_error_404_';
            show_error($this->lang->line($error.'message'), 404, $this->lang->line($error.'heading'));
        }
        // Ask for confirmation
        elseif (is_null($action)) {
            $count = $this->topic_model->count_by('FK_Parent_Topic='.$id.' AND (Archive IS NULL OR Archive = 0)');
            $output['ID'] = $id;
            $output['Topic'] = $topic->Topic;
            if ($count) {
                $output['topics'] = $count;
            }
            $this->display_view("topics/delete", $output);
        }
        // Delete
        else {
            $update = array('Archive' => 1);
            $this->topic_model->update($id, $update);
            // 
            $topics = $this->topic_model->get_many_by('FK_Parent_Topic='.$id);
            foreach ($topics as $topic) {
                $this->question_model->delete_by('FK_Topic='.$topic->ID);
                $this->topic_model->update($topic->ID, $update);
            }
            redirect('topic');
        }
    }

    /**
     * Displays a form to create a new module
     */
    public function add_module(){
        $outputs = array('action' => 'add');
        $this->display_view("modules/add", $outputs);
    }

}
