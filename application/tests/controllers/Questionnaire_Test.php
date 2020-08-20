<?php
require_once(__DIR__.'/../../third_party/Test_Trait.php');
require_once(__DIR__.'/../../libraries/TableTopics.php');
require_once(__DIR__.'/../../controllers/Questionnaire.php');

/**
 * Class for tests for Questionnaire controller
 * 
 * @author      Orif, section informatique (ViDi, MeSa, BuYa)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class Questionnaire_Test extends TestCase {
    use Test_Trait;
    /**
     * List of dummy values
     *
     * @var array
     */
    private static $_dummy_values = [
        'questionnaire' => [
            'name' => 'dummy_questionnaire',
            'name_alt' => 'questionnaire_dummy',
            'subtitle' => 'dummy_subtitle',
            'subtitle_alt' => 'subtitle_dummy',
            'pdf' => 'dummy_quest.pdf',
            'corrige' => 'dummy_quorrige.pdf'
        ],
        'model' => [
            'base_name' => 'dummy_model',
            'base_name_alt' => 'model_dummy',
            'name' => 'dummy_questmodel',
            'name_alt' => 'questmodel_dummy',
            'subtitle' => 'dummy_submodel',
            'subtitle_alt' => 'submodel_dummy'
        ],
        'question' => [
            'question_type' => 1,
            'question' => 'dummy_questionnaire_question',
            'nb_answers' => 1,
            'picture_name' => 'dummy_questionnaire_picture.jpg',
            'points' => 999
        ],
        'topic' => [
            'parent' => 1,
            'topic' => 'dummy_questionnaire_topic'
        ]
    ];
    /**
     * List of questionnaires and models created
     *
     * @var array
     */
    private static $_dummy_ids = [
        'questionnaire' => NULL,
        'model' => NULL,
        'question' => NULL,
        'topic' => NULL
    ];
    /*******************
     * START/END METHODS
     *******************/
    public function setUp()
    {
        $this->resetInstance();
        $this->CI->load->model(['questionnaire_model', 'questionnaire_model_model']);
        $this->CI->config->load('../modules/user/config/MY_user_config');
        $this->_login_as($this->CI->config->item('access_lvl_registered'));
        $this->CI->load->helper('url');

        // Clear tabletopics
        unset($_SESSION['temp_tableTopics']);
        unset($_SESSION['temp_tableTopics_model']);

        // Make sure whatever ID we're using exists
        self::_dummy_topic_get();
        self::_dummy_question_get();
        self::_dummy_questionnaire_get();
        self::_dummy_model_get();
    }
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        reset_instance();
        CIPHPUnitTest::createCodeIgniterInstance();

        self::_dummy_questionnaires_wipe();
        self::_dummy_models_wipe();
        self::_dummy_question_delete();
        self::_dummy_topic_delete();

    }

    /*******
     * TESTS
     *******/
    /**
     * Test for `Questionnaire::index`
     *
     * @return void
     */
    public function test_index()
    {
        $this->_db_errors_save();

        $output = $this->request('GET', 'questionnaire/index');

        $this->assertContains($this->CI->lang->line('title_model'), $output);
        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Home::index` while not logged in
     *
     * @return void
     */
    public function test_index_unlogged()
    {
        $this->_logout();

        $this->request('GET', 'questionnaire');

        $this->assertRedirect('user/auth/login');
    }
    /**
     * Test for `Questionnaire::update`
     * 
     * @dataProvider provider_update
     *
     * @param integer|null $id = ID of the model/questionnaire, leave NULL to
     *      generate/get one from the test (in case it is deleted)
     * @param boolean $is_model = Whether it is a model or a questionnaire
     * @param boolean $redirect_expected = Whether a redirect is expected
     * @return void
     */
    public function test_update(?int &$id, bool $is_model, bool $redirect_expected)
    {
        if(is_null($id)) {
            // ID is null, generate it here
            if($is_model) $id = self::_dummy_model_get();
            else $id = self::_dummy_questionnaire_get();
        }

        $this->_db_errors_save();

        $output = $this->request('GET', "questionnaire/update/{$id}/{$is_model}");

        if($redirect_expected) {
            $this->assertRedirect('questionnaire');
        } else {
            $expected = lang('title_questionnaire'.($is_model?'_model':'').'_update');
            $this->assertContains($expected, $output);
        }

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Questionnaire::form_update`
     * 
     * @dataProvider provider_form_update
     *
     * @param array $post_params = Parameters to pass to $_POST
     * @param boolean $redirect_expected = Whether a redirect is expected
     * @return void
     */
    public function test_form_update(array $post_params, bool $redirect_expected)
    {
        $this->_db_errors_save();

        $this->request('POST', 'questionnaire/form_update', $post_params);

        if($redirect_expected) {
            $this->assertRedirect('questionnaire');
        } else {
            $this->assertNotEmpty(validation_errors());
        }

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Questionnaire::quest_or_model_exists`
     * 
     * @dataProvider provider_quest_or_model_exists
     *
     * @param array $arguments = Arguments to unpack in the method
     * @param boolean $expected = Expected result of the method
     * @return void
     */
    public function test_quest_or_model_exists(array $arguments, bool $expected)
    {
        $this->resetInstance();
        $controller = new Questionnaire();
        $CI =& $controller;

        $actual = $CI->quest_or_model_exists(...$arguments);

        $this->assertSame($expected, $actual);
    }
    /**
     * Test for `Questionnaire::delete`
     * 
     * @dataProvider provider_delete
     *
     * @param integer|null $quest_id = ID of the questionnaire, leave null to get
     *      the current saved one
     * @param string $action = Action to pass to the request
     * @param array $status = Status of the questionnaires (before and after request)
     * @param boolean $redirect_expected = Whether a redirect is expected
     * @return void
     */
    public function test_delete(?int &$quest_id, string $action, array $status, bool $redirect_expected = FALSE)
    {
        $quest_id = $quest_id ?? self::_dummy_questionnaire_get();

        $this->_db_errors_save();

        $questionnaire = $this->CI->questionnaire_model->get($quest_id);
        if($status['deleted']['pre']) {
            $this->assertNull($questionnaire);
        }

        $this->request('GET', "questionnaire/delete/{$quest_id}/{$action}");

        $questionnaire = $this->CI->questionnaire_model->get($quest_id);
        if($status['deleted']['post']) {
            $this->assertNull($questionnaire);
        }
        if($redirect_expected) {
            $this->assertRedirect('questionnaire');
        }

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Questionnaire::get_nb_questions`
     * 
     * @dataProvider provider_get_nb_questions
     *
     * @param integer $topic_id = ID of the topic
     * @param integer $expected = Amount of questions expected
     * @return void
     */
    public function test_get_nb_questions(int &$topic_id, int $expected)
    {
        $this->_db_errors_save();

        $output = (int)$this->request('POST', 'questionnaire/get_nb_questions', ['topic' => $topic_id]);

        $this->assertEquals($expected, $output);
        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Questionnaire::add`
     * 
     * @dataProvider provider_add
     *
     * @param array $parameters = Parameters to pass to the url
     * @param boolean $is_model = Whether it is a model
     * @return void
     */
    public function test_add(array $parameters, bool $is_model = FALSE)
    {
        $output = $this->request('GET', 'questionnaire/add/'.implode('/', $parameters));

        $title = $this->CI->lang->line('add_questionnaire'.($is_model?'_model':'').'_title');

        $this->assertContains($title, $output);
    }
    /**
     * Test for `Questionnaire::form_add`
     *
     * @dataProvider provider_form_add
     *
     * @param array $post_params = Parameters to pass to $_POST
     * @param callable $setup = Setup to call before the test
     * @param boolean $redirect_expected = Whether a redirect is expected
     * @param boolean $error_expected = Whether one or more errors are expected
     */
    public function test_form_add(array $post_params, callable $setup, bool $redirect_expected = FALSE, bool $error_expected = FALSE)
    {
        $setup();

        $this->_db_errors_save();

        $output = $this->request('POST', 'questionnaire/form_add', $post_params);

        if($redirect_expected) {
            $this->assertRedirect('questionnaire');
        } else {
            $needle = $this->CI->lang->line('add_title_questionnaire');
            $this->assertContains($needle, $output);
        }
        if($error_expected) {
            $this->assertNotEmpty(validation_errors());
        }

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Questionnaire::form_add` with cancel
     * 
     * @dataProvider provider_form_add_cancel
     *
     * @param boolean $is_model = Whether it is a model
     * @return void
     */
    public function test_form_add_cancel(bool $is_model)
    {
        $temp_table_store = 'temp_tableTopics'.($is_model?'_model':'');

        $_SESSION[$temp_table_store] = serialize(new TableTopics());

        $this->request('POST', 'questionnaire/form_add', [
            'model' => $is_model,
            'cancel' => '1'
        ]);

        $this->assertEmpty($_SESSION[$temp_table_store] ?? NULL);
    }
    /**
     * Test for `Questionnaire::form_add` with delete_topic
     * 
     * @dataProvider provider_form_add_delete_topic
     *
     * @param array $post_params = Parameters to pass to $_POST
     * @param callable $setup = Setup method to call with the TableTopics
     * @param integer $expected_amount = Amount of items expected in TableTopics arrays
     * @return void
     */
    public function test_form_add_delete_topic(array $post_params, callable $setup, int $expected_amount, bool $error_expected)
    {
        $table_topic = new TableTopics();
        $setup($table_topic);
        $is_model = isset($post_params['model']) && (bool)$post_params['model'];
        $_SESSION['temp_tableTopics'.($is_model?'_model':'')] = serialize($table_topic);

        $this->_db_errors_save();

        $this->request('POST', 'questionnaire/form_add', $post_params);

        $table_topic = unserialize($_SESSION['temp_tableTopics'.($is_model?'_model':'')]);
        $this->assertCount($expected_amount, $table_topic->getArrayTopics());

        if($error_expected) {
            $this->assertNotEmpty(validation_errors());
        }

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Questionnaire::form_add` with add_form
     * 
     * @dataProvider provider_form_add_add_form
     *
     * @param array $post_params = Parameters to pass to $_POST
     * @param integer $expected_amount = Amount of topics listed
     * @param boolean $error_expected = Whether an error is expected
     * @return void
     */
    public function test_form_add_add_form(array $post_params, int $expected_amount, bool $error_expected)
    {
        $this->_db_errors_save();

        $output = $this->request('POST', 'questionnaire/form_add', $post_params);

        $actual = substr_count($output, 'delete_topic[');
        $this->assertSame($expected_amount, $actual);

        if($error_expected) {
            $this->assertNotEmpty(validation_errors());
        }

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Questionnaire::form_add` with add_form with topics already selected
     *
     * @return void
     */
    public function test_form_add_add_form_with_topics()
    {
        $this->CI->load->model('topic_model');
        $table_topic = new TableTopics();
        $table_topic->addArrayNbQuestion(5);
        $rand_topic = $this->CI->topic_model->order_by('RAND()')
            ->get_by('(Archive = 0 OR Archive IS NULL) AND FK_Parent_Topic IS NOT NULL');
        while($rand_topic->ID == self::_dummy_topic_get()) {
            $rand_topic = $this->CI->topic_model->order_by('RAND()')
                ->get_by('(Archive = 0 OR Archive IS NULL) AND FK_Parent_Topic IS NOT NULL');
        }
        $table_topic->addArrayTopics($rand_topic);
        $_SESSION['temp_tableTopics'] = serialize($table_topic);

        $this->_db_errors_save();

        $output = $this->request('POST', 'questionnaire/form_add', [
            'add_form' => 1,
            'topic_selected' => self::$_dummy_ids['topic'],
            'nb_questions' => 1
        ]);

        $actual = substr_count($output, 'delete_topic[');
        $this->assertSame(2, $actual);

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Questionnaire::form_add` with add_form with topics selected being added again
     *
     * @return void
     */
    public function test_form_add_add_form_double()
    {
        $this->CI->load->model('topic_model');
        $table_topic = new TableTopics();
        $table_topic->addArrayNbQuestion(5);
        $topic = $this->CI->topic_model->get(self::_dummy_topic_get());
        $table_topic->addArrayTopics($topic);
        $_SESSION['temp_tableTopics'] = serialize($table_topic);

        $this->_db_errors_save();

        $output = $this->request('POST', 'questionnaire/form_add', [
            'add_form' => 1,
            'topic_selected' => self::$_dummy_ids['topic'],
            'nb_questions' => 1
        ]);

        $actual = substr_count($output, 'delete_topic[');
        $this->assertSame(1, $actual);

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Questionnaire::form_add` with save
     * 
     * @dataProvider provider_form_add_save
     *
     * @param array $post_params = Parameters to pass to $_POST
     * @param boolean $error_expected = Whether an error is expected
     * @param integer $topic_amount = Amount of topics to load
     * @return void
     */
    public function test_form_add_save(array $post_params, bool $error_expected, int $topic_amount)
    {
        $this->CI->load->model('topic_model');

        $table_topic = new TableTopics;
        for($i = 0; $i < $topic_amount; $i++) {
            $rand_topic = $this->CI->topic_model->order_by('RAND()')
                ->get_by('(Archive = 0 OR Archive IS NULL) AND FK_Parent_Topic IS NOT NULL');
            $table_topic->addArrayNbQuestion(5);
            $table_topic->addArrayTopics($rand_topic);
        }
        $is_model = isset($post_params['model']) && (bool)$post_params['model'];
        $_SESSION['temp_tableTopics'.($is_model?'_model':'')] = serialize($table_topic);

        $this->_db_errors_save();

        $this->request('POST', 'questionnaire/form_add', $post_params);

        if($error_expected) {
            $this->assertNotEmpty(validation_errors());
        } else {
            $this->assertRedirect('questionnaire');
        }

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Questionnaire::get_false`
     * 
     * I am ashamed too
     *
     * @return void
     */
    public function test_get_false()
    {
        $this->resetInstance();
        $controller = new Questionnaire();
        $CI =& $controller;

        $this->assertFalse($CI->get_false());
    }
    /**
     * Test for `Questionnaire::generate_pdf`
     * 
     * @dataProvider provider_generate_pdf
     *
     * @param integer|null $quest_id = ID of the questionnaire, defaults to -1 if NULL
     * @param TableTopics|null $table_topic = TableTopics to provide to the method,
     *      defaults to NULL
     * @param boolean $file_expected = Whether the pdf files are expected to exist
     * @return void
     */
    public function test_generate_pdf(?int &$quest_id, ?TableTopics &$table_topic, bool $file_expected)
    {
        $this->expectException(CIPHPUnitTestRedirectException::class);
        // Set CI to a new Questionnaire, as we can only pass strings to requests
        $this->resetInstance();
        $controller = new Questionnaire();
        $CI =& $controller;
        // Use default value if it's NULL, as NULL isn't empty
        $quest_id = $quest_id ?? -1;
        // Use default values if they are not set in $table_topic
        if($quest_id == -1 && !is_null($table_topic)) {
            if(empty($table_topic->getModelName())) {
                $table_topic->setModelName(self::$_dummy_values['model']['base_name']);
            }
            if(empty($table_topic->getTitle())) {
                $table_topic->setTitle(self::$_dummy_values['questionnaire']['name']);
            }
            if(empty($table_topic->getSubtitle())) {
                $table_topic->setSubtitle(self::$_dummy_values['questionnaire']['subtitle']);
            }
        }

        $this->_db_errors_save();

        $CI->generate_pdf($quest_id, $table_topic);

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );

        $title = preg_replace('/[^a-z0-9]+/', '-', $table_topic->getTitle());
        $pdf_exists = file_exists("pdf_files/questionnaires/{$title}.pdf");
        $corrige_exists = file_exists("pdf_files/corriges/{$title}_corrige.pdf");
        if($file_expected) {
            $this->assertTrue($pdf_exists);
            $this->assertTrue($corrige_exists);
        }
        // Making sure PDFs don't exist anymore in case the next test has the same title
        if($pdf_exists) {
            unlink("pdf_files/questionnaires/{$title}.pdf");
        }
        if($corrige_exists) {
            unlink("pdf_files/corriges/{$title}_corrige.pdf");
        }
    }
    /**
     * Test for `Questionnaire::generate_model`
     * 
     * @dataProvider provider_generate_model
     *
     * @param TableTopics|null $table_topic = TableTopics to provide
     * @param boolean $exist_expected = Whether the model is expected to exist
     * @return void
     */
    public function test_generate_model(?TableTopics &$table_topic, bool $exist_expected)
    {
        $this->expectException(CIPHPUnitTestRedirectException::class);
        // Set CI to a new Questionnaire, as we can only pass strings to requests
        $this->resetInstance();
        $controller = new Questionnaire();
        $CI =& $controller;
        $model_name = '';
        if(!is_null($table_topic)) {
            $model_name = $table_topic->getTitle();
        }
        $amount_pre = $this->CI->questionnaire_model_model->count_by(['Questionnaire_Name' => $model_name]);

        $this->_db_errors_save();

        $CI->generate_model($table_topic);

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );

        $amount_post = $this->CI->questionnaire_model_model->count_by(['Questionnaire_Name' => $model_name]);

        $this->assertSame($amount_pre + $exist_expected, $amount_post);
    }
    /**
     * Test for `generate_pdf_from_model`
     * 
     * @dataProvider provider_generate_pdf_from_model
     * 
     * @param integer $model_id = ID of the model to request
     * @param boolean $redirect_expected = Whether a redirect is expected
     * @return void
     */
    public function test_generate_pdf_from_model(int &$model_id, bool $redirect_expected)
    {
        $this->_db_errors_save();

        $output = $this->request('GET', "questionnaire/generate_pdf_from_model/{$model_id}");

        if($redirect_expected) {
            $this->assertRedirect('questionnaire');
        } else {
            $this->assertContains($this->CI->lang->line('finish_questionnaire'), $output);
        }

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Questionnaire::model_generate_pdf`
     * 
     * @dataProvider provider_model_generate_pdf
     *
     * @param array $post_params = Parameters to pass to $_POST
     * @param boolean $error_expected = Whether or not `validation_errors` contains something
     * @return void
     */
    public function test_model_generate_pdf(array $post_params, bool $error_expected)
    {
        $title = preg_replace('/[^a-z0-9]+/', '', ($post_params['title'] ?? ''));
        $pdf = "pdf_files/questionnaires/{$title}.pdf";
        $corrige = "pdf_files/corriges/{$title}_corrige.pdf";

        $this->_db_errors_save();

        $this->request('POST', 'questionnaire/model_generate_pdf', $post_params);

        if($error_expected) {
            $this->assertNotEmpty(validation_errors());
        } else {
            $this->assertRedirect('questionnaire');
        }

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );

        if(file_exists($pdf)) {
            unlink($pdf);
        }
        if(file_exists($corrige)) {
            unlink($corrige);
        }
    }
    /**
     * Test for `Questionnaire::model_delete`
     * 
     * @dataProvider provider_model_delete
     *
     * @param integer $model_id = ID of the model
     * @param string $action = Action to pass to the URL
     * @param array $status = Status of the model before/after the request
     * @param boolean $redirect_expected = Whether a redirect is expected
     * @return void
     */
    public function test_model_delete(int &$model_id, string $action, array $status, bool $redirect_expected)
    {
        $model = $this->CI->questionnaire_model_model->get($model_id);
        if($status['deleted']['pre']) {
            $this->assertNull($model);
        } else {
            $this->assertNotNull($model);
        }

        $this->_db_errors_save();

        $this->request('GET', "questionnaire/model_delete/{$model_id}/{$action}");

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );

        $model = $this->CI->questionnaire_model_model->get($model_id);
        if($status['deleted']['post']) {
            $this->assertNull($model);
        } else {
            $this->assertNotNull($model);
        }
        if($redirect_expected) {
            $this->assertRedirect('questionnaire');
        }
    }
    /**
     * Test for `Questionnaire::cb_topic_exists`
     * 
     * @dataProvider provider_topic_exists
     *
     * @param integer $topic_id = ID of the topic
     * @param boolean $exist_expected = Whether it exists
     * @return void
     */
    public function test_topic_exists(int &$topic_id, bool $exist_expected)
    {
        // Set CI to a new Questionnaire, as we can only get strings from requests
        $this->resetInstance();
        $controller = new Questionnaire();
        $CI =& $controller;

        $this->_db_errors_save();

        $exists = $CI->cb_topic_exists($topic_id);

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );

        $this->assertSame($exist_expected, $exists);
    }

    /***********
     * PROVIDERS
     ***********/
    /**
     * Provider for `test_update`
     *
     * @return array
     */
    public function provider_update() : array
    {
        $this->resetInstance();
        $this->CI->load->model(['questionnaire_model', 'questionnaire_model_model']);

        $data = [];

        $data['no_error_quest'] = [
            NULL,
            FALSE,
            FALSE
        ];

        $data['no_error_model'] = [
            NULL,
            TRUE,
            FALSE
        ];

        $data['quest_not_exist'] = [
            $this->CI->questionnaire_model->get_next_id()+100,
            FALSE,
            TRUE
        ];

        $data['model_not_exist'] = [
            $this->CI->questionnaire_model_model->get_next_id()+100,
            TRUE,
            TRUE
        ];

        $data['quest_negative'] = [
            -1,
            FALSE,
            TRUE
        ];

        $data['model_negative'] = [
            -1,
            TRUE,
            TRUE
        ];

        return $data;
    }
    /**
     * Provider for `test_form_update`
     *
     * @return array
     */
    public function provider_form_update() : array
    {
        $this->resetInstance();
        $this->CI->load->model(['questionnaire_model', 'questionnaire_model_model']);

        $quest_id =& self::$_dummy_ids['questionnaire'];
        $quest_alt_name = self::$_dummy_values['questionnaire']['name_alt'];
        $quest_alt_subtitle = self::$_dummy_values['questionnaire']['subtitle_alt'];
        $model_id =& self::$_dummy_ids['model'];
        $model_alt_name = self::$_dummy_values['model']['name_alt'];
        $model_alt_subtitle = self::$_dummy_values['model']['subtitle_alt'];
        $data = [];

        $data['quest_no_error'] = [
            [
                'id' => &$quest_id,
                'model' => FALSE,
                'title' => $quest_alt_name,
                'subtitle' => $quest_alt_subtitle
            ],
            TRUE
        ];

        $data['model_no_error'] = [
            [
                'id' => &$model_id,
                'model' => TRUE,
                'title' => $model_alt_name,
                'subtitle' => $model_alt_subtitle
            ],
            TRUE
        ];

        $data['quest_not_exist'] = [
            [
                'id' => $this->CI->questionnaire_model->get_next_id()+100,
                'model' => FALSE,
                'title' => $quest_alt_name,
                'subtitle' => $quest_alt_subtitle
            ],
            FALSE
        ];

        $data['model_not_exist'] = [
            [
                'id' => $this->CI->questionnaire_model_model->get_next_id()+100,
                'model' => TRUE,
                'title' => $model_alt_name,
                'subtitle' => $model_alt_subtitle
            ],
            FALSE
        ];

        $data['quest_negative'] = [
            [
                'id' => -1,
                'model' => FALSE,
                'title' => $quest_alt_name,
                'subtitle' => $quest_alt_subtitle
            ],
            FALSE
        ];

        $data['model_negative'] = [
            [
                'id' => -1,
                'model' => TRUE,
                'title' => $model_alt_name,
                'subtitle' => $model_alt_subtitle
            ],
            FALSE
        ];

        return $data;
    }
    /**
     * Provider for `test_quest_or_model_exists`
     *
     * @return array
     */
    public function provider_quest_or_model_exists() : array
    {
        $this->resetInstance();
        $this->CI->load->model(['questionnaire_model', 'questionnaire_model_model']);
        $quest_id =& self::_dummy_questionnaire_get();
        $model_id =& self::_dummy_model_get();

        $data = [];

        $data['quest_no_error'] = [
            [&$quest_id, FALSE],
            TRUE
        ];

        $data['model_no_error'] = [
            [&$model_id, TRUE],
            TRUE
        ];

        $data['quest_not_exist'] = [
            [$this->CI->questionnaire_model->get_next_id()+100, FALSE],
            FALSE
        ];

        $data['model_not_exist'] = [
            [$this->CI->questionnaire_model_model->get_next_id()+100, TRUE],
            FALSE
        ];

        $data['quest_negative'] = [
            [-1, FALSE],
            FALSE
        ];

        $data['model_negative'] = [
            [-1, TRUE],
            FALSE
        ];

        return $data;
    }
    /**
     * Provider for `test_delete`
     *
     * @return array
     */
    public function provider_delete() : array
    {
        $this->resetInstance();
        $this->CI->load->model('questionnaire_model');
        $quest_id =& self::_dummy_questionnaire_get();

        $data = [];

        $data['display_no_error'] = [
            &$quest_id,
            '',
            array('deleted' => [
                'pre' => FALSE,
                'post' => FALSE
            ])
        ];

        $data['display_not_exist'] = [
            $this->CI->questionnaire_model->get_next_id()+100,
            '',
            array('deleted' => [
                'pre' => TRUE,
                'post' => TRUE
            ]),
            TRUE
        ];

        $data['display_negative'] = [
            -1,
            '',
            array('deleted' => [
                'pre' => TRUE,
                'post' => TRUE
            ]),
            TRUE
        ];

        $data['delete_no_error'] = [
            &$quest_id,
            '1',
            array('deleted' => [
                'pre' => FALSE,
                'post' => TRUE
            ]),
            TRUE
        ];

        $data['delete_not_exist'] = [
            $this->CI->questionnaire_model->get_next_id()+100,
            '1',
            array('deleted' => [
                'pre' => TRUE,
                'post' => TRUE
            ]),
            TRUE
        ];

        $data['delete_negative'] = [
            -1,
            '1',
            array('deleted' => [
                'pre' => TRUE,
                'post' => TRUE
            ]),
            TRUE
        ];

        return $data;
    }
    /**
     * Provider for `test_get_nb_questions`
     *
     * @return array
     */
    public function provider_get_nb_questions() : array
    {
        $this->resetInstance();
        $this->CI->load->model(['question_model', 'topic_model']);

        $data = [];

        $topic_id = $this->CI->topic_model->order_by('RAND()')
            ->get_by('FK_Parent_Topic IS NOT NULL')->ID;
        $expected_amount = count($this->CI->question_model->get_many_by("FK_Topic = {$topic_id} AND Archive = 0"));
        $data['no_problem'] = [
            $topic_id,
            $expected_amount
        ];

        $topic_id = $this->CI->topic_model->order_by('RAND()')
            ->get_by('FK_Parent_Topic IS NULL')->ID;
        $expected_amount = count($this->CI->question_model->get_many_by("FK_Topic = {$topic_id} AND Archive = 0"));
        $data['not_topic'] = [
            $topic_id,
            $expected_amount
        ];

        $topic_id = -1;
        $expected_amount = count($this->CI->question_model->get_many_by("FK_Topic = {$topic_id} AND Archive = 0"));
        $data['topic_not_exist'] = [
            $topic_id,
            $expected_amount
        ];

        return $data;
    }
    /**
     * Provider for `test_add`
     *
     * @return array
     */
    public function provider_add() : array
    {
        $data = [];

        $data['quest'] = [
            [
                0,
                NULL,
                NULL
            ]
        ];

        $data['model'] = [
            [
                1,
                NULL,
                NULL
            ],
            TRUE
        ];

        return $data;
    }
    /**
     * Provider for `test_form_add` without a target
     *
     * @return array
     */
    public function provider_form_add() : array
    {
        $data = [];

        $data['empty_quest_no_tt'] = [
            [],
            function() { }
        ];

        $data['empty_model_no_tt'] = [
            ['model' => TRUE],
            function() { }
        ];

        $data['empty_quest_tt'] = [
            [],
            function() {
                $table_topic = new TableTopics();
                $_SESSION['temp_tableTopics'] = serialize($table_topic);
            }
        ];

        $data['empty_model_tt'] = [
            ['model' => TRUE],
            function() {
                $table_topic = new TableTopics();
                $_SESSION['temp_tableTopics_model'] = serialize($table_topic);
            }
        ];

        $data['empty_quest_not_tt'] = [
            [],
            function() {
                $table_topic = (object)[];
                $_SESSION['temp_tableTopics'] = serialize($table_topic);
            }
        ];

        $data['empty_model_not_tt'] = [
            [],
            function() {
                $table_topic = (object)[];
                $_SESSION['temp_tableTopics_model'] = serialize($table_topic);
            }
        ];

        return $data;
    }
    /**
     * Provider for `test_form_add_cancel`
     *
     * @return array
     */
    public function provider_form_add_cancel() : array
    {
        $data = [];

        $data['quest'] = [FALSE];

        $data['model'] = [TRUE];

        return $data;
    }
    /**
     * Provider for `test_form_add_delete_topic`
     *
     * @return array
     */
    public function provider_form_add_delete_topic() : array
    {
        $data = [];

        $data['delete_topic_quest_no_error'] = [
            ['delete_topic' => [0 => 1]],
            function(TableTopics &$table_topic) {
                $table_topic->addArrayTopics((object)['ID' => 1, 'Topic' => '']);
                $table_topic->addArrayNbQuestion(1);
                $table_topic->addArrayTopics((object)['ID' => 2, 'Topic' => '']);
                $table_topic->addArrayNbQuestion(2);
            },
            1,
            FALSE
        ];

        $data['delete_topic_model_no_error'] = [
            [
                'delete_topic' => [0 => 1],
                'model' => TRUE
            ],
            function(TableTopics &$table_topic) {
                $table_topic->addArrayTopics((object)['ID' => 1, 'Topic' => '']);
                $table_topic->addArrayNbQuestion(1);
                $table_topic->addArrayTopics((object)['ID' => 2, 'Topic' => '']);
                $table_topic->addArrayNbQuestion(2);
            },
            1,
            FALSE
        ];

        $data['delete_topic_quest_not_exist'] = [
            ['delete_topic' => [6 => 1]],
            function(TableTopics &$table_topic) {
                $table_topic->addArrayTopics((object)['ID' => 1, 'Topic' => '']);
                $table_topic->addArrayNbQuestion(1);
            },
            1,
            TRUE
        ];

        $data['delete_topic_model_not_exist'] = [
            [
                'delete_topic' => [6 => 1],
                'model' => TRUE
            ],
            function(TableTopics &$table_topic) {
                $table_topic->addArrayTopics((object)['ID' => 1, 'Topic' => '']);
                $table_topic->addArrayNbQuestion(1);
            },
            1,
            TRUE
        ];

        $data['delete_topic_quest_negative'] = [
            ['delete_topic' => [-1 => 1]],
            function(TableTopics &$table_topic) {
                $table_topic->addArrayTopics((object)['ID' => 1, 'Topic' => '']);
                $table_topic->addArrayNbQuestion(1);
            },
            1,
            TRUE
        ];

        $data['delete_topic_model_negative'] = [
            [
                'delete_topic' => [-1 => 1],
                'model' => TRUE
            ],
            function(TableTopics &$table_topic) {
                $table_topic->addArrayTopics((object)['ID' => 1, 'Topic' => '']);
                $table_topic->addArrayNbQuestion(1);
            },
            1,
            TRUE
        ];

        return $data;
    }
    /**
     * Provider for `test_form_add_add_form`
     *
     * @return array
     */
    public function provider_form_add_add_form() : array
    {
        $this->resetInstance();
        $this->CI->load->model('topic_model');

        $data = [];

        $data['quest_no_error'] = [
            [
                'add_form' => 1,
                'topic_selected' => &self::$_dummy_ids['topic'],
                'nb_questions' => 1
            ],
            1,
            FALSE
        ];

        $data['model_no_error'] = [
            [
                'model' => TRUE,
                'add_form' => 1,
                'topic_selected' => &self::$_dummy_ids['topic'],
                'nb_questions' => 1
            ],
            1,
            FALSE
        ];

        $data['quest_not_exist'] = [
            [
                'add_form' => 1,
                'topic_selected' => $this->CI->topic_model->get_next_id()+100,
                'nb_questions' => 1
            ],
            0,
            TRUE
        ];

        $data['model_not_exist'] = [
            [
                'model' => TRUE,
                'add_form' => 1,
                'topic_selected' => $this->CI->topic_model->get_next_id()+100,
                'nb_questions' => 1
            ],
            0,
            TRUE
        ];

        $data['quest_negative'] = [
            [
                'add_form' => 1,
                'topic_selected' => -1,
                'nb_questions' => 1
            ],
            0,
            TRUE
        ];

        $data['model_negative'] = [
            [
                'model' => TRUE,
                'add_form' => 1,
                'topic_selected' => -1,
                'nb_questions' => 1
            ],
            0,
            TRUE
        ];

        return $data;
    }
    /**
     * Provider for `test_form_add_save`
     *
     * @return array
     */
    public function provider_form_add_save() : array
    {
        $quest_name = self::$_dummy_values['questionnaire']['name'];
        $quest_subtitle = self::$_dummy_values['questionnaire']['subtitle'];
        $model_base_name = self::$_dummy_values['model']['base_name'];
        $model_name = self::$_dummy_values['model']['name'];
        $model_subtitle = self::$_dummy_values['model']['subtitle'];

        $data = [];

        $data['quest_no_error'] = [
            [
                'save' => 1,
                'title' => $quest_name,
                'subtitle' => $quest_subtitle
            ],
            FALSE,
            1
        ];

        $data['model_no_error'] = [
            [
                'model' => 1,
                'save' => 1,
                'title' => $model_name,
                'subtitle' => $model_subtitle,
                'modelName' => $model_base_name
            ],
            FALSE,
            1
        ];

        $data['quest_empty_tt'] = [
            [
                'save' => 1,
                'title' => $quest_name,
                'subtitle' => $quest_subtitle
            ],
            TRUE,
            0
        ];

        $data['model_empty_tt'] = [
            [
                'save' => 1,
                'title' => $model_name,
                'subtitle' => $model_subtitle,
                'modelName' => $model_base_name
            ],
            TRUE,
            0
        ];

        return $data;
    }
    /**
     * Provider for `test_generate_pdf`
     *
     * @return array
     */
    public function provider_generate_pdf() : array
    {
        $this->resetInstance();
        $this->CI->load->model(['questionnaire_model', 'topic_model']);

        $data = [];

        $data['empty'] = [NULL,NULL,FALSE];

        $data['quest_no_problem'] = [
            &self::$_dummy_ids['questionnaire'],
            NULL,
            TRUE
        ];

        $data['quest_not_exist'] = [
            $this->CI->questionnaire_model->get_next_id()+100,
            NULL,
            FALSE
        ];

        $table_topic = new TableTopics();
        $table_topic->addArrayNbQuestion(5);
        $rand_topic = $this->CI->topic_model->order_by('RAND()')
            ->get_by('(Archive = 0 OR Archive IS NULL) AND FK_Parent_Topic IS NOT NULL');
        $table_topic->addArrayTopics($rand_topic);
        $data['tt_no_problem'] = [
            NULL,
            $table_topic,
            TRUE
        ];

        $data['tt_empty'] = [
            NULL,
            new TableTopics(),
            FALSE
        ];

        return $data;
    }
    /**
     * Provider for `test_generate_model`
     *
     * @return array
     */
    public function provider_generate_model() : array
    {
        $this->resetInstance();
        $this->CI->load->model('topic_model');

        $data = [];

        $data['tt_null'] = [
            NULL,
            FALSE
        ];

        $table_topic = new TableTopics;
        $table_topic->addArrayNbQuestion(5);
        $rand_topic = $this->CI->topic_model->order_by('RAND()')
            ->get_by('(Archive = 0 OR Archive IS NULL) AND FK_Parent_Topic IS NOT NULL');
        $table_topic->addArrayTopics($rand_topic);
        $table_topic->setModelName(self::$_dummy_values['model']['base_name']);
        $table_topic->setTitle(self::$_dummy_values['model']['name']);
        $table_topic->setSubtitle(self::$_dummy_values['model']['subtitle']);
        $data['tt_no_problem'] = [
            &$table_topic,
            TRUE
        ];

        $data['tt_empty'] = [
            new TableTopics,
            FALSE
        ];

        return $data;
    }
    /**
     * Provider for `test_generate_pdf_from_model`
     *
     * @return array
     */
    public function provider_generate_pdf_from_model() : array
    {
        $this->resetInstance();
        $this->CI->load->model('questionnaire_model_model');

        $data = [];

        $data['no_error'] = [
            &self::$_dummy_ids['model'],
            FALSE
        ];

        $data['not_exist'] = [
            $this->CI->questionnaire_model_model->get_next_id()+100,
            TRUE
        ];

        $data['negative'] = [
            -1,
            TRUE
        ];

        return $data;
    }
    /**
     * Provider for `model_generate_pdf`
     *
     * @return array
     */
    public function provider_model_generate_pdf() : array
    {
        $this->resetInstance();
        $this->CI->load->model('questionnaire_model_model');

        $model_name = self::$_dummy_values['model']['name'];
        $model_subtitle = self::$_dummy_values['model']['subtitle'];
        $model_id =& self::_dummy_model_get();

        $data = [];

        $data['no_error'] = [
            [
                'modelId' => &$model_id,
                'title' => $model_name,
                'subtitle' => $model_subtitle
            ],
            FALSE
        ];

        $data['not_exist'] = [
            [
                'modelId' => $this->CI->questionnaire_model_model->get_next_id()+100,
                'title' => $model_name,
                'subtitle' => $model_subtitle
            ],
            TRUE
        ];

        $data['no_title'] = [
            [
                'modelId' => &$model_id,
                'subtitle' => $model_subtitle
            ],
            TRUE
        ];

        $data['no_subtitle'] = [
            [
                'modelId' => &$model_id,
                'title' => $model_name
            ],
            FALSE
        ];

        return $data;
    }
    /**
     * Provider for `test_model_delete`
     *
     * @return array
     */
    public function provider_model_delete() : array
    {
        $this->resetInstance();
        $this->CI->load->model('questionnaire_model_model');

        $model_id =& self::$_dummy_ids['model'];

        $data = [];

        $data['display_no_problem'] = [
            &$model_id,
            '',
            [
                'deleted' => [
                    'pre' => FALSE,
                    'post' => FALSE
                ]
            ],
            FALSE
        ];

        $data['display_not_exist'] = [
            $this->CI->questionnaire_model_model->get_next_id()+100,
            '',
            [
                'deleted' => [
                    'pre' => TRUE,
                    'post' => TRUE
                ]
            ],
            TRUE
        ];

        $data['delete_no_problem'] = [
            &$model_id,
            '1',
            [
                'deleted' => [
                    'pre' => FALSE,
                    'post' => TRUE
                ]
            ],
            TRUE
        ];

        $data['delete_not_exist'] = [
            $this->CI->questionnaire_model_model->get_next_id()+100,
            '1',
            [
                'deleted' => [
                    'pre' => TRUE,
                    'post' => TRUE
                ]
            ],
            TRUE
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

        $data['no_problem'] = [
            &self::$_dummy_ids['topic'],
            TRUE
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
     * Obtains the ID of the dummy questionnaire, or creates it if it doesn't exist.
     *
     * @return integer = ID of the dummy questionnaire
     */
    private static function &_dummy_questionnaire_get() : int
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model(['questionnaire_model', 'question_questionnaire_model']);

        $questionnaire = $CI->questionnaire_model->get(self::$_dummy_ids['questionnaire']);
        if(is_null($questionnaire)) {
            // Questionnaire no longer exists, create a new one
            $dummy_values =& self::$_dummy_values['questionnaire'];

            $questionnaire = array(
                'Questionnaire_Name' => $dummy_values['name'],
                'Questionnaire_Subtitle' => $dummy_values['subtitle'],
                'PDF' => $dummy_values['pdf'],
                'Corrige_PDF' => $dummy_values['corrige']
            );
            self::$_dummy_ids['questionnaire'] = (int)$CI->questionnaire_model->insert($questionnaire);

            $link = array(
                'FK_Questionnaire' => self::$_dummy_ids['questionnaire'],
                'FK_Question' => self::$_dummy_ids['question']
            );
            $CI->question_questionnaire_model->insert($link);
        }

        return self::$_dummy_ids['questionnaire'];
    }
    /**
     * Resets the dummy questionnaire
     *
     * @return void
     */
    private static function _dummy_questionnaire_reset()
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('questionnaire_model');

        $questionnaire = $CI->questionnaire_model->get(self::$_dummy_ids['questionnaire']);
        if(is_null($questionnaire)) {
            self::_dummy_questionnaire_get();
        } else {
            $dummy_values =& self::$_dummy_values['questionnaire'];
    
            $old_pdf = $questionnaire->PDF;
            $old_corrige = $questionnaire->Corrige_PDF;
            $new_pdf = $dummy_values['pdf'];
            $new_corrige = $dummy_values['corrige'];
    
            rename('pdf_files/questionnaires/'.$old_pdf, 'pdf_files/questionnaires/'.$new_pdf);
            rename('pdf_files/corriges/'.$old_corrige, 'pdf_files/corriges/'.$new_corrige);
    
            $questionnaire_up = array(
                'Questionnaire_Name' => $dummy_values['name'],
                'Questionnaire_Subtitle' => $dummy_values['subtitle'],
                'PDF' => $new_pdf,
                'Corrige_PDF' => $new_corrige
            );
            $CI->questionnaire_model->update(self::$_dummy_ids['questionnaire'], $questionnaire_up);
        }
    }
    /**
     * Like `_dummy_questionnaire_delete`, but for all the questionnaires created
     * in tests.
     *
     * @return void
     */
    private static function _dummy_questionnaires_wipe()
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model(['questionnaire_model', 'question_questionnaire_model']);

        $dummy_values = [
            self::$_dummy_values['questionnaire']['name'],
            self::$_dummy_values['questionnaire']['name_alt'],
            self::$_dummy_values['model']['name'],
            self::$_dummy_values['model']['name_alt'],
            ''
        ];
        foreach($dummy_values as $value) {
            $questionnaires = $CI->questionnaire_model->get_many_by(['Questionnaire_Name' => $value]);
            foreach($questionnaires as $questionnaire) {
                if(file_exists('pdf_files/questionnaires/'.$questionnaire->PDF)) {
                    unlink('pdf_files/questionnaires/'.$questionnaire->PDF);
                }
                if(file_exists('pdf_files/corriges/'.$questionnaire->Corrige_PDF)) {
                    unlink('pdf_files/corriges/'.$questionnaire->Corrige_PDF);
                }
                $CI->question_questionnaire_model->delete_by(['FK_Questionnaire' => $questionnaire->ID]);
                $CI->questionnaire_model->delete($questionnaire->ID);
            }
        }
    }

    /**
     * Obtains the ID of the dummy model, or creates it if it doesn't exist.
     *
     * @return integer = ID of the dummy model
     */
    private static function &_dummy_model_get() : int
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model(['questionnaire_model_model', 'questionnaire_model_topic_model']);

        $model = $CI->questionnaire_model_model->get(self::$_dummy_ids['model']);
        if(is_null($model)) {
            // Model no longer exists, create a new one
            $dummy_values =& self::$_dummy_values['model'];

            $model = array(
                'Base_Name' => $dummy_values['base_name'],
                'Questionnaire_Name' => $dummy_values['name'],
                'Questionnaire_Subtitle' => $dummy_values['subtitle']
            );
            self::$_dummy_ids['model'] = (int)$CI->questionnaire_model_model->insert($model);

            $link = array(
                'FK_Questionnaire_Model' => self::$_dummy_ids['model'],
                'FK_Topic' => self::$_dummy_ids['topic'],
                'Nb_Topic_Questions' => 1
            );
            $CI->questionnaire_model_topic_model->insert($link);
        }

        return self::$_dummy_ids['model'];
    }
    /**
     * Resets the dummy model
     *
     * @return void
     */
    private static function _dummy_model_reset()
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('questionnaire_model_model');

        $model = $CI->questionnaire_model_model->get(self::$_dummy_ids['model']);
        if(is_null($model)) {
            self::_dummy_model_get();
        } else {
            $dummy_values =& self::$_dummy_values['model'];

            $model = array(
                'Base_Name' => $dummy_values['base_name'],
                'Questionnaire_Name' => $dummy_values['name'],
                'Questionnaire_Subtitle' => $dummy_values['subtitle']
            );
            $CI->questionnaire_model_model->update(self::$_dummy_ids['model'], $model);
        }
    }
    /**
     * Like `_dummy_model_delete`, but for all the models created in tests.
     *
     * @return void
     */
    private static function _dummy_models_wipe()
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model(['questionnaire_model_model', 'questionnaire_model_topic_model']);

        $dummy_values = [
            self::$_dummy_values['model']['name'],
            self::$_dummy_values['model']['name_alt'],
            ''
        ];
        foreach($dummy_values as $value) {
            $models = $CI->questionnaire_model_model->get_many_by(['Questionnaire_Name' => $value]);
            foreach($models as $model) {
                $CI->questionnaire_model_topic_model->delete_by(['FK_Questionnaire_Model' => $model->ID]);
                $CI->questionnaire_model_model->delete($model->ID);
            }
        }
    }

    /**
     * Obtains the ID of the dummy question, or creates it if it doesn't exist.
     *
     * @return integer = ID of the dummy question
     */
    private static function &_dummy_question_get() : int
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('question_model');

        $question = $CI->question_model->with_deleted()->get(self::$_dummy_ids['question']);
        if(is_null($question)) {
            // Question no longer exists, create a new one
            $dummy_values = self::$_dummy_values['question'];

            $question = array(
                'FK_Topic' => self::$_dummy_ids['topic'],
                'FK_Question_Type' => $dummy_values['question_type'],
                'Question' => $dummy_values['question'],
                'Nb_Desired_Answers' => $dummy_values['nb_answers'],
                'Picture_Name' => $dummy_values['picture_name'],
                'Points' => $dummy_values['points']
            );
            self::$_dummy_ids['question'] = (int)$CI->question_model->insert($question);
        }

        return self::$_dummy_ids['question'];
    }
    /**
     * Resets the dummy question
     *
     * @return void
     */
    private static function _dummy_question_reset()
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('question_model');

        $question = $CI->question_model->with_deleted()->get(self::$_dummy_ids['question']);
        if(is_null($question)) {
            self::_dummy_question_get();
        } else {
            $dummy_values =& self::$_dummy_values['question'];

            $question = array(
                'FK_Topic' => self::$_dummy_ids['topic'],
                'FK_Question_Type' => $dummy_values['question_type'],
                'Question' => $dummy_values['question'],
                'Nb_Desired_Answers' => $dummy_values['nb_answers'],
                'Picture_Name' => $dummy_values['picture_name'],
                'Points' => $dummy_values['points'],
                'Archive' => 0
            );
            $CI->question_model->update(self::$_dummy_ids['question'], $question);
        }
    }
    /**
     * Deletes the dummy question, and all questionnaire links to it.
     * 
     * Do note it does **not** delete or check children questions before deletion.
     *
     * @return void
     */
    private static function _dummy_question_delete()
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model(['question_model', 'question_questionnaire_model', 'user_model']);

        $question = $CI->question_model->with_deleted()->get(self::$_dummy_ids['question']);
        if(!is_null($question)) {
            $CI->question_questionnaire_model->delete_by(['FK_Question' => $question->ID]);

            $CI->question_model->delete($question->ID, TRUE);
        }
    }

    /**
     * Obtains the ID of the dummy topic, or creates it if it doesn't exist.
     *
     * @return integer = ID of the dummy topic
     */
    private static function &_dummy_topic_get() : int
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('topic_model');

        $topic = $CI->topic_model->with_deleted()->get(self::$_dummy_ids['topic']);
        if(is_null($topic)) {
            // Topic no longer exists, create a new one
            $dummy_values =& self::$_dummy_values['topic'];

            $topic = array(
                'FK_Parent_Topic' => $dummy_values['parent'],
                'Topic' => $dummy_values['topic']
            );
            self::$_dummy_ids['topic'] = (int)$CI->topic_model->insert($topic);
        }

        return self::$_dummy_ids['topic'];
    }
    /**
     * Resets the dummy topic
     *
     * @return void
     */
    private static function _dummy_topic_reset()
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('topic_model');

        $topic = $CI->topic_model->with_deleted()->get(self::$_dummy_ids['topic']);
        if(is_null($topic)) {
            self::_dummy_topic_get();
        } else {
            $dummy_values =& self::$_dummy_values['topic'];

            $topic = array(
                'FK_Parent_Topic' => $dummy_values['parent'],
                'Topic' => $dummy_values['topic'],
                'Archive' => 0
            );

            $CI->topic_model->update(self::$_dummy_ids['topic'], $topic);
        }
    }
    /**
     * Deletes the dummy topic, and all links to it
     *
     * @return void
     */
    private static function _dummy_topic_delete()
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model(['topic_model', 'questionnaire_model_topic_model']);

        $topic = $CI->topic_model->with_deleted()->get(self::$_dummy_ids['topic']);
        if(!is_null($topic)) {
            // Delete links
            $CI->questionnaire_model_topic_model->delete_by(['FK_Topic' => $topic->ID]);

            $CI->topic_model->delete($topic->ID, TRUE);
        }
    }
}