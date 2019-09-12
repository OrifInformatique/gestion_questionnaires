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
        'topic_delete'
    ];
    private $_old_db_error;

    /**
     * The public instance to update
     *
     * @var Topic_Test
     */
    public static $instance;

    public function __construct()
    {
        parent::__construct();

        self::$instance =& $this;
    }

    public function index($error = 0) { }

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
        if(array_search('topic_update', $this->tests) !== FALSE) {
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
        if(array_search('topic_delete', $this->tests) !== FALSE) {
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
        if(array_search('module_add', $this->_tests) !== FALSE) {}

        // Module updating tests
        if(array_search('module_update', $this->_tests) !== FALSE) {}

        // Module deleting tests
        if(array_search('module_delete', $this->_tests) !== FALSE) {}

        // Display test results
        print_r($this->_test_results);
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
        $module_id = (int) $this->topic_model->get_by('FK_Parent_Topic IS NULL')->ID;
        $title = 'test_topic_no_errors';

        $this->_db_error_save();
        $this->_send_form_request('form_add_topic', [
            'module_selected' => $module_id,
            'title' => $title
        ]);

        $this->_test_results['topic']['creation']['no_errors'] = (object) array(
            'errors' => !empty(validation_errors()) && $this->_db_error_diff(),
            'errors_expected' => FALSE,
            'db_update' => !is_null($this->topic_model->get_by('Topic', $title)),
            'db_update_expected' => TRUE
        );
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
            'errors' => !empty(validation_errors()) && $this->_db_error_diff(),
            'errors_expected' => TRUE,
            'db_update' => !is_null($this->topic_model->get_by('Topic', $title)),
            'db_update_expected' => FALSE
        );
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
            'errors' => !empty(validation_errors()) && $this->_db_error_diff(),
            'errors_expected' => TRUE,
            'db_update' => is_null($this->topic_model->get_by('Topic', $_POST['title'])),
            'db_update_expected' => FALSE
        );
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
            'errors' => !empty(validation_errors()) && $this->_db_error_diff(),
            'errors_expected' => TRUE,
            'db_update' => is_null($this->topic_model->get_by('Topic', $title)),
            'db_update_expected' => FALSE
        );
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
            'errors' => !empty(validation_errors()) && $this->_db_error_diff(),
            'errors_expected' => FALSE,
            'db_update' => $topic->Topic == $title,
            'db_update_expected' => TRUE
        );
    }
    /**
     * Test for topic update with a name too long
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
            'errors' => !empty(validation_errors()) && $this->_db_error_diff(),
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
            'errors' => !empty(validation_errors()) && $this->_db_error_diff(),
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

    /***********************
     * MODULE UPDATING TESTS
     ***********************/

    /***********************
     * MODULE DELETING TESTS
     ***********************/

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
    private function _dummy_reset(int $dummy_id)
    {
        $topic = array(
            'Topic' => 'dummy_topic',
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
    public function display_view($view_parts, $data = NULL) {}
}