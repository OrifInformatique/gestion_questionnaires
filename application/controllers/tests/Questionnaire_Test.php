<?php

include(__DIR__.'/../../core/MY_Test_Trait.php');
include(__DIR__.'/../Questionnaire.php');

class Questionnaire_Test extends Questionnaire {
    use MY_Test_Trait;
    /**
     * Stores the tests' results
     *
     * @var array
     */
    private $_test_results = [
        'questionnaire' => [
            'create' => [],
            'add_topic' => [],
            'delete_topic' => [],
            'update' => [],
            'delete' => []
        ],
        'model' => [
            'create' => [],
            'delete' => []
        ],
    ];

    /**
     * Contains the list of tests to run
     *
     * @var array
     */
    private $_tests = [
        'questionnaire_add', 'questionnaire_update', 'questionnaire_delete',
        'questionnaire_topic_delete', 'questionnaire_topic_add',
        'model_add', 'model_delete'
    ];

    /**
     * Contains the base values for dummies
     *
     * @var array
     */
    protected $_dummy_values = [
        'questionnaire' => [
            'title' => 'dummy_questionnaire',
            'subtitle' => '_dummy_questionnaire_'
        ],
        'model' => [
            'name' => 'dummy_model',
            'title' => 'dummy_model_questionnaire',
            'subtitle' => '_dummy_model_questionnaire_'
        ]
    ];

    /**
     * Runs the tests
     *
     * @return void
     */
    public function test()
    {
        if(in_array('questionnaire_add', $this->_tests)) {
            $dummy_tableTopics = $this->_random_table_topic();

            $this->test_questionnaire_create_no_tt();
            $this->test_questionnaire_create_no_error_tt($dummy_tableTopics);
            $this->test_questionnaire_create_error_not_tt();
            $this->test_questionnaire_create_no_subtitle($dummy_tableTopics);
            $this->test_questionnaire_create_error_no_title($dummy_tableTopics);
        }

        if(in_array('questionnaire_topic_add', $this->_tests)) {
            $this->test_questionnaire_topic_add_no_error();
            $this->test_questionnaire_topic_add_not_exist();
            $this->test_questionnaire_topic_add_negative();
            $this->test_questionnaire_topic_add_no_topic();
            $this->test_questionnaire_topic_add_no_question();
        }

        if(in_array('questionnaire_topic_delete', $this->_tests)) {
            $this->test_questionnaire_topic_remove_no_error();
            $this->test_questionnaire_topic_remove_not_exist();
            $this->test_questionnaire_topic_remove_negative();
        }

        if(in_array('questionnaire_update', $this->_tests)) {
            $dummy_quest_id = $this->_dummy_questionnaire_create();

            $this->test_questionnaire_update_no_error($dummy_quest_id);
            $this->_dummy_questionnaire_reset($dummy_quest_id);

            $this->test_questionnaire_update_no_title($dummy_quest_id);
            $this->_dummy_questionnaire_reset($dummy_quest_id);

            $this->test_questionnaire_update_no_subtitle($dummy_quest_id);
            $this->_dummy_questionnaire_reset($dummy_quest_id);

            $this->test_questionnaire_update_not_exist();
            $this->_dummy_questionnaire_reset($dummy_quest_id);

            $this->_dummy_questionnaire_delete($dummy_quest_id);
        }

        if(in_array('questionnaire_delete', $this->_tests)) {
            $this->test_questionnaire_delete_no_effect();
            $this->test_questionnaire_delete_no_error();
            $this->test_questionnaire_delete_not_exist();
        }

        if(in_array('model_add', $this->_tests)) {
            $tableTopics = $this->_random_table_topic();

            $this->test_model_create_no_tt();

            $this->test_model_create_no_error_tt($tableTopics);

            $this->test_model_create_error_not_tt();

            $this->test_model_create_no_subtitle($tableTopics);

            $this->test_model_create_no_title($tableTopics);

            $this->test_model_create_no_name($tableTopics);
        }

        if(in_array('model_delete', $this->_tests)) {
            $this->test_model_delete_no_effect();
            $this->test_model_delete_no_error();
            $this->test_model_delete_not_exist();
        }

        // Display test results
        $output['test_results'] = $this->_test_results;
        parent::display_view('tests/tests_results_new.php', $output);
    }

