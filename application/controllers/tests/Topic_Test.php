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
    private $test_results = [
        'topic' => [
            'creation' => [],
            'update' => []
        ]
    ];
    /**
     * Contains the list of tests to run
     *
     * @var array
     */
    private $tests = [
        'topic_update'
    ];

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
        if(array_search('topic_add', $this->tests) !== FALSE) {
            $this->test_add_topic_no_errors();
            $this->test_add_topic_long_title();
            $this->test_add_topic_module_selected_not_exist();
            $this->test_add_topic_module_selected_not_module();
        }

        // Topic updating tests
        if(array_search('topic_update', $this->tests) !== FALSE) {
            $dummy_id = $this->_create_dummy_topic();

            //

            $this->_delete_dummy_topic($dummy_id);
        }

        // Display test results
        print_r($this->test_results);
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
        $this->_send_form_request('form_add_topic', [
            'module_selected' => $module_id,
            'title' => 'test_topic_no_errors'
        ]);

        $this->test_results['topic']['creation']['no_errors'] = (object) array(
            'has_errors' => !empty(validation_errors()),
            'should_have_errors' => FALSE,
            'is_added' => !is_null($this->topic_model->get_by('Topic', 'test_topic_no_errors')),
            'should_be_added' => TRUE
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

        $this->_send_form_request('form_add_topic', [
            'module_selected' => $module_id,
            'title' => str_repeat($test_name, $repeat_count)
        ]);

        $this->test_results['topic']['creation']['long_title_error'] = (object) array(
            'has_errors' => !empty(validation_errors()),
            'should_have_errors' => TRUE,
            'is_added' => !is_null($this->topic_model->get_by('Topic', $_POST['title'])),
            'should_be_added' => FALSE
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

        $this->_send_form_request('form_add_topic', [
            'module_selected' => $bad_id,
            'title' => 'test_topic_module_selected_not_exist'
        ]);

        $this->test_results['topic']['creation']['module_selected_not_exist'] = (object) array(
            'has_errors' => !empty(validation_errors()),
            'should_have_errors' => TRUE,
            'is_added' => is_null($this->topic_model->get_by('Topic', $_POST['title'])),
            'should_be_added' => FALSE
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

        $this->_send_form_request('form_add_topic', [
            'module_selected' => $bad_id,
            'title' => 'test_topic_module_selected_not_module'
        ]);

        $this->test_results['topic']['creation']['module_selected_not_module'] = (object) array(
            'has_errors' => !empty(validation_errors()),
            'should_have_errors' => TRUE,
            'is_added' => is_null($this->topic_model->get_by('Topic', $_POST['title'])),
            'should_be_added' => FALSE
        );
    }

    /**********************
     * TOPIC UPDATING TESTS
     **********************/
    private function test_update_topic_no_errors(int $dummy_id) {
        $this->_send_form_request('form_update_topic', [
            'title' => 'topic_dummy',
            'id' => $dummy_id
        ]);

        $topic = $this->topic_model->get($dummy_id);

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
    private function _send_form_request($form_validation, $data = [])
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
     * Creates a dummy topic for the purpose of testing topic update.
     *
     * @return int = The id of the dummy topic
     */
    private function _create_dummy_topic() : int
    {
        $module_id = (int) $this->topic_model->get_by('FK_Parent_Topic IS NULL')->ID;
        $datestring = '%Y-%m-%d %h:%i:%s';
        $topic = array(
            'Topic' => 'dummy_topic',
            'FK_Parent_Topic' => $module_id,
            'Creation_Date' => mdate($datestring, time())
        );
        return $this->topic_model->insert($topic);
    }
    /**
     * Resets the dummy topic
     *
     * @param int $dummy_id = The id of the dummy topic
     * @return void
     */
    private function _reset_dummy_topic(int $dummy_id)
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
    private function _delete_dummy_topic(int $dummy_id) : bool
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