<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Module (Parent topic) controller
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */

class Module extends MY_Controller
{
    /* MY_Controller variables definition */
    protected $access_level = "2";

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('topic_model');
        $this->load->helper(array('form', 'url'));
		$this->load->helper('date');
    }

    /**
     * Display the module view
     */
    public function index($error = "")
    {
        $output['modules'] = $this->topic_model->get_many_by('FK_Parent_Topic is NULL');
		$output['error'] = $error;
        $this->display_view("modules/index", $output);		
    }

    /**
     * @param int $id = id of the selected module
     * @param int $error = Type of error :
     * 0 = no error
     * 1 = wrong identifiers
     * 2 = field(s) empty
     * Display the update module view
     */
    public function update($id = 0, $error = 0){
        $outputs['error'] = $error;
        if($id != 0){
			$topic = $this->topic_model->get($id);
			
			$outputs["id"] = $topic->ID;
			$outputs["title"] = $topic->Topic;
			$outputs["action"] = "update";
			
            $this->display_view("modules/update", $outputs);
        }else{
            $this->index();
        }
    }

    /**
     * Form validate to update or add a module (parent topic)
     */
    public function form_validate(){

		define('TIMEZONE', 'Europe/Zurich');
		date_default_timezone_set(TIMEZONE);
		$datestring = '%Y-%m-%d %h:%i:%s';
		$time = time();
	
		$this->form_validation->set_rules('title', 'Title', 'required');

        $id = $this->input->post('id');
		$title = array('Topic' => $this->input->post('title'),
					   'Creation_Date' => mdate($datestring, $time));
		$action = $this->input->post('action');
        if($this->form_validation->run() == true){
			if ($action == "update") {
				$this->topic_model->update($id, $title);
			} else {
				$this->topic_model->insert($title);
			}
			$this->index();
        } else {
			if ($action == "update") {
				$this->update($id, 1);
			} else {
				$this->add(1);
			}
        }
    }
	
    /**
     * @param int $id = id of the selected questionnaire
     * Delete selected module (parent topic) and redirect to module list
     */
    public function delete($id = 0, $action = NULL){
		if ($id != 0) {
			$topic = $this->topic_model->with("questions")->with("child_topics")->get($id);
			if (is_null($action)) {
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
     */
    public function add($error = NULL){
		$outputs["error"] = ($error == NULL ? NULL : true);
		$outputs["action"] = "add";
        $this->display_view("modules/add", $outputs);
    }	
}