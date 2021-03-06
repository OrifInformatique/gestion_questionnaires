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
	/**
	 * Constructor
	 */
	public function __construct()
	{   
    	$this->config->load(to_test_path('user/MY_user_config'));
        $this->access_level = $this->config->item('access_lvl_registered');
        parent::__construct();

        $this->load->model(array('question_questionnaire_model', 'questionnaire_model', 'question_model',
                                 'question_type_model', 'topic_model', 'multiple_choice_model',
                                 'multiple_answer_model', 'answer_distribution_model', 'cloze_text_model',
                                 'cloze_text_answer_model', 'table_cell_model', 'free_answer_model', 'picture_landmark_model'));
        $this->form_validation->CI =& $this;
	}

	/**
	 * Display question list
	 */
	public function index($page = 1)
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

		$where = "Archive = 0";

		if(!empty($_GET['topic'])){
			$where .= " AND FK_Topic = ".$_GET['topic'];
		}
		if(!empty($_GET['type'])){
			$where .= " AND FK_Question_Type = ".$_GET['type'];
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

			$where .= " AND (".$listIdQuestion.")";
		}

		if(!empty($_GET['search'])){
			$where .= ' AND Question LIKE "%'.$_GET['search'].'%"';
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

	    if(($page - 1) * ITEMS_PER_PAGE > count($output['questions'])) {
	    	redirect('question/index/1');
	    }

		$this->load->library('pagination');

		$config = array(
			'base_url' => base_url('/question/index/'),
			'total_rows' => count($output['questions']),
			'per_page' => ITEMS_PER_PAGE,
			'use_page_numbers' => TRUE,
			'reuse_query_string' => TRUE,

			'full_tag_open' => '<ul class="pagination">',
			'full_tag_close' => '</ul>',

			'first_link' => '&laquo;',
			'first_tag_open' => '<li class="page-item">',
			'first_tag_close' => '</li>',

			'last_link' => '&raquo;',
			'last_tag_open' => '<li class="page-item">',
			'last_tag_close' => '</li>',

			'next_link' => FALSE,
			'prev_link' => FALSE,

			'cur_tag_open' => '<li class="page-item active"><a class="page-link" href="#">',
			'cur_tag_close' => '</a></li>',
			'num_links' => 5,

			'num_tag_open' => '<li class="page-item">',
			'num_tag_close' => '</li>',
			'attributes' => ['class' => 'page-link']
		);

		$this->pagination->initialize($config);

		$output = array(
			'title' => $this->lang->line('title_question'),
			'pagination' => $this->pagination->create_links(),
			'questions' => array_slice($output['questions'], ($page-1)*ITEMS_PER_PAGE, ITEMS_PER_PAGE),
			'topics' => $this->topic_model->get_many_by('Archive IS NULL OR Archive = 0'),
			'questionTypes' => $this->question_type_model->get_all()
		);

		$this->display_view('questions/index', $output);
	}

	/**
	 * Resets the index filters stored in $_SESSION['filtres']
	 * and redirects the user to index
	 */
	public function reset_filters() {
		unset($_SESSION['filtres']);
		redirect('question');
	}

	/**
	 * @param int $id = id of the question
	 * Delete selected question
	 */
	public function delete($id = 0, $action = NULL)
	{
		$question = $this->question_model->get($id);
		if(is_null($question)) {
			redirect('question');
		} elseif (is_null($action)) {
			$output = get_object_vars($question);
			$output["question"] = $this->question_model->get_all();
			$output["title"] = $this->lang->line("delete_question");
			$this->display_view("questions/confirm", $output);
		} else {
			$this->question_model->delete($id);
			redirect('question');
		}
	}

	/**
	 * Form validation to update question
	 */
	public function form_update()
	{
		$this->form_validation->set_rules('name', 'Title', 'required');
		$this->form_validation->set_rules('points', 'Points', 'required');
		$this->form_validation->set_rules(
			'id', 'Id', 'callback_cb_question_exists',
			['callback_cb_question_exists' => 'lang:question_error_404_heading']
		);

		$id = $this->input->post('id');
		$title = array(
			'Question' => $this->input->post('name'),
			'Points' => $this->input->post('points')
		);

		if ($this->form_validation->run()) {
			$this->question_model->update($id, $title);
			redirect('question');
		} else {
			$this->update($id, 1);
		}
	}

	/**
	 * Checks that a question exists
	 *
	 * @param int $idQuestion = ID of the question
	 * @return boolean = TRUE if it exists, FALSE otherwise
	 */
	public function cb_question_exists($idQuestion) : bool
	{
		return is_null($idQuestion) || !is_null($this->question_model->get($idQuestion));
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
		$question = $this->question_model->get($id);
		if(is_null($question)) {
			redirect('question');
		}

		$output['error'] = $error;
		$output['id'] = $id;
		$output['topics'] = $this->topic_model->get_tree();
		$output['focus_topic'] = $this->topic_model->get($question->FK_Topic);
		$output['question_type'] = $this->question_type_model->get($question->FK_Question_Type);
		$output['name'] = $question->Question;
		$output['points'] = $question->Points;
		$output['title'] = $this->lang->line('title_question_update');

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
				$this->db->order_by("Answer_Order");
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
	}

	/**
	 * Display form to add a question
	 */
	public function add($step=1)
	{
		if ($step==1){
			// Display a form to choose a topic and a question type
			$output = array(
				'title' => $this->lang->line('title_question_add'),
				'topics' => $this->topic_model->get_tree(),
				'list_question_type' => $this->question_type_model->dropdown('Type_Name')
			);
			$this->display_view('questions/add', $output);

		} elseif ($step==2){
			// Display a specific form for the choosen question type
			$output['title'] = $this->lang->line('title_question_add');
			$output['topics'] = $this->topic_model->get_tree();
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
					$this->display_view('questions/noimplemented');
					// TODO
					break;
				case 4:
					$this->display_view('cloze_text/add', $output);
					break;
				case 5:
					$this->display_view('questions/noimplemented');
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
	public function add_multiple_choice()
	{
		if (isset($_POST['cancel'])){
			redirect('question');
		}

		if (isset($_POST['save'])){
			$this->form_validation->set_rules('name', $this->lang->line('question_text'), 'required');
			$this->form_validation->set_rules('points', $this->lang->line('points'), 'required|numeric|is_natural_no_zero');
			$this->form_validation->set_rules(
				'id', 'Id', 'callback_cb_question_exists',
				['callback_cb_question_exists' => 'lang:question_error_404_heading']
			);
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
								"Answer" => $answers[$i]['question'],
								"Valid" => $answers[$i]['answer']
							);
							$idQuestion = $this->multiple_choice_model->insert($inputQuestion);
						} else {
							$inputAnswer = array(
								"FK_Question" => $_POST['id'],
								"Answer" => $answers[$i]['question'],
								"Valid" => $answers[$i]['answer']
							);
							$this->multiple_choice_model->update($answers[$i]['id'], $inputAnswer);

							unset($answersDb[array_search($answers[$i]['id'], $answersDb)]);
						}
					}

					foreach ($answersDb as $answerDb) {
						$this->multiple_choice_model->delete($answerDb);
					}
				}
				redirect('question');
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
					$output['points'] = $_POST['points'];
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
				$output['topics'] = $this->topic_model->get_tree();

				$output['title'] = $this->lang->line('title_question_add');
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
				$output['points'] = $_POST['points'];
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
			$output['topics'] = $this->topic_model->get_tree();

			$output['title'] = $this->lang->line('title_question_add');
			$this->display_view('multiple_choice/add', $output);
		}
	}

	/**
	 * Function for save multiple answer
	 */
	public function add_multiple_answer()
	{
		if (isset($_POST['save'])){
			$this->form_validation->set_rules('name', $this->lang->line('question_text'), 'required');
			$this->form_validation->set_rules('points', $this->lang->line('points'), 'required|numeric|is_natural');
			$this->form_validation->set_rules('nb_desired_answers', $this->lang->line('nb_desired_answers'), 'required|integer|is_natural_no_zero');
			$this->form_validation->set_rules(
				'id', 'Id', 'callback_cb_question_exists',
				['callback_cb_question_exists' => 'lang:question_error_404_heading']
			);
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
						'Nb_Desired_Answers' => $_POST['nb_desired_answers'],
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
								"Answer" => $answers[$i]['answer']
							);
							$idQuestion = $this->multiple_answer_model->insert($inputQuestion);

							unset($answersDb[array_search($idQuestion, $answersDb)]);
						} else {
							$inputAnswer = array(
								"FK_Question" => $_POST['id'],
								"Answer" => $answers[$i]['answer']
							);
							$this->multiple_answer_model->update($answers[$i]['id'], $inputAnswer);

							unset($answersDb[array_search($answers[$i]['id'], $answersDb)]);
						}
					}

					foreach ($answersDb as $answerDb) {
						$this->multiple_answer_model->delete($answerDb);
					}
				}
				redirect('question');
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
					$output['points'] = $_POST['points'];
				}
				if(isset($_POST['nb_desired_answers'])){
					$output['nb_desired_answers'] = $_POST['nb_desired_answers'];
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
				$output['topics'] = $this->topic_model->get_tree();

				$output['title'] = $this->lang->line('title_question_add');
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
				$output['points'] = $_POST['points'];
			}
			if(isset($_POST['nb_desired_answers'])){
				$output['nb_desired_answers'] = $_POST['nb_desired_answers'];
			}

			$output['nbAnswer'] = $_POST['nbAnswer'];

			$i = 0;
			foreach($_POST['reponses'] as $reponse){
				if(!empty($reponse['id'])){
					$answers[$i]['id'] = $reponse['id'];
				} else {
					$answers[$i]['id'] = 0;
				}
				if(!empty($reponse['answer'])){
					$answers[$i]['answer'] = $reponse['answer'];
				} else {
					$answers[$i]['answer'] = "";
				}
				$i++;
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
			$output['topics'] = $this->topic_model->get_tree();

			$output['title'] = $this->lang->line('title_question_add');
			$this->display_view('multiple_answer/add', $output);
		}
	}

	/**
	 * Function for save cloze text
	 */
	public function add_cloze_text()
	{
		if (isset($_POST['cancel'])){
			redirect('question');
		}

		if (isset($_POST['save'])){
			$this->form_validation->set_rules('name', $this->lang->line('cloze_text_consign'), 'required');
			$this->form_validation->set_rules('points', $this->lang->line('points'), 'required|numeric|is_natural');
			$this->form_validation->set_rules('cloze_text', $this->lang->line('text'), 'required');
			$this->form_validation->set_rules(
				'id', 'Id', 'callback_cb_question_exists',
				['callback_cb_question_exists' => 'lang:question_error_404_heading']
			);
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

					$i = 0;
					foreach($_POST['reponses'] as $reponse){
						$inputAnswer = array(
							"FK_Cloze_Text" => $idClozeText,
							"Answer" => $reponse['answer'],
							"Answer_Order" => $i
						);
						$this->cloze_text_answer_model->insert($inputAnswer);
						$i++;
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
						"Cloze_Text" => $_POST['cloze_text']
					);
					$this->cloze_text_model->update($_POST['id_cloze_text'], $inputClozeText);

					for($i=0; $i < $_POST['nbAnswer']; $i++){
						$answers[$i]['id'] = $_POST['reponses'][$i]['id'];
						$answers[$i]['answer'] = $_POST['reponses'][$i]['answer'];
					}

					$this->db->order_by("Answer_Order");
					$reponses = $this->cloze_text_answer_model->get_many_by('FK_Cloze_Text = ' . $_POST['id_cloze_text']);
					$i = 0;
					$answersDb = array();
					foreach ($reponses as $reponse) {
						$answersDb[$i] = $reponse->ID;
						$i++;
					}

					$i = 0;
					foreach($_POST['reponses'] as $reponse){
						if($answers[$i]['id'] == 0){
							$inputQuestion = array(
								"FK_Cloze_Text" => $_POST['id_cloze_text'],
								"Answer" => $reponse['answer'],
								"Answer_Order" => $i
							);
							$idQuestion = $this->cloze_text_answer_model->insert($inputQuestion);
						} else {
							$inputAnswer = array(
								"FK_Cloze_Text" => $_POST['id_cloze_text'],
								"Answer" => $reponse['answer'],
								"Answer_Order" => $i
							);
							$this->cloze_text_answer_model->update($answers[$i]['id'], $inputAnswer);

							unset($answersDb[array_search($answers[$i]['id'], $answersDb)]);
						}
						$i++;
					}

					foreach ($answersDb as $answerDb) {
						$this->cloze_text_answer_model->delete($answerDb);
					}
				}
				redirect('question');
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
					$output['points'] = $_POST['points'];
				}
				if(isset($_POST['cloze_text'])){
					$output['cloze_text'] = $_POST['cloze_text'];
				}

				$output['nbAnswer'] = $_POST['nbAnswer'];
				$i = 0;
				foreach($_POST['reponses'] as $reponse){
					if(!empty($reponse['id'])){
						$answers[$i]['id'] = $reponse['id'];
					} else {
						$answers[$i]['id'] = 0;
					}
					if(!empty($reponse['answer'])){
						$answers[$i]['answer'] = $reponse['answer'];
					} else {
						$answers[$i]['answer'] = "";
					}
					$i++;
				}

				$output['answers'] = $answers;
				$output['topics'] = $this->topic_model->get_tree();

				$output['title'] = $this->lang->line('title_question_add');
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
				$output['points'] = $_POST['points'];
			}
			if(isset($_POST['cloze_text'])){
				$output['cloze_text'] = $_POST['cloze_text'];
			}

			$output['nbAnswer'] = $_POST['nbAnswer'];

			$i = 0;
			foreach($_POST['reponses'] as $reponse){
				if(!empty($reponse['id'])){
					$answers[$i]['id'] = $reponse['id'];
				} else {
					$answers[$i]['id'] = 0;
				}
				if(!empty($reponse['answer'])){
					$answers[$i]['answer'] = $reponse['answer'];
				} else {
					$answers[$i]['answer'] = "";
				}
				$i++;
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
			$output['topics'] = $this->topic_model->get_tree();

			$output['title'] = $this->lang->line('title_question_add');
			$this->display_view('cloze_text/add', $output);
		}
	}

	/**
	 * Function for save free answer
	 */
	public function add_free_answer()
	{
		if (isset($_POST['cancel'])){
			redirect('question');
		}
		if (!isset($_POST['id'])){
			if (isset($_POST['save'])){
				$_SESSION['filtres'] = "Question?module=&topic=&type=";
				$this->form_validation->set_rules('name', $this->lang->line('question_text'), 'required');
				$this->form_validation->set_rules('points', $this->lang->line('points'), 'required|numeric|is_natural');
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

					redirect('question');
				} else {
					$output['topics'] = $this->topic_model->get_tree();
					$output['focus_topic'] = $this->topic_model->get($_POST['focus_topic']);
					$output['question_type'] = $this->question_type_model->get($_POST['question_type']);
					if(isset($_POST['name'])){
						$output['name'] = $_POST['name'];
					}
					if(isset($_POST['points'])){
						$output['points'] = $_POST['points'];
					}
					if(isset($_POST['answer'])){
						$output['answer'] = $_POST['answer'];
					}
					$output['title'] = $this->lang->line('title_question_add');
					$this->display_view('free_answers/add', $output);
				}
			}
		} else {
			if (isset($_POST['save'])){
				$_SESSION['filtres'] = "Question?module=&topic=&type=";
				$this->form_validation->set_rules('name', $this->lang->line('question_text'), 'required');
				$this->form_validation->set_rules('points', $this->lang->line('points'), 'required|numeric|is_natural');
				$this->form_validation->set_rules('answer', $this->lang->line('answer_question_add'), 'required');
				$this->form_validation->set_rules(
					'id', 'Id', 'callback_cb_question_exists',
					['callback_cb_question_exists' => 'lang:question_error_404_heading']
				);

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

					redirect('question');
				} else {
					$output['id'] = $_POST['id'];
					$output['id_answer'] = $_POST['id_answer'];
					$output['topics'] = $this->topic_model->get_tree();
					$output['focus_topic'] = $this->topic_model->get($_POST['focus_topic']);
					$output['question_type'] = $this->question_type_model->get($_POST['question_type']);
					if(isset($_POST['name'])){
						$output['name'] = $_POST['name'];
					}
					if(isset($_POST['points'])){
						$output['points'] = $_POST['points'];
					}
					if(isset($_POST['answer'])){
						$output['answer'] = $_POST['answer'];
					}
					$output['title'] = $this->lang->line('title_question_add');
					$this->display_view('free_answers/add', $output);
				}
			}
		}
	}

	/**
	 * Function for save picture landmark
	 */
	public function add_picture_landmark($step=1)
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
				$output['points'] = $_POST['points'];
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
			$output['topics'] = $this->topic_model->get_tree();

			if (isset($_POST['cancel'])){
				if(isset($_POST['id'])) {
					$output['title'] = $this->lang->line('title_question_add');
					$this->display_view('picture_landmark/add', $output);
				} else {
					redirect('question');
				}
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
							$output['title'] = $this->lang->line('title_question_add');
							$this->display_view('picture_landmark/file', $output);
					}
					else
					{
							$output['upload_data'] = $this->upload->data();

							$output['title'] = $this->lang->line('title_question_add');
							$this->display_view('picture_landmark/add', $output);
					}
				} else {
					// Whatever was pressed, it is not recognized yet
					redirect('question');
				}
			}
		} elseif($step==2){
			if (isset($_POST['cancel'])){
				redirect('question');
			} elseif (isset($_POST['save'])){
				$this->form_validation->set_rules('name', $this->lang->line('question_text'), 'required');
				$this->form_validation->set_rules('points', $this->lang->line('points'), 'required|numeric|is_natural');
				$this->form_validation->set_rules(
					'id', 'Id', 'callback_cb_question_exists',
					['callback_cb_question_exists' => 'lang:question_error_404_heading']
				);
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
						$file_name = $_POST['picture_name'] ?? '';
						if(isset($_POST['upload_data'])){
							$inputQuestion = array(
								"FK_Topic" => $_POST['focus_topic'],
								"FK_Question_Type" => $_POST['question_type'],
								"Question" => $_POST['name'],
								"Points" => $_POST['points'],
								"Picture_Name" => $_POST['upload_data']['file_name']
							);
							$file_name = $_POST['upload_data']['file_name'];
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
							if(strpos($file, $_POST['id']."_") !== false && $file != $file_name){
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
					redirect('question');
				} else {
					$output['topics'] = $this->topic_model->get_tree();
					$output['focus_topic'] = $this->topic_model->get($_POST['focus_topic']);
					$output['question_type'] = $this->question_type_model->get($_POST['question_type']);

					if(isset($_POST['id'])){
						$output['id'] = $_POST['id'];
					}
					if(isset($_POST['name'])){
						$output['name'] = $_POST['name'];
					}
					if(isset($_POST['points'])){
						$output['points'] = $_POST['points'];
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
					$output['title'] = $this->lang->line('title_question_add');
					$this->display_view('picture_landmark/add', $output);
				}
			} else {
				$output['topics'] = $this->topic_model->get_tree();
				$output['focus_topic'] = $this->topic_model->get($_POST['focus_topic']);
				$output['question_type'] = $this->question_type_model->get($_POST['question_type']);

				if(isset($_POST['id'])){
					$output['id'] = $_POST['id'];
				}
				if(isset($_POST['name'])){
					$output['name'] = $_POST['name'];
				}
				if(isset($_POST['points'])){
					$output['points'] = $_POST['points'];
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

				$output['title'] = $this->lang->line('title_question_add');
				if(isset($_POST['change_picture'])){
					$this->display_view('picture_landmark/file', $output);
				} else {
					$this->display_view('picture_landmark/add', $output);
				}
			}
		}
	}
}
