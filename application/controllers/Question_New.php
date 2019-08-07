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
	}

	/**
	 * Display question list
	 *
	 * @param int $page = The currently selected page
	 */
	// Libraries: pagination
	public function index($page = 1) {
		$this->load->model(['topic_model', 'question_model', 'question_type_model']);
		$this->load->library('pagination');

		$module = $this->input->get('module');
		$topic = $this->input->get('topic');
		$type = $this->input->get('type');
		$search = $this->input->get('search');
		$sort = $this->input->get('sort');
		$where = 'Archive = 0';
		$orderby = '';
		$questions = array();

		if(!empty($module) && !empty($topic)) {
			$nbTopic = $this->topic_model->count_by(array(
				'ID' => $topic,
				'FK_Parent_Topic' => $module
			));
			if(!$nbTopic) $topic = NULL;
		}

		if(!empty($module) && !empty($topic) && !empty($type))
			$_SESSION['filters'] = 'Question?module='.$module.'&topic'.$topic.'&type='.$type;

		if(isset($_SESSION['filters']) && strpos($this->input->server('REQUEST_URI'), '?') === FALSE)
			redirect($_SESSION['filters']);

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

		$this->db->order_by($orderby);
		echo $where;
		$questions = $this->question_model->with_all()->get_many_by($where);

		if(($page - 1) * ITEMS_PER_PAGE > count($questions))
			redirect('question/index/1');

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
		$this->load->model('question_model');

		$question = $this->question_model->get($id);
		if(is_null($question)) redirect('Question');
		elseif(is_null($action)) {
			$output = get_object_vars($question);
			$this->display_view('questions/confirm', $output);
		} else {
			$this->question_model->delete($id);
			redirect('Question');
		}
	}

	/**
	 * Form validation to update question
	 */
	public function form_update() {
		$this->load->model('question_model');

		$this->form_validation->set_rules('name', 'Title', 'required');
		$this->form_validation->set_rules('points', 'Points', ['required','numeric']);

		$id = $this->input->post('id');
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
		$this->load->model(['question_model', 'topic_model', 'question_type_model',
			'multiple_choice_model', 'multiple_answer_model', 'cloze_text_model',
			'free_answer_model', 'picture_landmark_model', 'cloze_text_answer_model']);

		$question = $this->question_model->get($id);
		if(is_null($question)) redirect('Question');

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
				$output['nbAnswer'] = conut($answers);
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
		$this->load->model(['question_model', 'multiple_choice_model', 'multiple_answer_model',
			'answer_distribution_model', 'cloze_text_model', 'cloze_text_answer_model',
			'table_cell_model', 'free_answer_model', 'picture_landmark_model']);

		$question = $this->question_model->get($id);
		if(is_null($question)) redirect('Question');

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
		$this->load->model(['topic_model', 'question_type_model']);

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
		$this->load->model(['topic_model', 'question_type_model', 'question_model',
			'multiple_answer_model', 'multiple_choice_model']);
		if(!is_null($this->input->post('cancel'))) redirect('Question');
		elseif(!is_null($this->input->post('save'))) {
			$this->form_validation->set_rules('name', $this->lang->line('question_text'), 'required');
			$this->form_validation->set_rules('points', $this->lang->line('points'), 'required|numeric|greater_than_equal_to[0]');
			for($i = 0; $i < $this->input->post('nbAnswer'); $i++) {
				$this->form_validation->set_rules('reponses['.$i.'][question]', $this->lang->line('answers_list'), 'required');
				$this->form_validation->set_rules('reponses['.$i.'][answer]', $this->lang->line('valid_answer'), 'required');
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
					$answers = $this->input->post('reponses');
					foreach($answers as $answer) {
						$answerNew = array(
							'FK_Question' => $id,
							'Answer' => $answer['answer']
						);
						$this->multiple_answer_model->insert($answerNew);
					}
				} else {
					$this->question_model->update($id, $question);

					$answers = $this->input->post('reponses');
					$oldanswers = $this->multiple_choice_model->get_many_by('FK_Question = ' . $_POST['id']);
					$answersDb = [];

					foreach($oldanswers as $oldanswer) {
						$oldid = $oldanswer->ID;
						$answersDb[$oldid] = $oldid;
					}
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

			if(!empty($this->input->post('add_answer'))) {
				$output['answers'][] = array(
					'id' => 0,
					'question' => '',
					'answer' => ''
				);
				$output['nbAnswer']++;
			}

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
		$this->load->model(['topic_model', 'question_type_model']);
		if(!is_null($this->input->post('cancel'))) redirect('Question');
		elseif(!is_null($this->input->post('save'))) {
		} else {
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

			if(!is_null($this->input->post('add_answer'))) {
				$output['answers'][] = array(
					'id' => 0,
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

			$this->display_view('multiple_answer/add', $output);
		}
	}
}