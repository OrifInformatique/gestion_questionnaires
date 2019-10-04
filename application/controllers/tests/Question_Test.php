<?php

include(__DIR__.'/../../core/MY_Test_Trait.php');
include(__DIR__.'/../Question.php');

/**
 * Test class for Question controller
 * 
 * @author      Orif, section informatique (ViDi, MeSa, BuYa)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class Question_Test extends Question {
    use MY_Test_Trait;
    /**
     * Stores the tests' results
     *
     * @var array
     */
    private $_test_results = [
        'question' => [
            'add' => [],
            'update' => [],
            'delete' => []
        ],
        'clozetext' => [
            'add' => [],
            'update' => []
        ],
        'freeanswer' => [
            'add' => [],
            'update' => []
        ],
        'multipleanswer' => [
            'add' => [],
            'update' => []
        ],
        'multiplechoice' => [
            'add' => [],
            'update' => []
        ],
        'picturelandmark' => [
            'add' => [],
            'update' => []
        ],
        'answerdistribution' => [
            'add' => [],
            'update' => []
        ],
        'tablecell' => [
            'add' => [],
            'update' => []
        ]
    ];

    /**
     * Contains the list of tests to run
     *
     * @var array
     */
    private $_tests = [
    ];

    /**
     * Saves the dummy values
     *
     * @var array
     */
    private $_dummy_values = [
        'question' => [
            'topic' => 0,
            'question_type' => 1,
            'question' => 'dummy_question',
            'nb_desired_answers' => 1,
            'table_with_definition' => FALSE,
            'picture_name' => 'picture.jpg',
            'points' => 999,
            'archive' => 0
        ],
        'free_answer' => [
            'answer' => 'dummy_free_answer'
        ],
        'multiple_answer' => [
            'answer' => 'dummy_multiple_answer'
        ],
        'picture_landmark' => [
            'symbol' => 'I',
            'answer' => 'dummy_picture_landmark'
        ],
        'multiple_choice' => [
            'answer' => 'dummy_multiple_choice',
            'valid' => TRUE
        ],
        'cloze_text' => [
            'cloze_text' => 'dummy_[...]_text'
        ],
        'cloze_text_answer' => [
            'answer' => 'cloze',
            'answer_order' => 0
        ],
        'answer_distribution' => [
            'question_part' => 'dummy_ans',
            'answer_part' => 'wer_distribution'
        ],
        'table_cell' => [
            'content' => 'dummy_table_cell',
            'column' => 0,
            'row' => 0,
            'header' => 1,
            'display_in_question' => 0
        ],
    ];

    public function __construct()
    {
        parent::__construct();

        // Dummy values
        $this->_dummy_values['question']['topic'] = $this->topic_model->get_by('FK_Parent_Topic IS NOT NULL')->ID;
        $this->_dummy_values['question']['question_type'] = $this->question_type_model->get_all()[0]->ID;
    }

    public function test()
    {
        if(in_array('question_add', $this->_tests)) {
            $this->test_question_add_step_1_no_effect();
            $this->test_question_add_step_2_no_effect();
            $this->test_question_add_step_2_bad_type();
            $this->test_question_add_step_2_bad_topic();
        }
        if(in_array('question_update', $this->_tests)) {
        }
        if(in_array('question_delete', $this->_tests)) {
        }
        if(in_array('cloze_text_add', $this->_tests)) {
        }
        if(in_array('cloze_text_update', $this->_tests)) {
        }
        if(in_array('free_answer_add', $this->_tests)) {
        }
        if(in_array('free_answer_update', $this->_tests)) {
        }
        if(in_array('multiple_answer_add', $this->_tests)) {
        }
        if(in_array('multiple_answer_update', $this->_tests)) {
        }
        if(in_array('multiple_choice_add', $this->_tests)) {
        }
        if(in_array('multiple_choice_update', $this->_tests)) {
        }
        if(in_array('picture_landmark_add', $this->_tests)) {
        }
        if(in_array('picture_landmark_update', $this->_tests)) {
        }
        if(in_array('answer_distribution_add', $this->_tests)) {
        }
        if(in_array('answer_distribution_update', $this->_tests)) {
        }
        if(in_array('table_cell_add', $this->_tests)) {
        }
        if(in_array('table_cell_update', $this->_tests)) {
        }
    }

    /***********************
     * QUESTION ADDING TESTS
     ***********************/
    /**
     * Test for add($step = 1)
     *
     * @return void
     */
    private function test_question_add_step_1_no_effect()
    {
        $this->_db_errors_save();
        $this->_send_form_request('add');

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }

        $this->_test_results['question']['add']['step_1_no_effect'] = $result;
    }
    /**
     * Test for add($step = 2)
     *
     * @return void
     */
    private function test_question_add_step_2_no_effect()
    {
        $focus_topic = $this->topic_model->get_by('FK_Parent_Topic IS NOT NULL')->ID;
        $question_type = $this->question_type_model->get_all()[0]->ID;

        $this->_db_errors_save();
        $this->_send_form_request('add', [
            'focus_topic' => $focus_topic,
            'question_type' => $question_type
        ], [1]);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }

        $this->_test_results['question']['add']['step_2_no_effect'] = $result;
    }
    /**
     * Test for failed add($step = 2) due to question_type not existing
     *
     * @return void
     */
    private function test_question_add_step_2_bad_type()
    {
        $focus_topic = $this->topic_model->get_by('FK_Parent_Topic IS NOT NULL')->ID;
        $question_type = $this->question_type_model->get_next_id()+1;

        $this->_db_errors_save();
        $this->_send_form_request('add', [
            'focus_topic' => $focus_topic,
            'question_type' => $question_type
        ], [1]);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }

        $this->_test_results['question']['add']['step_2_bad_type'] = $result;
    }
    /**
     * Test for failed add($step = 2) due to topic not existing
     *
     * @return void
     */
    private function test_question_add_step_2_bad_topic()
    {
        $focus_topic = $this->topic_model->get_next_id()+1;
        $question_type = $this->question_type_model->get_all()[0]->ID;

        $this->_db_errors_save();
        $this->_send_form_request('add', [
            'focus_topic' => $focus_topic,
            'question_type' => $question_type
        ], [1]);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }

        $this->_test_results['question']['add']['step_2_bad_topic'] = $result;
    }

    /*************************
     * QUESTION UPDATING TESTS
     *************************/
    private function test_question_update_no_error(int $dummy_question_id)
    {
        $name = 'question_update_no_error';
        $points = 666;

        $this->_db_errors_save();
        $this->_send_form_request('form_update', [
            'name' => $name,
            'points' => $points,
            'id' => $dummy_question_id
        ]);

        $question = $this->question_model->get($dummy_question_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($question->Question !== $name) {
            $result->success = FALSE;
            $result->errors[] = "Expected question {$question->ID}'s question to be '{$name}', but is '{$question->Question}'";
        }

        $this->_test_results['question']['update']['no_error'] = $result;
    }

    /*************************
     * QUESTION DELETING TESTS
     *************************/
    /*************************
     * CLOZE TEXT ADDING TESTS
     *************************/
    /***************************
     * CLOZE TEXT UPDATING TESTS
     ***************************/
    /**************************
     * FREE ANSWER ADDING TESTS
     **************************/
    /****************************
     * FREE ANSWER UPDATING TESTS
     ****************************/
    /******************************
     * MULTIPLE ANSWER ADDING TESTS
     ******************************/
    /********************************
     * MULTIPLE ANSWER UPDATING TESTS
     ********************************/
    /******************************
     * MULTIPLE CHOICE ADDING TESTS
     ******************************/
    /********************************
     * MULTIPLE CHOICE UPDATING TESTS
     ********************************/
    /*******************************
     * PICTURE LANDMARK ADDING TESTS
     *******************************/
    /*********************************
     * PICTURE LANDMARK UPDATING TESTS
     *********************************/
    /**********************************
     * ANSWER DISTRIBUTION ADDING TESTS
     **********************************/
    /************************************
     * ANSWER DISTRIBUTION UPDATING TESTS
     ************************************/
    /*************************
     * TABLE CELL ADDING TESTS
     *************************/
    /***************************
     * TABLE CELL UPDATING TESTS
     ***************************/

    /*************************
     * DUMMIES-RELATED METHODS
     * Do not use with actual objects!
     *************************/
    /**
     * Creates a dummy question
     *
     * @param integer $question_type = Type of the question,
     *      defaults to the first found
     * @return integer = ID of the dummy question
     */
    private function _dummy_question_create(int $question_type = 0) : int
    {
        if(is_null($this->question_type_model->get($question_type))) {
            $question_type = 0;
            log_message('error','Question type is invalid, defaulting to dummy value');
        }
        $values = $this->_dummy_values['question'];

        $dummyQuestion = Array(
            'FK_Topic' => $values['topic'],
            'FK_Question_Type' => $question_type ?: $values['question_type'],
            'Question' => $values['question'],
            'Nb_Desired_Answers' => $values['nb_desired_answers'],
            'Table_With_Definition' => $values['table_with_definition'],
            'Picture_Name' => $values['picture_name'],
            'Points' => $values['points']
        );

        return $this->question_model->insert($dummyQuestion);
    }
    /**
     * Resets a dummy question
     *
     * @param integer $dummy_question_id = ID of the dummy question
     * @param integer $question_type = Type of the dummy question,
     *      defaults to the first found
     * @return void
     */
    private function _dummy_question_reset(int $dummy_question_id, int $question_type = 0)
    {
        if(is_null($this->question_type_model->get($question_type))) {
            $question_type = 0;
            log_message('error','Question type is invalid, defaulting to dummy value');
        }
        $values = $this->_dummy_values['question'];

        $dummyQuestion = Array(
            'FK_Topic' => $values['topic'],
            'FK_Question_Type' => $question_type ?: $values['question_type'],
            'Question' => $values['question'],
            'Nb_Desired_Answers' => $values['nb_desired_answers'],
            'Table_With_Definition' => $values['table_with_definition'],
            'Picture_Name' => $values['picture_name'],
            'Points' => $values['points']
        );

        $this->question_model->update($dummy_question_id, $dummyQuestion);
    }
    /**
     * Deletes a dummy question and all the linked questions,
     * independently from type.
     *
     * @param integer $dummy_question_id = ID of the question to delete
     * @return void
     */
    private function _dummy_question_delete(int $dummy_question_id)
    {
        // Prepare the filter
        $filter = "FK_Question = {$dummy_question_id}";

        // Delete the questions without children
        $this->free_answer_model->delete_by($filter);
        $this->multiple_answer_model->delete_by($filter);
        $this->picture_landmark_model->delete_by($filter);
        $this->multiple_choice_model->delete_by($filter);
        $this->answer_distribution_model->delete_by($filter);
        $this->table_cell_model->delete_by($filter);

        // Delete cloze_text_answers and parents
        $cloze_texts = $this->cloze_text_model->get_many_by($filter);
        foreach($cloze_texts as $cloze_text) {
            $this->cloze_text_answer_model->delete_by("FK_Cloze_Text = {$cloze_text->ID}");
            $this->cloze_text_model->delete($cloze_text->ID);
        }

        // Delete related picture
        if(!empty($picture_name = $this->question_model->get($dummy_question_id)->Picture_Name)) {
            unlink("uploads/{$picture_name}");
        }

        // No deletion for questionnaires, as they are not linked here
        // Delete question, nothing is left
        $this->question_model->delete($dummy_question_id);
    }

    /**
     * Creates a dummy free answer
     *
     * @param integer $question_id = ID of the question to link to,
     *      if left out, will use a new question
     * @return integer = ID of the dummy free answer
     */
    private function _dummy_free_answer_create(int $question_id = 0) : int
    {
        $question_id = $question_id ?: $this->_dummy_question_create(6);

        $dummyFreeAnswer = array(
            'FK_Question' => $question_id,
            'Answer' => $this->_dummy_values['free_answer']['answer']
        );

        return $this->free_answer_model->insert($dummyFreeAnswer);
    }
    /**
     * Resets a dummy free answer
     *
     * @param integer $dummy_free_answer_id = ID of the free answer
     * @return void
     */
    private function _dummy_free_answer_reset(int $dummy_free_answer_id)
    {
        $question_id = $this->free_answer_model->get($dummy_free_answer_id)->FK_Question;
        $this->_dummy_question_reset($question_id, 6);

        $dummyFreeAnswer = array(
            'Answer' => $this->_dummy_values['free_answer']['answer']
        );
        $this->free_answer_model->update($dummy_free_answer_id, $dummyFreeAnswer);
    }
    /**
     * Deletes a dummy free answer
     *
     * @param integer $dummy_free_answer_id = ID of the dummy free answer
     * @return void
     */
    private function _dummy_free_answer_delete(int $dummy_free_answer_id)
    {
        $question_id = $this->free_answer_model->get($dummy_free_answer_id)->FK_Question;

        $this->free_answer_model->delete($dummy_free_answer_id);

        $this->_dummy_question_delete($question_id);
    }

    /**
     * Creates a dummy multiple answer
     *
     * @param integer $question_id = ID of the question to link to,
     *      if left out, will use a new question
     * @return integer = ID of the multiple answer
     */
    private function _dummy_multiple_answer_create(int $question_id = 0) : int
    {
        $question_id = $question_id ?: $this->_dummy_question_create(2);

        $dummyMultipleAnswer = array(
            'FK_Question' => $question_id,
            'Answer' => $this->_dummy_values['multiple_answer']['answer']
        );

        return $this->multiple_answer_model->insert($dummyMultipleAnswer);
    }
    /**
     * Resets a dummy multiple answer
     *
     * @param integer $dummy_multiple_answer_id = ID of the dummy free answer
     * @return void
     */
    private function _dummy_multiple_answer_reset(int $dummy_multiple_answer_id)
    {
        $question_id = $this->multiple_answer_model->get($dummy_multiple_answer_id)->FK_Question;
        $this->_dummy_question_reset($question_id, 2);

        $dummyMultipleAnswer = array(
            'Answer' => $this->_dummy_values['multiple_answer']['answer']
        );
        $this->multiple_answer_model->update($dummy_multiple_answer_id, $dummyMultipleAnswer);
    }
    /**
     * Deletes a multiple answer
     *
     * @param integer $dummy_multiple_answer_id = ID of the multiple answer to delete
     * @return void
     */
    private function _dummy_multiple_answer_delete(int $dummy_multiple_answer_id)
    {
        $question_id = $this->multiple_answer_model->get($dummy_multiple_answer_id)->FK_Question;

        $this->multiple_answer_model->delete($dummy_multiple_answer_id);

        $this->_dummy_question_delete($question_id);
    }

    /**
     * Creates a dummy picture landmark
     *
     * @param integer $question_id = ID of the question to link to,
     *      if left out, will use a new question
     * @return integer = ID of the picture landmark answer
     */
    private function _dummy_picture_landmark_create(int $question_id = 0) : int
    {
        $question_id = $question_id ?: $this->_dummy_question_create(7);

        $dummyPictureLandmark = array(
            'FK_Question' => $question_id,
            'Symbol' => $this->_dummy_values['picture_landmark']['symbol'],
            'Answer' => $this->_dummy_values['picture_landmark']['answer']
        );

        return $this->picture_landmark_model->insert($dummyPictureLandmark);
    }
    /**
     * Resets a dummy picture landmark
     *
     * @param integer $dummy_picture_landmark_id = ID of the dummy picture landmark
     * @return void
     */
    private function _dummy_picture_landmark_reset(int $dummy_picture_landmark_id)
    {
        $question_id = $this->picture_landmark_model->get($dummy_picture_landmark_id)->FK_Question;
        $this->_dummy_question_reset($question_id, 7);

        $dummyPictureLandmark = array(
            'Symbol' => $this->_dummy_values['picture_landmark']['symbol'],
            'Answer' => $this->_dummy_values['picture_landmark']['answer']
        );
        $this->picture_landmark_model->update($dummy_picture_landmark_id, $dummyPictureLandmark);
    }
    /**
     * Deletes a picture landmark
     *
     * @param integer $dummy_picture_landmark_id = ID of the picture landmark to delete
     * @return void
     */
    private function _dummy_picture_landmark_delete(int $dummy_picture_landmark_id)
    {
        $question_id = $this->picture_landmark_model->get($dummy_picture_landmark_id)->FK_Question;

        $this->picture_landmark_model->delete($dummy_picture_landmark_id);

        $this->_dummy_question_delete($question_id);
    }

    /**
     * Creates a multiple choice
     *
     * @param integer $question_id = ID of the question to link to,
     *      if left out, will use a new question
     * @return integer = ID of the multiple choice
     */
    private function _dummy_multiple_choice_create(int $question_id = 0) : int
    {
        $question_id = $question_id ?: $this->_dummy_question_create(1);

        $dummyMultipleChoice = array(
            'FK_Question' => $question_id,
            'Valid' => $this->_dummy_values['multiple_choice']['valid'],
            'Answer' => $this->_dummy_values['multiple_choice']['answer']
        );

        return $this->multiple_choice_model->insert($dummyMultipleChoice);
    }
    /**
     * Resets a dummy multiple choice
     *
     * @param integer $dummy_multiple_choice_id = ID of the multiple choice
     * @return void
     */
    private function _dummy_multiple_choice_reset(int $dummy_multiple_choice_id)
    {
        $question_id = $this->multiple_choice_model->get($dummy_multiple_choice_id)->FK_Question;
        $this->_dummy_question_reset($question_id, 1);

        $dummyMultipleChoice = array(
            'Valid' => $this->_dummy_values['multiple_choice']['valid'],
            'Answer' => $this->_dummy_values['multiple_choice']['answer']
        );
        $this->multiple_choice_model->update($dummy_multiple_choice_id, $dummyMultipleChoice);
    }
    /**
     * Deletes a multiple choice
     *
     * @param integer $dummy_multiple_choice_id = ID of the multiple choice to delete
     * @return void
     */
    private function _dummy_multiple_choice_delete(int $dummy_multiple_choice_id)
    {
        $question_id = $this->multiple_choice_model->get($dummy_multiple_choice_id)->FK_Question;

        $this->multiple_choice_model->delete($dummy_multiple_choice_id);

        $this->_dummy_question_delete($question_id);
    }

    /**
     * Creates a dummy answer distribution
     *
     * @param integer $question_id = ID of the question to link to,
     *      if left out, will use a new question
     * @return integer = ID of the dummy answer distribution
     */
    private function _dummy_answer_distribution_create(int $question_id = 0) : int
    {
        $question_id = $question_id ?: $this->_dummy_question_create(3);

        $dummyAnswerDistribution = array(
            'FK_Question' => $question_id,
            'Question_Part' => $this->_dummy_values['answer_distribution']['question_part'],
            'Answer_Part' => $this->_dummy_values['answer_distribution']['answer_part']
        );

        return $this->answer_distribution_model->insert($dummyAnswerDistribution);
    }
    /**
     * Resets a dummy answer distribution
     *
     * @param integer $dummy_answer_distribution_id = ID of the dummy answer distribution
     * @return void
     */
    private function _dummy_answer_distribution_reset(int $dummy_answer_distribution_id)
    {
        $question_id = $this->answer_distribution_model->get($dummy_answer_distribution_id)->FK_Question;
        $this->_dummy_question_reset($question_id, 3);

        $dummyAnswerDistribution = array(
            'Question_Part' => $this->_dummy_values['answer_distribution']['question_part'],
            'Answer_Part' => $this->_dummy_values['answer_distribution']['answer_part']
        );
        $this->answer_distribution_model->update($dummy_answer_distribution_id, $dummyAnswerDistribution);
    }
    /**
     * Deletes a dummy answer distribution
     *
     * @param integer $dummy_answer_distribution_id = ID of the answer distribution to delete
     * @return void
     */
    private function _dummy_answer_distribution_delete(int $dummy_answer_distribution_id)
    {
        $question_id = $this->answer_distribution_model->get($dummy_answer_distribution_id)->FK_Question;

        $this->answer_distribution_model->delete($dummy_answer_distribution_id);

        $this->_dummy_question_delete($question_id);
    }

    /**
     * Creates a dummy table cell
     *
     * @param integer $question_id = ID of the question to link to,
     *      if left out, will use a new question
     * @return integer = ID of the dummy table cell
     */
    private function _dummy_table_cell_create(int $question_id = 0) : int
    {
        $question_id = $question_id ?: $this->_dummy_question_create(5);

        $dummyTableCell = array(
            'FK_Question' => $question_id,
            'Content' => $this->_dummy_values['table_cell']['content'],
            'Column_Nb' => $this->_dummy_values['table_cell']['column'],
            'Row_Nb' => $this->_dummy_values['table_cell']['row'],
            'Header' => $this->_dummy_values['table_cell']['header'],
            'Display_In_Question' => $this->_dummy_values['table_cell']['display_in_question']
        );

        return $this->table_cell_model->insert($dummyTableCell);
    }
    /**
     * Resets a dummy table cell
     *
     * @param integer $dummy_table_cell_id = ID of the dummy table cell
     * @return void
     */
    private function _dummy_table_cell_reset(int $dummy_table_cell_id)
    {
        $question_id = $this->table_cell_model->get($dummy_table_cell_id)->FK_Question;
        $this->_dummy_question_reset($question_id, 5);

        $dummyTableCell = array(
            'Content' => $this->_dummy_values['table_cell']['content'],
            'Column_Nb' => $this->_dummy_values['table_cell']['column'],
            'Row_Nb' => $this->_dummy_values['table_cell']['row'],
            'Header' => $this->_dummy_values['table_cell']['header'],
            'Display_In_Question' => $this->_dummy_values['table_cell']['display_in_question']
        );
        $this->table_cell_model->update($dummy_table_cell_id, $dummyTableCell);
    }
    /**
     * Deletes a dummy table cell
     *
     * @param integer $dummy_table_cell_id = ID of the table cell to delete
     * @return void
     */
    private function _dummy_table_cell_delete(int $dummy_table_cell_id)
    {
        $question_id = $this->table_cell_model->get($dummy_table_cell_id)->FK_Question;

        $this->table_cell_model->delete($dummy_table_cell_id);

        $this->_dummy_question_delete($question_id);
    }

    /**
     * Creates a dummy cloze text
     *
     * @param integer $question_id = ID of the question to link to,
     *      if left out, will use a new question
     * @return integer = ID of the dummy cloze text
     */
    private function _dummy_cloze_text_create(int $question_id = 0) : int
    {
        $question_id = $question_id ?: $this->_dummy_question_create(4);

        $dummyClozeText = array(
            'FK_Question' => $question_id,
            'Cloze_Text' => $this->_dummy_values['cloze_text']['cloze_text']
        );

        return $this->cloze_text_model->insert($dummyClozeText);
    }
    /**
     * Resets a dummy cloze text
     *
     * @param integer $dummy_cloze_text_id = ID of the dummy cloze text
     * @return void
     */
    private function _dummy_cloze_text_reset(int $dummy_cloze_text_id)
    {
        $question_id = $this->cloze_text_model->get($dummy_cloze_text_id)->FK_Question;
        $this->_dummy_question_reset($question_id, 4);

        $dummyClozeText = array(
            'Cloze_Text' => $this->_dummy_values['cloze_text']['cloze_text']
        );
        $this->cloze_text_model->update($dummy_cloze_text_id, $dummyClozeText);
    }
    /**
     * Deletes a dummy cloze text
     *
     * @param integer $dummy_cloze_text_id = ID of the cloze text to delete
     * @return void
     */
    private function _dummy_cloze_text_delete(int $dummy_cloze_text_id)
    {
        $question_id = $this->cloze_text_model->get($dummy_cloze_text_id)->FK_Question;

        $this->cloze_text_model->delete($dummy_cloze_text_id);

        $this->_dummy_question_delete($question_id);
    }

    /**
     * Creates a dummy cloze text answer
     *
     * @param integer $cloze_text_id = ID of the cloze text to link to,
     *      if left out, will use a new cloze text
     * @return integer = ID of the dummy cloze text answer
     */
    private function _dummy_cloze_text_answer_create(int $cloze_text_id = 0) : int
    {
        $cloze_text_id = $cloze_text_id ?: $this->_dummy_cloze_text_create();

        $dummyClozeTextAnswer = array(
            'FK_Cloze_Text' => $cloze_text_id,
            'Answer' => $this->_dummy_values['cloze_text_answer']['answer'],
            'Answer_Order' => $this->_dummy_values['cloze_text_answer']['answer_order'],
        );

        return $this->cloze_text_answer_model->insert($dummyClozeTextAnswer);
    }
    /**
     * Resets a dummy cloze text answer
     *
     * @param integer $dummy_cloze_text_answer_id = ID of the dummy cloze text answer
     * @return void
     */
    private function _dummy_cloze_text_answer_reset(int $dummy_cloze_text_answer_id)
    {
        $cloze_text_id = $this->cloze_text_answer_model->get($dummy_cloze_text_answer_id)->FK_Cloze_TextIndex;
        $this->_dummy_cloze_text_reset($cloze_text_id);

        $dummyClozeTextAnswer = array(
            'Answer' => $this->_dummy_values['cloze_text_answer']['answer'],
            'Answer_Order' => $this->_dummy_values['cloze_text_answer']['answer_order'],
        );
        $this->cloze_text_answer_model->update($dummy_cloze_text_answer_id, $dummyClozeTextAnswer);
    }
    /**
     * Deletes a dummy cloze text answer
     *
     * @param integer $dummy_cloze_text_answer_id = ID of the cloze text answer to delete
     * @return void
     */
    private function _dummy_cloze_text_answer_delete(int $dummy_cloze_text_answer_id)
    {
        $cloze_text_id = $this->cloze_text_answer_model->get($dummy_cloze_text_answer_id)->FK_Cloze_TextIndex;

        $this->cloze_text_answer_model->delete($dummy_cloze_text_answer_id);

        $this->_dummy_cloze_text_delete($cloze_text_id);
    }

    /***********
     * OVERRIDES
     ***********/
    /**
     * Prevents view displaying, call `parent::display_view` instead
     *
     * @param mixed $view_parts
     * @param mixed|null $data
     * @return void
     */
    public function display_view($view_parts, $data = NULL) { }
}