    /******************************
     * QUESTIONNAIRE CREATION TESTS
     ******************************/
    /**
     * Test for failed questionnaire creation due to no TableTopic
     *
     * @return void
     */
    private function test_questionnaire_create_no_tt()
    {
        $model = FALSE;
        $title = 'questionnaire_create_no_tt';
        $subtitle = "_{$title}_";
        // Make sure there is no saved TableTopics
        unset($_SESSION['temp_tableTopics']);

        $this->_db_errors_save();
        $this->_send_form_request('form_add', [
            'model' => $model,
            'title' => $title,
            'subtitle' => $subtitle,
            'save' => 1
        ]);

        $questionnaire = $this->questionnaire_model->get_by('Questionnaire_Name', $title);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(!empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to be empty, but it contains an error';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!is_null($questionnaire)) {
            $result->success = FALSE;
            $result->errors[] = "Expected questionnaire {$questionnaire->ID} to not exist, but it does";
        }

        $this->_test_results['questionnaire']['create']['no_tt'] = $result;

        if(!is_null($questionnaire)) {
            $this->_dummy_questionnaire_delete((int) $questionnaire->ID);
        }
    }
    /**
     * Test for questionnaire creation
     *
     * @param TableTopics $tableTopics = TableTopics of the questionnaire
     * @return void
     */
    private function test_questionnaire_create_no_error_tt(TableTopics $tableTopics)
    {
        $model = FALSE;
        $title = 'questionnaire_create_no_error_tt';
        $subtitle = "_{$title}_";
        // Save tableTopics
        $tableTopics->setTitle($title);
        $tableTopics->setSubtitle($subtitle);
        $_SESSION['temp_tableTopics'] = serialize($tableTopics);

        $this->_db_errors_save();
        $this->_send_form_request('form_add', [
            'model' => $model,
            'title' => $title,
            'subtitle' => $subtitle,
            'save' => 1
        ]);

        $questionnaire = $this->questionnaire_model->get_by('Questionnaire_Name', $title);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(!empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to be empty, but it contains an error';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(is_null($questionnaire)) {
            $result->success = FALSE;
            $result->errors[] = 'Expected questionnaire to exist, but it does not';
        }

        $this->_test_results['questionnaire']['create']['no_error_tt'] = $result;

        if(!is_null($questionnaire)) {
            $this->_dummy_questionnaire_delete((int) $questionnaire->ID);
        }
    }
    /**
     * Test for failed questionnaire creation due to TableTopic not being of
     * TableTopic type
     *
     * @return void
     */
    private function test_questionnaire_create_error_not_tt()
    {
        $model = FALSE;
        $title = 'questionnaire_create_error_not_tt';
        $subtitle = "_{$title}_";
        // Create and save fake tableTopics
        $tableTopics = new stdClass();
        $_SESSION['temp_tableTopics'] = serialize($tableTopics);

        $this->_db_errors_save();
        $this->_send_form_request('form_add', [
            'model' => $model,
            'title' => $title,
            'subtitle' => $subtitle,
            'save' => 1
        ]);

        $questionnaire = $this->questionnaire_model->get_by('Questionnaire_Name', $title);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!is_null($questionnaire)) {
            $result->success = FALSE;
            $result->errors[] = 'Expected questionnaire to not exist, but it does';
        }

        $this->_test_results['questionnaire']['create']['error_not_tt'] = $result;

