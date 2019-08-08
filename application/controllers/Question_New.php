<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Question controller
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */

class Question extends MY_Controller {
	/* MY_Controller variables definition */
	protected $access_level = ACCESS_LVL_MANAGER;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model(['question_model', 'question_type_model', 'topic_model']);
	}

	/**
	 * Display question list
	 *
	 * @param int $page = The currently selected page
	 */
	public function index($page = 1) {
		// Load required libraries
		$this->load->library('pagination');

		// Declare variables
		$module = $this->input->get('module');
		$topic = $this->input->get('topic');
		$type = $this->input->get('type');
		$search = $this->input->get('search');
		$sort = $this->input->get('sort');
		$where = 'Archive = 0';
		$orderby = '';

		// Check that module is in topic
		if(!empty($module) && !empty($topic)) {
			$nbTopic = $this->topic_model->count_by(array(
				'ID' => $topic,
				'FK_Parent_Topic' => $module
			));
			if(!$nbTopic) $topic = NULL;
		}

		// Save URL to session in case the person wants to come back
		if(!empty($module) && !empty($topic) && !empty($type))
			$_SESSION['filters'] = 'Question?module='.$module.'&topic'.$topic.'&type='.$type;

		// Redirect to the saved URL if there is no ? in the URL
		if(isset($_SESSION['filters']) && strpos($this->input->server('REQUEST_URI'), '?') === FALSE)
			redirect($_SESSION['filters']);

		// Create the where sql request
		if(!empty($topic)) $where .= ' AND FK_Topic = '.$topic;
		if(!empty($type)) $where .= ' AND FK_Question_Type = '.$type;
		if(!empty($module)) {
			$topics = $this->topic_model->get_many_by(['FK_Parent_Topic' => $module]);
			$listIdQuestion = '';

			if((bool) $topics) {
				foreach($topics as $topic_)
					$listIdQuestion .= 'FK_Topic = '.$topic_->ID.' OR ';
				$listIdQuestion = substr($listIdQuestion, 0, -4);
			} else {
				$listIdQuestion = 'FK_Topic = 0';
			}

			$where .= " AND ({$listIdQuestion})";
		}
		if(!empty($search)) $where .= " AND Question LIKE '%{$search}%'";
		// Set the order
		switch($sort ?? '') {
			default:
			case 'question_asc':
				$orderby = "Question ASC, FK_Question_Type ASC, Points ASC, ID ASC"; break;
			case 'question_desc':
				$orderby = "Question DESC, FK_Question_Type ASC, Points ASC, ID ASC"; break;
			case 'question_type_asc':
				$orderby = "FK_Question_Type ASC, Question ASC, Points ASC, ID ASC"; break;
			case 'question_type_desc':
				$orderby = "FK_Question_Type DESC, Question ASC, Points ASC, ID ASC"; break;
			case 'points_asc':
				$orderby = "Points ASC, Question ASC, FK_Question_Type ASC, ID ASC"; break;
			case 'points_desc':
				$orderby = "Points DESC, Question ASC, FK_Question_Type ASC, ID ASC"; break;
		}

		// Do the requests
		$this->db->order_by($orderby);
		$questions = $this->question_model->with_all()->get_many_by($where);

		if(($page - 1) * ITEMS_PER_PAGE > count($questions))
			redirect('question/index/1');

		// Prepare and load the pagination
		$config = array(
			'base_url' => base_url('/question/index/'),
			'total_rows' => count($questions),
			'per_page' => ITEMS_PER_PAGE,
			'use_page_numbers' => TRUE,
			'reuse_query_string' => TRUE,

			'full_tag_open' => '<ul class="pagination">',
			'full_tag_close' => '</ul>',

			'first_link' => '&laquo;',
			'first_tag_open' => '<li>',
			'first_tag_close' => '</li>',

			'last_link' => '&raquo;',
			'last_tag_open' => '<li>',
			'last_tag_close' => '</li>',

			'next_link' => FALSE,
			'prev_link' => FALSE,

			'cur_tag_open' => '<li class="active"><a>',
			'cur_tag_close' => '</li></a>',
			'num_links' => 5,

			'num_tag_open' => '<li>',
			'num_tag_close' => '</li>'
		);
		$this->pagination->initialize($config);

		// Output variable, for display page
		$output = array(
			'pagination' => $this->pagination->create_links(),
			'questions' => array_slice($questions, ($page - 1) * ITEMS_PER_PAGE, ITEMS_PER_PAGE),
			'topics' => $this->topic_model->get_many_by('Archive IS NULL OR Archive = 0'),
			'questionTypes' => $this->question_type_model->get_all()
		);
		$this->display_view('questions/index', $output);
	}

	/**
	 * Resets the index filters stored in $_SESSION['filtres']
	 * and redirects the user to index
	 */
	public function resetFilters() {
		unset($_SESSION['filtres']);
		redirect('Question');
	}

	/**
	 * Delete selected question
	 *
	 * @param int $id = id of the question
	 * @param mixed $action
	 */
	public function delete($id = 0, $action = NULL) {

		$question = $this->question_model->get($id);
		// No question, cancel
		if(is_null($question)) redirect('Question');
		// $action is null, display confirmation
		elseif(is_null($action)) {
			$output = get_object_vars($question);
			$this->display_view('questions/confirm', $output);
		// $action is literally anything, delete
		} else {
			$this->question_model->delete($id);
			redirect('Question');
		}
	}

	/**
	 * Form validation to update question
	 */
	public function form_update() {

		$this->form_validation->set_rules('name', 'Title', 'required');
		$this->form_validation->set_rules('points', 'Points', ['required','numeric']);
				
				$id = $this->input->post('id');

		// Updated question
		$question = array(
			'Question' => $this->input->post('name'),
			'Points' => $this->input->post('points')
		);

		if($this->form_validation->run()) {
			$this->question_model->update($id, $question);
			redirect('Question');
		} else $this->update($id);
	}

	/**
	 * Display the detailed view to update a question
	 *
	 * @param int $id = selected question id
	 */
	public function update($id = 0) {
		$this->load->model(['multiple_choice_model', 'multiple_answer_model', 'cloze_text_model',
			'free_answer_model', 'picture_landmark_model', 'cloze_text_answer_model']);

		$question = $this->question_model->get($id);
		if(is_null($question)) redirect('Question');

		// Load the question to update
		$output = array(
			'id' => $id,
			'topics' => $this->topic_model->get_tree(),
			'focus_topic' => $this->topic_model->get($question->FK_Topic),
			'question_type' => $this->question_type_model->get($question->FK_Question_Type),
			'name' => $question->Question,
			'points' => $question->Points
		);
		switch($question->FK_Question_Type) {
			case 1: // MUTLIPLE CHOICE
				$answers = $this->multiple_choice_model->get_many_by('FK_Question = ' . $id);
				$output['answers'] = [];
				foreach($answers as $answer) {
					$output['answers'][] = array(
						'id' => $answer->ID,
						'question' => $answer->Answer,
						'answer' => $answer->Valid
					);
				}
				$output['nbAnswer'] = count($answers);
				$this->display_view('multiple_choice/add', $output);
				break;
			case 2: // MUTLIPLE ANSWER
				$output['nb_desired_answers'] = $question->Nb_Desired_Answers;
				$answers = $this->multiple_answer_model->get_many_by('FK_Question = ' . $id);
				$output['answers'] = [];
				foreach($answers as $answer) {
					$output['answers'][] = array(
						'id' => $answer->ID,
						'answer' => $answer->Answer
					);
				}
				$output['nbAnswer'] = count($answers);
				$this->display_view('multiple_answer/add', $output);
				break;
			case 3: // TODO // ANSWER DISTRIBUTION
				break;
			case 4: // CLOZE TEXT
				$cloze_text = $this->cloze_text_model->get_by('FK_Question = '.$id);
				$this->db->order_by("Answer_Order");
				$answers = $this->cloze_text_answer_model->get_many_by('FK_Cloze_Text = '.$cloze_text->ID);
				$output['answers'] = [];
				foreach($answers as $answer) {
					$output['answers'][] = array(
						'id' => $answer->ID,
						'answer' => $answer->Answer
					);
				}
				$output['nbAnswer'] = count($answers);
				$output['cloze_text'] = $cloze_text->Cloze_Text;
				$output['id_cloze_text'] = $cloze_text->ID;
				$this->display_view('cloze_text/add', $output);
				break;
			case 5: // TODO // TABLE
				break;
			case 6: // FREE ANSWER
				$free_answer = $this->free_answer_model->get_by('FK_Question ='.$question->ID);
				$output['id_answer'] = $free_answer->ID;
				$output['answer'] = $free_answer->Answer;
				$this->display_view('free_answers/add', $output);
				break;
			case 7: // PICTURE LANDMARK
				$output['picture_name'] = $question->Picture_Name;
				$answers = $this->picture_landmark_model->get_many_by('FK_Question = ' . $id);
				$output['answers'] = [];
				foreach($answers as $answer) {
					$output['answers'][] = array(
						'id' => $answer->ID,
						'symbol' => $answer->Symbol,
						'answer' => $answer->Answer
					);
				}
				$output['nbAnswer'] = count($answers);
				$this->display_view('picture_landmark/add', $output);
				break;
			default: // Unsupported question type
				redirect('Question');
		}
	}

	/**
	 * Displays the information about a question
	 *
	 * @param int $id = selected question id
	 */
	public function detail($id = 0) {
		$this->load->model(['multiple_choice_model', 'multiple_answer_model',
			'answer_distribution_model', 'cloze_text_model', 'cloze_text_answer_model',
			'table_cell_model', 'free_answer_model', 'picture_landmark_model']);

		$question = $this->question_model->get($id);
		if(is_null($question)) redirect('Question');

		// A base for the data to display
		$output = array(
			'id' => $id,
			'question' => $question,
			'image' => '',
			'reponse' => ''
		);
		switch($question->FK_Question_Type) {
			case 1: // MULTIPLE CHOICE
				$answers = $this->multiple_choice_model->get_many_by('FK_Question = ' . $id);
				foreach($answers as $answer) {
					$valid = $this->lang->line($answer->Valid ? 'yes' : 'no');
					$output['reponse'] .= '<br>'.$answer->Answer.':'.$valid;
				}
				break;
			case 2: // MULTIPLE ANSWER
				$answers = $this->multiple_answer_model->get_many_by('FK_Question = ' . $id);
				foreach($answers as $answer) {
					$output['reponse'] .= '<br>'.$answer->Answer;
				}
				break;
			case 3: // TODO // ANSWER DISTRIBUTION
				$answers = $this->answer_distribution_model->get_many_by('FK_Question = ' . $id);
				foreach($answers as $answer) {
					$output['reponse'] .= '<br>'.$answer->Question_Part.'/'.$answer->Answer_Part;
				}
				break;
			case 4: // CLOZE TEXT
				$question = $this->cloze_text_model->get_by('FK_Question = ' . $id);
				$output['question']->Question .= ': '.$question->Cloze_Text;
				$this->db->order_by('Answer_Order');
				$answers = $this->cloze_text_answer_model->get_many_by('FK_Cloze_Text = ' . $question->ID);
				foreach($answers as $answer) {
					$output['reponse'] .= '<br>'.$answer->Answer_Order.'/'.$answer->Answer;
				}
				break;
			case 5: // TODO // TABLE
				$answers = $this->table_cell_model->get_many_by('FK_Question = '.$id);
				break;
			case 6: // FREE ANSWER
				$answers = $this->free_answer_model->get_many_by('FK_Question = ' . $id);
				foreach($answers as $answer) {
					$output['reponse'] .= ' '.$answer->Answer;
				}
				break;
			case 7: // PICTURE LANDMARK
				$output['image'] = $question->Picture_Name;
				$answers = $this->picture_landmark_model->get_many_by('FK_Question = ' . $id);
				foreach($answers as $answer) {
					$output['reponse'] .= '<br>'.$answer->Symbol.'/'.$answer->Answer;
				}
				break;
			default: // Unsupported question type
				redirect('Question');
		}
		$this->display_view('questions/detail', $output);
	}

	/**
	 * Display form to add a question
	 *
	 * @param int $step = Current step of question creation
	 */
	public function add($step = 1) {
		switch((int) $step) {
			case 1: // Choose topic and question type
				$output = array(
					'topics' => $this->topic_model->get_tree(),
					'list_question_type' => $this->question_type_model->dropdown('Type_Name')
				);
				$this->display_view('questions/add', $output);
				break;
			case 2: // Question form
				$output = array(
					'topics' => $this->topic_model->get_tree(),
					'focus_topic' => $this->topic_model->get($this->input->post('focus_topic')),
					'question_type' => $this->question_type_model->get($this->input->post('question_type')),
					'nbAnswer' => 1,
					'answers' => [0 => array(
						'id' => 0,
						'question' => FALSE,
						'answer' => '',
						'symbol' => ''
					)]
				);
				switch($this->input->post('question_type')) {
					case 1: // MULTIPLE CHOICE
						$this->display_view('multiple_choice/add', $output);
						break;
					case 2: // MULTIPLE ANSWER
						$this->display_view('multiple_answer/add', $output);
						break;
					case 3: // TODO // ANSWER DISTRIBUTION
						$this->display_view('questions/noimplemented');
						break;
					case 4: // CLOZE TEXT
						$this->display_view('cloze_text/add');
						break;
					case 5: // TODO // TABLE
						$this->display_view('questions/noimplemented');
						break;
					case 6: // FREE ANSWER
						$this->display_view('free_answers/add');
						break;
					case 7: // PICTURE LANDMARK
						$this->display_view('picture_landmark/file');
						break;
					default: // Unsupported question type
						redirect('Question');
				}
				break;
			default: // Not a step
				redirect('Question');
		}
	}

	/**
	 * Function to save a multiple choice question
	 */
	public function add_MultipleChoice() {
		$this->load->model(['multiple_answer_model', 'multiple_choice_model']);

		if(!is_null($this->input->post('cancel'))) redirect('Question');
		// Check the inputs and save the question
		elseif(!is_null($this->input->post('save'))) {
			$this->form_validation->set_rules('name', $this->lang->line('question_text'), 'required');
			$this->form_validation->set_rules('points', $this->lang->line('points'), 'required|numeric|greater_than_equal_to[0]');
			for($i = 0; $i < $this->input->post('nbAnswer'); $i++) {
				$this->form_validation->set_rules('reponses['.$i.'][question]', $this->lang->line('answers_list'), 'required');
				$this->form_validation->set_rules('reponses['.$i.'][answer]', $this->lang->line('valid_answer'), 'required');
			}

			// Check the inputs
			if($this->form_validation->run()) {
				$question = array(
					'FK_Topic' => $this->input->post('focus_topic'),
					'FK_Question_Type' => $this->input->post('question_type'),
					'Question' => $this->input->post('name'),
					'Points' => $this->input->post('points')
				);
				$id = $this->input->post('id');
				// Create a new question
				if(is_null($id)) {
					$id = $this->question_model->insert($question);
					$answers = $this->input->post('reponses');
					foreach($answers as $answer) {
						$answerNew = array(
							'FK_Question' => $id,
							'Answer' => $answer['answer']
						);
						$this->multiple_answer_model->insert($answerNew);
					}
				// Update a question
				} else {
					$this->question_model->update($id, $question);

					$answers = $this->input->post('reponses');
					$oldanswers = $this->multiple_choice_model->get_many_by('FK_Question = ' . $this->input->post('id'));
					$answersDb = [];

					foreach($oldanswers as $oldanswer) {
						$oldid = $oldanswer->ID;
						$answersDb[$oldid] = $oldid;
					}
					// Update the answers
					foreach($answers as $answer) {
						$question = array(
							'FK_Question' => $id,
							'Answer' => $answer['question'],
							'Valid' => $answer['answer']
						);
						if($answer['id'] == 0) $this->multiple_choice_model->insert($question);
						else {
							$this->multiple_choice_model->update($answer['id'], $question);
							unset($answersDb[$answer['id']]);
						}
					}
					// Delete the unused answers
					foreach($answersDb as $answerDb) {
						$this->multiple_choice_model->delete($answerDb);
					}
				}
				redirect('Question');
			} else {
				$output = array(
					'focus_topic' => $this->topic_model->get($this->input->post('focus_topic')),
					'question_type' => $this->question_type_model->get($this->input->post('question_type')),
					'id' => $this->input->post('id'),
					'name' => $this->input->post('name'),
					'points' => $this->input->post('points'),
					'nbAnswer' => $this->input->post('nbAnswer'),
					'answers' => [],
					'topics' => $this->topic_model->get_tree()
				);

				$answers = $this->input->post('reponses');
				foreach($answers as $answer) {
					$output['answers'][] = array(
						'id' => $answer['id'] ?? 0,
						'question' => $answer['question'] ?? '',
						'answer' => $answer['answer'] ?? ''
					);
				}
				$this->display_view('multiple_choice/add', $output);
			}
		} else {
			// Data to display
			$output = array(
				'focus_topic' => $this->topic_model->get($this->input->post('focus_topic')),
				'question_type' => $this->question_type_model->get($this->input->post('question_type')),
				'id' => $this->input->post('id'),
				'name' => $this->input->post('name'),
				'points' => $this->input->post('points'),
				'nbAnswer' => $this->input->post('nbAnswer'),
				'topics' => $this->topic_model->get_tree(),
				'answers' => []
			);

			$answers = $this->input->post('reponses');
			foreach($answers as $answer) {
				$output['answers'][] = array(
					'id' => $answer['id'] ??  0,
					'question' => $answer['question'] ?? '',
					'answer' => $answer['answer'] ?? ''
				);
			}

			// Add a new answer to the list
			if(!empty($this->input->post('add_answer'))) {
				$output['answers'][] = array(
					'id' => 0,
					'question' => '',
					'answer' => ''
				);
				$output['nbAnswer']++;
			}

			// Check if there is an answer to remove
			for($i = 0; $i < $output['nbAnswer']; $i++) {
				if(empty($this->input->post('del_answer'.$i))) continue;
				elseif($output['nbAnswer'] <= 1) $output['nbAnswer'] = $this->input->post('nbAnswer');
				else {
					$output['nbAnswer']--;
					for($j = $i; $j < $output['nbAnswer'];) {
						$output['answers'][$j] = $output['answers'][++$j];
					}
				}
			}

			$this->display_view('multiple_choice/add', $output);
		}
	}

	/**
	 * Function for save multiple answer
	 */
	public function add_MultipleAnswer() {
		$this->load->model(['multiple_answer_model']);

		if(!is_null($this->input->post('cancel'))) redirect('Question');
		// Check the inputs and save the question
		elseif(!is_null($this->input->post('save'))) {
			$this->form_validation->set_rules('name', $this->lang->line('question_text'), 'required');
			$this->form_validation->set_rules('points', $this->lang->line('points'), 'required|numeric|greater_than_equal_to[0]');
			$this->form_validation->set_rules('nb_desired_answers', $this->lang->line('nb_desired_answers'), 'required|integer|greater_than[0]');
			for($i = 0; $i < $this->input->post('nbAnswer'); $i++) {
				$this->form_validation->set_rules('reponses['.$i.'][answer]', $this->lang->line('answers_list'), 'required');
			}

			// Check the inputs
			if($this->form_validation->run()) {
				$question = array(
					'FK_Topic' => $this->input->post('focus_topic'),
					'FK_Question_Type' => $this->input->post('question_type'),
					'Question' => $this->input->post('name'),
					'Nb_Desired_Answers' => $this->input->post('nb_desired_answers'),
					'Points' => $this->input->post('points')
				);
				$id = $this->input->post('id');

				// Create a new question
				if(is_null($id)) {
					$id = $this->question_model->insert($question);
					$answers = $this->input->post('reponses');
					foreach($answers as $answer) {
						$answerNew = array(
							'FK_Question' => $id,
							'Answer' => $answer['answer']
						);
						$this->multiple_answer_model->insert($answerNew);
					}
				// Update a question
				} else {
					$this->question_model->update($id, $question);

					$answers = $this->input->post('reponses');
					$oldanswers = $this->multiple_answer_model->get_many_by('FK_Question = ' . $this->input->post('id'));
					$answersDb = [];

					foreach($oldanswers as $oldanswer) {
						$oldid = $oldanswer->ID;
						$answersDb[$oldid] = $oldid;
					}
					// Update the answers
					foreach($answers as $answer) {
						// Insert new answer
						$answerNew = array(
							'FK_Question' => $id,
							'Answer' => $answer['answer']
						);
						if($answer['id'] == 0) {
							$answer['id'] = $this->multiple_answer_model->insert($answerNew);
						// Update old answer
						} else {
							$this->multiple_answer_model->update($answer['id'], $answerNew);
						}
						unset($answersDb[$answer['id']]);
					}
					// Delete the unused answers
					foreach($answersDb as $answerDb) {
						$this->multiple_choice_model->delete($answerDb);
					}
				}
				redirect('/Question');
			} else {
				$output = array(
					'focus_topic' => $this->topic_model->get($this->input->post('focus_topic')),
					'question_type' => $this->question_type_model->get($this->input->post('question_type')),
					'id' => $this->input->post('id'),
					'name' => $this->input->post('name'),
					'points' => $this->input->post('points'),
					'nb_desired_answers' => $this->input->post('nb_desired_answers'),
					'nbAnswer' => $this->input->post('nbAnswer'),
					'answers' => [],
					'topics' => $this->topic_model->get_tree()
				);

				$answers = $this->input->post('reponses');
				foreach($answers as $answer) {
					$output['answers'][] = array(
						'id' => $answer['id'] ?? 0,
						'question' => $answer['question'] ?? '',
						'answer' => $answer['answer'] ?? ''
					);
				}

				$this->display_view('multiple_answer/add', $output);
			}
		} else {
			// Data to display
			$output = array(
				'focus_topic' => $this->topic_model->get($this->input->post('focus_topic')),
				'question_type' => $this->question_type_model->get($this->input->post('question_type')),
				'id' => $this->input->post('id'),
				'name' => $this->input->post('name'),
				'points' => $this->input->post('points'),
				'nb_desired_answers' => $this->input->post('nb_desired_answers'),
				'nbAnswer' => $this->input->post('nbAnswer'),
				'topics' => $this->topic_model->get_tree(),
				'answers' => []
			);

			$answers = $this->input->post('reponses');
			foreach($answer as $answer) {
				$output['answers'][] = array(
					'id' => $answer['id'] ?? 0,
					'answer' => $answer['answer'] ?? ''
				);
			}

			// Add an answer if needed
			if(!is_null($this->input->post('add_answer'))) {
				$output['answers'][] = array(
					'id' => 0,
					'answer' => ''
				);
				$output['nbAnswer']++;
			}

			// Remove an answer if needed
			for($i = 0; $i < $output['nbAnswer']; $i++) {
				if(is_null($this->input->post('del_answer'.$i))) continue;
				elseif($output['nbAnswer'] <= 1) $output['nbAnswer'] = $this->input->post('nbAnswer');
				else {
					$output['nbAnswer']--;
					for($j = $i; $j < $output['nbAnswer'];) {
						$output['answers'][$j] = $output['answers'][++$j];
					}
				}
			}

			$this->display_view('multiple_answer/add', $output);
		}
	}

	/**
	 * Function for save cloze text
	 */
	public function add_ClozeText() {
		$this->load->model(['cloze_text_model', 'cloze_text_answer_model']);

		if(!is_null($this->input->post('cancel'))) redirect('Question');
		// Check the inputs and save the question
		elseif(!is_null($this->input->post('save'))) {
			$this->form_validation->set_rules('name', $this->lang->line('cloze_text_consign'), 'required');
			$this->form_validation->set_rules('points', $this->lang->line('points'), 'required|numeric|greater_than_equal_to[0]');
			$this->form_validation->set_rules('cloze_text', $this->lang->line('text'), 'required');
			for($i = 0; $i < $this->input->post('nbAnswer'); $i++) {
				$this->form_validation->set_rules('reponses['.$i.'][answer]', $this->lang->line('answers_list'), 'required');
			}

			if($this->form_validation->run()) {
				$question = array(
					'FK_Topic' => $this->input->post('focus_topic'),
					'FK_Question_Type' => $this->input->post('question_type'),
					'Question' => $this->input->post('name'),
					'Points' => $this->input->post('points')
				);
				$id = $this->input->post('id');

				if(is_null($id)) {
					$id = $this->question_model->insert($question);
					$cloze_text = array(
						'FK_Question' => $id,
						'Cloze_Text' => $this->input->post('cloze_text')
					);
					$id_cloze_text = $this->cloze_text_model->insert($cloze_text);

					$answers = $this->input->post('reponses');
					for($i = 0; $i < count($answers); $i++) {
						$answer = $answers[$i];
						$answerNew = array(
							'FK_Cloze_Text' => $id_cloze_text,
							'Answer' => $answer['answer'],
							'Answer_Order' => $i
						);
						$this->cloze_text_answer_model->insert($answerNew);
					}
				} else {
					$this->question_model->update($id, $question);
					$cloze_text = array(
						'Cloze_Text' => $this->input->post('cloze_text')
					);
					$this->cloze_text_model->update($this->input->post('id_cloze_text', $cloze_text));

					$answers = $this->input->post('reponses');
					$this->db->order_by('Answer_Order');
					$oldanswers = $this->cloze_text_answer_model->get_many_by('FK_Cloze_Text = '.$this->input->post('id_cloze_text'));
					$answersDb = [];
					foreach($oldanswers as $oldanswer) {
						$answersDb[$oldanswer->ID] = $oldanswer->ID;
					}
					for($i = 0; $i < count($answers); $i++) {
						$answer = array(
							'FK_Cloze_Text' => $this->input->post('id_cloze_text'),
							'Answer' => $answers[$i]['answer'],
							'Answer_Order' => $i
						);
						if($answers[$i]['id'] == 0)
							$this->cloze_text_answer_model->insert($answer);
						else {
							$id = $answers[$i]['id'];
							$this->cloze_text_answer_model->update($id, $answer);
							unset($answersDb[$id]);
						}
					}
					foreach($answersDb as $answerDb) {
						$this->cloze_text_answer_model->delete($answerDb);
					}
				}
			} else {
				$output = array(
					'focus_topic' => $this->topic_model->get($this->input->post('focus_topic')),
					'question_type' => $this->question_type_model->get($this->input->post('question_type')),
					'id' => $this->input->post('id'),
					'id_cloze_text' => $this->input->post('id_cloze_text'),
					'name' => $this->input->post('name'),
					'points' => $this->input->post('points'),
					'cloze_text' => $this->input->post('cloze_text'),
					'nbAnswer' => $this->input->post('nbAnswer'),
					'answers' => [],
					'topics' => $this->topic_model->get_tree()
				);

				$answers = $this->input->post('reponses');
				foreach($answers as $answer) {
					$output['answers'][] = array(
						'id' => $answer['id'] ?? 0,
						'answer' => $answer['answer'] ?? ''
					);
				}

				$this->display_view('cloze_text/add', $output);
			}
		} else {
			// Data to display
			$output = array(
				'focus_topic' => $this->focus_topic->get($this->input->post('focus_topic')),
				'question_type' => $this->question_type_model->get($this->input->post('question_type')),
				'id' => $this->input->post('id'),
				'id_cloze_text' => $this->input->post('id_cloze_text'),
				'name' => $this->input->post('name'),
				'points' => $this->input->post('points'),
				'cloze_text' => $this->input->post('cloze_text'),
				'nbAnswer' => $this->input->post('nbAnswer'),
				'answers' => [],
				'topics' => $this->topic_model->get_tree()
			);

			$answers = $this->input->post('reponses');
			foreach($answer as $answer) {
				$output['answers'][] = array(
					'id' => $answer['id'] ?? 0,
					'answer' => $answer['answer'] ?? ''
				);
			}

			if(!is_null($this->input->post('add_answer'))) {
				$output['answer'][] = array(
					'id' => 0,
					'answer' => ''
				);
				$output['nbAnswer']++;
			}

			for($i = 0; $i < $output['nbAnswer']; $i++) {
				if(is_null($this->input->post('del_answer'.$i))) continue;
				elseif($output['nbAnswer'] <= 1) $output['nbAnswer'] = $this->input->post('nbAnswer')-1;
				else {
					$output['nbAnswer']--;
					for($j = $i; $j < $output['nbAnswer'];) {
						$output['answers'][$j] = $output['answers'][++$j];
					}
				}
			}

			$this->display_view('cloze_text/add', $output);
		}
	}

	/**
	 * Function for save free answer
	 */
	public function add_FreeAnswer() {
		$this->load->model(['free_answer_model']);

		if(!is_null($this->input->post('cancel'))) redirect('Question');
		elseif(!is_null($this->input->post('save'))) {
			$this->form_validation->set_rules('name', $this->lang->line('question_text'), 'required');
			$this->form_validation->set_rules('points', $this->lang->line('points'), 'required|numeric|greater_than_equal_to[0]');
			$this->form_validation->set_rules('answer', $this->lang->line('answer_question_add'), 'required');

			if($this->form_validation->run()) {
				$question = array(
					'FK_Topic' => $this->input->post('focus_topic'),
					'FK_Question_Type' => $this->input->post('question_type'),
					'Question' => $this->input->post('name'),
					'Points' => $this->input->post('points')
				);
				$id = $this->input->post('id');

				if(is_null($id)) {
					$id = $this->question_model->insert($question);
					$answer = array(
						'FK_Question' => $id,
						'Answer' => $this->input->post('answer')
					);
					$this->free_answer_model->insert($answer);
				} else {
					$this->question_model->update($id, $question);
					$answer = array(
						'FK_Question' => $id,
						'Answer' => $this->input->post('answer')
					);
					$this->free_answer_model->update($this->input->post('id_answer'), $answer);
				}
				redirect('Question');
			} else {
				$output = array(
					'topics' => $this->topic_model->get_tree(),
					'focus_topic' => $this->topic_model->get($this->input->post('focus_topic')),
					'question_type' => $this->question_type_model->get($this->input->post('question_type')),
					'id' => $this->input->post('id'),
					'id_answer' => $this->input->post('id_answer'),
					'name' => $this->input->post('name'),
					'points' => $this->input->post('points'),
					'answer' => $this->input->post('answer')
				);

				$this->display_view('free_answers/add', $output);
			}
		} else redirect('Question');
	}

	/**
	 * Function for save picture landmark
	 *
	 * @param int $step = Current step of question creation
	 */
	public function add_PictureLandmark($step = 1) {
		$this->load->model(['picture_landmark_model']);

		switch ($step) {
			case 1: // Enter Image
				$output = array(
					'focus_topic' => $this->topic_model->get($this->input->post('focus_topic')),
					'question_type' => $this->question_type_model->get($this->input->post('question_type')),
					'id' => $this->input->post('id'),
					'name' => $this->input->post('name'),
					'points' => $this->input->post('points'),
					'picture_name' => $this->input->post('picture_name'),
					'upload_data' => $this->input->post('upload_data'),
					'nbAnswer' => $this->input->post('nbAnswer'),
					'answers' => [],
					'topics' => $this->topic_model->get_tree()
				);
				$answers = $this->input->post('reponses');
				foreach($answers as $answer) {
					$output['answers'][] = array(
						'id' => $answer['id'] ?? 0,
						'symbol' => $answer['symbol'] ?? '',
						'answer' => $answer['answer'] ?? ''
					);
				}
				$id = $this->input->post('id');

				if(!is_null($this->input->post('id'))) {
					if(is_null($id)) redirect('Question');
					else $this->display_view('picture_landmark/add', $output);
				} elseif(isset($_FILES['picture'])) {
					$id = $id ?? $this->question_model->get_next_id();
					$config = array(
						'upload_path' => './uploads/pictures',
						'allowed_types' => 'gif|jpg|jpeg|png',
						'max_size' => 2048,
						'file_name' => $id."_".$_FILES['picture']['name']
					);

					$this->upload->initialize($config);

					if($this->upload->do_upload('picture')) {
						$output['upload_data'] = $this->upload->data();
						$this->display_view('picture_landmark/add', $output);
					} else {
						$output['error'] = $this->upload->display_errors();
						$this->display_view('picture_landmark/file', $output);
					}
				} else redirect('Question');
				break;
			case 2: // Enter Values
				if(!is_null($this->input->post('cancel'))) redirect('Question');
				elseif(!is_null($this->input->post('save'))) {
					$this->form_validation->set_rules('name', $this->lang->line('question_text'), 'required');
					$this->form_validation->set_rules('points', $this->lang->line('points'), 'required|numeric|greater_than_equal_to[0]');
					for($i=0; $i < $_POST['nbAnswer']; $i++){
						$this->form_validation->set_rules('reponses['.$i.'][symbol]', $this->lang->line('landmark'), 'required');
						$this->form_validation->set_rules('reponses['.$i.'][answer]', $this->lang->line('answers_list'), 'required');
					}

					if($this->form_validation->run()) {
						$id = $this->input->post('id');
						$picture_name = $this->input->post('upload_data')['file_name'] ?? $this->input->post('picture_name');
						$question = array(
							'FK_Topic' => $this->input->post('focus_topic'),
							'FK_Question_Type' => $this->input->post('question_type'),
							'Question' => $this->input->post('name'),
							'Points' => $this->input->post('points'),
							'Picture_Name' => $picture_name
						);

						if(is_null($id)) {
							$id = $this->question_model->insert($question);

							$list = scandir('./uploads/pictures');
							foreach($list as $file) {
								if(strpos($file, $id.'_') !== FALSE && $file != $this->input->post('upload_data')['file_name']) {
									unlink('uploads/pictures/'.$file);
								}
							}

							$answers = $this->input->post('reponses');
							foreach($answers as $answer) {
								$answerNew = array(
									'FK_Question' => $id,
									'Symbol' => $answer['symbol'],
									'Answer' => $answer['answer']
								);
								$this->picture_landmark_model->insert($answerNew);
							}
						} else {
							$this->question_model->update($id, $question);

							$answers = $this->input->post('reponses');
							$oldanswers = $this->picture_landmark_model->get_many_by('FK_Question = '.$id);
							$answersDb = [];
							$list = scandir("./uploads/pictures");

							foreach($list as $file) {
								if(strpos($file, $id."_") !== false && $file != $this->input->post('upload_data')['file_name']) {
									unlink("uploads/pictures/".$file);
								}
							}
							foreach($oldanswers as $oldanswer) {
								$oldid = $oldanswer->ID;
								$answersDb[$oldid] = $oldid;
							}
							foreach($answers as $answer) {
								$question = array(
									'FK_Question' => $id,
									'Symbol' => $answer['symbol'],
									'Answer' => $answer['answer']
								);
								if($answer['id'] == 0) {
									$this->picture_landmark_model->insert($question);
								} else {
									$this->picture_landmark_model->update($answer['id'], $question);
									unset($answersDb[$answer['id']]);
								}
							}
							foreach($answersDb as $answerDb) {
								$this->picture_landmark_model->delete($answerDb);
							}
						}

						redirect('Question');
					} else {
						$output = array(
							'topics' => $this->topic_model->get_tree(),
							'focus_topic' => $this->topic_model->get($this->input->post('focus_topic')),
							'question_type' => $this->question_type_model->get($this->input->post('question_type')),
							'id' => $this->input->post('id'),
							'name' => $this->input->post('name'),
							'points' => $this->input->post('points'),
							'picture_name' => $this->input->post('picture_name'),
							'upload_data' => $this->input->post('upload_data'),
							'nbAnswer' => $this->input->post('nbAnswer'),
							'answers' => []
						);
						$answers = $this->input->post('reponses');

						foreach($answers as $answer) {
							$output['answers'][] = array(
								'id' => $answer['id'] ?? 0,
								'symbol' => $answer['symbol'] ?? '',
								'answer' => $answer['answer'] ?? ''
							);
						}
						$this->display_view('picture_landmark/add', $output);
					}
				} else {
					$output = array(
						'topics' => $this->topic_model->get_tree(),
						'focus_topic' => $this->topic_model->get($this->input->post('focus_topic')),
						'question_type' => $this->question_type_model->get($this->input->post('question_type')),
						'id' => $this->input->post('id'),
						'name' => $this->input->post('name'),
						'points' => $this->input->post('points'),
						'picture_name' => $this->input->post('picture_name'),
						'upload_data' => $this->input->post('upload_data'),
						'nbAnswer' => $this->input->post('nbAnswer'),
						'answers' => []
					);
					$answers = $this->input->post('reponses');

					foreach($answers as $answer) {
						$output['answers'][] = array(
							'id' => $answer['id'] ?? 0,
							'symbol' => $answer['symbol'] ?? '',
							'answer' => $answer['answer'] ?? ''
						);
					}

					if(!is_null($this->input->post('add_answer'))) {
						$output['answers'][] = array(
							'id' => 0,
							'symbol' => '',
							'answer' => ''
						);
						$output['nbAnswer']++;
					}

					for($i = 0; $i < $output['nbAnswer']; $i++) {
						if(is_null($this->input->post('del_answer'.$i))) continue;
						elseif($output['nbAnswer'] <= 1) $output['nbAnswer'] = $this->input->post('nbAnswer');
						else {
							$output['nbAnswer']--;
							for($j = $i; $j < $output['nbAnswer'];) {
								$output['answers'][$j] = $output['answers'][++$j];
							}
						}
					}

					if(!is_null($this->input->post('change_picture')))
						$this->display_view('picture_landmark/file', $output);
					else
						$this->display_view('picture_landmark/add', $output);
				}
				break;
			default:
				redirect('Question');
		}
	}

	// Import functions are omitted as they are no longer needed
}