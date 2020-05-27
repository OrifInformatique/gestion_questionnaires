<?php
require_once(__DIR__.'/../../third_party/Test_Trait.php');
require_once(__DIR__.'/../../controllers/Topic.php');

/**
 * Class for tests for Topic controller
 * 
 * @author      Orif, section informatique (ViDi, MeSa, BuYa)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class Topic_Test extends TestCase {
    use Test_Trait;

    /**
     * List of dummy values
     *
     * @var array
     */
    private static $_dummy_values = [
        'topic' => [
            'parent_topic' => NULL,
            'topic' => 'dummy_topic',
            'topic_alt' => 'topic_dummy'
        ],
        'module' => [
            'parent_topic' => NULL,
            'topic' => 'dummy_module',
            'topic_alt' => 'module_dummy'
        ]
    ];
    /**
     * List of topics and models created
     *
     * @var array
     */
    private static $_dummy_ids = [];

    /*******************
     * START/END METHODS
     *******************/
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

		reset_instance();
		CIPHPUnitTest::createCodeIgniterInstance();
        $CI =& get_instance();
        $CI->load->model('topic_model');
        $module = $CI->topic_model->get_by('FK_Parent_Topic IS NULL AND (Archive IS NULL OR Archive = 0)');
        self::$_dummy_values['topic']['parent_topic'] = $module->ID;
    }
    public function setUp()
    {
        $this->resetInstance();
        $this->CI->load->helper('url');
        $this->CI->load->model('topic_model');
        $this->CI->config->load('../modules/user/config/MY_user_config');
        $this->_login_as($this->CI->config->item('access_lvl_registered'));
    }
    public function tearDown()
    {
        parent::tearDown();

        self::_dummy_topics_reset();

        $_SESSION = [];
        session_reset();
    }
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        self::_dummy_topics_delete();
    }

    /*******
     * TESTS
     *******/
    /**
     * Test for `Topic::index`
     * 
     * @dataProvider provider_index
     *
     * @param integer $access_level = Access level for checking
     * @param boolean $redirect_expected = Whether a redirect is expected
     * @return void
     */
    public function test_index(int $access_level, bool $redirect_expected, bool $error_expected)
    {
        $this->_login_as($access_level);

        $this->_db_errors_save();

        $output = $this->request('GET', 'topic/index');

        if($redirect_expected) {
            $this->assertRedirect('user/auth/login');
        } elseif($error_expected) {
            $this->assertResponseCode(500);
        } else {
            $this->assertContains($this->CI->lang->line('title_topic'), $output);
        }

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Topic::update_topic`
     * 
     * @dataProvider provider_update_topic
     *
     * @param integer $topic_id = ID of the topic to use
     * @param boolean $redirect_expected = Whether a redirect is expected
     * @return void
     */
    public function test_update_topic(int $topic_id, bool $redirect_expected)
    {
        $this->_db_errors_save();

        $output = $this->request('GET', "topic/update_topic/{$topic_id}");

        if($redirect_expected) {
            $this->assertRedirect('topic');
        } else {
            $this->assertContains($this->CI->lang->line('title_topic_update'), $output);
        }

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Topic::form_update_topic`
     * 
     * @dataProvider provider_form_update_topic
     *
     * @param array $post_params = Parameters to pass to $_POST
     * @param boolean $error_expected = Whether an error is expected
     * @return void
     */
    public function test_form_update_topic(array $post_params, bool $error_expected)
    {
        $this->_db_errors_save();

        $this->request('POST', 'topic/form_update_topic', $post_params);

        if ($error_expected) {
            $this->assertTrue(!empty(validation_errors()));
        } else {
            $this->assertRedirect('topic');
        }

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Topic::delete_topic`
     * 
     * @dataProvider provider_delete_topic
     *
     * @param integer $topic_id = ID of the topic
     * @param array $status = Array containing the pre-delete and post-delete
     *  status of the topic
     * @param string $action = Action to pass to the url call
     * @param boolean $redirect_expected = Whether a redirect is expected
     * @param callable $setup = Setup method, will be given the topic_id
     * @return void
     */
    public function test_delete_topic(int $topic_id, array $status, string $action, bool $redirect_expected, callable $setup)
    {
        $setup($topic_id);
        $this->_db_errors_save();

        $topic = $this->CI->topic_model->get($topic_id);
        if($status['deleted']['pre']) {
            $this->assertNull($topic);
        } elseif($status['archived']['pre']) {
            $this->assertTrue($topic->Archive == 1);
        }

        $this->request('GET', "topic/delete_topic/{$topic_id}/{$action}");

        $topic = $this->CI->topic_model->get($topic_id);
        if($redirect_expected) {
            $this->assertRedirect('topic');
        }
        if($status['deleted']['post']) {
            $this->assertNull($topic);
        } elseif($status['archived']['post']) {
            $this->assertTrue($topic->Archive == 1);
        }

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Topic::delete_topic` with a topic linked to a question.
     * Automatically deletes the question afterward.
     *
     * @return void
     */
    public function test_delete_topic_with_questions()
    {
        $topic_id = self::_dummy_topic_create();
        $this->resetInstance();
        $this->CI->load->model(['topic_model', 'question_model']);

        $this->CI->question_model->insert([
            'FK_Topic' => $topic_id,
            'FK_Question_Type' => 1,
            'Question' => 'dummy_question'
        ]);

        $this->_db_errors_save();

        $topic = $this->CI->topic_model->get($topic_id);
        $this->assertNotNull($topic);

        $this->request('GET', "topic/delete_topic/{$topic_id}");

        $topic = $this->CI->topic_model->get($topic_id);
        $this->assertNotNull($topic);

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );

        // Clean up question
        $questions = $this->CI->question_model->get_many_by(['Question' => 'dummy_question']);
        foreach($questions as $question) {
            $this->CI->question_model->delete($question->ID, TRUE);
        }
    }
    /**
     * Test for `Topic::add_topic`
     * 
     * @dataProvider provider_add_topic
     *
     * @param integer $module_id = ID of the selected module
     * @return void
     */
    public function test_add_topic(?int $module_id)
    {
        $this->_db_errors_save();

        $output = $this->request('GET', "topic/add_topic/{$module_id}");

        $this->assertContains($this->CI->lang->line('title_topic_add'), $output);

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Topic::form_add_topic`
     * 
     * @dataProvider provider_form_add_topic
     *
     * @param array $post_params = Parameters to pass to $_POST
     * @param boolean $redirect_expected = Whether a redirect is expected
     * @return void
     */
    public function test_form_add_topic(array $post_params, bool $redirect_expected)
    {
        $this->_db_errors_save();

        $this->request('POST', 'topic/form_add_topic', $post_params);

        if($redirect_expected) {
            $this->assertRedirect('topic');
        } else {
            $this->assertTrue(!empty(validation_errors()));
        }

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Topic::update_module`
     * 
     * @dataProvider provider_update_module
     *
     * @param integer $module_id = ID of the module
     * @param boolean $redirect_expected = Whether a redirect is expected
     * @return void
     */
    public function test_update_module(int $module_id, bool $redirect_expected)
    {
        $this->_db_errors_save();

        $output = $this->request('GET', "topic/update_module/{$module_id}");

        if($redirect_expected) {
            $this->assertRedirect('topic');
        } else {
            $this->assertContains($this->CI->lang->line('title_module_update'), $output);
        }

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Topic::form_validate_module`
     * 
     * @dataProvider provider_form_validate_module_add
     * @dataProvider provider_form_validate_module_update
     *
     * @param array $post_params = Parameters to pass to $_POST
     * @param string $action = Action passed to $_POST
     * @param boolean $redirect_expected = Whether a redirect is expected
     * @return void
     */
    public function test_form_validate_module(array $post_params, string $action, bool $redirect_expected)
    {
        $this->_db_errors_save();

        $output = $this->request('POST', 'topic/form_validate_module', $post_params);

        if($redirect_expected) {
            $this->assertRedirect('topic');
        } else {
            $this->assertTrue(!empty(validation_errors()));
            $text = '';
            switch($action) {
                case 'add':
                    $text = $this->CI->lang->line('title_module_add');
                    break;
                case 'update':
                    $text = $this->CI->lang->line('title_module_update');
                    break;
            }
            $this->assertContains($text, $output);
        }

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Topic::delete_module`
     * 
     * @dataProvider provider_delete_module
     *
     * @param integer $module_id = ID of the module
     * @param array $status = Array containing the pre-delete and post-delete
     *  status of the topic
     * @param string $action = Action to pass to the url call
     * @param boolean $redirect_expected = Whether a redirect is expected
     * @param callable $setup = Setup method, will be given the topic_id
     * @return void
     */
    public function test_delete_module(int $module_id, array $status, string $action, bool $redirect_expected, callable $setup)
    {
        $setup($module_id);
        $this->_db_errors_save();

        $module = $this->CI->topic_model->get($module_id);
        if($status['deleted']['pre']) {
            $this->assertNull($module);
        } elseif($status['archived']['pre']) {
            $this->assertTrue($module->Archive == 1);
        }

        $this->request('GET', "topic/delete_module/{$module_id}/{$action}");

        $module = $this->CI->topic_model->get($module_id);
        if($redirect_expected) {
            $this->assertRedirect('topic');
        }
        if($status['deleted']['post']) {
            $this->assertNull($module);
        } elseif($status['archived']['post']) {
            $this->assertTrue($module->Archive == 1);
        }

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Topic::delete_module` with a module linked to a topic
     *
     * @return void
     */
    public function test_delete_module_with_topics()
    {
        $module_id = self::_dummy_topic_create(TRUE);
        $this->resetInstance();
        $this->CI->load->model('topic_model');

        $this->CI->topic_model->insert([
            'FK_Parent_Topic' => $module_id,
            'Topic' => self::$_dummy_values['topic']['topic']
        ]);

        $this->_db_errors_save();
        
        $module = $this->CI->topic_model->get($module_id);
        $this->assertNotNull($module);

        $this->request('GET', "topic/delete_module/{$module_id}");
        
        $module = $this->CI->topic_model->get($module_id);
        $this->assertNotNull($module);
        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Topic::delete_module` with a module linked to a topic linked
     * to a question
     *
     * @return void
     */
    public function test_delete_module_with_topics_questions()
    {
        $module_id = self::_dummy_topic_create(TRUE);
        $this->resetInstance();
        $this->CI->load->model(['topic_model', 'question_model']);

        $topic_id = (int) $this->CI->topic_model->insert([
            'FK_Parent_Topic' => $module_id,
            'Topic' => self::$_dummy_values['topic']['topic']
        ]);
        $this->CI->question_model->insert([
            'FK_Topic' => $topic_id,
            'FK_Question_Type' => 1,
            'Question' => 'dummy_question'
        ]);

        $this->_db_errors_save();
        
        $module = $this->CI->topic_model->get($module_id);
        $this->assertNotNull($module);

        $this->request('GET', "topic/delete_module/{$module_id}/1");

        $module = $this->CI->topic_model->get($module_id);
        $this->assertTrue($module->Archive == 1);
        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Topic::add_module`
     *
     * @return void
     */
    public function test_add_module()
    {
        $this->_db_errors_save();

        $output = $this->request('GET', 'topic/add_module');

        $this->assertContains($this->CI->lang->line('title_module_add'), $output);

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Topic::cb_topic_exists`
     * 
     * @dataProvider provider_topic_exists
     *
     * @param integer $topic_id = ID of the topic
     * @param boolean $expected = Expected result
     * @return void
     */
    public function test_topic_exists(int $topic_id, bool $expected)
    {
        $this->resetInstance();
        $controller = new Topic();
        $this->CI =& $controller;

        $this->_db_errors_save();

        $this->assertSame($expected, $this->CI->cb_topic_exists($topic_id));

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }

    /***********
     * PROVIDERS
     ***********/
    /**
     * Provider for `test_index`
     *
     * @return array
     */
    public function provider_index() : array
    {
        $this->resetInstance();
        $this->CI->config->load('../modules/user/config/MY_user_config');

        $data = [];

        $data['not_logged'] = [
            0,
            TRUE,
            FALSE
        ];

        $data['logged_guest'] = [
            $this->CI->config->item('access_lvl_guest'),
            FALSE,
            TRUE
        ];

        $data['logged_registered'] = [
            $this->CI->config->item('access_lvl_registered'),
            FALSE,
            FALSE
        ];

        $data['logged_admin'] = [
            $this->CI->config->item('access_lvl_admin'),
            FALSE,
            FALSE
        ];

        return $data;
    }
    /**
     * Provider for `test_update_topic`
     *
     * @return array
     */
    public function provider_update_topic() : array
    {
        $topic_id = self::_dummy_topic_create();

        $this->resetInstance();
        $this->CI->load->model('topic_model');

        $data = [];

        $data['no_error'] = [
            $topic_id,
            FALSE
        ];

        $data['negative'] = [
            -1,
            TRUE
        ];

        $data['not_exist'] = [
            $this->CI->topic_model->get_next_id()+100,
            TRUE
        ];

        return $data;
    }
    /**
     * Data provider for `test_form_update_topic`
     *
     * @return array
     */
    public function provider_form_update_topic() : array
    {
        $topic_id = self::_dummy_topic_create();

        $this->resetInstance();
        $this->CI->load->model('topic_model');
        $topic_name = self::$_dummy_values['topic']['topic'];

        $data = [];

        $data['no_error'] = [
            [
                'id' => $topic_id,
                'title' => $topic_name
            ],
            FALSE
        ];

        $repeat_count = ceil(TOPIC_MAX_LENGTH / strlen($topic_name))+1;
        $topic_name_long = str_repeat($topic_name, $repeat_count);
        $data['long_title'] = [
            [
                'id' => $topic_id,
                'title' => $topic_name_long
            ],
            TRUE
        ];

        $data['negative'] = [
            [
                'id' => -1,
                'title' => $topic_name
            ],
            TRUE
        ];

        $data['not_exist'] = [
            [
                'id' => $this->CI->topic_model->get_next_id()+100,
                'title' => $topic_name
            ],
            TRUE
        ];

        return $data;
    }
    /**
     * Provider for `test_delete_topic`
     *
     * @return array
     */
    public function provider_delete_topic() : array
    {
        $topic_id = self::_dummy_topic_create();

        $this->resetInstance();
        $this->CI->load->model('topic_model');

        $data = [];

        $data['display_no_error'] = [
            $topic_id,
            [
                'deleted' => [
                    'pre' => FALSE,
                    'post' => FALSE
                ],
                'archived' => [
                    'pre' => FALSE,
                    'post' => FALSE
                ]
            ],
            '',
            FALSE,
            function() { }
        ];

        $data['display_negative'] = [
            -1,
            [
                'deleted' => [
                    'pre' => TRUE,
                    'post' => TRUE
                ],
                'archived' => [
                    'pre' => FALSE,
                    'post' => FALSE
                ]
            ],
            '',
            TRUE,
            function() { }
        ];

        $data['display_not_exist'] = [
            $this->CI->topic_model->get_next_id()+100,
            [
                'deleted' => [
                    'pre' => TRUE,
                    'post' => TRUE
                ],
                'archived' => [
                    'pre' => FALSE,
                    'post' => FALSE
                ]
            ],
            '',
            TRUE,
            function() { }
        ];

        $data['delete_no_error'] = [
            $topic_id,
            [
                'deleted' => [
                    'pre' => FALSE,
                    'post' => FALSE
                ],
                'archived' => [
                    'pre' => FALSE,
                    'post' => TRUE
                ]
            ],
            '1',
            TRUE,
            function() { }
        ];

        $data['delete_archived'] = [
            $topic_id,
            [
                'deleted' => [
                    'pre' => FALSE,
                    'post' => FALSE
                ],
                'archived' => [
                    'pre' => TRUE,
                    'post' => TRUE
                ]
            ],
            '1',
            TRUE,
            function($topic_id) {
                $CI =& get_instance();
                $CI->load->model('topic_model');
                $CI->topic_model->update($topic_id, ['Archive' => 1]);
            }
        ];

        $data['delete_negative'] = [
            -1,
            [
                'deleted' => [
                    'pre' => TRUE,
                    'post' => TRUE
                ],
                'archived' => [
                    'pre' => FALSE,
                    'post' => FALSE
                ]
            ],
            '1',
            TRUE,
            function() { }
        ];

        $data['delete_not_exist'] = [
            $this->CI->topic_model->get_next_id()+100,
            [
                'deleted' => [
                    'pre' => TRUE,
                    'post' => TRUE
                ],
                'archived' => [
                    'pre' => FALSE,
                    'post' => FALSE
                ]
            ],
            '1',
            TRUE,
            function() { }
        ];

        return $data;
    }
    /**
     * Provider for `test_add_topic`
     *
     * @return array
     */
    public function provider_add_topic() : array
    {
        $this->resetInstance();
        $this->CI->load->model('topic_model');
        $module_id = $this->CI->topic_model->get_by('(FK_Parent_Topic IS NULL OR FK_Parent_Topic = 0) AND (Archive IS NULL OR Archive = 0)')->ID;

        $data = [];

        $data['no_error'] = [$module_id];

        $data['no_error_null'] = [NULL];

        return $data;
    }
    /**
     * Provider for `test_form_add_topic`
     *
     * @return array
     */
    public function provider_form_add_topic() : array
    {
        $this->resetInstance();
        $this->CI->load->model('topic_model');
        $module_id = $this->CI->topic_model->get_by('(FK_Parent_Topic IS NULL OR FK_Parent_Topic = 0) AND (Archive IS NULL OR Archive = 0)')->ID;
        $topic_name = self::$_dummy_values['topic']['topic'];

        $data = [];

        $data['no_error'] = [
            [
                'title' => $topic_name,
                'module_selected' => $module_id
            ],
            TRUE
        ];

        $repeat_count = ceil(TOPIC_MAX_LENGTH / strlen($topic_name))+1;
        $topic_name_long = str_repeat($topic_name, $repeat_count);
        $data['long_title'] = [
            [
                'title' => $topic_name_long,
                'module_selected' => $module_id
            ],
            FALSE
        ];

        $data['negative'] = [
            [
                'title' => $topic_name,
                'module_selected' => -1
            ],
            FALSE
        ];

        $bad_id = $this->CI->topic_model->get_by('FK_Parent_Topic IS NOT NULL AND (Archive IS NULL OR Archive = 0)')->ID;
        $data['not_module'] = [
            [
                'title' => $topic_name,
                'module_selected' => $bad_id
            ],
            FALSE
        ];

        $bad_id = $this->CI->topic_model->get_next_id()+100;
        $data['not_exist'] = [
            [
                'title' => $topic_name,
                'module_selected' => $bad_id
            ],
            FALSE
        ];

        return $data;
    }
    /**
     * Provider for `test_update_module`
     *
     * @return array
     */
    public function provider_update_module() : array
    {
        $module_id = self::_dummy_topic_create(TRUE);

        $this->resetInstance();
        $this->CI->load->model('topic_model');

        $data = [];

        $data['no_error'] = [
            $module_id,
            FALSE
        ];

        $bad_id = $this->CI->topic_model->get_by('FK_Parent_Topic IS NOT NULL AND (Archive IS NULL OR Archive = 0)')->ID;
        $data['not_module'] = [
            $bad_id,
            FALSE
        ];

        $data['negative'] = [
            -1,
            TRUE
        ];

        $bad_id = $this->CI->topic_model->get_next_id()+100;
        $data['not_exist'] = [
            $bad_id,
            TRUE
        ];

        return $data;
    }
    /**
     * Provider for `test_form_validate_module` for adding new modules
     *
     * @return array
     */
    public function provider_form_validate_module_add() : array
    {
        $module_name = self::$_dummy_values['module']['topic'];

        $data = [];

        $data['add_no_error'] = [
            [
                'title' => $module_name,
                'id' => 0,
                'action' => 'add'
            ],
            'add',
            TRUE
        ];

        $repeat_count = ceil(TOPIC_MAX_LENGTH / strlen($module_name))+1;
        $module_name_long = str_repeat($module_name, $repeat_count);
        $data['add_long_title'] = [
            [
                'title' => $module_name_long,
                'id' => 0,
                'action' => 'add'
            ],
            'add',
            FALSE
        ];

        return $data;
    }
    /**
     * Provider for `test_form_validate_module` for updating modules
     *
     * @return array
     */
    public function provider_form_validate_module_update() : array
    {
        $module_id = self::_dummy_topic_create(TRUE);
        $module_name = self::$_dummy_values['module']['topic_alt'];

        $this->resetInstance();
        $this->CI->load->model('topic_model');

        $data = [];

        $data['update_no_error'] = [
            [
                'title' => $module_name,
                'id' => $module_id,
                'action' => 'update'
            ],
            'update',
            TRUE
        ];

        $repeat_count = ceil(TOPIC_MAX_LENGTH / strlen($module_name))+1;
        $module_name_long = str_repeat($module_name, $repeat_count);
        $data['update_long_title'] = [
            [
                'title' => $module_name_long,
                'id' => $module_id,
                'action' => 'update'
            ],
            'update',
            FALSE
        ];

        $data['update_negative'] = [
            [
                'title' => $module_name,
                'id' => -1,
                'action' => 'update'
            ],
            'update',
            TRUE
        ];

        $bad_id = $this->CI->topic_model->get_by('FK_Parent_Topic IS NOT NULL AND (Archive IS NULL OR Archive = 0)')->ID;
        $data['update_not_module'] = [
            [
                'title' => $module_name,
                'id' => $bad_id,
                'action' => 'update'
            ],
            'update',
            TRUE
        ];

        $bad_id = $this->CI->topic_model->get_next_id()+100;
        $data['update_not_exist'] = [
            [
                'title' => $module_name,
                'id' => $bad_id,
                'action' => 'update'
            ],
            'update',
            TRUE
        ];

        return $data;
    }
    /**
     * Provider for `test_delete_module`
     *
     * @return array
     */
    public function provider_delete_module() : array
    {
        $module_id = self::_dummy_topic_create(TRUE);

        $this->resetInstance();
        $this->CI->load->model('topic_model');

        $data = [];

        $data['display_no_error'] = [
            $module_id,
            [
                'deleted' => [
                    'pre' => FALSE,
                    'post' => FALSE
                ],
                'archived' => [
                    'pre' => FALSE,
                    'post' => FALSE
                ]
            ],
            '',
            FALSE,
            function() { }
        ];

        $data['display_negative'] = [
            -1,
            [
                'deleted' => [
                    'pre' => TRUE,
                    'post' => TRUE
                ],
                'archived' => [
                    'pre' => FALSE,
                    'post' => FALSE
                ]
            ],
            '',
            TRUE,
            function() { }
        ];

        $bad_id = $this->CI->topic_model->get_next_id()+100;
        $data['display_not_exist'] = [
            $bad_id,
            [
                'deleted' => [
                    'pre' => TRUE,
                    'post' => TRUE
                ],
                'archived' => [
                    'pre' => FALSE,
                    'post' => FALSE
                ]
            ],
            '',
            TRUE,
            function() { }
        ];

        $data['delete_no_error'] = [
            $module_id,
            [
                'deleted' => [
                    'pre' => FALSE,
                    'post' => FALSE
                ],
                'archived' => [
                    'pre' => FALSE,
                    'post' => TRUE
                ]
            ],
            '1',
            TRUE,
            function() { }
        ];

        $data['delete_archived'] = [
            $module_id,
            [
                'deleted' => [
                    'pre' => FALSE,
                    'post' => FALSE
                ],
                'archived' => [
                    'pre' => TRUE,
                    'post' => TRUE
                ]
            ],
            '1',
            TRUE,
            function($module_id) {
                $CI =& get_instance();
                $CI->load->model('topic_model');
                $CI->topic_model->update($module_id, ['Archive' => 1]);
            }
        ];

        $data['delete_negative'] = [
            -1,
            [
                'deleted' => [
                    'pre' => TRUE,
                    'post' => TRUE
                ],
                'archived' => [
                    'pre' => FALSE,
                    'post' => FALSE
                ]
            ],
            '1',
            TRUE,
            function () { }
        ];

        $bad_id = $this->CI->topic_model->get_next_id()+100;
        $data['delete_not_exist'] = [
            $bad_id,
            [
                'deleted' => [
                    'pre' => TRUE,
                    'post' => TRUE
                ],
                'archived' => [
                    'pre' => FALSE,
                    'post' => FALSE
                ]
            ],
            '1',
            TRUE,
            function() { }
        ];

        return $data;
    }
    /**
     * Provider for `test_topic_exists`
     *
     * @return array
     */
    public function provider_topic_exists() : array
    {
        $this->resetInstance();
        $this->CI->load->model('topic_model');

        $data = [];

        $data['no_error'] = [
            $this->CI->topic_model->get_all()[0]->ID,
            TRUE
        ];

        $data['zero'] = [
            0,
            TRUE
        ];

        $data['negative'] = [
            -1,
            FALSE
        ];

        $data['not_exist'] = [
            $this->CI->topic_model->get_next_id()+100,
            FALSE
        ];

        return $data;
    }

    /**************
     * MISC METHODS
     **************/
    /**
     * Creates a new module/topic according to the stored dummy values
     *
     * @param boolean $module = Whether it is a module, FALSE by default
     * @return integer = ID of the module/topic
     */
    private static function _dummy_topic_create(bool $module = FALSE) : int
    {
		reset_instance();
		CIPHPUnitTest::createCodeIgniterInstance();
        $CI =& get_instance();
        $CI->load->model('topic_model');

        $dummy_values = self::$_dummy_values[($module ? 'module' : 'topic')];
        $dummy_object = array(
            'FK_Parent_Topic' => $dummy_values['parent_topic'],
            'Topic' => $dummy_values['topic']
        );

        return self::$_dummy_ids[] = $CI->topic_model->insert($dummy_object);
    }
    /**
     * Resets all dummy topics/modules. Automatically detects whether it is a
     * module or a topic
     *
     * @return void
     */
    private static function _dummy_topics_reset()
    {
		reset_instance();
		CIPHPUnitTest::createCodeIgniterInstance();
        $CI =& get_instance();
        $CI->load->model('topic_model');

        $_dummy_ids =& self::$_dummy_ids;

        foreach($_dummy_ids as $topic_id) {
            $topic = $CI->topic_model->get($topic_id);
            $type = '';
            if(is_null($topic)) {
                // The topic/module does not exist anymore
                unset($_dummy_ids[array_search($topic_id, $_dummy_ids)]);
                continue;
            }
            $type = (is_null($topic->FK_Parent_Topic) ? 'module' : 'topic');
            $dummy_values = self::$_dummy_values[$type];
            $reset_object = array(
                'FK_Parent_Topic' => $dummy_values['parent_topic'],
                'Topic' => $dummy_values['topic'],
                'Archive' => NULL
            );
            $CI->topic_model->update($topic_id, $reset_object);
        }
    }
    /**
     * Deletes all dummy topics/modules
     *
     * @return void
     */
    private static function _dummy_topics_delete()
    {
		reset_instance();
		CIPHPUnitTest::createCodeIgniterInstance();
        $CI =& get_instance();
        $CI->load->model(['topic_model', 'question_model']);

        $filters = array(
            self::$_dummy_values['topic']['topic'],
            self::$_dummy_values['topic']['topic_alt'],
            self::$_dummy_values['module']['topic'],
            self::$_dummy_values['module']['topic_alt']
        );
        foreach($filters as $filter) {
            $topics = $CI->topic_model->get_many_by(['Topic' => $filter]);
            foreach($topics as $topic) {
                $questions = $CI->question_model->get_many_by(['FK_Topic' => $topic->ID]);
                foreach($questions as $question) {
                    $CI->question_model->delete($question->ID, TRUE);
                }
            }
        }
    }
}