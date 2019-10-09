<?php

include(__DIR__.'/../../core/MY_Test_Trait.php');

/**
 * Controller for model testing
 * 
 * @author      Orif, section informatique (ViDi, BuYa, MeSa)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class Model_Test extends MY_Controller {
    use MY_Test_Trait;
    /**
     * Stores the tests' results
     *
     * @var array
     */
    private $_test_results = [
        'user_model' => [
            'check_password' => [],
            'hard_delete' => []
        ],
        'topic_model' => [
            'get_tree' => []
        ],
        'question_model' => [
            'getRNDQuestions' => [],
            'getNbQuestionByTopic' => []
        ]
    ];
    /**
     * Contains the list of tests to run
     *
     * @var array
     */
    private $_tests = [
        'user_model_check_password', 'user_model_hard_delete',
        'topic_model_get_tree',
        'question_model_getrndquestion', 'question_model_getnbquestionbytopic'
    ];
    /**
     * List of models to load
     *
     * @var array
     */
    private $_models = [
        'user_model', 'topic_model', 'question_model'
    ];
    /**
     * Default values for dummies
     *
     * @var array
     */
    private $_dummy_values = [
        'user' => [
            'user' => 'dummy_user',
            'password' => 'dummy_password'
        ],
        'topic' => ['topic' => 'dummy_topic'],
        'module' => ['topic' => 'dummy_module'],
        'question' => [
            'question' => 'dummy_question',
            'topic' => 0,
            'question_type' => 0
        ]
    ];

    public function __construct()
    {
        parent::__construct();

        $this->load->model($this->_models);
        $this->load->model('question_type_model');

        // Dummy values
        $this->_dummy_values['question']['topic'] = $this->topic_model->get_by('FK_Parent_Topic IS NOT NULL')->ID;
        $this->_dummy_values['question']['question_type'] = $this->question_type_model->get_all()[0]->ID;
    }

    public function test()
    {
        if(in_array('user_model_check_password', $this->_tests)) {
            $dummy_user_id = $this->_dummy_user_create();

            $this->test_user_model_c_p_no_error();
            $this->test_user_model_c_p_wrong_password();
            $this->test_user_model_c_p_not_exist();

            $this->_dummy_user_delete($dummy_user_id);
        }
        if(in_array('user_model_hard_delete', $this->_tests)) {
            $this->test_user_model_hd_no_error();
            $this->test_user_model_hd_not_exist();
        }
        if(in_array('topic_model_get_tree', $this->_tests)) {
            $this->test_topic_model_gt_comp();
        }
        if(in_array('question_model_getrndquestion', $this->_tests)) {
            $this->test_question_model_grq_no_error();
            $this->test_question_model_grq_not_exist();
            $this->test_question_model_grq_not_topic();
            $this->test_question_model_grq_negative_nb_question();
            $this->test_question_model_grq_negative_topic();
        }
        if(in_array('question_model_getnbquestionbytopic', $this->_tests)) {
            $this->test_question_model_gnqbt_no_error();
            $this->test_question_model_gnqbt_not_topic();
            $this->test_question_model_gnqbt_negative();
        }

        // Display test results
        $output['test_results'] = $this->_test_results;
        parent::display_view('tests/tests_results_new.php', $output);
    }

    /************************************
     * `user_model::check_password` TESTS
     ************************************/
    /**
     * Test for `user_model::check_password`
     *
     * @return void
     */
    private function test_user_model_c_p_no_error()
    {
        $username = $this->_dummy_values['user']['user'];
        $password = $this->_dummy_values['user']['password'];

        $this->_db_errors_save();
        $cp = $this->user_model->check_password($username, $password);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($cp == FALSE) {
            $result->success = FALSE;
            $result->errors[] = "Expected <code>user_model->check_password({$username},{$password})</code> to return TRUE, but it does not";
        }

        $this->_test_results['user_model']['check_password']['no_error'] = $result;
    }
    /**
     * Test for failed `user_model::check_password` due to password being wrong
     *
     * @return void
     */
    private function test_user_model_c_p_wrong_password()
    {
        $username = $this->_dummy_values['user']['user'];
        $password = $this->_dummy_values['user']['password'].'_wrong';

        $this->_db_errors_save();
        $cp = $this->user_model->check_password($username, $password);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($cp == TRUE) {
            $result->success = FALSE;
            $result->errors[] = "Expected <code>user_model->check_password({$username},{$password})</code> to return FALSE, but it does not";
        }

        $this->_test_results['user_model']['check_password']['wrong_password'] = $result;
    }
    /**
     * Test for failed `user_model::check_password` due to usernot existing
     *
     * @return void
     */
    private function test_user_model_c_p_not_exist()
    {
        $username = $this->_dummy_values['user']['user'];
        $loop = 0;
        while(!is_null($this->user_model->get_by('User', $username.$loop))) {
            $loop++;
        }
        $username .= $loop;
        $password = $this->_dummy_values['user']['password'];

        $this->_db_errors_save();
        $cp = $this->user_model->check_password($username, $password);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($cp == TRUE) {
            $result->success = FALSE;
            $result->errors[] = "Expected <code>user_model->check_password({$username},{$password})</code> to return FALSE, but it does not";
        }

        $this->_test_results['user_model']['check_password']['not_exist'] = $result;
    }

    /*********************************
     * `user_model::hard_delete` TESTS
     *********************************/
    /**
     * Test for `user_model::hard_delete`
     *
     * @return void
     */
    private function test_user_model_hd_no_error()
    {
        $dummy_user_id = $this->_dummy_user_create();

        $this->_db_errors_save();
        $this->user_model->delete($dummy_user_id, TRUE);

        $user = $this->user_model->with_deleted()->get($dummy_user_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!is_null($user)) {
            $result->success = FALSE;
            $result->errors[] = "User {$user->ID} was expected to not exist, but it does";
        }

        $this->_test_results['user_model']['hard_delete']['no_error'] = $result;
    }
    /**
     * Test for failed `user_model::hard_delete` due to user not existing
     *
     * @return void
     */
    private function test_user_model_hd_not_exist()
    {
        $bad_id = $this->user_model->get_next_id()+1;

        $this->_db_errors_save();
        $this->user_model->delete($bad_id, TRUE);

        $user = $this->user_model->with_deleted()->get($bad_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!is_null($user)) {
            $result->success = FALSE;
            $result->errors[] = "User {$user->ID} was expected to not exist, but it does";
        }

        $this->_test_results['user_model']['hard_delete']['not_exist'] = $result;
    }

    /*******************************
     * `topic_model::get_tree` TESTS
     *******************************/
    /**
     * Test for `topic_model::get_tree`
     *
     * @return void
     */
    private function test_topic_model_gt_comp()
    {
        $this->_db_errors_save();
        $count_no_archive = count($this->topic_model->get_tree(), COUNT_RECURSIVE);
        $count_with_archive = count($this->topic_model->get_tree(TRUE), COUNT_RECURSIVE);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($count_no_archive > $count_with_archive) {
            $result->success = FALSE;
            $result->errors[] = "Expected \$count_no_archive to be smaller than \$count_with_archive, but it is not";
        }

        $this->_test_results['topic_model']['get_tree']['comp'] = $result;
    }
    /*****************************************
     * `question_model::getRNDQuestions` TESTS
     *****************************************/
    /**
     * Test for `question_model::getRNDQuestions`
     *
     * @return void
     */
    private function test_question_model_grq_no_error()
    {
        $this->_db_errors_save();
        $questions = $this->question_model->getRNDQuestions($this->_dummy_values['question']['topic'], 5);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(count($questions) != 5) {
            $result->success = FALSE;
            $result->errors[] = 'Expected 5 questions from <code>question_model->getRNDQuestions</code>, but found '.count($questions);
        }

        $this->_test_results['question_model']['getRNDQuestions']['no_error'] = $result;
    }
    /**
     * Test for failed `question_model::getRNDQuestions` due to
     * non-existing topic
     *
     * @return void
     */
    private function test_question_model_grq_not_exist()
    {
        $bad_id = $this->topic_model->get_next_id()+1;

        $this->_db_errors_save();
        $questions = $this->question_model->getRNDQuestions($bad_id, 5);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(count($questions) != 0) {
            $result->success = FALSE;
            $result->errors[] = 'Expected no questions from <code>question_model->getRNDQuestions</code>, but found '.count($questions);
        }

        $this->_test_results['question_model']['getRNDQuestions']['not_exist'] = $result;
    }
    /**
     * Test for failed `question_model::getRNDQuestions` due to
     * topic being a module
     *
     * @return void
     */
    private function test_question_model_grq_not_topic()
    {
        $bad_id = $this->topic_model->get_by('FK_Parent_Topic IS NULL')->ID;

        $this->_db_errors_save();
        $questions = $this->question_model->getRNDQuestions($bad_id, 5);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(count($questions) != 0) {
            $result->success = FALSE;
            $result->errors[] = 'Expected no questions from <code>question_model->getRNDQuestions</code>, but found '.count($questions);
        }

        $this->_test_results['question_model']['getRNDQuestions']['not_topic'] = $result;
    }
    /**
     * Test for failed `question_model::getRNDQuestions` due to a negative
     * amount of questions
     *
     * @return void
     */
    private function test_question_model_grq_negative_nb_question()
    {
        $this->_db_errors_save();
        $questions = $this->question_model->getRNDQuestions($this->_dummy_values['question']['topic'], -1);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(count($questions) != 0) {
            $result->success = FALSE;
            $result->errors[] = 'Expected no questions from <code>question_model->getRNDQuestions</code>, but found '.count($questions);
        }

        $this->_test_results['question_model']['getRNDQuestions']['negative_nb_question'] = $result;
    }
    /**
     * Test for failed `question_model::getRNDQuestions` due to
     * topic being negative
     *
     * @return void
     */
    private function test_question_model_grq_negative_topic()
    {
        $this->_db_errors_save();
        $questions = $this->question_model->getRNDQuestions(-1, 5);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(count($questions) != 0) {
            $result->success = FALSE;
            $result->errors[] = 'Expected no questions from <code>question_model->getRNDQuestions</code>, but found '.count($questions);
        }

        $this->_test_results['question_model']['getRNDQuestions']['negative_topic'] = $result;
    }

    /**********************************************
     * `question_model::getNbQuestionByTopic` TESTS
     **********************************************/
    /**
     * Test for `question_model::getNbQuestionByTopic`
     *
     * @return void
     */
    private function test_question_model_gnqbt_no_error()
    {
        $topic_id = $this->topic_model->order_by('RAND()')
            ->get_by('FK_Parent_Topic IS NOT NULL')->ID;
        $nb_questions_expected = count($this->question_model->get_many_by("FK_Topic = {$topic_id} AND Archive = 0"));

        $this->_db_errors_save();
        $nb_questions = $this->question_model->getNbQuestionByTopic($topic_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($nb_questions != $nb_questions_expected) {
            $result->success = FALSE;
            $result->errors[] = "Expected result of <code>question_model->getNbQuestionByTopic</code> was {$nb_questions_expected}, but it is {$nb_questions}";
        }

        $this->_test_results['question_model']['getNbQuestionByTopic']['no_error'] = $result;
    }
    /**
     * Test for failed `question_model::getNbQuestionByTopic` due to
     * not being a topic
     *
     * @return void
     */
    private function test_question_model_gnqbt_not_topic()
    {
        $topic_id = $this->topic_model->order_by('RAND()')
            ->get_by('FK_Parent_Topic IS NULL')->ID;
        $nb_questions_expected = count($this->question_model->get_many_by("FK_Topic = {$topic_id} AND Archive = 0"));

        $this->_db_errors_save();
        $nb_questions = $this->question_model->getNbQuestionByTopic($topic_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($nb_questions != $nb_questions_expected) {
            $result->success = FALSE;
            $result->errors[] = "Expected result of <code>question_model->getNbQuestionByTopic</code> was {$nb_questions_expected}, but it is {$nb_questions}";
        }

        $this->_test_results['question_model']['getNbQuestionByTopic']['not_topic'] = $result;
    }
    /**
     * Test for failed `question_model::getNbQuestionByTopic` due to
     * topic being negative
     *
     * @return void
     */
    private function test_question_model_gnqbt_negative()
    {
        $topic_id = -1;
        $nb_questions_expected = count($this->question_model->get_many_by("FK_Topic = {$topic_id} AND Archive = 0"));

        $this->_db_errors_save();
        $nb_questions = $this->question_model->getNbQuestionByTopic($topic_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($nb_questions != $nb_questions_expected) {
            $result->success = FALSE;
            $result->errors[] = "Expected result of <code>question_model->getNbQuestionByTopic</code> was {$nb_questions_expected}, but it is {$nb_questions}";
        }

        $this->_test_results['question_model']['getNbQuestionByTopic']['negative'] = $result;
    }

    /**************
     * MISC METHODS
     **************/

    /*************************
     * DUMMIES-RELATED METHODS
     * Do not use with actual objects!
     *************************/
    /**
     * Creates a new dummy user
     *
     * @return integer = ID of the dummy user
     */
    private function _dummy_user_create() : int
    {
        $username = $this->_dummy_values['user']['user'];
        $password = $this->_dummy_values['user']['password'];

        $user = array(
            'User' => $username,
            'Password' => password_hash($password, PASSWORD_HASH_ALGORITHM),
            'FK_User_Type' => 1
        );
        return $this->user_model->insert($user);
    }
    /**
     * Resets the dummy user
     *
     * @param integer $dummy_user_id = ID of the dummy user
     * @return void
     */
    private function _dummy_user_reset(int $dummy_user_id)
    {
        $username = $this->_dummy_values['user']['user'];
        $password = $this->_dummy_values['user']['password'];

        $user = array(
            'User' => $username,
            'Password' => password_hash($password, PASSWORD_HASH_ALGORITHM),
            'FK_User_Type' => 1,
            'Archive' => 0
        );
        $this->user_model->update($dummy_user_id, $user);
    }
    /**
     * Deletes the dummy user
     *
     * @param int $dummy_user_id = ID of the dummy user to delete
     * @return void
     */
    private function _dummy_user_delete(int $dummy_user_id)
    {
        $this->user_model->delete($dummy_user_id, TRUE);
    }
}