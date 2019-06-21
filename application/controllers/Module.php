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
    protected $access_level = ACCESS_LVL_ADMIN;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('topic_model');
        $this->load->helper(array('form', 'url', 'date'));
    }

    /**
     * Display the module view
     *
     * @param string $error = The error to display
     */
    public function index($error = "")
    {
        $outputs = array(
            'modules' => $this->topic_model->get_many_by('FK_Parent_Topic is NULL'),
            'error' => $error
        );
        $this->display_view("modules/index", $outputs);
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
    public function form_validate(){

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
				$this->update($id, 1);
			} else {
				$this->add(1);
			}
        }
    }

    /**
     * Delete selected module (parent topic) and redirect to module list
     *
     * @param int $id = id of the selected questionnaire
     * @param any $action = Set to not null to delete the module
     */
    public function delete($id = 0, $action = NULL){
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
    public function add($error = NULL){
        $outputs = array(
            'error' => ($error == NULL ? NULL : true),
            'action' => 'add'
        );
        $this->display_view("modules/add", $outputs);
    }
}