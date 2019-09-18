<?php

include(__DIR__.'/../Topic.php');

/**
 * Exists only to rewrite the `redirect` function from codeigniter.
 *
 * @param string $uri
 * @param string $method
 * @param integer $code
 * @return void
 */
function redirect($uri = '', $method = 'auto', $code = NULL) { }

/**
 * Test class for Topic controller
 * 
 * @author      Orif, section informatique (ViDi, MeSa, BuYa)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class Topic_Test extends Topic {
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
     * Last saved database error
     *
     * @var object
     */
    private $_old_db_error;

    /**
     * Runs the tests
     *
     * @return void
     */
    public function test()
    {
        // Topic adding tests
        if(array_search('topic_add', $this->_tests) !== FALSE) {
            $this->test_add_topic_no_errors();
            $this->test_add_topic_long_title();
            $this->test_add_topic_module_selected_not_exist();
            $this->test_add_topic_module_selected_not_module();
        }

        // Topic updating tests
        if(array_search('topic_update', $this->_tests) !== FALSE) {
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
        if(array_search('topic_delete', $this->_tests) !== FALSE) {
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
        if(array_search('module_add', $this->_tests) !== FALSE) {
            $this->test_add_module_no_errors();
            $this->test_add_module_long_title();
        }

        // Module updating tests
        if(array_search('module_update', $this->_tests) !== FALSE) {
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
        if(array_search('module_delete', $this->_tests) !== FALSE) {
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
        parent::display_view('tests/tests_results.php', $output);
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

        $this->_db_error_save();
        $this->_send_form_request('form_add_topic', [
            'module_selected' => $module_id,
            'title' => $title
        ]);

        $this->_test_results['topic']['creation']['no_errors'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_error_diff(),
            'errors_expected' => FALSE,
            'db_update' => !is_null($this->topic_model->get_by('Topic', $title)),
            'db_update_expected' => TRUE
        );
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
        $test_name = 'test_topic_long_title_error';
        $repeat_count = ceil(TOPIC_MAX_LENGTH / strlen($test_name)) + 1;
        $module_id = (int) $this->topic_model->get_by('FK_Parent_Topic IS NULL')->ID;
        $title = str_repeat($test_name, $repeat_count);

        $this->_db_error_save();
        $this->_send_form_request('form_add_topic', [
            'module_selected' => $module_id,
            'title' => $title
        ]);

        $this->_test_results['topic']['creation']['long_title_error'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_error_diff(),
            'errors_expected' => TRUE,
            'db_update' => !is_null($this->topic_model->get_by('Topic', $title)),
            'db_update_expected' => FALSE
        );
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

        $this->_db_error_save();
        $this->_send_form_request('form_add_topic', [
            'module_selected' => $bad_id,
            'title' => $title
        ]);

        $this->_test_results['topic']['creation']['module_selected_not_exist'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_error_diff(),
            'errors_expected' => TRUE,
            'db_update' => !is_null($this->topic_model->get_by('Topic', $title)),
            'db_update_expected' => FALSE
        );
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

        $this->_db_error_save();
        $this->_send_form_request('form_add_topic', [
            'module_selected' => $bad_id,
            'title' => $title
        ]);

        $this->_test_results['topic']['creation']['module_selected_not_module'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_error_diff(),
            'errors_expected' => TRUE,
            'db_update' => !is_null($this->topic_model->get_by('Topic', $title)),
            'db_update_expected' => FALSE
        );
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

        $this->_db_error_save();
        $this->_send_form_request('form_update_topic', [
            'title' => $title,
            'id' => $dummy_id
        ]);

        $topic = $this->topic_model->get($dummy_id);

        $this->_test_results['topic']['update']['no_errors'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_error_diff(),
            'errors_expected' => FALSE,
            'db_update' => $topic->Topic == $title,
            'db_update_expected' => TRUE
        );
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

        $this->_db_error_save();
        $this->_send_form_request('form_update_topic', [
            'title' => $title,
            'id' => $dummy_id
        ]);

        $topic = $this->topic_model->get($dummy_id);

        $this->_test_results['topic']['update']['long_title_error'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_error_diff(),
            'errors_expected' => TRUE,
            'db_update' => $topic->Topic == $title,
            'db_update_expected' => FALSE
        );
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

        $this->_db_error_save();
        $this->_send_form_request('form_update_topic', [
            'title' => $title,
            'id' => $bad_id
        ]);

        $topic = $this->topic_model->get($bad_id);

        $this->_test_results['topic']['update']['not_exist'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_error_diff(),
            'errors_expected' => FALSE,
            'db_update' => !is_null($topic),
            'db_update_expected' => FALSE
        );
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
        $topicArchive = $this->topic_model->get($dummy_id)->Archive;

        $this->_db_error_save();
        $this->delete_topic($dummy_id);

        $topic = $this->topic_model->get($dummy_id);

        $this->_test_results['topic']['delete']['no_delete'] = (object) array(
            'errors' => $this->_db_error_diff(),
            'errors_expected' => FALSE,
            'db_update' => $topic->Archive != $topicArchive,
            'db_update_expected' => FALSE
        );
    }
    /**
     * Test for topic deletion, which ends up archiving it
     *
     * @param integer $dummy_id = The id of the dummy to archive
     * @return void
     */
    private function test_delete_topic_archive(int $dummy_id)
    {
        $this->_db_error_save();
        $this->delete_topic($dummy_id, 1);

        $topic = $this->topic_model->get($dummy_id);

        $this->_test_results['topic']['delete']['archive'] = (object) array(
            'errors' => $this->_db_error_diff(),
            'errors_expected' => FALSE,
            'db_update' => $topic->Archive == 1,
            'db_update_expected' => TRUE
        );
    }
    /**
     * Test for failed topic deletion due to inexistant topic.
     *
     * @return void
     */
    private function test_delete_topic_not_exist()
    {
        $bad_id = $this->topic_model->get_next_id()+1;

        $this->_db_error_save();
        $this->delete_topic($bad_id, 1);

        $topic = $this->topic_model->get($bad_id);

        $this->_test_results['topic']['delete']['not_exist'] = (object) array(
            'errors' => $this->_db_error_diff(),
            'errors_expected' => FALSE,
            'db_update' => !is_null($topic),
            'db_update_expected' => FALSE
        );
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

        $this->_db_error_save();
        $this->_send_form_request('form_validate_module', [
            'action' => 'add',
            'title' => $title
        ]);

        $this->_test_results['module']['creation']['no_errors'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_error_diff(),
            'errors_expected' => FALSE,
            'db_update' => !is_null($this->topic_model->get_by('Topic', $title)),
            'db_update_expected' => TRUE
        );
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

        $this->_db_error_save();
        $this->_send_form_request('form_validate_module', [
            'title' => $title
        ]);

        $this->_test_results['module']['creation']['long_title_error'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_error_diff(),
            'errors_expected' => TRUE,
            'db_update' => !is_null($this->topic_model->get_by('Topic', $title)),
            'db_update_expected' => FALSE
        );
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

        $this->_db_error_save();
        $this->_send_form_request('form_validate_module', [
            'title' => $title,
            'id' => $dummy_id,
            'action' => 'update'
        ]);

        $module = $this->topic_model->get($dummy_id);

        $this->_test_results['module']['update']['no_errors'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_error_diff(),
            'errors_expected' => FALSE,
            'db_update' => $module->Topic == $title,
            'db_update_expected' => TRUE
        );
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

        $this->_db_error_save();
        $this->_send_form_request('form_validate_module', [
            'title' => $title,
            'id' => $dummy_id,
            'action' => 'update'
        ]);

        $module = $this->topic_model->get($dummy_id);

        $this->_test_results['module']['update']['long_title_error'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_error_diff(),
            'errors_expected' => TRUE,
            'db_update' => $module->Topic == $title,
            'db_update_expected' => FALSE
        );
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

        $this->_db_error_save();
        $this->_send_form_request('form_validate_module', [
            'id' => $bad_id,
            'title' => $title,
            'action' => 'update'
        ]);

        $module = $this->topic_model->get($bad_id);

        $this->_test_results['module']['update']['not_exist'] = (object) array(
            'errors' => !empty(validation_errors()) || $this->_db_error_diff(),
            'errors_expected' => FALSE,
            'db_update' => !is_null($module),
            'db_update_expected' => FALSE
        );
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
        $moduleArchive = $this->topic_model->get($dummy_id)->Archive;

        $this->_db_error_save();
        $this->delete_module($dummy_id);

        $module = $this->topic_model->get($dummy_id);

        $this->_test_results['topic']['delete']['no_delete'] = (object) array(
            'errors' => $this->_db_error_diff(),
            'errors_expected' => FALSE,
            'db_update' => $module->Archive != $moduleArchive,
            'db_update_expected' => FALSE
        );
    }
    /**
     * Test for topic deletion, which ends up archiving it
     *
     * @param integer $dummy_id = ID of the dummy to archive
     * @return void
     */
    private function test_delete_module_archive(int $dummy_id)
    {
        $this->_db_error_save();
        $this->delete_topic($dummy_id, 1);

        $module = $this->topic_model->get($dummy_id);

        $this->_test_results['topic']['delete']['archive'] = (object) array(
            'errors' => $this->_db_error_diff(),
            'errors_expected' => FALSE,
            'db_update' => $module->Archive == 1,
            'db_update_expected' => TRUE
        );
    }
    /**
     * Test for failed module deletion due to inexistant module
     *
     * @return void
     */
    private function test_delete_module_not_exist()
    {
        $bad_id = $this->topic_model->get_next_id()+1;

        $this->_db_error_save();
        $this->delete_topic($bad_id, 1);

        $module = $this->topic_model->get($bad_id);

        $this->_test_results['module']['delete']['not_exist'] = (object) array(
            'errors' => $this->_db_error_diff(),
            'errors_expected' => FALSE,
            'db_update' => !is_null($module),
            'db_update_expected' => FALSE
        );
    }

    /**************
     * MISC METHODS
     **************/
    /**
     * Sends a request to the form_validation.
     *
     * @param string $form_validation = The name of the function to call
     * @param array $data = The data to store in $_POST
     * @return void
     */
    private function _send_form_request(string $form_validation, array $data = [])
    {
        if(!method_exists($this, $form_validation)) {
            return;
        }

        $this->form_validation->reset_validation();
        foreach($data as $key => $value) {
            $_POST[$key] = $value;
        }
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $this->{$form_validation}();
    }

    /**
     * Saves the most recent database error.
     *
     * @return void
     */
    private function _db_error_save()
    {
        $this->_old_db_error = $this->topic_model->_database->error();
    }
    /**
     * Compares the saved error and the most recent error.
     *
     * @return boolean = True if the 2 errors are different
     */
    private function _db_error_diff() : bool
    {
        return $this->_old_db_error != $this->topic_model->_database->error();
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
            'Topic' => 'dummy_'.($is_topic ? 'topic' : 'module'),
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
            'Topic' => 'dummy_'.($is_topic ? 'topic' : 'module'),
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

    /***********
     * OVERRIDES
     ***********/
    /**
     * Prevents view displaying, call `parent::display_view` instead
     *
     * @param [type] $view_parts
     * @param [type] $data
     * @return void
     */
    public function display_view($view_parts, $data = NULL) { }
}