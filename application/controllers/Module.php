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
		$this->load->model('question_model');
		$this->load->model('question_questionnaire_model');
        $this->load->helper(array('form', 'url'));
    }

    /**
     * Display the module view
     */
    public function index()
    {
        $output['modules'] = $this->topic_model->get_many_by('FK_Parent_Topic is NULL');
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
			
            $this->display_view("modules/update", $outputs);
        }else{
            $this->index();
        }
    }

    /**
     * Form validation to update a module (parent topic)
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
     * Delete selected module (parent topic) and redirect to module list
     */
    public function delete($id = 0, $action = NULL){
	if ($id > 0) {
		
		$topic = $this->topic_model->with("questions")->with("child_topics")->get($id);
		//var_dump($topic);
		
		if (is_null($action)) {
			if ((count($topic->child_topics) > 0) OR (count($topic->questions) > 0)) {
				$this->index();
				echo "Ce module possède des questions et/ou des sujets liés, il ne peut être supprimé...";
			} else {
				$output = get_object_vars($this->topic_model->get($id));
				$output["modules"] = $this->topic_model->get_all();
				$this->display_view("modules/delete", $output);
			}
		} else {
			$this->topic_model->delete($id);
			$this->index();
		}
      }
    }

    /**
     * not build
     * To add a new module
     */
    public function add($error = NULL){
		$output['error'] = ($error == NULL ? NULL : true);
        $this->display_view("modules/add", $output);
    }

    /**
     * Form validation to update a module (parent topic)
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