<?php

include(__DIR__.'/../../core/MY_Test_Trait.php');
include(__DIR__.'/../Topic.php');

/**
 * Test class for Topic controller
 * 
 * @author      Orif, section informatique (ViDi, MeSa, BuYa)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class Topic_Test extends Topic {
    use MY_Test_Trait;
    /**
     * Stores the tests' results
     *
     * @var array
     */
    private $_test_results = [
        'topic' => [
            'creation' => [],
            'update' => [],
            'delete' => []
        ],
        'module' => [
            'creation' => [],
            'update' => [],
            'delete' => []
        ]
    ];
    /**
     * Contains the list of tests to run
     *
     * @var array
     */
    private $_tests = [
        'topic_add', 'topic_update', 'topic_delete',
        'module_add', 'module_update', 'module_delete'
    ];
    /**
     * Default values for dummies
     *
     * @var array
     */
    private $_dummy_values = [
        'topic' => ['topic' => 'dummy_topic'],
        'module' => ['topic' => 'dummy_module']
    ];

    /**
     * Runs the tests
     *
     * @return void
     */
    public function test()
    {
        // Topic adding tests
        if(in_array('topic_add', $this->_tests)) {
            $this->test_add_topic_no_errors();
            $this->test_add_topic_long_title();
            $this->test_add_topic_module_selected_not_exist();
            $this->test_add_topic_module_selected_not_module();
        }

        // Topic updating tests
        if(in_array('topic_update', $this->_tests)) {
            $dummy_id = $this->_dummy_create();
            // Reset dummy after each use

            $this->test_update_topic_no_errors($dummy_id);
            $this->_dummy_reset($dummy_id);

            $this->test_update_topic_long_title($dummy_id);
            $this->_dummy_reset($dummy_id);

            $this->test_update_topic_not_exist();
            $this->_dummy_reset($dummy_id);

            $this->_dummy_delete($dummy_id);
        }

        // Topic deleting tests
        if(in_array('topic_delete', $this->_tests)) {
            $dummy_id = $this->_dummy_create();
            // Reset topic after each use

            $this->test_delete_topic_no_delete($dummy_id);
            $this->_dummy_reset($dummy_id);

            $this->test_delete_topic_archive($dummy_id);
            $this->_dummy_reset($dummy_id);

            $this->test_delete_topic_not_exist();
            // Just because it's not an argument does not mean it's not possible
            $this->_dummy_reset($dummy_id);

            $this->_dummy_delete($dummy_id);
        }

        // Module adding tests
        if(in_array('module_add', $this->_tests)) {
            $this->test_add_module_no_errors();
            $this->test_add_module_long_title();
        }

        // Module updating tests
        if(in_array('module_update', $this->_tests)) {
            $dummy_id = $this->_dummy_create(FALSE);

            $this->test_update_module_no_errors($dummy_id);
            $this->_dummy_reset($dummy_id, FALSE);

            $this->test_update_module_long_title($dummy_id);
            $this->_dummy_reset($dummy_id, FALSE);

            $this->test_update_module_not_exist();
            $this->_dummy_reset($dummy_id, FALSE);

            $this->_dummy_delete($dummy_id);
        }

        // Module deleting tests
        if(in_array('module_delete', $this->_tests)) {
            $dummy_id = $this->_dummy_create(FALSE);

            $this->test_delete_module_no_delete($dummy_id);
            $this->_dummy_reset($dummy_id, FALSE);

            $this->test_delete_module_archive($dummy_id);
            $this->_dummy_reset($dummy_id, FALSE);

            $this->test_delete_module_not_exist();
            $this->_dummy_reset($dummy_id, FALSE);

            $this->_dummy_delete($dummy_id);
        }

        // Display test results
        $output['test_results'] = $this->_test_results;
        parent::display_view('tests/tests_results_new.php', $output);
    }

    /********************
     * TOPIC ADDING TESTS
     ********************/
    /**
     * Test for topic addition.
     *
     * @return void
     */
    private function test_add_topic_no_errors()
    {
        $module_id = $this->topic_model->get_by('FK_Parent_Topic IS NULL AND (Archive IS NULL OR Archive = 0)')->ID;
        $title = 'test_topic_no_errors';

        $this->_db_errors_save();
        $this->_send_form_request('form_add_topic', [
            'module_selected' => $module_id,
            'title' => $title
        ]);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(!empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to be empty, but contains an error';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(is_null($this->topic_model->get_by('Topic', $title))) {
            $result->success = FALSE;
            $result->errors[] = "Expected a topic named {$title}, but found none";
        }

        $this->_test_results['topic']['creation']['no_errors'] = $result;
        $this->topic_model->delete_by('Topic', $title);
    }
    /**
     * Test for failed topic addition due to the title being too long.
     *
     * @return void
     */
    private function test_add_topic_long_title()
    {
        // repeat the title enough times to go past the limit
        $test_name = 'test_topic_long_title';
        $repeat_count = ceil(TOPIC_MAX_LENGTH / strlen($test_name)) + 1;
        $module_id = (int) $this->topic_model->get_by('FK_Parent_Topic IS NULL')->ID;
        $title = str_repeat($test_name, $repeat_count);

        $this->_db_errors_save();
        $this->_send_form_request('form_add_topic', [
            'module_selected' => $module_id,
            'title' => $title
        ]);

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
        if(!is_null($this->topic_model->get_by('Topic', $title))) {
            $result->success = FALSE;
            $result->errors[] = "Expected no topic named {$title}, but found one";
        }

        $this->_test_results['topic']['creation']['long_title'] = $result;
        $this->topic_model->delete_by('Topic', $title);
    }
    /**
     * Test for failed topic addition due to inexistant module
     *
     * @return void
     */
    private function test_add_topic_module_selected_not_exist()
    {
        // Grab the next available id and add 1
        $bad_id = $this->topic_model->get_next_id()+1;
        $title = 'test_topic_module_selected_not_exist';

        $this->_db_errors_save();
        $this->_send_form_request('form_add_topic', [
            'module_selected' => $bad_id,
            'title' => $title
        ]);

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
        if(!is_null($this->topic_model->get_by('Topic', $title))) {
            $result->success = FALSE;
            $result->errors[] = "Expected no topic named {$title}, but found one";
        }

        $this->_test_results['topic']['creation']['module_selected_not_exist'] = $result;
        $this->topic_model->delete_by('Topic', $title);
    }
    /**
     * Test for failed topic addition due to module selected being a topic
     *
     * @return void
     */
    private function test_add_topic_module_selected_not_module()
    {
        // Grab a topic (not a module) and get its id
        $bad_id = (int) $this->topic_model->get_by('FK_Parent_Topic IS NOT NULL')->ID;
        $title = 'test_topic_module_selected_not_module';

        $this->_db_errors_save();
        $this->_send_form_request('form_add_topic', [
            'module_selected' => $bad_id,
            'title' => $title
        ]);

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
        if(!is_null($this->topic_model->get_by('Topic', $title))) {
            $result->success = FALSE;
            $result->errors[] = "Expected no topic named {$title}, but found one";
        }

        $this->_test_results['topic']['creation']['module_selected_not_module'] = $result;
        $this->topic_model->delete_by('Topic', $title);
    }

    /**********************
     * TOPIC UPDATING TESTS
     **********************/
    /**
     * Test for topic update
     *
     * @param integer $dummy_id = ID of the dummy topic
     * @return void
     */
    private function test_update_topic_no_errors(int $dummy_id)
    {
        $title = 'topic_dummy';

        $this->_db_errors_save();
        $this->_send_form_request('form_update_topic', [
            'title' => $title,
            'id' => $dummy_id
        ]);

        $topic = $this->topic_model->get($dummy_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(!empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to be empty, but contains an error';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($topic->Topic != $title) {
            $result->success = FALSE;
            $result->errors[] = "Expected topic's name to be '{$title}', but '{$topic->Topic}' was found";
        }

        $this->_test_results['topic']['update']['no_errors'] = $result;
    }
    /**
     * Test for failed topic update due to name being too long
     *
     * @param integer $dummy_id = ID of the dummy topic
     * @return void
     */
    private function test_update_topic_long_title(int $dummy_id)
    {
        $test_name = 'test_topic_long_title_error';
        $repeat_count = ceil(TOPIC_MAX_LENGTH / strlen($test_name)) + 1;
        $title = str_repeat($test_name, $repeat_count);

        $this->_db_errors_save();
        $this->_send_form_request('form_update_topic', [
            'title' => $title,
            'id' => $dummy_id
        ]);

        $topic = $this->topic_model->get($dummy_id);

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
        if($topic->Topic == $title) {
            $result->success = FALSE;
            $result->errors[] = "Expected topic's name to not be updated, but it is";
        }

        $this->_test_results['topic']['update']['long_title'] = $result;
    }
    /**
     * Test for failed topic update due to inexistant topic.
     *
     * @return void
     */
    private function test_update_topic_not_exist()
    {
        $title = 'test_topic_not_exist';
        $bad_id = $this->topic_model->get_next_id()+1;

        $this->_db_errors_save();
        $this->_send_form_request('form_update_topic', [
            'title' => $title,
            'id' => $bad_id
        ]);

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
        if(!is_null($topic)) {
            $result->success = FALSE;
            $result->errors[] = "Expected topic {$bad_id} to not exist, but it does";
        }

        $this->_test_results['topic']['update']['not_exist'] = $result;
    }

    /**********************
     * TOPIC DELETING TESTS
     **********************/
    /**
     * Test for topic deletion, without actually deleting the topic.
     *
     * @param integer $dummy_id = ID of the dummy topic
     * @return void
     */
    private function test_delete_topic_no_delete(int $dummy_id)
    {
        $this->_db_errors_save();
        $this->delete_topic($dummy_id);

        $topic = $this->topic_model->get($dummy_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($topic->Archive == 1) {
            $result->success = FALSE;
            $result->errors[] = 'Expected topic to not be archived, but it is';
        }

        $this->_test_results['topic']['delete']['no_delete'] = $result;
    }
    /**
     * Test for topic deletion, which ends up archiving it
     *
     * @param integer $dummy_id = The id of the dummy to archive
     * @return void
     */
    private function test_delete_topic_archive(int $dummy_id)
    {
        $this->_db_errors_save();
        $this->delete_topic($dummy_id, 1);

        $topic = $this->topic_model->get($dummy_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($topic->Archive != 1) {
            $result->success = FALSE;
            $result->errors[] = 'Expected topic to be archived, but it is not';
        }

        $this->_test_results['topic']['delete']['archive'] = $result;
    }
    /**
     * Test for failed topic deletion due to inexistant topic.
     *
     * @return void
     */
    private function test_delete_topic_not_exist()
    {
        $bad_id = $this->topic_model->get_next_id()+1;

        $this->_db_errors_save();
        $this->delete_topic($bad_id, 1);

        $topic = $this->topic_model->get($bad_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!is_null($topic)) {
            $result->success = FALSE;
            $result->errors[] = "Expected topic {$bad_id} to not exist, but it does";
        }

        $this->_test_results['topic']['delete']['not_exist'] = $result;
    }

    /*********************
     * MODULE ADDING TESTS
     *********************/
    /**
     * Test for module addition
     *
     * @return void
     */
    private function test_add_module_no_errors()
    {
        $title = 'test_module_no_errors';

        $this->_db_errors_save();
        $this->_send_form_request('form_validate_module', [
            'action' => 'add',
            'title' => $title,
            'id' => 0
        ]);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(!empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to be empty, but contains an error';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(is_null($this->topic_model->get_by('Topic', $title))) {
            $result->success = FALSE;
            $result->errors[] = "Expected a module named {$title}, but found none";
        }

        $this->_test_results['module']['creation']['no_errors'] = $result;
        $this->topic_model->delete_by('Topic', $title);
    }
    /**
     * Test for failed module addition due to name being too long
     *
     * @return void
     */
    private function test_add_module_long_title()
    {
        $test_name = 'test_module_long_title_error';
        $repeat_count = ceil(TOPIC_MAX_LENGTH / strlen($test_name)) + 1;
        $title = str_repeat($test_name, $repeat_count);

        $this->_db_errors_save();
        $this->_send_form_request('form_validate_module', [
            'title' => $title
        ]);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = "<code>validation_errors()</code> was expected to contain an error, but it is empty";
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($this->topic_model->get_by('Topic', $title)) {
            $result->success = FALSE;
            $result->errors[] = "Expected no module named {$title}, but found one";
        }

        $this->_test_results['module']['creation']['long_title_error'] = $result;
        $this->topic_model->delete_by('Topic', $title);
    }

    /***********************
     * MODULE UPDATING TESTS
     ***********************/
    /**
     * Test for module update
     *
     * @param integer $dummy_id = ID of the dummy module
     * @return void
     */
    private function test_update_module_no_errors(int $dummy_id)
    {
        $title = 'module_dummy';

        $this->_db_errors_save();
        $this->_send_form_request('form_validate_module', [
            'title' => $title,
            'id' => $dummy_id,
            'action' => 'update'
        ]);

        $module = $this->topic_model->get($dummy_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(!empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = '<code>validation_errors()</code> was expected to be empty, but contains an error';
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($module->Topic != $title) {
            $result->success = FALSE;
            $result->errors[] = "Expected module's name to be '{$title}', but '{$module->Topic}' was found";
        }

        $this->_test_results['module']['update']['no_errors'] = $result;
    }
    /**
     * Test for failed module update due to name being too long
     *
     * @param integer $dummy_id = ID of the dummy module
     * @return void
     */
    private function test_update_module_long_title(int $dummy_id)
    {
        $test_name = 'test_module_long_title_error';
        $repeat_count = ceil(TOPIC_MAX_LENGTH / strlen($test_name)) + 1;
        $title = str_repeat($test_name, $repeat_count);

        $this->_db_errors_save();
        $this->_send_form_request('form_validate_module', [
            'title' => $title,
            'id' => $dummy_id,
            'action' => 'update'
        ]);

        $module = $this->topic_model->get($dummy_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = "<code>validation_errors()</code> was expected to contain an error, but was empty";
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($module->Topic == $title) {
            $result->success = FALSE;
            $result->errors[] = 'Expected module\'s name to not be changed, but it is';
        }

        $this->_test_results['module']['update']['long_title'] = $result;
    }
    /**
     * Test for failed module update due to inexistant module.
     * 
     * @return void
     */
    private function test_update_module_not_exist()
    {
        $title = 'test_module_not_exist';
        $bad_id = $this->topic_model->get_next_id()+1;

        $this->_db_errors_save();
        $this->_send_form_request('form_validate_module', [
            'id' => $bad_id,
            'title' => $title,
            'action' => 'update'
        ]);

        $module = $this->topic_model->get($bad_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if(empty(validation_errors())) {
            $result->success = FALSE;
            $result->errors[] = "<code>validation_errors()</code> was expected to contain an error, but was empty";
        }
        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!is_null($module)) {
            $result->success = FALSE;
            $result->errors[] = "Expected module {$module->ID} to not exist, but it does";
        }

        $this->_test_results['module']['update']['not_exist'] = $result;
    }

    /***********************
     * MODULE DELETING TESTS
     ***********************/
    /**
     * Test for topic deletion, without actually deleting the topic.
     *
     * @param integer $dummy_id = ID of the dummy topic
     * @return void
     */
    private function test_delete_module_no_delete(int $dummy_id)
    {
        $this->_db_errors_save();
        $this->delete_module($dummy_id);

        $module = $this->topic_model->get($dummy_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($module->Archive == 1) {
            $result->success = FALSE;
            $result->errors[] = 'Expected module to not be archived, but it is';
        }

        $this->_test_results['module']['delete']['no_delete'] = $result;
    }
    /**
     * Test for topic deletion, which ends up archiving it
     *
     * @param integer $dummy_id = ID of the dummy to archive
     * @return void
     */
    private function test_delete_module_archive(int $dummy_id)
    {
        $this->_db_errors_save();
        $this->delete_topic($dummy_id, 1);

        $module = $this->topic_model->get($dummy_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if($module->Archive != 1) {
            $result->success = FALSE;
            $result->errors[] = 'Expected module to be archived, but it is not';
        }

        $this->_test_results['module']['delete']['archive'] = $result;
    }
    /**
     * Test for failed module deletion due to inexistant module
     *
     * @return void
     */
    private function test_delete_module_not_exist()
    {
        $bad_id = $this->topic_model->get_next_id()+1;

        $this->_db_errors_save();
        $this->delete_topic($bad_id, 1);

        $module = $this->topic_model->get($bad_id);

        $result = (object) array(
            'success' => TRUE,
            'errors' => []
        );

        if($this->_db_errors_diff()) {
            $result->success = FALSE;
            $result->errors[] = 'One or more errors occured in an SQL statement';
        }
        if(!is_null($module)) {
            $result->success = FALSE;
            $result->errors[] = "Expected module {$module->ID} to not exist, but it does";
        }

        $this->_test_results['module']['delete']['not_exist'] = $result;
    }

    /**
     * Creates a dummy topic for the purpose of testing topic update.
     *
     * @param bool = True if it's a topic
     * @return int = The id of the dummy topic
     */
    private function _dummy_create(bool $is_topic = TRUE) : int
    {
        $datestring = '%Y-%m-%d %h:%i:%s';
        $topic = array(
            'Topic' => $this->_dummy_values[($is_topic?'topic':'module')]['topic'],
            'Creation_Date' => mdate($datestring, time())
        );
        if($is_topic) {
            $module_id = (int) $this->topic_model->get_by('FK_Parent_Topic IS NULL')->ID;
            $topic['FK_Parent_Topic'] = $module_id;
        }
        return $this->topic_model->insert($topic);
    }
    /**
     * Resets the dummy topic
     *
     * @param int $dummy_id = The id of the dummy topic
     * @return void
     */
    private function _dummy_reset(int $dummy_id, bool $is_topic = TRUE)
    {
        $topic = array(
            'Topic' => $this->_dummy_values[($is_topic?'topic':'module')]['topic'],
            'Archive' => NULL
        );
        $this->topic_model->update($dummy_id, $topic);
    }
    /**
     * Deletes the dummy topic, for real
     *
     * @param integer $dummy_id
     * @return boolean
     */
    private function _dummy_delete(int $dummy_id) : bool
    {
        return $this->topic_model->delete($dummy_id);
    }

}