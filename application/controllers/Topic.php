<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Topic controller
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */

class Topic extends MY_Controller
{
    /* MY_Controller variables definition */
    protected $access_level = "2";

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('topic_model');
        $this->load->helper(array('form', 'url'));
    }

    /**
     * Display the topic view
     */
    public function index($error = "")
    {
        $output['topics'] = $this->topic_model->get_all();
		$output['error'] = $error;
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
    public function update($id = 0, $error = 0){
        $outputs['error'] = $error;
        if($id != 0){
			$topic = $this->topic_model->get($id);
			
			$outputs["id"] = $topic->ID;
			$outputs["title"] = $topic->Topic;
			$output["action"] = "update";
			
            $this->display_view("topics/update", $outputs);
        }else{
            $this->index();
        }
    }

    /**
     * Form validation to update a topic
     */
    public function form_update(){
		$this->form_validation->set_rules('title', 'Title', 'required');

        $id = $this->input->post('id');
		$title = array('Topic' => $this->input->post('title'));
        if($this->form_validation->run() == true){
			$this->topic_model->update($id, $title);
            $this->index();
        }else{;
            $this->update($id, 1);
        }	
    }

    /**
     * @param int $id = id of the selected questionnaire
     * Delete selected topic and redirect to topic list
     */
    public function delete($id = 0, $action = NULL){
		
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
    public function add($error = NULL){
		$output['error'] = ($error == NULL ? NULL : true);
		$output["action"] = "add";
        $this->display_view("topics/add", $output);
    }

    /**
     * Form validation to update a topic (parent topic)
     */
    public function form_add(){
		
		$this->form_validation->set_rules('title', 'Title', 'required');

		$title = array('Topic' => $this->input->post('title'));
        if($this->form_validation->run() == true){
			$this->topic_model->insert($title);
            $this->index();
        }else{;
            $this->add(1);
        }
    }
	
	
}