        if(!is_null($questionnaire)) {
            $this->_dummy_questionnaire_delete((int) $questionnaire->ID);
        }
    }
    /**
     * Test for questionnaire creation without a subtitle
     *
     * @param TableTopics $tableTopics = TableTopics of the questionnaire
     * @return void
     */
    private function test_questionnaire_create_no_subtitle(TableTopics $tableTopics)
    {
        $model = FALSE;
        $title = 'questionnaire_create_no_subtitle';
        // Save tableTopics
        $tableTopics->setTitle($title);
        $_SESSION['temp_tableTopics'] = serialize($tableTopics);

        $this->_db_errors_save();
        $this->_send_form_request('form_add', [
            'model' => $model,
            'title' => $title,
            'save' => 1
        ]);

        $questionnaire = $this->questionnaire_model->get_by('Questionnaire_Name', $title);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(!empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to be empty, but it contains an error';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(is_null($questionnaire)) {
            $result->success = FALSE;
            $result->errors[] = 'Expected questionnaire to exist, but it does not';
        }

        $this->_test_results['questionnaire']['create']['no_subtitle'] = $result;

        if(!is_null($questionnaire)) {
            $this->_dummy_questionnaire_delete((int) $questionnaire->ID);
        }
    }
    /**
     * Test for failed questionnaire creation due to no title
     *
     * @param TableTopics $tableTopics = TableTopics of the questionnaire
     * @return void
     */
    private function test_questionnaire_create_error_no_title(TableTopics $tableTopics)
    {
        $model = FALSE;
        $subtitle = '_questionnaire_create_no_title_';
        // Save tableTopics
        $tableTopics->setSubtitle($subtitle);
        $_SESSION['temp_tableTopics'] = serialize($tableTopics);

        $this->_db_errors_save();
        $this->_send_form_request('form_add', [
            'model' => $model,
            'subtitle' => $subtitle,
            'save' => 1
        ]);

        $questionnaire = $this->questionnaire_model->get_by('Questionnaire_Subtitle', $subtitle);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to contain an error, but it is empty';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!is_null($questionnaire)) {
            $result->success = FALSE;
            $result->errors[] = 'Expected questionnaire to not exist, but it does';
        }

        $this->_test_results['questionnaire']['create']['no_title'] = $result;

        if(!is_null($questionnaire)) {
            $this->_dummy_questionnaire_delete((int) $questionnaire->ID);
        }
    }

    /**********************************
     * QUESTIONNAIRE TOPIC ADDING TESTS
     **********************************/
    /**
     * Test for topic adding without any error
     *
     * @return void
     */
    private function test_questionnaire_topic_add_no_error()
    {
        $title = 'questionnaire_topic_add_no_error';
        $subtitle = "_{$title}_";
        $topics = $this->topic_model->get_all();
        $topic_id = $topics[array_rand($topics)]->ID;
        $nb_questions = rand(1, $this->question_model->getNbQuestionByTopic($topic_id));
        $tableTopics = new TableTopics();
        // Prepare TableTopics
        $tableTopics->setTitle($title);
        $tableTopics->setSubtitle($subtitle);

        $_SESSION['temp_tableTopics'] = serialize($tableTopics);

        $this->_db_errors_save();
        $this->_send_form_request('form_add', [
            'title' => $title,
            'subtitle' => $subtitle,
            'add_form' => 1,
            'topic_selected' => $topic_id,
            'nb_questions' => $nb_questions
        ]);

        // Get updated TableTopics
        $tableTopics = unserialize($_SESSION['temp_tableTopics']);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(!empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to be empty, but it contains an error';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($tableTopics->getArrayNbQuestion()[0] != $nb_questions) {
            $result->success = FALSE;
            $result->errors[] = "Expected {$nb_questions} questions in the first slot, but found {$tableTopics->getArrayNbQuestion()[0]}";
        }
        if($tableTopics->getArrayTopics()[0]->ID != $topic_id) {
            $result->success = FALSE;
            $result->errors[] = "Expected topic {$topic_id} in the first slot, but found {$tableTopics->getArrayTopics()[0]->ID}";
        }

        $this->_test_results['questionnaire']['add_topic']['no_error'] = $result;
    }
    /**
     * Test for failed topic adding due to topic not existing
     *
     * @return void
     */
    private function test_questionnaire_topic_add_not_exist()
    {
        $title = 'questionnaire_topic_add_not_exist';
        $subtitle = "_{$title}_";
        // Get id of non existant topic
        $bad_id = $this->topic_model->get_next_id()+1;
        $nb_questions = 1;
        $tableTopics = new TableTopics();
        // Prepare TableTopics
        $tableTopics->setTitle($title);
        $tableTopics->setSubtitle($subtitle);

        $_SESSION['temp_tableTopics'] = serialize($tableTopics);

        $this->_db_errors_save();
        $this->_send_form_request('form_add', [
            'title' => $title,
            'subtitle' => $subtitle,
            'add_form' => 1,
            'topic_selected' => $bad_id,
            'nb_questions' => $nb_questions
        ]);

        // Get updated TableTopics
        $tableTopics = unserialize($_SESSION['temp_tableTopics']);
        $topic = $this->topic_model->get($bad_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to contain an error, but it is empty';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!empty($tableTopics->getArrayNbQuestion()[0])) {
            $result->success = FALSE;
            $result->errors[] = 'Expected nothing in the first slot of <code>$tableTopics->getArrayNbQuestion()</code>, but found a value';
        }
        if(!empty($tableTopics->getArrayTopics()[0])) {
            $result->success = FALSE;
            $result->errors[] = 'Expected nothing in the first slot of <code>$tableTopics->getArrayTopics()</code>, but found a value';
        }
        if(!is_null($topic)) {
            $result->success = FALSE;
            $result->errors[] = "Expected topic {$topic->ID} to not exist, but it does";
        }

        $this->_test_results['questionnaire']['add_topic']['not_exist'] = $result;

        if(!is_null($topic)) {
            $this->topic_model->delete($bad_id);
        }
    }
    /**
     * Test for failed topic adding due to question amount being negative
     *
     * @return void
     */
    private function test_questionnaire_topic_add_negative()
    {
        $title = 'questionnaire_topic_add_negative';
        $subtitle = "_{$title}_";
        $topics = $this->topic_model->get_all();
        $topic_id = $topics[array_rand($topics)]->ID;
        $nb_questions = -1;
        $tableTopics = new TableTopics();
        // Prepare TableTopics
        $tableTopics->setTitle($title);
        $tableTopics->setSubtitle($subtitle);

        $_SESSION['temp_tableTopics'] = serialize($tableTopics);

        $this->_db_errors_save();
        $this->_send_form_request('form_add', [
            'title' => $title,
            'subtitle' => $subtitle,
            'add_form' => 1,
            'topic_selected' => $topic_id,
            'nb_questions' => $nb_questions
        ]);

        // Get updated TableTopics
        $tableTopics = unserialize($_SESSION['temp_tableTopics']);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to contain an error, but it is empty';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!empty($tableTopics->getArrayNbQuestion()[0])) {
            $result->success = FALSE;
            $result->errors[] = 'Expected nothing in the first slot of <code>$tableTopics->getArrayNbQuestion()</code>, but found a value';
        }
        if(!empty($tableTopics->getArrayTopics()[0])) {
            $result->success = FALSE;
            $result->errors[] = 'Expected nothing in the first slot of <code>$tableTopics->getArrayTopics()</code>, but found a value';
        }

        $this->_test_results['questionnaire']['add_topic']['negative'] = $result;
    }
    /**
     * Test for failed topic adding due to no topic being provided
     *
     * @return void
     */
    private function test_questionnaire_topic_add_no_topic()
    {
        $title = 'questionnaire_topic_add_no_topic';
        $subtitle = "_{$title}_";
        $nb_questions = 1;
        $tableTopics = new TableTopics();
        // Prepare TableTopics
        $tableTopics->setTitle($title);
        $tableTopics->setSubtitle($subtitle);

        $_SESSION['temp_tableTopics'] = serialize($tableTopics);

        $this->_db_errors_save();
        $this->_send_form_request('form_add', [
            'title' => $title,
            'subtitle' => $subtitle,
            'add_form' => 1,
            'nb_questions' => $nb_questions
        ]);

        // Get updated TableTopics
        $tableTopics = unserialize($_SESSION['temp_tableTopics']);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to contain an error, but it is empty';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!empty($tableTopics->getArrayNbQuestion()[0])) {
            $result->success = FALSE;
            $result->errors[] = 'Expected nothing in the first slot of <code>$tableTopics->getArrayNbQuestion()</code>, but found a value';
        }
        if(!empty($tableTopics->getArrayTopics()[0])) {
            $result->success = FALSE;
            $result->errors[] = 'Expected nothing in the first slot of <code>$tableTopics->getArrayTopics()</code>, but found a value';
        }

        $this->_test_results['questionnaire']['add_topic']['no_topic'] = $result;
    }
    /**
     * Test for failed topic adding due to no question amount being provided
     *
     * @return void
     */
    private function test_questionnaire_topic_add_no_question()
    {
        $title = 'questionnaire_topic_add_no_question';
        $subtitle = "_{$title}_";
        $topics = $this->topic_model->get_all();
        $topic_id = $topics[array_rand($topics)]->ID;
        $tableTopics = new TableTopics();
        // Prepare TableTopics
        $tableTopics->setTitle($title);
        $tableTopics->setSubtitle($subtitle);

        $_SESSION['temp_tableTopics'] = serialize($tableTopics);

        $this->_db_errors_save();
        $this->_send_form_request('form_add', [
            'title' => $title,
            'subtitle' => $subtitle,
            'add_form' => 1,
            'topic_selected' => $topic_id
        ]);

        // Get updated TableTopics
        $tableTopics = unserialize($_SESSION['temp_tableTopics']);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to contain an error, but it is empty';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!empty($tableTopics->getArrayNbQuestion()[0])) {
            $result->success = FALSE;
            $result->errors[] = 'Expected nothing in the first slot of <code>$tableTopics->getArrayNbQuestion()</code>, but found a value';
        }
        if(!empty($tableTopics->getArrayTopics()[0])) {
            $result->success = FALSE;
            $result->errors[] = 'Expected nothing in the first slot of <code>$tableTopics->getArrayTopics()</code>, but found a value';
        }

        $this->_test_results['questionnaire']['add_topic']['no_question'] = $result;
    }

    /************************************
     * QUESTIONNAIRE TOPIC DELETING TESTS
     ************************************/
    /**
     * Test for topic deleting
     *
     * @return void
     */
    private function test_questionnaire_topic_remove_no_error()
    {
        $title = 'questionnaire_topic_remove_no_error';
        $subtitle = "_{$title}_";
        $topics = $this->topic_model->get_all();
        $topic = $topics[array_rand($topics)];
        $nb_questions = rand(1, $this->question_model->getNbQuestionByTopic($topic->ID));
        $tableTopics = new TableTopics();
        // Prepare TableTopics
        $tableTopics->setTitle($title);
        $tableTopics->setSubtitle($subtitle);
        $tableTopics->setArrayTopics($topic);
        $tableTopics->setArrayNbQuestion($nb_questions);

        $_SESSION['temp_tableTopics'] = serialize($tableTopics);

        $this->_db_errors_save();
        $this->_send_form_request('form_add', [
            'title' => $title,
            'subtitle' => $subtitle,
            'delete_topic' => [0 => 0]
        ]);

        // Get updated TableTopics
        $tableTopics = unserialize($_SESSION['temp_tableTopics']);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(!empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to be empty, but it contains an error';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(isset($tableTopics->getArrayNbQuestion()[0])) {
            $result->success = FALSE;
            $result->errors[] = 'Expected nothing in <code>$tableTopics->getArrayNbQuestion()[0]</code>, but found a value';
        }
        if(isset($tableTopics->getArrayTopics()[0])) {
            $result->success = FALSE;
            $result->errors[] = 'Expected nothing in <code>$tableTopics->getArrayTopics()[0]</code>, but found a value';
        }

        $this->_test_results['questionnaire']['delete_topic']['no_error'] = $result;
    }
    /**
     * Test for failed topic deleting due to topic not existing
     *
     * @return void
     */
    private function test_questionnaire_topic_remove_not_exist()
    {
        $title = 'questionnaire_topic_remove_not_exist';
        $subtitle = "_{$title}_";
        $tableTopics = new TableTopics();
        // Prepare TableTopics
        $tableTopics->setTitle($title);
        $tableTopics->setSubtitle($subtitle);

        $_SESSION['temp_tableTopics'] = serialize($tableTopics);

        $this->_db_errors_save();
        $this->_send_form_request('form_add', [
            'title' => $title,
            'subtitle' => $subtitle,
            'delete_topic' => [0 => 0]
        ]);

        // Get updated TableTopics
        $tableTopics = unserialize($_SESSION['temp_tableTopics']);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to contain an error, but it is empty';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(isset($tableTopics->getArrayNbQuestion()[0])) {
            $result->success = FALSE;
            $result->errors[] = 'Expected nothing in <code>$tableTopics->getArrayNbQuestion()[0]</code>, but found a value';
        }
        if(isset($tableTopics->getArrayTopics()[0])) {
            $result->success = FALSE;
            $result->errors[] = 'Expected nothing in <code>$tableTopics->getArrayTopics()[0]</code>, but found a value';
        }

        $this->_test_results['questionnaire']['delete_topic']['not_exist'] = $result;
    }
    /**
     * Test for failed topic deleting due to negative number
     *
     * @return void
     */
    private function test_questionnaire_topic_remove_negative()
    {
        $title = 'questionnaire_topic_remove_negative';
        $subtitle = "_{$title}_";
        $topics = $this->topic_model->get_all();
        $topic = $topics[array_rand($topics)];
        $nb_questions = rand(1, $this->question_model->getNbQuestionByTopic($topic->ID));
        $tableTopics = new TableTopics();
        // Prepare TableTopics
        $tableTopics->setTitle($title);
        $tableTopics->setSubtitle($subtitle);
        $tableTopics->setArrayTopics($topic);
        $tableTopics->setArrayNbQuestion($nb_questions);

        $_SESSION['temp_tableTopics'] = serialize($tableTopics);

        $this->_db_errors_save();
        $this->_send_form_request('form_add', [
            'title' => $title,
            'subtitle' => $subtitle,
            'delete_topic' => [-1 => -1]
        ]);

        // Get updated TableTopics
        $tableTopics = unserialize($_SESSION['temp_tableTopics']);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to contain an error, but it is empty';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!isset($tableTopics->getArrayNbQuestion()[0])) {
            $result->success = FALSE;
            $result->errors[] = 'Expected a value in <code>$tableTopics->getArrayNbQuestion()[0]</code>, but found nothing';
        }
        if(!isset($tableTopics->getArrayTopics()[0])) {
            $result->success = FALSE;
            $result->errors[] = 'Expected a value in <code>$tableTopics->getArrayTopics()[0]</code>, but found nothing';
        }

        $this->_test_results['questionnaire']['delete_topic']['negative'] = $result;
    }

    /******************************
     * QUESTIONNAIRE UPDATING TESTS
     ******************************/
    /**
     * Test for questionnaire update
     *
     * @param integer $dummy_quest_id = ID of the dummy questionnaire
     * @return void
     */
    private function test_questionnaire_update_no_error(int $dummy_quest_id)
    {
        $model = FALSE;
        $title = 'questionnaire_update_no_error';
        $subtitle = "_{$title}_";

        $this->_db_errors_save();
        $this->_send_form_request('form_update', [
            'id' => $dummy_quest_id,
            'model' => $model,
            'title' => $title,
            'subtitle' => $subtitle
        ]);

        $questionnaire = $this->questionnaire_model->get($dummy_quest_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(!empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to be empty, but it contains an error';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($questionnaire->Questionnaire_Name !== $title) {
            $result->success = FALSE;
            $result->errors[] = "Expected questionnaire name to be {$title}, but found {$questionnaire->Questionnaire_Name}";
        }
        if($questionnaire->Questionnaire_Subtitle !== $subtitle) {
            $result->success = FALSE;
            $result->errors[] = "Expected questionnaire subtitle to be {$subtitle}, but found {$questionnaire->Questionnaire_Subtitle}";
        }

        $this->_test_results['questionnaire']['update']['no_error'] = $result;
    }
    /**
     * Test for failed questionnaire update due to no title being provided
     *
     * @param integer $dummy_quest_id = ID of the dummy questionnaire
     * @return void
     */
    private function test_questionnaire_update_no_title(int $dummy_quest_id)
    {
        $model = FALSE;
        $subtitle = '_questionnaire_update_no_title_';

        $this->_db_errors_save();
        $this->_send_form_request('form_update', [
            'id' => $dummy_quest_id,
            'model' => $model,
            'subtitle' => $subtitle
        ]);

        $questionnaire = $this->questionnaire_model->get($dummy_quest_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to contain an error, but it is empty';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($questionnaire->Questionnaire_Subtitle === $subtitle) {
            $result->success = FALSE;
            $result->errors[] = "Expected questionnaire subtitle to not be updated, but it is";
        }

        $this->_test_results['questionnaire']['update']['no_title'] = $result;
    }
    /**
     * Test for questionnaire update even without a subtitle
     *
     * @param integer $dummy_quest_id = ID of the dummy questionnaire
     * @return void
     */
    private function test_questionnaire_update_no_subtitle(int $dummy_quest_id)
    {
        $model = FALSE;
        $title = 'questionnaire_update_no_subtitle';

        $this->_db_errors_save();
        $this->_send_form_request('form_update', [
            'id' => $dummy_quest_id,
            'model' => $model,
            'title' => $title
        ]);

        $questionnaire = $this->questionnaire_model->get($dummy_quest_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(!empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to be empty, but it contains an error';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($questionnaire->Questionnaire_Name !== $title) {
            $result->success = FALSE;
            $result->errors[] = "Expected questionnaire name to be updated, but it is not";
        }

        $this->_test_results['questionnaire']['update']['no_subtitle'] = $result;
    }
    /**
     * Test for failed questionnaire update due to not due to it not existing
     *
     * @return void
     */
    private function test_questionnaire_update_not_exist()
    {
        $bad_id = $this->questionnaire_model->get_next_id()+1;
        $model = FALSE;
        $title = 'questionnaire_update_not_exist';
        $subtitle = "_{$title}_";

        $this->_db_errors_save();
        $this->_send_form_request('form_update', [
            'id' => $bad_id,
            'model' => $model,
            'title' => $title,
            'subtitle' => $subtitle
        ]);

        $questionnaire = $this->questionnaire_model->get_by('Questionnaire_Name', $title);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to contain an error, but it is empty';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!is_null($questionnaire)) {
            $result->success = FALSE;
            $result->errors[] = "Expected questionnaire {$questionnaire->ID} to not exist, but it does";
        }

        $this->_test_results['questionnaire']['update']['not_exist'] = $result;

        if(!is_null($questionnaire)) {
            $this->_dummy_questionnaire_delete((int) $questionnaire->ID);
        }
    }

    /******************************
     * QUESTIONNAIRE DELETION TESTS
     ******************************/
    /**
     * Test for questionnaire deletion without any effect
     *
     * @return void
     */
    private function test_questionnaire_delete_no_effect()
    {
        $dummy_quest_id = $this->_dummy_questionnaire_create();

        $this->_db_errors_save();
        $this->delete($dummy_quest_id);

        $questionnaire = $this->questionnaire_model->get($dummy_quest_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(is_null($questionnaire)) {
            $result->success = FALSE;
            $result->errors[] = "Expected questionnaire {$dummy_quest_id} to exist, but it does not";
        }

        $this->_test_results['questionnaire']['delete']['no_effect'] = $result;

        if(!is_null($questionnaire)) {
            $this->_dummy_questionnaire_delete($dummy_quest_id);
        }
    }
    /**
     * Test for questionnaire deletion
     *
     * @return void
     */
    private function test_questionnaire_delete_no_error()
    {
        $dummy_quest_id = $this->_dummy_questionnaire_create();

        $this->_db_errors_save();
        $this->delete($dummy_quest_id, 1);

        $questionnaire = $this->questionnaire_model->get($dummy_quest_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!is_null($questionnaire)) {
            $result->success = FALSE;
            $result->errors[] = "Expected questionnaire {$dummy_quest_id} to not exist, but it does";
        }

        $this->_test_results['questionnaire']['delete']['no_error'] = $result;

        if(!is_null($questionnaire)) {
            $this->_dummy_questionnaire_delete($dummy_quest_id);
        }
    }
    /**
     * Test for failed questionnaire deletion due to questionnaire not existing
     *
     * @return void
     */
    private function test_questionnaire_delete_not_exist()
    {
        $bad_id = $this->questionnaire_model->get_next_id()+1;

        $this->_db_errors_save();
        $this->delete($bad_id, 1);

        $questionnaire = $this->questionnaire_model->get($bad_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!is_null($questionnaire)) {
            $result->success = FALSE;
            $result->errors[] = "Expected questionnaire {$questionnaire->ID} to not exist, but it does";
        }

        $this->_test_results['questionnaire']['delete']['not_exist'] = $result;

        if(!is_null($questionnaire)) {
            $this->questionnaire_model->delete($bad_id);
        }
    }

    /**********************
     * MODEL CREATION TESTS
     **********************/
    /**
     * Test for failed model creation due to no tableTopics being provided
     *
     * @return void
     */
    private function test_model_create_no_tt()
    {
        $model = TRUE;
        $modelName = $title = 'model_create_no_tt';
        $subtitle = "_{$title}_";
        // Make sure there is no saved TableTopics
        unset($_SESSION['temp_tableTopics_model']);

        $this->_db_errors_save();
        $this->_send_form_request('form_add', [
            'model' => $model,
            'title' => $title,
            'subtitle' => $subtitle,
            'save' => 1
        ]);

        $model = $this->questionnaire_model_model->get_by('Base_Name', $modelName);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to be contain an error, but it is empty';
        }
        if(!is_null($model)) {
            $result->success = FALSE;
            $result->errors[] = "Expected model {$model->ID} to not exist, but it does";
        }

        $this->_test_results['model']['create']['no_tt'] = $result;

        if(!is_null($model)) {
            $this->_dummy_model_delete((int) $model->ID);
        }
    }
    /**
     * Test for model creation
     *
     * @param TableTopics $tableTopics = TableTopics of the model's questionnaires
     * @return void
     */
    private function test_model_create_no_error_tt(TableTopics $tableTopics)
    {
        $model = TRUE;
        $modelName = $title = 'model_create_no_error_tt';
        $subtitle = "_{$title}_";
        // Save tableTopics
        $tableTopics->setTitle($title);
        $tableTopics->setSubtitle($subtitle);
        $tableTopics->setModelName($modelName);
        $_SESSION['temp_tableTopics_model'] = serialize($tableTopics);

        $this->_db_errors_save();
        $this->_send_form_request('form_add', [
            'model' => $model,
            'title' => $title,
            'subtitle' => $subtitle,
            'modelName' => $modelName,
            'save' => 1
        ]);

        $model = $this->questionnaire_model_model->get_by('Base_Name', $modelName);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to be empty, but it contains an error';
        }
        if(is_null($model)) {
            $result->success = FALSE;
            $result->errors[] = "Expected model '{$modelName}' to exist, but it does not";
        }

        $this->_test_results['model']['create']['no_error_tt'] = $result;

        if(!is_null($model)) {
            $this->_dummy_model_delete((int) $model->ID);
        }
    }
    /**
     * Test for failed model creation due to tableTopics provided not being an
     * instance of TableTopics
     *
     * @return void
     */
    private function test_model_create_error_not_tt()
    {
        $model = TRUE;
        $title = $modelName = 'model_create_error_not_tt';
        $subtitle = "_{$title}_";
        // Create and save fake tableTopics
        $tableTopics = new stdClass();
        $_SESSION['temp_tableTopics_model'] = serialize($tableTopics);

        $this->_db_errors_save();
        $this->_send_form_request('form_add', [
            'model' => $model,
            'title' => $title,
            'subtitle' => $subtitle,
            'modelName' => $modelName,
            'save' => 1
        ]);

        $model = $this->questionnaire_model_model->get_by('Base_Name', $modelName);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!is_null($model)) {
            $result->success = FALSE;
            $result->errors[] = "Expected model {$model->ID} to not exist, but it does";
        }

        $this->_test_results['model']['create']['error_not_tt'] = $result;

        if(!is_null($model)) {
            $this->_dummy_model_delete((int) $model->ID);
        }
    }
    /**
     * Test for model creation without a subtitle
     *
     * @param TableTopics $tableTopics = TableTopics of the model's questionnaires
     * @return void
     */
    private function test_model_create_no_subtitle(TableTopics $tableTopics)
    {
        $model = TRUE;
        $title = $modelName = 'model_create_no_subtitle';
        // Save tableTopics
        $tableTopics->setTitle($title);
        $tableTopics->setModelName($modelName);
        $_SESSION['temp_tableTopics_model'] = serialize($tableTopics);

        $this->_db_errors_save();
        $this->_send_form_request('form_add', [
            'model' => $model,
            'title' => $title,
            'modelName' => $modelName,
            'save' => 1
        ]);

        $model = $this->questionnaire_model_model->get_by('Base_Name', $modelName);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to be empty, but it contains an error';
        }
        if(is_null($model)) {
            $result->success = FALSE;
            $result->errors[] = "Expected model '{$modelName}' to exist, but it does not";
        }

        $this->_test_results['model']['create']['no_subtitle'] = $result;

        if(!is_null($model)) {
            $this->_dummy_model_delete((int) $model->ID);
        }
    }
    /**
     * Test for failed model creation due to no title provided
     *
     * @param TableTopics $tableTopics = TableTopics of the model's questionnaires
     * @return void
     */
    private function test_model_create_no_title(TableTopics $tableTopics)
    {
        $model = TRUE;
        $modelName = 'model_create_no_title';
        $subtitle = "_{$modelName}_";
        // Save tableTopics
        $tableTopics->setSubtitle($subtitle);
        $tableTopics->setModelName($modelName);
        $_SESSION['temp_tableTopics_model'] = serialize($tableTopics);

        $this->_db_errors_save();
        $this->_send_form_request('form_add', [
            'model' => $model,
            'subtitle' => $subtitle,
            'modelName' => $modelName,
            'save' => 1
        ]);

        $model = $this->questionnaire_model_model->get_by('Base_Name', $modelName);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to contain an error, but it is empty';
        }
        if(!is_null($model)) {
            $result->success = FALSE;
            $result->errors[] = "Expected model {$model->ID} to not exist, but it does";
        }

        $this->_test_results['model']['create']['no_title']  = $result;

        if(!is_null($model)) {
            $this->_dummy_model_delete((int) $model->ID);
        }
    }
    /**
     * Test for failed model creation due to no name provided
     *
     * @param TableTopics $tableTopics = TableTopics of the model's questionnaires
     * @return void
     */
    private function test_model_create_no_name(TableTopics $tableTopics)
    {
        $model = TRUE;
        $title = 'model_create_no_name';
        $subtitle = "_{$title}_";
        // Save tableTopics
        $tableTopics->setTitle($title);
        $tableTopics->setSubtitle($subtitle);
        $_SESSION['temp_tableTopics_model'] = serialize($tableTopics);

        $this->_db_errors_save();
        $this->_send_form_request('form_add', [
            'model' => $model,
            'title' => $title,
            'subtitle' => $subtitle,
            'save' => 1
        ]);

        $model = $this->questionnaire_model_model->get_by('Questionnaire_Name', $title);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to contain an error, but it is empty';
        }
        if(!is_null($model)) {
            $result->success = FALSE;
            $result->errors[] = "Expected model {$model->ID} to not exist, but it does";
        }

        $this->_test_results['model']['create']['no_name']  = $result;

        if(!is_null($model)) {
            $this->_dummy_model_delete((int) $model->ID);
        }
    }

    /**********************
     * MODEL DELETION TESTS
     **********************/
    /**
     * Test for model deletion with no actual deletion
     *
     * @return void
     */
    private function test_model_delete_no_effect()
    {
        $dummy_model_id = $this->_dummy_model_create();

        $this->_db_errors_save();
        $this->model_delete($dummy_model_id);

        $model = $this->questionnaire_model_model->get($dummy_model_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(is_null($model)) {
            $result->success = FALSE;
            $result->errors[] = "Expected model {$dummy_model_id} to exist, but it does not";
        }

        $this->_test_results['model']['delete']['no_effect'] = $result;

        if(!is_null($model)) {
            $this->_dummy_model_delete($dummy_model_id);
        }
    }
    /**
     * Test for model deletion
     *
     * @return void
     */
    private function test_model_delete_no_error()
    {
        $dummy_model_id = $this->_dummy_model_create();

        $this->_db_errors_save();
        $this->model_delete($dummy_model_id, 1);

        $model = $this->questionnaire_model_model->get($dummy_model_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!is_null($model)) {
            $result->success = FALSE;
            $result->errors[] = "Expected model {$dummy_model_id} to not exist, but it does";
        }

        $this->_test_results['model']['delete']['no_error'] = $result;

        if(!is_null($model)) {
            $this->_dummy_model_delete($dummy_model_id);
        }
    }
    /**
     * Test for failed model deletion due to no model existing with the id
     *
     * @return void
     */
    private function test_model_delete_not_exist()
    {
        $bad_id = $this->questionnaire_model_model->get_next_id()+1;

        $this->_db_errors_save();
        $this->model_delete($bad_id, 1);

        $model = $this->questionnaire_model_model->get($bad_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!is_null($model)) {
            $result->success = FALSE;
            $result->errors[] = "Expected model {$bad_id} to not exist, but it does";
        }

        $this->_test_results['model']['delete']['not_exist'] = $result;

        if(!is_null($model)) {
            $this->questionnaire_model_model->delete($bad_id);
        }
    }

    /**
     * Generates a random TableTopics for questionnaires and models
     *
     * @param string $title = Title of the questionnaire
     * @param string $subtitle = Subtitle of the questionnaire
     * @param integer $amount = Amount of topics to enter
     * @return TableTopics = A new random array of questions
     */
    private function _random_table_topic(string $title = '', string $subtitle = '', int $amount = 3) : TableTopics
    {
        $tableTopics = new TableTopics();
        $topics = array_map(function($topic) {return $topic->ID;},
            $this->topic_model->get_many_by('FK_Parent_Topic IS NOT NULL'));
        // Prevent amount being above the amount of topics available
        $amount = min($amount, count($topics));
        if($amount > 1)
            $topics_keys = array_rand($topics, $amount);
        else
            $topics_keys = [array_rand($topics)];

        $tableTopics->setTitle($title);
        $tableTopics->setSubtitle($subtitle);

        for($i = 0; $i < $amount; $i++) {
            $topic_id = $topics[$topics_keys[$i]];
            $nb_questions = rand(1, $this->question_model->getNbQuestionByTopic($topic_id));

            $tableTopics->setArrayTopics($this->topic_model->get($topic_id));
            $tableTopics->setArrayNbQuestion($nb_questions);
        }

        return $tableTopics;
    }

    /*************************
     * DUMMIES-RELATED METHODS
     * Do not use with actual objects!
     *************************/
    /**
     * Creates a dummy questionnaire according to the dummy values
     *
     * @return integer = ID of the dummy questionnaire
     */
    private function _dummy_questionnaire_create() : int
    {
        $title = $this->_dummy_values['questionnaire']['title'];
        $subtitle = $this->_dummy_values['questionnaire']['subtitle'];

        $questionnaire = array(
            'Questionnaire_Name' => $title,
            'Questionnaire_Subtitle' => $subtitle,
            'PDF' => $title.'.pdf',
            'Corrige_PDF' => $title.'_c.pdf'
        );

        // Create fake pdf files for deletion tests
        file_put_contents('pdf_files/questionnaires/'.$questionnaire['PDF'], '');
        file_put_contents('pdf_files/corriges/'.$questionnaire['Corrige_PDF'], '');

        return $this->questionnaire_model->insert($questionnaire);
    }
    /**
     * Resets the dummy questionnaire according to the saved dummy values
     *
     * @param integer $dummy_quest_id = ID of the dummy questionnaire
     * @return void
     */
    private function _dummy_questionnaire_reset(int $dummy_quest_id)
    {
        $title = $this->_dummy_values['questionnaire']['title'];
        $subtitle = $this->_dummy_values['questionnaire']['subtitle'];

        $questionnaire = array(
            'Questionnaire_Name' => $title,
            'Questionnaire_Subtitle' => $subtitle
        );

        $this->questionnaire_model->update($dummy_quest_id, $questionnaire);
    }
    /**
     * Deletes the dummy questionnaire
     *
     * @param integer $dummy_quest_id = ID of the dummy questionnaire
     * @return void
     */
    private function _dummy_questionnaire_delete(int $dummy_quest_id)
    {
        // Delete related questions
        $this->load->model('question_questionnaire_model');
        $this->question_questionnaire_model->delete_by('FK_Questionnaire = '.$dummy_quest_id);
        $questionnaire = $this->questionnaire_model->get($dummy_quest_id);

        // Delete file
        unlink('pdf_files/questionnaires/'.$questionnaire->PDF);
        unlink('pdf_files/corriges/'.$questionnaire->Corrige_PDF);

        $this->questionnaire_model->delete($dummy_quest_id);
    }

    /**
     * Creates a dummy questionnaire model
     *
     * @return integer = ID of the dummy questionnaire model
     */
    private function _dummy_model_create() : int
    {
        $name = $this->_dummy_values['model']['name'];
        $title = $this->_dummy_values['questionnaire']['title'];
        $subtitle = $this->_dummy_values['questionnaire']['subtitle'];

        $model = array(
            'Base_Name' => $name,
            'Questionnaire_Name' => $title,
            'Questionnaire_Subtitle' => $subtitle
        );

        return $this->questionnaire_model_model->insert($model);
    }
    /**
     * Resets the dummy questionnaire model according to dummy_values
     *
     * @param integer $dummy_model_id = ID of the dummy questionnaire model
     * @return void
     */
    private function _dummy_model_reset(int $dummy_model_id)
    {
        $name = $this->_dummy_values['model']['name'];
        $title = $this->_dummy_values['questionnaire']['title'];
        $subtitle = $this->_dummy_values['questionnaire']['subtitle'];

        $model = array(
            'Base_Name' => $name,
            'Questionnaire_Name' => $title,
            'Questionnaire_Subtitle' => $subtitle
        );

        $this->questionnaire_model_model->update($dummy_model_id, $model);
    }
    /**
     * Deletes the dummy questionnaire model
     *
     * @param integer $dummy_model_id = ID of the dummy questionnaire model
     * @return void
     */
    private function _dummy_model_delete(int $dummy_model_id)
    {
        // Delete relations before model
        $this->questionnaire_model_topic_model->delete_by('FK_Questionnaire_Model = '.$dummy_model_id);
        $this->questionnaire_model_model->delete($dummy_model_id);
    }

}