<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Question controller
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */

class Question extends MY_Controller
{
	/* MY_Controller variables definition */
	protected $access_level = "2";


	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('question_questionnaire_model', 'questionnaire_model', 'question_model', 'question_type_model', 'topic_model', 'multiple_choice_model',
							'multiple_answer_model', 'answer_distribution_model', 'cloze_text_model',
							'cloze_text_answer_model', 'table_cell_model', 'free_answer_model', 'picture_landmark_model'));
		$this->load->helper(array('url', 'form'));
		$this->load->library(array('PHPExcel-1.8/Classes/PHPExcel', 'upload', 'form_validation'));
	}

	/**
	 * Display question list
	 */
	public function index()
	{
		if(!empty($_GET['module']) && !empty($_GET['topic'])){
			$nbTopic = $this->topic_model->count_by("ID = ".$_GET['topic']." AND FK_Parent_Topic = ".$_GET['module']);
			if($nbTopic==0){
				redirect("Question?module=".$_GET['module']."&topic="."&type=".$_GET['type']);
			}
		}

		if(isset($_GET['module']) && isset($_GET['topic']) && isset($_GET['type'])){
			$_SESSION['filtres'] = "Question?module=".$_GET['module']."&topic=".$_GET['topic']."&type=".$_GET['type'];
		}

		if(isset($_SESSION['filtres']) && strpos($_SERVER['REQUEST_URI'], "?") === false){
			redirect($_SESSION['filtres']);
		}

		$where = "";

		if(!empty($_GET['topic'])){
			$where .= "FK_Topic = ".$_GET['topic'];
		}
		if(!empty($_GET['type'])){
			if(!empty($where)){
				$where .= " AND ";
			}
			$where .= "FK_Question_Type = ".$_GET['type'];
		}
		if(!empty($_GET['module'])){

			$topics = $this->topic_model->get_many_by("FK_Parent_Topic = ".$_GET['module']);

			$listIdQuestion = '';

			if ($topics != false) {
				foreach ($topics as $topic) {
					$listIdQuestion .= 'FK_Topic = '.$topic->ID.' OR ';
				}
				$listIdQuestion = substr($listIdQuestion, 0, -4);
			} else {
				// There is no topic for the selected module (parent topic)
				$listIdQuestion .= 'FK_Topic = 0';
			}

			if(!empty($where)){
				$where .= " AND ";
			}
			$where .= "(".$listIdQuestion.")";
		}

		$orderby="";

		if (!empty($where)){
			$where ="($where)";
		}

		if (!empty($_GET['sort'])){
			switch ($_GET['sort']){
				case 'question_asc': $orderby = "Question ASC, FK_Question_Type ASC, Points ASC, ID ASC";break;
				case 'question_desc': $orderby = "Question DESC, FK_Question_Type ASC, Points ASC, ID ASC";break;
				case 'question_type_asc': $orderby = "FK_Question_Type ASC, Question ASC, Points ASC, ID ASC";break;
				case 'question_type_desc': $orderby = "FK_Question_Type DESC, Question ASC, Points ASC, ID ASC";break;
				case 'points_asc': $orderby = "Points ASC, Question ASC, FK_Question_Type ASC, ID ASC";break;
				case 'points_desc': $orderby = "Points DESC, Question ASC, FK_Question_Type ASC, ID ASC";break;
				default:$orderby = "Question ASC, FK_Question_Type ASC, Points ASC, ID ASC";
			}	
		}
		else{
			$orderby = "Question ASC, FK_Question_Type ASC, Points ASC, ID ASC";
		}

		$this->db->order_by($orderby);
		if(empty($where)){
			$output['questions'] = $this->question_model->with_all()->get_all();

		} else {
			$output['questions'] = $this->question_model->with_all()->get_many_by($where);
		}

		
		$output['topics'] = $this->topic_model->get_all();
		$output['questionTypes'] = $this->question_type_model->get_all();
		$this->display_view('questions/index', $output);
	}

	/**
	 * @param int $id = id of the question
	 * Delete selected question
	 */
	public function delete($id = 0)
	{
		if ($id != 0) {

			$nbQuestionnaires = $this->question_questionnaire_model->count_by("FK_Question = ".$id);
			if($nbQuestionnaires == 0){

				$question = $this->question_model->get($id);

				switch ($question->FK_Question_Type){
				case 1:
					$this->multiple_choice_model->delete_by("FK_Question = ".$id);
					break;
				case 2:
					$this->multiple_answer_model->delete_by("FK_Question = ".$id);
					break;
				case 3:
					// TODO 
					break;
				case 4:
					$cloze_text = $this->cloze_text_model->get_by("FK_Question = ".$id);
					$this->cloze_text_answer_model->delete_by("FK_Cloze_Text = ".$cloze_text->ID);
					$this->cloze_text_model->delete_by("FK_Question = ".$id);
					break;
				case 5:
					// TODO 
					break;
				case 6:
					$this->free_answer_model->delete_by("FK_Question = ".$id);
					break;
				case 7:
					$picture = "uploads/pictures/".$question->Picture_Name;
					if(file_exists($picture)){
						unlink($picture);
					}
					$this->picture_landmark_model->delete_by("FK_Question = ".$id);
				}

				$this->question_model->delete($id);
				redirect('/Question');
			} else {
				$questionnaires = $this->question_questionnaire_model->get_many_by("FK_Question = ".$id);
				for($i = 0; $i < $nbQuestionnaires; $i++){
					$output['questionnaires'][$i] = $this->questionnaire_model->get($questionnaires[$i]->FK_Questionnaire);
				}
				$output['error'] = "";

				$this->display_view('questionnaires/index', $output);
			}
		}
	}

	/**
	 * Form validation to update question
	 */
	public function form_update()
	{
		$this->form_validation->set_rules('name', 'Title', 'required');
		$this->form_validation->set_rules('points', 'Points', 'required');
		
		$id = $this->input->post('id');
		$title = array(	'Question' => $this->input->post('name'),
						'Points' => $this->input->post('points'));
		
		if ($this->form_validation->run() == true) {
			$this->question_model->update($id, $title);
			$this->index();
		} else {
			$this->update($id, 1);
		}
	}

	/**
	 * @param int $id = selected question id
	 * @param int $error = Type of error :
	 * 0 = no error
	 * 1 = wrong identifiers
	 * 2 = field(s) empty
	 * Display the detailed view to update a question
	 */
	public function update($id = 0, $error = 0)
	{
		$output['error'] = $error;
		$output['id'] = $id;

		if ($id != 0) {
			$question = $this->question_model->get($id);
			$output['focus_topic'] = $this->topic_model->get($question->FK_Topic);
			$output['question_type'] = $this->question_type_model->get($question->FK_Question_Type);
			$output['name'] = $question->Question;
			$output['points'] = $question->Points;

			switch ($question->FK_Question_Type){
			case 1:
				// MUTLIPLE CHOICE
				$reponses = $this->multiple_choice_model->get_many_by('FK_Question = ' . $id);
				$i = 0;
				foreach ($reponses as $reponse) {
					$answers[$i]['id'] = $reponse->ID;
					$answers[$i]['question'] = $reponse->Answer;
					$answers[$i]['answer'] = $reponse->Valid;
					$i++;
				}
				$output['nbAnswer'] = count($reponses);
				$output['answers'] = $answers;
				$this->display_view('multiple_choice/add', $output);
				break;
			case 2:
				// MUTLIPLE ANSWER
				$output['nb_desired_answers'] = $question->Nb_Desired_Answers;
				$reponses = $this->multiple_answer_model->get_many_by('FK_Question = ' . $id);
				$i = 0;
				foreach ($reponses as $reponse) {
					$answers[$i]['id'] = $reponse->ID;
					$answers[$i]['answer'] = $reponse->Answer;
					$i++;
				}
				$output['nbAnswer'] = count($reponses);
				$output['answers'] = $answers;
				$this->display_view('multiple_answer/add', $output);
				break;
			case 3:
				// TODO
				break;
			case 4:
				// CLOZE TEXT
				$cloze_text = $this->cloze_text_model->get_by('FK_Question = ' . $id);
				$reponses = $this->cloze_text_answer_model->get_many_by('FK_Cloze_Text = ' . $cloze_text->ID);
				$i = 0;
				foreach ($reponses as $reponse) {
					$answers[$i]['id'] = $reponse->ID;
					$answers[$i]['answer'] = $reponse->Answer;
					$i++;
				}
				$output['nbAnswer'] = count($reponses);
				$output['answers'] = $answers;
				$output['cloze_text'] = $cloze_text->Cloze_Text;
				$output['id_cloze_text'] = $cloze_text->ID;
				$this->display_view('cloze_text/add', $output);
				break;
			case 5:
				// TODO
				break;
			case 6:
				// FREE ANSWER
				$output['id_answer'] = $this->free_answer_model->get_by('FK_Question ='.$question->ID)->ID;
				$output['answer'] = $this->free_answer_model->get_by('FK_Question ='.$question->ID)->Answer;
				$this->display_view('free_answers/add', $output);
				break;
			case 7:
				// PICTURE LANDMARK
				$output['picture_name'] = $question->Picture_Name;
				$reponses = $this->picture_landmark_model->get_many_by('FK_Question = ' . $id);
				$i = 0;
				foreach ($reponses as $reponse) {
					$answers[$i]['id'] = $reponse->ID;
					$answers[$i]['symbol'] = $reponse->Symbol;
					$answers[$i]['answer'] = $reponse->Answer;
					$i++;
				}
				$output['nbAnswer'] = count($reponses);
				$output['answers'] = $answers;

				$this->display_view('picture_landmark/add', $output);
			}
		} else {
			$this->index();
		}
	}

	public function detail($id = 0, $error = 0)
	{
		$output['error'] = $error;
		$output['id'] = $id;

		if ($id != 0) {
			$question = $this->question_model->with_all()->get_by('ID = ' . $id);
			$output['question'] = $question;
			$output['image'] = "";
			$output['reponse'] = "";
			if (!is_null($question)){
				switch ($question->FK_Question_Type){ 
				case 1:
					$reponses = $this->multiple_choice_model->get_many_by('FK_Question = ' . $id);
					foreach ($reponses as $reponse) {
						if ($reponse->Valid == '1'){
							$cocher = $this->lang->line('yes');
						} else {
							$cocher = $this->lang->line('no');
						}
						$output['reponse'] = $output['reponse']."<br>".$reponse->Answer.":".$cocher;
					}
					break;
				case 2:
					$reponses = $this->multiple_answer_model->get_many_by('FK_Question = ' . $id);
					foreach ($reponses as $reponse) {
						$output['reponse'] = $output['reponse']."<br>".$reponse->Answer;
					}				
					break;
				case 3:
					$reponses = $this->answer_distribution_model->get_many_by('FK_Question = ' . $id);
					foreach ($reponses as $reponse) {
						$output['reponse'] = $output['reponse']."<br>".$reponse->Question_Part."/".$reponse->Answer_Part;
					}				
					break;
				case 4:
					$question = $this->cloze_text_model->get_by('FK_Question = ' . $id);
					$output['question']->Question = $output['question']->Question.': '.$question->Cloze_Text;
					$reponses = $this->cloze_text_answer_model->get_many_by('FK_Cloze_Text = ' . $question->ID);
					foreach ($reponses as $reponse) {
						$output['reponse'] = $output['reponse']."<br>".$reponse->Answer_Order."/".$reponse->Answer;
					}				
					break;
				case 5:
					$reponses = $this->table_cell_model->get_many_by('FK_Question = ' . $id);
					break;
				case 6:
					$reponses = $this->free_answer_model->get_many_by('FK_Question = ' . $id);
					foreach ($reponses as $reponse) {
						$output['reponse'] = $output['reponse']." ".$reponse->Answer;
					}		
					break;			
				case 7:
					$output['image'] = $question->Picture_Name;
					$reponses = $this->picture_landmark_model->get_many_by('FK_Question = ' . $id);
					foreach ($reponses as $reponse) {
						$output['reponse'] = $output['reponse']."<br>".$reponse->Symbol."/".$reponse->Answer;
					}			
				}
				//var_dump($reponses);
				$this->display_view('questions/detail', $output);
			} else {
				//todo déclancher la page 404 
			}
		} else {
			$this->index();
		}
	}
	
	
	/**
	 * Display form to add a question
	 */
	public function add($step=1)
	{
		if ($step==1){
			// Display a form to choose a topic and a question type
			$output['topics'] = $this->topic_model->get_tree();
			$output['list_question_type'] = $this->question_type_model->dropdown('Type_Name');
			$this->display_view('questions/add', $output);

		} elseif ($step==2){
			// Display a specific form for the choosen question type
			$output['focus_topic'] = $this->topic_model->get($_POST['focus_topic']);
			$output['question_type'] = $this->question_type_model->get($_POST['question_type']);
			$output['nbAnswer'] = 1;
			$answers[0]['id'] = 0;
			$answers[0]['question'] = false;
			$answers[0]['answer'] = "";
			$answers[0]['symbol'] = "";
			$output['answers'] = $answers;
		
			switch ($_POST['question_type']){
			case 1:
				$this->display_view('multiple_choice/add', $output);
				break;
			case 2:
				$this->display_view('multiple_answer/add', $output);
				break;
			case 3:
				// TODO
				break;
			case 4:
				$this->display_view('cloze_text/add', $output);
				break;
			case 5:
				// TODO
				break;
			case 6:
				$this->display_view('free_answers/add', $output);
				break;
			case 7:	
				$this->display_view('picture_landmark/file', $output);
			}
		}
	}
	

	/**
	 * Function to save a multiple choice question
	 */
	public function add_MultipleChoice()
	{
		if (isset($_POST['cancel'])){
			redirect('/Question');
		}

		if (isset($_POST['save'])){
			$this->form_validation->set_rules('name', $this->lang->line('question_text'), 'required');
			$this->form_validation->set_rules('points', $this->lang->line('points'), 'required');
			for($i=0; $i < $_POST['nbAnswer']; $i++){
				$this->form_validation->set_rules('reponses['.$i.'][question]', $this->lang->line('answers_list'), 'required');
				$this->form_validation->set_rules('reponses['.$i.'][answer]', $this->lang->line('valid_answer'), 'required');
			}

			if ($this->form_validation->run()){
				if(!isset($_POST['id'])){
					$inputQuestion = array(
						"FK_Topic" => $_POST['focus_topic'],
						"FK_Question_Type" => $_POST['question_type'],
						"Question" => $_POST['name'],
						"Points" => $_POST['points']
					);
					$idQuestion = $this->question_model->insert($inputQuestion);
					
					for($i=0; $i < $_POST['nbAnswer']; $i++){
						$inputAnswer = array(
							"FK_Question" => $idQuestion,
							"Answer" => $_POST['reponses'][$i]['question'],
							"Valid" => $_POST['reponses'][$i]['answer']
						);
						$this->multiple_choice_model->insert($inputAnswer);
					}
				} else {
					$inputQuestion = array(
						"FK_Topic" => $_POST['focus_topic'],
						"FK_Question_Type" => $_POST['question_type'],
						"Question" => $_POST['name'],
						"Points" => $_POST['points']
					);
					$this->question_model->update($_POST['id'], $inputQuestion);

					for($i=0; $i < $_POST['nbAnswer']; $i++){
						$answers[$i]['id'] = $_POST['reponses'][$i]['id'];
						$answers[$i]['question'] = $_POST['reponses'][$i]['question'];
						$answers[$i]['answer'] = $_POST['reponses'][$i]['answer'];
					}

					$reponses = $this->multiple_choice_model->get_many_by('FK_Question = ' . $_POST['id']);
					$i = 0;
					foreach ($reponses as $reponse) {
						$answersDb[$i] = $reponse->ID;
						$i++;
					}

					for($i=0; $i < $_POST['nbAnswer']; $i++){
						if($answers[$i]['id'] == 0){
							$inputQuestion = array(
								"FK_Question" => $_POST['id'],
								"Answer" => $_POST['reponses'][$i]['question'],
								"Valid" => $_POST['reponses'][$i]['answer']
							);
							$idQuestion = $this->multiple_answer_model->insert($inputQuestion);
						} else {
							$inputAnswer = array(
								"FK_Question" => $_POST['id'],
								"Answer" => $_POST['reponses'][$i]['question'],
								"Valid" => $_POST['reponses'][$i]['answer']
							);
							$this->multiple_answer_model->update($answers[$i]['id'], $inputAnswer);

							unset($answersDb[array_search($answers[$i]['id'], $answersDb)]);
						}
					}

					foreach ($answersDb as $answerDb) {
						$this->multiple_answer_model->delete($answerDb);
					}
				}
				redirect('/Question');
			} else {
				$output['focus_topic'] = $this->topic_model->get($_POST['focus_topic']);
				$output['question_type'] = $this->question_type_model->get($_POST['question_type']);

				if(isset($_POST['id'])){
					$output['id'] = $_POST['id'];
				}
				if(isset($_POST['name'])){
					$output['name'] = $_POST['name'];
				}
				if(isset($_POST['points'])){
					$output['points'] = (int)$_POST['points'];
				}

				$output['nbAnswer'] = $_POST['nbAnswer'];
				
				for($i=0; $i < $output['nbAnswer']; $i++){
					if(!empty($_POST['reponses'][$i]['id'])){
						$answers[$i]['id'] = $_POST['reponses'][$i]['id'];
					} else {
						$answers[$i]['id'] = 0;
					}
					if(!empty($_POST['reponses'][$i]['question'])){
						$answers[$i]['question'] = $_POST['reponses'][$i]['question'];
					} else {
						$answers[$i]['question'] = "";
					}
					if(!empty($_POST['reponses'][$i]['answer'])){
						$answers[$i]['answer'] = $_POST['reponses'][$i]['answer'];
					} else {
						$answers[$i]['answer'] = "";
					}
				}
				
				$output['answers'] = $answers;
				
				$this->display_view('multiple_choice/add', $output);
			}
		} else {
			$output['focus_topic'] = $this->topic_model->get($_POST['focus_topic']);
			$output['question_type'] = $this->question_type_model->get($_POST['question_type']);

			if(isset($_POST['id'])){
				$output['id'] = $_POST['id'];
			}
			if(isset($_POST['name'])){
				$output['name'] = $_POST['name'];
			}
			if(isset($_POST['points'])){
				$output['points'] = (int)$_POST['points'];
			}

			$output['nbAnswer'] = $_POST['nbAnswer'];
			

			for($i=0; $i < $output['nbAnswer']; $i++){
				if(!empty($_POST['reponses'][$i]['id'])){
					$answers[$i]['id'] = $_POST['reponses'][$i]['id'];
				} else {
					$answers[$i]['id'] = 0;
				}
				if(!empty($_POST['reponses'][$i]['question'])){
					$answers[$i]['question'] = $_POST['reponses'][$i]['question'];
				} else {
					$answers[$i]['question'] = "";
				}
				if(!empty($_POST['reponses'][$i]['answer'])){
					$answers[$i]['answer'] = $_POST['reponses'][$i]['answer'];
				} else {
					$answers[$i]['answer'] = "";
				}
			}

			if (isset($_POST['add_answer'])){
				$answers[$i]['id'] = 0;
				$answers[$i]['question'] = false;
				$answers[$i]['answer'] = "";
				$output['nbAnswer'] = $output['nbAnswer']+1;
			}

			for($i=0; $i < $output['nbAnswer']; $i++){
				if (isset($_POST['del_answer'.$i])){
					if($output['nbAnswer']>1){
						$output['nbAnswer'] = $_POST['nbAnswer']-1;
						for($j=$i; $j < $output['nbAnswer']; $j++){
							$answers[$j] = $answers[$j+1];
						}
					} else {
						$output['nbAnswer'] = $_POST['nbAnswer'];
					}
				}
			}

			$output['answers'] = $answers;
		
			$this->display_view('multiple_choice/add', $output);
		}
	}

	/**
	 * Function for save multiple answer
	 */
	public function add_MultipleAnswer()
	{
		if (isset($_POST['cancel'])){
			redirect('/Question');
		}

		if (isset($_POST['save'])){
			$this->form_validation->set_rules('name', $this->lang->line('question_text'), 'required');
			$this->form_validation->set_rules('points', $this->lang->line('points'), 'required');
			$this->form_validation->set_rules('nb_desired_answers', $this->lang->line('nb_desired_answers'), 'required');
			for($i=0; $i < $_POST['nbAnswer']; $i++){
				$this->form_validation->set_rules('reponses['.$i.'][answer]', $this->lang->line('answers_list'), 'required');
			}

			if ($this->form_validation->run()){
				if(!isset($_POST['id'])){
					$inputQuestion = array(
						"FK_Topic" => $_POST['focus_topic'],
						"FK_Question_Type" => $_POST['question_type'],
						"Question" => $_POST['name'],
						"nb_desired_answers" => $_POST['nb_desired_answers'],
						"Points" => $_POST['points']
					);
					$idQuestion = $this->question_model->insert($inputQuestion);
					
					for($i=0; $i < $_POST['nbAnswer']; $i++){
						$inputAnswer = array(
							"FK_Question" => $idQuestion,
							"Answer" => $_POST['reponses'][$i]['answer']
						);
						$this->multiple_answer_model->insert($inputAnswer);
					}
				} else {
					$inputQuestion = array(
						"FK_Topic" => $_POST['focus_topic'],
						"FK_Question_Type" => $_POST['question_type'],
						"Question" => $_POST['name'],
						"Points" => $_POST['points']
					);
					$this->question_model->update($_POST['id'], $inputQuestion);

					for($i=0; $i < $_POST['nbAnswer']; $i++){
						$answers[$i]['id'] = $_POST['reponses'][$i]['id'];
						$answers[$i]['answer'] = $_POST['reponses'][$i]['answer'];
					}

					$reponses = $this->multiple_answer_model->get_many_by('FK_Question = ' . $_POST['id']);
					$i = 0;
					foreach ($reponses as $reponse) {
						$answersDb[$i] = $reponse->ID;
						$i++;
					}

					for($i=0; $i < $_POST['nbAnswer']; $i++){
						if($answers[$i]['id'] == 0){
							$inputQuestion = array(
								"FK_Question" => $_POST['id'],
								"Answer" => $_POST['reponses'][$i]['answer']
							);
							$idQuestion = $this->multiple_answer_model->insert($inputQuestion);
						} else {
							$inputAnswer = array(
								"FK_Question" => $_POST['id'],
								"Answer" => $_POST['reponses'][$i]['answer']
							);
							$this->multiple_answer_model->update($answers[$i]['id'], $inputAnswer);

							unset($answersDb[array_search($answers[$i]['id'], $answersDb)]);
						}
					}

					foreach ($answersDb as $answerDb) {
						$this->multiple_answer_model->delete($answerDb);
					}
				}
				redirect('/Question');
			} else {
				$output['focus_topic'] = $this->topic_model->get($_POST['focus_topic']);
				$output['question_type'] = $this->question_type_model->get($_POST['question_type']);
				
				if(isset($_POST['id'])){
					$output['id'] = $_POST['id'];
				}
				if(isset($_POST['name'])){
					$output['name'] = $_POST['name'];
				}
				if(isset($_POST['points'])){
					$output['points'] = (int)$_POST['points'];
				}
				if(isset($_POST['nb_desired_answers'])){
					$output['nb_desired_answers'] = (int)$_POST['nb_desired_answers'];
				}
				$output['nbAnswer'] = $_POST['nbAnswer'];
				
				for($i=0; $i < $output['nbAnswer']; $i++){
					if(!empty($_POST['reponses'][$i]['id'])){
						$answers[$i]['id'] = $_POST['reponses'][$i]['id'];
					} else {
						$answers[$i]['id'] = 0;
					}
					if(!empty($_POST['reponses'][$i]['question'])){
						$answers[$i]['question'] = $_POST['reponses'][$i]['question'];
					} else {
						$answers[$i]['question'] = "";
					}
					if(!empty($_POST['reponses'][$i]['answer'])){
						$answers[$i]['answer'] = $_POST['reponses'][$i]['answer'];
					} else {
						$answers[$i]['answer'] = "";
					}
				}
				$output['answers'] = $answers;
				
				$this->display_view('multiple_answer/add', $output);
			}
		} else {
			$output['focus_topic'] = $this->topic_model->get($_POST['focus_topic']);
			$output['question_type'] = $this->question_type_model->get($_POST['question_type']);

			if(isset($_POST['id'])){
				$output['id'] = $_POST['id'];
			}
			if(isset($_POST['name'])){
				$output['name'] = $_POST['name'];
			}
			if(isset($_POST['points'])){
				$output['points'] = (int)$_POST['points'];
			}
			if(isset($_POST['nb_desired_answers'])){
				$output['nb_desired_answers'] = (int)$_POST['nb_desired_answers'];
			}

			$output['nbAnswer'] = $_POST['nbAnswer'];
			

			for($i=0; $i < $output['nbAnswer']; $i++){
				if(!empty($_POST['reponses'][$i]['id'])){
					$answers[$i]['id'] = $_POST['reponses'][$i]['id'];
				} else {
					$answers[$i]['id'] = 0;
				}
				if(!empty($_POST['reponses'][$i]['answer'])){
					$answers[$i]['answer'] = $_POST['reponses'][$i]['answer'];
				} else {
					$answers[$i]['answer'] = "";
				}
			}

			if (isset($_POST['add_answer'])){
				$answers[$i]['id'] = 0;
				$answers[$i]['answer'] = "";
				$output['nbAnswer'] = $output['nbAnswer']+1;
			}

			for($i=0; $i < $output['nbAnswer']; $i++){
				if (isset($_POST['del_answer'.$i])){
					if($output['nbAnswer']>1){
						$output['nbAnswer'] = $_POST['nbAnswer']-1;
						for($j=$i; $j < $output['nbAnswer']; $j++){
							$answers[$j] = $answers[$j+1];
						}
					} else {
						$output['nbAnswer'] = $_POST['nbAnswer'];
					}
				}
			}

			$output['answers'] = $answers;
		
			$this->display_view('multiple_answer/add', $output);
		}
	}
	
	/**
	 * Function for save cloze text
	 */
	public function add_ClozeText()
	{
		if (isset($_POST['cancel'])){
			redirect('/Question');
		}

		if (isset($_POST['save'])){
			$this->form_validation->set_rules('name', $this->lang->line('question_text'), 'required');
			$this->form_validation->set_rules('points', $this->lang->line('points'), 'required');
			$this->form_validation->set_rules('cloze_text', $this->lang->line('text'), 'required');
			for($i=0; $i < $_POST['nbAnswer']; $i++){
				$this->form_validation->set_rules('reponses['.$i.'][answer]', $this->lang->line('answers_list'), 'required');
			}

			if ($this->form_validation->run()){
				if(!isset($_POST['id'])){
					$inputQuestion = array(
						"FK_Topic" => $_POST['focus_topic'],
						"FK_Question_Type" => $_POST['question_type'],
						"Question" => $_POST['name'],
						"Points" => $_POST['points']
					);
					$idQuestion = $this->question_model->insert($inputQuestion);

					$inputClozeText = array(
						"FK_Question" => $idQuestion,
						"Cloze_Text" => $_POST['cloze_text']
					);
					$idClozeText = $this->cloze_text_model->insert($inputClozeText);
					
					for($i=0; $i < $_POST['nbAnswer']; $i++){
						$inputAnswer = array(
							"FK_Cloze_Text" => $idClozeText,
							"Answer" => $_POST['reponses'][$i]['answer'],
							"Answer_Order" => $i
						);
						$this->cloze_text_answer_model->insert($inputAnswer);
					}
				} else {
					$inputQuestion = array(
						"FK_Topic" => $_POST['focus_topic'],
						"FK_Question_Type" => $_POST['question_type'],
						"Question" => $_POST['name'],
						"Points" => $_POST['points']
					);
					$this->question_model->update($_POST['id'], $inputQuestion);

					$inputClozeText = array(
						"FK_Question" => $_POST['id_cloze_text'],
						"Cloze_Text" => $_POST['cloze_text']
					);
					$this->cloze_text_model->update($_POST['id_cloze_text'], $inputClozeText);

					for($i=0; $i < $_POST['nbAnswer']; $i++){
						$answers[$i]['id'] = $_POST['reponses'][$i]['id'];
						$answers[$i]['answer'] = $_POST['reponses'][$i]['answer'];
					}

					$reponses = $this->cloze_text_answer_model->get_many_by('FK_Cloze_Text = ' . $_POST['id_cloze_text']);
					$i = 0;
					foreach ($reponses as $reponse) {
						$answersDb[$i] = $reponse->ID;
						$i++;
					}

					for($i=0; $i < $_POST['nbAnswer']; $i++){
						if($answers[$i]['id'] == 0){
							$inputQuestion = array(
								"FK_Cloze_Text" => $_POST['id_cloze_text'],
								"Answer" => $_POST['reponses'][$i]['answer'],
								"Answer_Order" => $i
							);
							$idQuestion = $this->cloze_text_answer_model->insert($inputQuestion);
						} else {
							$inputAnswer = array(
								"FK_Cloze_Text" => $_POST['id_cloze_text'],
								"Answer" => $_POST['reponses'][$i]['answer'],
								"Answer_Order" => $i
							);
							$this->cloze_text_answer_model->update($answers[$i]['id'], $inputAnswer);

							unset($answersDb[array_search($answers[$i]['id'], $answersDb)]);
						}
					}

					foreach ($answersDb as $answerDb) {
						$this->cloze_text_answer_model->delete($answerDb);
					}
				}
				redirect('/Question');
			} else {
				$output['focus_topic'] = $this->topic_model->get($_POST['focus_topic']);
				$output['question_type'] = $this->question_type_model->get($_POST['question_type']);
				
				if(isset($_POST['id'])){
					$output['id'] = $_POST['id'];
				}
				if(isset($_POST['id_cloze_text'])){
					$output['id_cloze_text'] = $_POST['id_cloze_text'];
				}
				if(isset($_POST['name'])){
					$output['name'] = $_POST['name'];
				}
				if(isset($_POST['points'])){
					$output['points'] = (int)$_POST['points'];
				}
				if(isset($_POST['cloze_text'])){
					$output['cloze_text'] = $_POST['cloze_text'];
				}
				
				$output['nbAnswer'] = $_POST['nbAnswer'];
				for($i=0; $i < $output['nbAnswer']; $i++){
					if(!empty($_POST['reponses'][$i]['id'])){
						$answers[$i]['id'] = $_POST['reponses'][$i]['id'];
					} else {
						$answers[$i]['id'] = 0;
					}
					if(!empty($_POST['reponses'][$i]['answer'])){
						$answers[$i]['answer'] = $_POST['reponses'][$i]['answer'];
					} else {
						$answers[$i]['answer'] = "";
					}
				}
				$output['answers'] = $answers;
				
				$this->display_view('cloze_text/add', $output);
			}
		} else {
			$output['focus_topic'] = $this->topic_model->get($_POST['focus_topic']);
			$output['question_type'] = $this->question_type_model->get($_POST['question_type']);

			if(isset($_POST['id'])){
				$output['id'] = $_POST['id'];
			}
			if(isset($_POST['id_cloze_text'])){
				$output['id_cloze_text'] = $_POST['id_cloze_text'];
			}
			if(isset($_POST['name'])){
				$output['name'] = $_POST['name'];
			}
			if(isset($_POST['points'])){
				$output['points'] = (int)$_POST['points'];
			}
			if(isset($_POST['cloze_text'])){
				$output['cloze_text'] = $_POST['cloze_text'];
			}

			$output['nbAnswer'] = $_POST['nbAnswer'];
			

			for($i=0; $i < $output['nbAnswer']; $i++){
				if(!empty($_POST['reponses'][$i]['id'])){
					$answers[$i]['id'] = $_POST['reponses'][$i]['id'];
				} else {
					$answers[$i]['id'] = 0;
				}
				if(!empty($_POST['reponses'][$i]['answer'])){
					$answers[$i]['answer'] = $_POST['reponses'][$i]['answer'];
				} else {
					$answers[$i]['answer'] = "";
				}
			}

			if (isset($_POST['add_answer'])){
				$answers[$i]['id'] = 0;
				$answers[$i]['answer'] = "";
				$output['nbAnswer'] = $output['nbAnswer']+1;
			}

			for($i=0; $i < $output['nbAnswer']; $i++){
				if (isset($_POST['del_answer'.$i])){
					if($output['nbAnswer']>1){
						$output['nbAnswer'] = $_POST['nbAnswer']-1;
						for($j=$i; $j < $output['nbAnswer']; $j++){
							$answers[$j] = $answers[$j+1];
						}
					} else {
						$output['nbAnswer'] = $_POST['nbAnswer'];
					}
				}
			}

			$output['answers'] = $answers;
		
			$this->display_view('cloze_text/add', $output);
		}
	} 
	
	/**
	 * Function for save free answer
	 */
	public function add_FreeAnswer()
	{
		if (!isset($_POST['id'])){
			if (isset($_POST['cancel'])){
				redirect('/Question');
			}
			if (isset($_POST['save'])){
				$_SESSION['filtres'] = "Question?module=&topic=&type=";
				$this->form_validation->set_rules('name', $this->lang->line('question_text'), 'required');
				$this->form_validation->set_rules('points', $this->lang->line('points'), 'required');
				$this->form_validation->set_rules('answer', $this->lang->line('answer_question_add'), 'required');
				
				if ($this->form_validation->run()){
					$inputQuestion = array(
						"FK_Topic" => $_POST['focus_topic'],
						"FK_Question_Type" => $_POST['question_type'],
						"Question" => $_POST['name'],
						"Points" => $_POST['points']
					);
					$idQuestion = $this->question_model->insert($inputQuestion);
					
					$inputAnswer = array(
						"FK_Question" => $idQuestion,
						"Answer" => $_POST['answer']
					);
					$this->free_answer_model->insert($inputAnswer);
					
					redirect('/Question');
				} else {
					$output['focus_topic'] = $this->topic_model->get($_POST['focus_topic']);
					$output['question_type'] = $this->question_type_model->get($_POST['question_type']);
					if(isset($_POST['name'])){
						$output['name'] = $_POST['name'];
					}
					if(isset($_POST['points'])){
						$output['points'] = (int)$_POST['points'];
					}
					if(isset($_POST['answer'])){
						$output['answer'] = $_POST['answer'];
					}
					$this->display_view('free_answers/add', $output);
				}
			}
		} else {
			if (isset($_POST['cancel'])){
				redirect('/Question');
			}
			if (isset($_POST['save'])){
				$_SESSION['filtres'] = "Question?module=&topic=&type=";
				$this->form_validation->set_rules('name', $this->lang->line('question_text'), 'required');
				$this->form_validation->set_rules('points', $this->lang->line('points'), 'required');
				$this->form_validation->set_rules('answer', $this->lang->line('answer_question_add'), 'required');
				
				if ($this->form_validation->run()){
					$id = $this->input->post('id');
					
					$inputQuestion = array(
						"FK_Topic" => $_POST['focus_topic'],
						"FK_Question_Type" => $_POST['question_type'],
						"Question" => $_POST['name'],
						"Points" => $_POST['points']
					);
					$this->question_model->update($id, $inputQuestion);
					
					$inputAnswer = array(
						"FK_Question" => $id,
						"Answer" => $_POST['answer']
					);
					$this->free_answer_model->update($_POST['id_answer'], $inputAnswer);
					
					redirect('/Question');
				} else {
					$output['id'] = $_POST['id'];
					$output['id_answer'] = $_POST['id_answer'];
					$output['focus_topic'] = $this->topic_model->get($_POST['focus_topic']);
					$output['question_type'] = $this->question_type_model->get($_POST['question_type']);
					if(isset($_POST['name'])){
						$output['name'] = $_POST['name'];
					}
					if(isset($_POST['points'])){
						$output['points'] = (int)$_POST['points'];
					}
					if(isset($_POST['answer'])){
						$output['answer'] = $_POST['answer'];
					}
					$this->display_view('free_answers/update', $output);
				}
			}
		}
	}
	
	/**
	 * Function for save picture landmark
	 */
	public function add_PictureLandmark($step=1)
	{
		if ($step==1){
			if(isset($_POST['focus_topic'])){
				$output['focus_topic'] = $this->topic_model->get($_POST['focus_topic']);
			}
			if(isset($_POST['question_type'])){
				$output['question_type'] = $this->question_type_model->get($_POST['question_type']);
			}
			if(isset($_POST['id'])){
				$output['id'] = $_POST['id'];
			}
			if(isset($_POST['name'])){
				$output['name'] = $_POST['name'];
			}
			if(isset($_POST['points'])){
				$output['points'] = (int)$_POST['points'];
			}
			
			if(isset($_POST['picture_name'])){
				$output['picture_name'] = $_POST['picture_name'];
			}
			if(isset($_POST['upload_data'])){
				$output['upload_data'] = $_POST['upload_data'];
			}

			if(isset($_POST['nbAnswer'])){
				$output['nbAnswer'] = $_POST['nbAnswer'];
			}

			for($i=0; $i < $output['nbAnswer']; $i++){
				if(!empty($_POST['reponses'][$i]['id'])){
					$answers[$i]['id'] = $_POST['reponses'][$i]['id'];
				} else {
					$answers[$i]['id'] = 0;
				}
				if(!empty($_POST['reponses'][$i]['symbol'])){
					$answers[$i]['symbol'] = $_POST['reponses'][$i]['symbol'];
				} else {
					$answers[$i]['symbol'] = "";
				}
				if(!empty($_POST['reponses'][$i]['answer'])){
					$answers[$i]['answer'] = $_POST['reponses'][$i]['answer'];
				} else {
					$answers[$i]['answer'] = "";
				}
			}

			$output['answers'] = $answers;

			if (isset($_POST['cancel']) & isset($_POST['id'])){
				$this->display_view('picture_landmark/add', $output);
			} elseif (isset($_POST['cancel']) & !isset($_POST['id'])){
				redirect('/Question');
			} else {
				if(isset($_FILES['picture'])) {
					$config['upload_path']          = './uploads/pictures';
					$config['allowed_types']        = 'gif|jpg|jpeg|png';
					$config['max_size']				= '2048';
					if(isset($_POST['id'])){
						$config['file_name']			= $_POST['id']."_".$_FILES['picture']['name'];
					} else {
						$config['file_name']			= $this->question_model->get_next_id()."_".$_FILES['picture']['name'];
					}

					$this->upload->initialize($config);

					if ( ! $this->upload->do_upload('picture'))
					{
							$output['error'] = $this->upload->display_errors();
							$this->display_view('picture_landmark/file', $output);
					}
					else
					{
							$output['upload_data'] = $this->upload->data();
							
							$this->display_view('picture_landmark/add', $output);
					}
				}
			}
		} elseif($step==2){
			if (isset($_POST['cancel'])){
				redirect('/Question');
			} elseif (isset($_POST['save'])){
				$this->form_validation->set_rules('name', $this->lang->line('question_text'), 'required');
				$this->form_validation->set_rules('points', $this->lang->line('points'), 'required');
				for($i=0; $i < $_POST['nbAnswer']; $i++){
					$this->form_validation->set_rules('reponses['.$i.'][symbol]', $this->lang->line('landmark'), 'required');
					$this->form_validation->set_rules('reponses['.$i.'][answer]', $this->lang->line('answers_list'), 'required');
				}

				if ($this->form_validation->run()){
					if(!isset($_POST['id'])){
						$inputQuestion = array(
							"FK_Topic" => $_POST['focus_topic'],
							"FK_Question_Type" => $_POST['question_type'],
							"Question" => $_POST['name'],
							"Points" => $_POST['points'],
							"Picture_Name" => $_POST['upload_data']['file_name']
						);
						$idQuestion = $this->question_model->insert($inputQuestion);

						$list = scandir("./uploads/pictures");
						foreach ($list as $file) {
							if(strpos($file, $idQuestion."_") !== false && $file != $_POST['upload_data']['file_name']){
								unlink("uploads/pictures/".$file);
							}
						}

						for($i=0; $i < $_POST['nbAnswer']; $i++){
							$inputAnswer = array(
								"FK_Question" => $idQuestion,
								"Symbol" => $_POST['reponses'][$i]['symbol'],
								"Answer" => $_POST['reponses'][$i]['answer']
							);
							$this->picture_landmark_model->insert($inputAnswer);
						}
					} else {
						if(isset($_POST['upload_data'])){
							$inputQuestion = array(
								"FK_Topic" => $_POST['focus_topic'],
								"FK_Question_Type" => $_POST['question_type'],
								"Question" => $_POST['name'],
								"Points" => $_POST['points'],
								"Picture_Name" => $_POST['upload_data']['file_name']
							);
						} else {
							$inputQuestion = array(
								"FK_Topic" => $_POST['focus_topic'],
								"FK_Question_Type" => $_POST['question_type'],
								"Question" => $_POST['name'],
								"Points" => $_POST['points'],
								"Picture_Name" => $_POST['picture_name']
							);
						}
						$this->question_model->update($_POST['id'], $inputQuestion);

						$list = scandir("./uploads/pictures");
						foreach ($list as $file) {
							if(strpos($file, $_POST['id']."_") !== false && $file != $_POST['upload_data']['file_name']){
								unlink("uploads/pictures/".$file);
							}
						}

						for($i=0; $i < $_POST['nbAnswer']; $i++){
							$answers[$i]['id'] = $_POST['reponses'][$i]['id'];
							$answers[$i]['symbol'] = $_POST['reponses'][$i]['symbol'];
							$answers[$i]['answer'] = $_POST['reponses'][$i]['answer'];
						}

						$reponses = $this->picture_landmark_model->get_many_by('FK_Question = ' . $_POST['id']);
						$i = 0;
						foreach ($reponses as $reponse) {
							$answersDb[$i] = $reponse->ID;
							$i++;
						}

						for($i=0; $i < $_POST['nbAnswer']; $i++){
							if($answers[$i]['id'] == 0){
								$inputQuestion = array(
									"FK_Question" => $_POST['id'],
									"Symbol" => $_POST['reponses'][$i]['symbol'],
									"Answer" => $_POST['reponses'][$i]['answer']
								);
								$idQuestion = $this->picture_landmark_model->insert($inputQuestion);
							} else {
								$inputAnswer = array(
									"FK_Question" => $_POST['id'],
									"Symbol" => $_POST['reponses'][$i]['symbol'],
									"Answer" => $_POST['reponses'][$i]['answer']
								);
								$this->picture_landmark_model->update($answers[$i]['id'], $inputAnswer);

								unset($answersDb[array_search($answers[$i]['id'], $answersDb)]);
							}
						}

						foreach ($answersDb as $answerDb) {
							$this->picture_landmark_model->delete($answerDb);
						}
					}
					redirect('/Question');
				} else {
					$output['focus_topic'] = $this->topic_model->get($_POST['focus_topic']);
					$output['question_type'] = $this->question_type_model->get($_POST['question_type']);
					
					if(isset($_POST['id'])){
						$output['id'] = $_POST['id'];
					}
					if(isset($_POST['name'])){
						$output['name'] = $_POST['name'];
					}
					if(isset($_POST['points'])){
						$output['points'] = (int)$_POST['points'];
					}
					
					if(isset($_POST['picture_name'])){
						$output['picture_name'] = $_POST['picture_name'];
					}
					if(isset($_POST['upload_data'])){
						$output['upload_data'] = $_POST['upload_data'];
					}

					for($i=0; $i < $output['nbAnswer']; $i++){
						if(!empty($_POST['reponses'][$i]['id'])){
							$answers[$i]['id'] = $_POST['reponses'][$i]['id'];
						} else {
							$answers[$i]['id'] = 0;
						}
						if(!empty($_POST['reponses'][$i]['symbol'])){
							$answers[$i]['symbol'] = $_POST['reponses'][$i]['symbol'];
						} else {
							$answers[$i]['symbol'] = "";
						}
						if(!empty($_POST['reponses'][$i]['answer'])){
							$answers[$i]['answer'] = $_POST['reponses'][$i]['answer'];
						} else {
							$answers[$i]['answer'] = "";
						}
					}
					$output['answers'] = $answers;
					$this->display_view('picture_landmark/add', $output);
				}
			} else {
				$output['focus_topic'] = $this->topic_model->get($_POST['focus_topic']);
				$output['question_type'] = $this->question_type_model->get($_POST['question_type']);

				if(isset($_POST['id'])){
					$output['id'] = $_POST['id'];
				}
				if(isset($_POST['name'])){
					$output['name'] = $_POST['name'];
				}
				if(isset($_POST['points'])){
					$output['points'] = (int)$_POST['points'];
				}

				if(isset($_POST['picture_name'])){
					$output['picture_name'] = $_POST['picture_name'];
				}
				if(isset($_POST['upload_data'])){
					$output['upload_data'] = $_POST['upload_data'];
				}

				$output['nbAnswer'] = $_POST['nbAnswer'];
				

				for($i=0; $i < $output['nbAnswer']; $i++){
					if(!empty($_POST['reponses'][$i]['id'])){
						$answers[$i]['id'] = $_POST['reponses'][$i]['id'];
					} else {
						$answers[$i]['id'] = 0;
					}
					if(!empty($_POST['reponses'][$i]['symbol'])){
						$answers[$i]['symbol'] = $_POST['reponses'][$i]['symbol'];
					} else {
						$answers[$i]['symbol'] = "";
					}
					if(!empty($_POST['reponses'][$i]['answer'])){
						$answers[$i]['answer'] = $_POST['reponses'][$i]['answer'];
					} else {
						$answers[$i]['answer'] = "";
					}
				}

				if (isset($_POST['add_answer'])){
					$answers[$i]['id'] = 0;
					$answers[$i]['symbol'] = "";
					$answers[$i]['answer'] = "";
					$output['nbAnswer'] = $output['nbAnswer']+1;
				}

				for($i=0; $i < $output['nbAnswer']; $i++){
					if (isset($_POST['del_answer'.$i])){
						if($output['nbAnswer']>1){
							$output['nbAnswer'] = $_POST['nbAnswer']-1;
							for($j=$i; $j < $output['nbAnswer']; $j++){
								$answers[$j] = $answers[$j+1];
							}
						} else {
							$output['nbAnswer'] = $_POST['nbAnswer'];
						}
					}
				}

				$output['answers'] = $answers;

				if(isset($_POST['change_picture'])){
					$this->display_view('picture_landmark/file', $output);
				} else {
					$this->display_view('picture_landmark/add', $output);
				}
			}
		}
	}
	
	/**
	 * ON BUILDING
	 * Useful to import all questions already written on Excel
	 */
	public function import()
	{

		if(isset($_POST['submitExcel']))
		{
			$config['upload_path'] = './uploads/excel';
			$config['allowed_types'] = 'xlsx';
			$config['max_size'] = 100;
			$config['max_width'] = 1024;
			$config['max_height'] = 768;

			$this->load->library('upload', $config);
			$this->upload->initialize($config);

			if (!$this->upload->do_upload('excelfile')) {
				$error = array('error' => $this->upload->display_errors());
				var_dump($error);

			} else {

				$data = array('upload_data' => $this->upload->data());

				$inputFileName = $data['upload_data']['full_path'];

				$idTopic = $this->input->post('topic_selected');

				$inputFileType = 'Excel2007';

				/**  Create a new Reader of the type defined in $inputFileType  **/
				$objReader = PHPExcel_IOFactory::createReader($inputFileType);

				$this->Import_MultipleChoices($idTopic, $objReader, $inputFileName);
				$this->Import_MultipleAnswers($idTopic, $objReader, $inputFileName);
				$this->Import_AnswerDistribution($idTopic, $objReader, $inputFileName);
				$this->Import_ClozeText($idTopic, $objReader, $inputFileName);
				$this->Import_TableCell($idTopic, $objReader, $inputFileName);
				$this->Import_FreeAnswer($idTopic, $objReader, $inputFileName);
				$this->Import_PictureLandmark($idTopic, $objReader, $inputFileName);

				redirect("./Question?");

			}


			if (isset($_FILES['excelfile'])) {
				if ($_FILES['excelfile']['error'] == 0 &&
					$_FILES['excelfile']['type'] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
				) {

				} else {
					$output['error'] = true;
					$output['questions'] = $this->question_model->with_all()->get_all();
					$output['topics'] = $this->topic_model->get_all();
					$this->display_view('questions/import', $output);
				}
			} else {
				$output['questions'] = $this->question_model->with_all()->get_all();
				$output['topics'] = $this->topic_model->get_all();
				$this->display_view('questions/import', $output);
			}

		}else if(isset($_POST['submitPictures']))
		{

			$files = $_FILES;
			$countfile = count($_FILES['picturesfile']['name']);

			for($i = 0; $i < $countfile; $i++)
			{
				//More optimal object
				$_FILES['picturesfile']['name'] = $files['picturesfile']['name'][$i];
				$_FILES['picturesfile']['type'] = $files['picturesfile']['type'][$i];
				$_FILES['picturesfile']['tmp_name'] = $files['picturesfile']['tmp_name'][$i];
				$_FILES['picturesfile']['error'] = $files['picturesfile']['error'][$i];
				$_FILES['picturesfile']['size'] = $files['picturesfile']['size'][$i];

				$config['upload_path'] = './uploads/pictures';
				$config['allowed_types'] = 'png|jpg|jpeg';
				$config['max_size'] = 0;

				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if (!$this->upload->do_upload('picturesfile'))
				{
					$error = array('error' => $this->upload->display_errors());
				}
			}

			if(!isset($error))
			{
				redirect("./Question/import");
			}
		}else
		{
			$output['questions'] = $this->question_model->with_all()->get_all();
			$output['topics'] = $this->topic_model->get_tree();
			$this->display_view('questions/import', $output);
		}

	}


	/**
	 * Import 'Multiple_Choice' question type by Excel sheet 'ChoixMultiple'
	 * @param $idTopic = the topic select on the select attribute
	 * @param $objReader = the object that allow we to read the excel sheet
	 * @param $inputFileName = the name of sheet 'ChoixMultiple'
	 */
	private function Import_MultipleChoices($idTopic, $objReader, $inputFileName)
	{
		$sheetName = 'ChoixMultiples';
		$questionType = 1;

		/**  Advise the Reader of which WorkSheets we want to load  **/
		$objReader->setLoadSheetsOnly($sheetName);
		$objReader->setReadDataOnly(true);
		/**  Load $inputFileName to a PHPExcel Object  **/
		$objPHPExcel = $objReader->load($inputFileName);

		foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
		{

			$column = 0;
			$row = 3;

			//Browse the sheet
			//0 is the start column
			while($worksheet->getCellByColumnAndRow($column, $row)->getValue() != NULL)
			{
				//Current question
				$question = $worksheet->getCellByColumnAndRow($column, $row)->getValue();

				//Data to insert to the table 'T_Question'
				$inputQuestion = array(
								"FK_Topic" => $idTopic,
								"FK_Question_Type" => $questionType,
								"Question" => $question,
								"Creation_Date" => date("Y-m-d H:i:s")
				);

				$idQuestion = $this->question_model->insert($inputQuestion);

				//Take next to the question the data to insert to 'T_Multiple_Choice'
				while($worksheet->getCellByColumnAndRow($column + 1, $row)->getValue() != NULL)
				{
					$answerField = $worksheet->getCellByColumnAndRow($column + 1, $row)->getValue();
					$validField = $worksheet->getCellByColumnAndRow($column + 1, $row + 1)->getValue();

					if($validField == "x")$valid = true;
					else $valid = false;
					
					$inputMultipleChoice = array(
						"FK_Question" => $idQuestion,
						"Answer" => $answerField,
						"Valid" => $valid,
						"Creation_Date" => date("Y-m-d H:i:s")
					);

					$this->multiple_choice_model->insert($inputMultipleChoice);
					$column += 1;
				}
				$row += 2;
				$column = 0;
			}
		}
	}

	/**
	 * Import 'Multiple_Answer' question type by Excel sheet 'ReponsesMultiple'
	 * @param $idTopic = the topic select on the select attribute
	 * @param $objReader = the object that allow we to read the excel sheet
	 * @param $inputFileName = the name of sheet 'ReponsesMultiple'
	 */
	private function Import_MultipleAnswers($idTopic, $objReader, $inputFileName)
	{
		$sheetName = 'ReponsesMultiples';
		$questionType = 2;

		/**  Advise the Reader of which WorkSheets we want to load  **/
		$objReader->setLoadSheetsOnly($sheetName);
		$objReader->setReadDataOnly(true);
		/**  Load $inputFileName to a PHPExcel Object  **/
		$objPHPExcel = $objReader->load($inputFileName);

		foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
		{
			$column = 0;
			$row = 3;

			//Browse the sheet
			//0 is the start column
			while($worksheet->getCellByColumnAndRow($column, $row)->getValue() != NULL)
			{
				//Current question
				$question = $worksheet->getCellByColumnAndRow($column, $row)->getValue();
				//Nb of answers needed
				$nbAnswerDesired = $worksheet->getCellByColumnAndRow($column + 1, $row)->getValue();
				//Nb of points of the question
				$nbPoints = $worksheet->getCellByColumnAndRow($column + 2, $row)->getValue();

				//Data to insert to the table 'T_Question'
				$inputQuestion = array(
					"FK_Topic" => $idTopic,
					"FK_Question_Type" => $questionType,
					"Question" => $question,
					"Nb_Desired_Answers" => $nbAnswerDesired,
					"Points" => $nbPoints,
					"Creation_Date" => date("Y-m-d H:i:s")
				);

				$idQuestion = $this->question_model->insert($inputQuestion);

				$column += 3;

				//Take next to the question the data to insert to 'T_Multiple_Answer'
				while($worksheet->getCellByColumnAndRow($column, $row)->getValue() != NULL)
				{
					$answer = $worksheet->getCellByColumnAndRow($column, $row)->getValue();

					$inputMultipleAnswer = array(
						"FK_Question" => $idQuestion,
						"Answer" => $answer,
						"Creation_Date" => date("Y-m-d H:i:s")
					);

					$this->multiple_answer_model->insert($inputMultipleAnswer);

					$column += 1;
				}

				$column = 0;
				$row++;
			}

		}

	}

	/**
	 * Import 'Answer_Distribution' question type by Excel sheet 'DistributionReponses'
	 * @param $idTopic = the topic select on the select attribute
	 * @param $objReader = the object that allow we to read the excel sheet
	 * @param $inputFileName = the name of sheet 'DistributionReponses'
	 */
	private function Import_AnswerDistribution($idTopic, $objReader, $inputFileName)
	{
		$sheetName = 'DistributionReponses';
		$questionType = 3;

		/**  Advise the Reader of which WorkSheets we want to load  **/
		$objReader->setLoadSheetsOnly($sheetName);
		$objReader->setReadDataOnly(true);
		/**  Load $inputFileName to a PHPExcel Object  **/
		$objPHPExcel = $objReader->load($inputFileName);

		foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
		{
			$column = 1;
			$row = 3;

			while($worksheet->getCellByColumnAndRow($column, $row)->getValue() != NULL)
			{
				if($worksheet->getCellByColumnAndRow($column - 1, $row)->getValue() != NULL)
				{
					//Current question
					$question = $worksheet->getCellByColumnAndRow($column - 1, $row)->getValue();

					//Data to insert to the table 'T_Question'
					$inputQuestion = array(
						"FK_Topic" => $idTopic,
						"FK_Question_Type" => $questionType,
						"Question" => $question,
						"Creation_Date" => date("Y-m-d H:i:s")
					);

					$idQuestion = $this->question_model->insert($inputQuestion);
				}

				$questionElement = $worksheet->getCellByColumnAndRow($column, $row)->getValue();
				$answerElement = $worksheet->getCellByColumnAndRow($column + 1, $row)->getValue();

				$inputAnswerDistribution = array(
					"FK_Question" => $idQuestion,
					"Question_Part" => $questionElement,
					"Answer_Part" => $answerElement,
					"Creation_Date" => date("Y-m-d H:i:s")
				);

				$this->answer_distribution_model->insert($inputAnswerDistribution);

				$row++;
			}

		}
	}

	/**
	 * Import 'Cloze_Text' question type by Excel sheet 'TexteATrous'
	 * @param $idTopic = the topic select on the select attribute
	 * @param $objReader = the object that allow we to read the excel sheet
	 * @param $inputFileName = the name of sheet 'TexteATrous'
	 */
	private function Import_ClozeText($idTopic, $objReader, $inputFileName)
	{
		$sheetName = 'TexteATrous';
		$questionType = 4;

		/**  Advise the Reader of which WorkSheets we want to load  **/
		$objReader->setLoadSheetsOnly($sheetName);
		$objReader->setReadDataOnly(true);
		/**  Load $inputFileName to a PHPExcel Object  **/
		$objPHPExcel = $objReader->load($inputFileName);

		foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
		{
			$column = 1;
			$row = 3;
			$answerOrder = 0;

			while($worksheet->getCellByColumnAndRow($column, $row)->getValue() != NULL)
			{
				if($worksheet->getCellByColumnAndRow($column - 1, $row)->getValue() != NULL)
				{
					//Current question
					$question = $worksheet->getCellByColumnAndRow($column - 1, $row)->getValue();

					//Data to insert to the table 'T_Question'
					$inputQuestion = array(
						"FK_Topic" => $idTopic,
						"FK_Question_Type" => $questionType,
						"Question" => $question,
						"Creation_Date" => date("Y-m-d H:i:s")
					);

					$idQuestion = $this->question_model->insert($inputQuestion);
				}

				$clozeText = $worksheet->getCellByColumnAndRow($column, $row)->getValue();

				$inputClozeText = array(
					"FK_Question" => $idQuestion,
					"Cloze_Text" => $clozeText,
					"Creation_Date" => date("Y-m-d H:i:s")
				);

				$idClozeText = $this->cloze_text_model->insert($inputClozeText);

				$column = 2;

				while($worksheet->getCellByColumnAndRow($column, $row)->getValue() != NULL)
				{
					$answerOrder++;
					$answer = $worksheet->getCellByColumnAndRow($column, $row)->getValue();

					$inputClozeTextAnswer = array(
						"FK_Cloze_Text" => $idClozeText,
						"Answer" => $answer,
						"Answer_Order" => $answerOrder,
						"Creation_Date" => date("Y-m-d H:i:s")
					);

					$this->cloze_text_answer_model->insert($inputClozeTextAnswer);

					$column++;
				}

				$answerOrder = 0;
				$row++;
				$column = 1;
			}


		}
	}

	/**
	 * Import 'Table_Cell' question type by Excel sheet 'Tableaux'
	 * @param $idTopic = the topic select on the select attribute
	 * @param $objReader = the object that allow we to read the excel sheet
	 * @param $inputFileName = the name of sheet 'Tableaux'
	 */
	private function Import_TableCell($idTopic, $objReader, $inputFileName)
	{
		$sheetName = 'Tableaux';
		$questionType = 5;
		$tableDefinition = false;

		/**  Advise the Reader of which WorkSheets we want to load  **/
		$objReader->setLoadSheetsOnly($sheetName);

		/**  Load $inputFileName to a PHPExcel Object  **/
		$objPHPExcel = $objReader->load($inputFileName);

		foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
		{
			$column = 1;
			$row = 3;
			$regPattern = '/^\[.*\]$/';

			while($worksheet->getCellByColumnAndRow($column, $row)->getValue() != NULL)
			{
				if($worksheet->getCellByColumnAndRow($column - 1, $row)->getValue() != NULL &&
					$worksheet->getCellByColumnAndRow($column - 1, $row + 1)->getValue() != NULL)
				{
					//Current question
					$question = $worksheet->getCellByColumnAndRow($column - 1, $row)->getValue();
					$tableType = $worksheet->getCellByColumnAndRow($column - 1, $row + 1)->getValue();

					if($tableType == 'simple')
						$tableDefinition = false;
					else
						$tableDefinition = true;


					//Data to insert to the table 'T_Question'
					$inputQuestion = array(
						"FK_Topic" => $idTopic,
						"FK_Question_Type" => $questionType,
						"Question" => $question,
						"Table_With_Definition" => $tableDefinition,
						"Creation_Date" => date("Y-m-d H:i:s")
					);

					$idQuestion = $this->question_model->insert($inputQuestion);

					$nbColumn = 1;
					$nbRow = 1;
					$d = 1;
				}

				while($worksheet->getCellByColumnAndRow($column, $row)->getValue() != NULL) {

					$tableCell = $worksheet->getCellByColumnAndRow($column, $row)->getValue();

					//Test if the field is in bold
					if ($worksheet->getCellByColumnAndRow($column, $row)->getStyle()->getFont()->getBold())
					{
						$header = true;
					}else
					{
						$header = false;
					}

					//Test if the field need to be displayed
					if (preg_match($regPattern, $tableCell))
						$displayOnQuestion = true;
					else
						$displayOnQuestion = false;

					$inputTableCell = array(
						"FK_Question" => $idQuestion,
						"Content" => $tableCell,
						"Column_Nb" => $nbColumn,
						"Row_Nb" => $nbRow,
						"Header" => $header,
						"Display_In_Question" => $displayOnQuestion,
						"Creation_Date" => date("Y-m-d H:i:s")
					);

					$this->table_cell_model->insert($inputTableCell);

					$nbColumn++;
					$column++;
				}

				$nbColumn = 1;
				$nbRow++;
				$row++;
				$column = 1;
			}
		}
	}

	/**
	 * Import 'Free_Answer' question type by Excel sheet 'ReponsesLibre'
	 * @param $idTopic = the topic select on the select attribute
	 * @param $objReader = the object that allow we to read the excel sheet
	 * @param $inputFileName = the name of sheet 'ReponsesLibre'
	 */
	private function Import_FreeAnswer($idTopic, $objReader, $inputFileName)
	{
		$sheetName = 'ReponsesLibre';
		$questionType = 6;


		/**  Advise the Reader of which WorkSheets we want to load  **/
		$objReader->setLoadSheetsOnly($sheetName);
		$objReader->setReadDataOnly(true);
		/**  Load $inputFileName to a PHPExcel Object  **/
		$objPHPExcel = $objReader->load($inputFileName);

		foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
			$column = 0;
			$row = 3;


			while ($worksheet->getCellByColumnAndRow($column, $row)->getValue() != NULL) {

				$question = $worksheet->getCellByColumnAndRow($column, $row)->getValue();

				if($worksheet->getCellByColumnAndRow($column + 2, $row)->getValue() != NULL)
				{
					$nbPoints = $worksheet->getCellByColumnAndRow($column + 2, $row)->getValue();
				}else
				{
					$nbPoints = 1;
				}

				$inputQuestion = array(
					"FK_Topic" => $idTopic,
					"FK_Question_Type" => $questionType,
					"Question" => $question,
					"Points" => $nbPoints,
					"Creation_Date" => date("Y-m-d H:i:s")
				);

				$idQuestion = $this->question_model->insert($inputQuestion);
				$answer = $worksheet->getCellByColumnAndRow($column + 1, $row)->getValue();

				$inputFreeAnswer = array(
					"FK_Question" => $idQuestion,
					"Answer" => $answer,
					"Creation_Date" => date("Y-m-d H:i:s")
				);

				$this->free_answer_model->insert($inputFreeAnswer);

				$row++;
			}
		}
	}

	/**
	 * Import 'Picture_Landmark' question type by Excel sheet 'ImageReperes'
	 * @param $idTopic = the topic select on the select attribute
	 * @param $objReader = the object that allow we to read the excel sheet
	 * @param $inputFileName = the name of sheet 'ImageReperes'
	 */
	private function Import_PictureLandmark($idTopic, $objReader, $inputFileName)
	{
		$sheetName = 'ImageReperes';
		$questionType = 7;
		$salt = 'f56ih58g0e';


		/**  Advise the Reader of which WorkSheets we want to load  **/
		$objReader->setLoadSheetsOnly($sheetName);
		$objReader->setReadDataOnly(true);
		/**  Load $inputFileName to a PHPExcel Object  **/
		$objPHPExcel = $objReader->load($inputFileName);

		foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
			$column = 0;
			$row = 3;

			while ($worksheet->getCellByColumnAndRow($column, $row)->getValue() != NULL) {
				$question = $worksheet->getCellByColumnAndRow($column, $row)->getValue();
				//$pictureName = uniqid($salt).$worksheet->getCellByColumnAndRow($column + 1, $row)->getValue();
				$pictureName = $worksheet->getCellByColumnAndRow($column + 1, $row)->getValue();

				$inputQuestion = array(
					"FK_Topic" => $idTopic,
					"FK_Question_Type" => $questionType,
					"Question" => $question,
					"Picture_Name" => $pictureName,
					"Creation_Date" => date("Y-m-d H:i:s")
				);

				$idQuestion = $this->question_model->insert($inputQuestion);

				$column = 2;

				while ($worksheet->getCellByColumnAndRow($column, $row)->getValue() != NULL)
				{
					$symbol = $worksheet->getCellByColumnAndRow($column, $row)->getValue();
					$answer = $worksheet->getCellByColumnAndRow($column, $row + 1)->getValue();

					$inputPictureLandmarks = array(
						"FK_Question" => $idQuestion,
						"Symbol" => $symbol,
						"Answer" => $answer,
						"Creation_Date" => date("Y-m-d H:i:s")
					);

					$this->picture_landmark_model->insert($inputPictureLandmarks);

					$column++;
				}

				$column = 0;
				$row += 2;
			}
		}
	}
}