<?php
require_once(__DIR__.'/../../third_party/Test_Trait.php');

/**
 * Class for tests for Question controller
 *
 * @author      Orif, section informatique (ViDi, MeSa, BuYa)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class Question_Test extends TestCase {
    use Test_Trait;

    /**
     * Stores dummy values
     *
     * @var array
     */
    private static $_dummy_values = [
        'question' => [
            'topic' => NULL,
            'type' => 1,
            'question' => 'dummy_question',
            'question_alt' => 'question_dummy',
            'nb_answers' => 1,
            'table' => FALSE,
            'picture_name' => 'question_picture.jpg',
            'points' => 789
        ],
        'cloze_text' => [
            'text' => 'dummy_[...]_text',
            'text_alt' => 'text_[...]_dummy'
        ],
        'cloze_text_answer' => [
            'answer' => 'dummy_cloze_answer',
            'answer_alt' => 'cloze_answer_dummy',
            'order' => 0
        ],
        'free_answer' => [
            'answer' => 'dummy_free_answer',
            'answer_alt' => 'free_answer_dummy'
        ],
        'multiple_answer' => [
            'answer' => 'dummy_multiple_answer',
            'answer_alt' => 'multiple_answer_dummy'
        ],
        'multiple_choice' => [
            'answer' => 'dummy_multiple_choice',
            'answer_alt' => 'multiple_choice_dummy',
            'valid' => FALSE
        ],
        'picture_landmark' => [
            'symbol' => 'd_1.',
            'answer' => 'dummy_picture_landmark',
            'answer_alt' => 'picture_landmark_dummy'
        ],
        'answer_distribution' => [
            'question_part' => 'dummy_answer_dis',
            'question_part_alt' => 'answer_dis_dummy',
            'answer_part' => 'dummy_ans_distribution',
            'answer_part_alt' => 'ans_distribution_dummy'
        ],
        'table_cell' => [
            'content' => 'dummy_table_cell',
            'content_alt' => 'table_cell_dummy',
            'column' => 1,
            'row' => 1,
            'header' => FALSE,
            'display' => TRUE
        ],
    ];

    /**
     * ID of the dummies
     *
     * @var integer
     */
    private static $_dummy_ids = [
        'question' => NULL,
        'cloze_text' => NULL,
        'cloze_text_answer' => NULL,
        'free_answer' => NULL,
        'multiple_answer' => NULL,
        'multiple_choice' => NULL,
        'picture_landmark' => NULL,
        'answer_distribution' => NULL,
        'table_cell' => NULL,
    ];
    /*******************
     * START/END METHODS
     *******************/
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model(['question_type_model', 'topic_model']);

        // Load a random type and a topic for the question
        self::$_dummy_values['question']['type'] = $CI->question_type_model->get_all()[0]->ID;
        self::$_dummy_values['question']['topic'] = $CI->topic_model->get_by('(Archive = 0 OR Archive IS NULL) AND FK_Parent_Topic IS NOT NULL')->ID;
        // Set the foreign keys to the values they should represent
        self::$_dummy_values['cloze_text']['question'] =& self::$_dummy_ids['question'];
        self::$_dummy_values['cloze_text_answer']['cloze_text'] =& self::$_dummy_ids['cloze_text'];
        self::$_dummy_values['free_answer']['question'] =& self::$_dummy_ids['question'];
        self::$_dummy_values['multiple_answer']['question'] =& self::$_dummy_ids['question'];
        self::$_dummy_values['multiple_choice']['question'] =& self::$_dummy_ids['question'];
        self::$_dummy_values['picture_landmark']['question'] =& self::$_dummy_ids['question'];
        self::$_dummy_values['answer_distribution']['question'] =& self::$_dummy_ids['question'];
        self::$_dummy_values['table_cell']['question'] =& self::$_dummy_ids['question'];
    }
    public function setUp()
    {
        $this->resetInstance();
        $this->CI->load->model('question_model');
        $this->_login_as($this->config->item('access_lvl_manager'));

        self::_dummy_question_get();
        self::_dummy_cloze_text_get();
        self::_dummy_cloze_text_answer_get();
        self::_dummy_free_answer_get();
        self::_dummy_multiple_answer_get();
        self::_dummy_multiple_choice_get();
        self::_dummy_picture_landmark_get();
        self::_dummy_answer_distribution_get();
        self::_dummy_table_cell_get();
    }
    public function tearDown()
    {
        parent::tearDown();

        // Reset all question types
        self::_dummy_question_reset();
        self::_dummy_cloze_text_reset();
        self::_dummy_cloze_text_answer_reset();
        self::_dummy_free_answer_reset();
        self::_dummy_multiple_answer_reset();
        self::_dummy_multiple_choice_reset();
        self::_dummy_picture_landmark_reset();
        self::_dummy_answer_distribution_reset();
        self::_dummy_table_cell_reset();
    }
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        reset_instance();
        CIPHPUnitTest::createCodeIgniterInstance();

        self::_dummy_table_cells_wipe();
        self::_dummy_answer_distributions_wipe();
        self::_dummy_picture_landmarks_wipe();
        self::_dummy_multiple_choices_wipe();
        self::_dummy_multiple_answers_wipe();
        self::_dummy_free_answers_wipe();
        self::_dummy_cloze_text_answers_wipe();
        self::_dummy_cloze_texts_wipe();
        self::_dummy_questions_wipe();
    }

    /*******
     * TESTS
     *******/
    /**
     * Test for `Question::index`
     *
     * @return void
     */
    public function test_index()
    {
        $this->_db_errors_save();

        $output = $this->request('GET', 'question');

        $this->assertContains($this->CI->lang->line('title_question'), $output);

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Question::resetFilters`
     *
     * @return void
     */
    public function test_reset_filters()
    {
        $_SESSION['filtres'] = 'test';

        $this->request('GET', 'question/resetFilters');

        $this->assertRedirect('question');
        $this->assertFalse(isset($_SESSION['filtres']));
    }
    /**
     * Test for `Question::delete`
     *
     * @dataProvider provider_delete
     *
     * @param integer $question_id = ID of the question to use
     * @param string $action = Action to pass to URL
     * @param array $status = Whether the question exists before the request or
     *      it was archived after the request
     * @param boolean $redirect_expected = Whether a redirect is expected
     * @return void
     */
    public function test_delete(int &$question_id, string $action, array $status, bool $redirect_expected)
    {
        if($status['deleted']) {
            $question = $this->CI->question_model->with_deleted()->get($question_id);
            $this->assertNull($question);
        }

        $this->_db_errors_save();

        $output = $this->request('GET', "question/delete/{$question_id}/{$action}");

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );

        $question = $this->CI->question_model->with_deleted()->get($question_id);

        if($redirect_expected) {
            $this->assertRedirect('question');
        } else {
            $this->assertContains($question->Question, $output);
        }

        if(!$status['deleted']) {
            $this->assertSame((int)$status['archived'], (int)$question->Archive);
        }
    }
    /**
     * Test for `Question::form_update`
     *
     * @dataProvider provider_form_update
     *
     * @param array $post_params = Array of parameters to pass to `$_POST`
     * @param boolean $error_expected = Whether an error is expected
     * @param boolean $redirect_expected = Whether a redirect is expected
     * @return void
     */
    public function test_form_update(array $post_params, bool $error_expected, bool $redirect_expected)
    {
        $this->_db_errors_save();

        $this->request('POST', 'question/form_update', $post_params);

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );

        if($error_expected) {
            $this->assertNotEmpty(validation_errors());
        } else {
            $this->assertEmpty(validation_errors());
        }

        if($redirect_expected) {
            $this->assertRedirect('question');
        }
    }
    /**
     * Test for `Question::cb_question_exists`
     *
     * @return void
     */
    public function test_question_exists()
    {
        reset_instance();
        $controller = new Question();
        $this->CI =& get_instance();

        $bad_id = $this->CI->question_model->get_next_id()+100;
        $question_id = self::$_dummy_ids['question'];

        $this->_db_errors_save();

        $this->assertFalse($this->CI->cb_question_exists($bad_id));
        $this->assertTrue($this->CI->cb_question_exists($question_id));

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `Question::update`
     *
     * @dataProvider provider_update
     *
     * @param integer $question_id = ID of the question
     * @param integer $question_type = Type of the question
     * @param boolean $redirect_expected = Whether a redirect is expected
     * @return void
     */
    public function test_update(int &$question_id, int $question_type, bool $redirect_expected)
    {
        if(!is_null($this->CI->question_model->get($question_id))) {
            $this->CI->question_model->update($question_id, ['FK_Question_Type' => $question_type]);
        }

        $this->_db_errors_save();

        $output = $this->request('GET', "question/update/{$question_id}");

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );

        if($redirect_expected) {
            $this->assertRedirect('question');
        } else switch($question_type) {
            case 1:
            case 2:
            case 4:
            case 6:
            case 7:
                $this->assertContains(lang('title_question_update'), $output);
                break;
            case 3:
            case 5:
                // Nothing to expect yet
                break;
        }
    }
    /**
     * Test for `Question::detail`
     *
     * @dataProvider provider_detail
     *
     * @param integer $question_id = ID of the question
     * @param boolean $redirect_expected = Whether a redirect is expected
     * @param string $text_expected = Text expected in the output
     * @return void
     */
    public function test_detail(int &$question_id, bool $redirect_expected, string $text_expected)
    {
        $this->_db_errors_save();

        $output = $this->request('GET', "question/detail/{$question_id}");

        if($redirect_expected) {
            $this->assertRedirect('question');
        }
        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
        if(!empty($text_expected)) {
            $this->assertContains($text_expected, $output);
        }
    }
    /**
     * Test for `Question::detail` with an archived question
     *
     * @return void
     */
    public function test_detail_archived()
    {
        $question_id =& self::$_dummy_ids['question'];
        $this->CI->question_model->update($question_id, ['Archive' => 1]);

        $this->_db_errors_save();

        $output = $this->request('GET', "question/detail/{$question_id}");

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
        $this->assertContains(lang('question_deleted'), $output);
    }
    /**
     * Test for `Question::detail` with a non-randomly typed question
     *
     * @dataProvider provider_detail_type
     *
     * @param integer $question_type = Type of the question
     * @return void
     */
    public function test_detail_type(int $question_type)
    {
        $question_id =& self::$_dummy_ids['question'];
        $this->CI->question_model->update($question_id, ['FK_Question_Type' => $question_type]);

        $this->_db_errors_save();

        $output = $this->request('GET', "question/detail/{$question_id}");

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
        $this->assertContains(lang('return'), $output);
    }
    /**
     * Test for `Question::add($step=1)`
     *
     * @return void
     */
    public function test_add_1()
    {
        $this->_db_errors_save();

        $output = $this->request('GET', 'question/add/1');

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
        $this->assertContains(lang('title_question_add'), $output);
    }
    /**
     * Test for `Question::add($step=2)`
     *
     * @dataProvider provider_add_2
     *
     * @param array $post_params = Array of parameters to pass to $_POST
     * @param string $text_expected = Text expected
     * @return void
     */
    public function test_add_2(array $post_params, string $text_expected)
    {
        $this->_db_errors_save();

        $output = $this->request('POST', 'question/add/2', $post_params);

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
        $this->assertContains($text_expected, $output);
    }
    /**
     * Test for `Question::add_{type}`
     *
     * @dataProvider provider_add_mc
     * @dataProvider provider_add_ma
     * @dataProvider provider_add_ct
     * @dataProvider provider_add_fa
     * @dataProvider provider_add_pl
     *
     * @param integer $type = ID of the type
     * @param array $post_params = Parameters to pass to $_POST
     * @param boolean $redirect_expected = Whether a redirect is expected
     * @param boolean $error_expected = Whether an error is expected
     * @param integer $step = Step for picture landmarks
     * @return void
     */
    public function test_add_type(int $type, array $post_params, bool $redirect_expected, bool $error_expected, int $step = 0)
    {
        $target = NULL;

        switch($type) {
            case 1:
                $target = 'add_MultipleChoice';
                break;
            case 2:
                $target = 'add_MultipleAnswer';
                break;
            case 3:
                $this->markTestIncomplete('Answer distributions have not been implemented yet');
                return;
            case 4:
                $target = 'add_ClozeText';
                break;
            case 5:
                $this->markTestIncomplete('Table cells have not been implemented yet');
                return;
            case 6:
                $target = 'add_FreeAnswer';
                break;
            case 7:
                $target = "add_PictureLandmark/{$step}";
                break;
            default:
                $this->markTestIncomplete("Type {$type} has not been implemented yet");
                return;
        }

        $this->_db_errors_save();

        $this->request('POST', "question/{$target}", $post_params);

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement.'
        );
        if($redirect_expected) {
            $this->assertRedirect('question');
        }
        if($error_expected) {
            $this->assertNotEmpty(validation_errors());
        }
    }
    /**
     * Test for `Question::add_{type}` with checks for the amount of answers
     *
     * @dataProvider provider_add_ma_da
     * @dataProvider provider_add_mc_da
     * @dataProvider provider_add_ct_da
     * @dataProvider provider_add_pl_da
     *
     * @param integer $type = ID of the type
     * @param array $post_params = Parameters to pass to $_POST
     * @param integer $diff = Amount of answers added/removed
     * @param integer $step = Step for picture landmarks
     * @return void
     */
    public function test_add_type_da(int $type, array $post_params, int $diff, int $step = 0)
    {
        $target = '';
        $multiplier = 0;

        switch($type) {
            case 1:
                $target = 'add_MultipleChoice';
                $multiplier = 4;
                break;
            case 2:
                $target = 'add_MultipleAnswer';
                $multiplier = 2;
                break;
            case 3:
                $this->markTestIncomplete('Answer distributions have not been implemented yet');
                return;
            case 4:
                $target = 'add_ClozeText';
                $multiplier = 2;
                break;
            case 5:
                $this->markTestIncomplete('Table cells have not been implemented yet');
                return;
            case 6:
                $this->markTestIncomplete('Free answers do not have a counter');
                return;
            case 7:
                $target = "add_PictureLandmark/{$step}";
                $multiplier = 3;
                break;
            default:
                $this->markTestIncomplete("Type {$type} has not been implemented yet");
                return;
        }

        $this->_db_errors_save();

        $output = $this->request('POST', "question/{$target}", $post_params);

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
        $this->assertEquals(max(($diff + 1) * $multiplier, $multiplier), substr_count($output, 'reponses['));
    }

    /***********
     * PROVIDERS
     ***********/
    /**
     * Provider for `test_delete`
     *
     * @return array
     */
    public function provider_delete() : array
    {
        $this->resetInstance();
        $this->CI->load->model('question_model');

        $data = [];

        $data['not_exist'] = [
            $this->CI->question_model->get_next_id()+100,
            '',
            ['deleted' => TRUE],
            TRUE
        ];

        $data['display_no_error'] = [
            &self::$_dummy_ids['question'],
            '',
            [
                'deleted' => FALSE,
                'archived' => FALSE
            ],
            FALSE
        ];

        $data['delete_no_error'] = [
            &self::$_dummy_ids['question'],
            '1',
            [
                'deleted' => FALSE,
                'archived' => TRUE
            ],
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
        $this->CI->load->model('question_model');

        $d_question = self::$_dummy_values['question']['question'];
        $d_points = self::$_dummy_values['question']['points'];
        $question_id =& self::$_dummy_ids['question'];

        $data = [];

        $data['not_exist'] = [
            [
                'id' => $this->CI->question_model->get_next_id()+100,
                'name' => $d_question,
                'points' => $d_points
            ],
            TRUE,
            TRUE
        ];

        $data['no_error'] = [
            [
                'id' => &$question_id,
                'name' => $d_question,
                'points' => $d_points
            ],
            FALSE,
            TRUE
        ];

        $data['no_name'] = [
            [
                'id' => &$question_id,
                'points' => $d_points
            ],
            TRUE,
            FALSE
        ];

        $data['no_points'] = [
            [
                'id' => &$question_id,
                'name' => $d_question
            ],
            TRUE,
            FALSE
        ];

        return $data;
    }
    /**
     * Provider for `Question::update`
     *
     * @return array
     */
    public function provider_update() : array
    {
        $this->resetInstance();
        $this->CI->load->model('question_model');

        $data = [];

        $data['not_exist'] = [
            $this->CI->question_model->get_next_id()+100,
            1,
            TRUE
        ];

        for($i = 1; $i <= 7; $i++) {
            $data["type_{$i}"] = [
                &self::$_dummy_ids['question'],
                $i,
                FALSE
            ];
        }

        return $data;
    }
    /**
     * Provider for `test_detail`
     *
     * @return array
     */
    public function provider_detail() : array
    {
        $this->resetInstance();
        $this->CI->load->model('question_model');

        $data = [];

        $data['not_exist'] = [
            $this->CI->question_model->get_next_id()+100,
            TRUE,
            ''
        ];

        $data['no_error'] = [
            &self::$_dummy_ids['question'],
            FALSE,
            lang('return')
        ];

        return $data;
    }
    /**
     * Provider for `test_detail_type`
     *
     * @return array
     */
    public function provider_detail_type() : array
    {
        $data = [];

        for($i = 1; $i <= 7; $i++) {
            $data["detail_{$i}"] = [$i];
        }

        return $data;
    }
    /**
     * Provider for `test_add_2`
     *
     * @return array
     */
    public function provider_add_2() : array
    {
        $data = [];
        $focus_topic =& self::$_dummy_values['question']['topic'];

        $data['no_error_multiple_choice'] = [
            [
                'focus_topic' => &$focus_topic,
                'question_type' => 1
            ],
            lang('title_question_update')
        ];

        $data['no_error_multiple_answer'] = [
            [
                'focus_topic' => &$focus_topic,
                'question_type' => 2
            ],
            lang('title_question_update')
        ];

        $data['no_error_answer_distribution'] = [
            [
                'focus_topic' => &$focus_topic,
                'question_type' => 3
            ],
            lang('no_implemented_question_type')
        ];

        $data['no_error_cloze_text'] = [
            [
                'focus_topic' => &$focus_topic,
                'question_type' => 4
            ],
            lang('title_question_update')
        ];

        $data['no_error_table_cell'] = [
            [
                'focus_topic' => &$focus_topic,
                'question_type' => 5
            ],
            lang('no_implemented_question_type')
        ];

        $data['no_error_free_answer'] = [
            [
                'focus_topic' => &$focus_topic,
                'question_type' => 6
            ],
            lang('title_question_update')
        ];

        $data['no_error_picture_landmark'] = [
            [
                'focus_topic' => &$focus_topic,
                'question_type' => 7
            ],
            lang('title_question_add')
        ];

        return $data;
    }
    /**
     * Provider for `test_add_type` with multiple choices
     *
     * @return array
     */
    public function provider_add_mc() : array
    {
        $this->resetInstance();
        $this->CI->load->model('question_model');

        $question_id =& self::$_dummy_ids['question'];
        $q_question = self::$_dummy_values['question']['question'];
        $q_points = self::$_dummy_values['question']['points'];
        $q_topic =& self::$_dummy_values['question']['topic'];
        $q_type = self::$_dummy_values['question']['type'];
        $mc_question = self::$_dummy_values['multiple_choice']['answer'];
        $mc_answer = (int)self::$_dummy_values['multiple_choice']['valid'];
        $mc_id =& self::$_dummy_ids['multiple_choice'];

        $data = [];

        $data['mc_cancel'] = [
            1,
            ['cancel' => TRUE],
            TRUE,
            FALSE
        ];

        $data['mc_save_no_error_new'] = [
            1,
            [
                'save' => TRUE,
                'name' => $q_question,
                'points' => $q_points,
                'reponses' => [[
                    'question' => $mc_question,
                    'answer' => $mc_answer
                ]],
                'nbAnswer' => 1,
                'focus_topic' => &$q_topic,
                'question_type' => $q_type
            ],
            TRUE,
            FALSE
        ];

        $data['mc_save_no_error_old'] = [
            1,
            [
                'save' => TRUE,
                'name' => $q_question,
                'points' => $q_points,
                'reponses' => [[
                    'id' => &$mc_id,
                    'question' => $mc_question,
                    'answer' => $mc_answer
                ]],
                'nbAnswer' => 1,
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'id' => &$question_id
            ],
            TRUE,
            FALSE
        ];

        $data['mc_save_error_new'] = [
            1,
            [
                'save' => TRUE,
                'name' => $q_question,
                'points' => -1,
                'reponses' => [[
                    'question' => $mc_question,
                    'answer' => $mc_answer
                ]],
                'nbAnswer' => 1,
                'focus_topic' => &$q_topic,
                'question_type' => $q_type
            ],
            FALSE,
            TRUE
        ];

        $data['mc_save_error_old'] = [
            1,
            [
                'save' => TRUE,
                'name' => $q_question,
                'points' => -1,
                'reponses' => [[
                    'id' => &$mc_id,
                    'question' => $mc_question,
                    'answer' => $mc_answer
                ]],
                'nbAnswer' => 1,
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'id' => &$question_id
            ],
            FALSE,
            TRUE
        ];

        $data['mc_save_error_not_exist'] = [
            1,
            [
                'save' => TRUE,
                'name' => $q_question,
                'points' => -1,
                'reponses' => [[
                    'question' => $mc_question,
                    'answer' => $mc_answer
                ]],
                'nbAnswer' => 1,
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'id' => $this->CI->question_model->get_next_id()+100
            ],
            FALSE,
            TRUE
        ];

        $data['mc_blank_new'] = [
            1,
            [
                'name' => $q_question,
                'points' => -1,
                'reponses' => [[
                    'question' => $mc_question,
                    'answer' => $mc_answer
                ]],
                'nbAnswer' => 1,
                'focus_topic' => &$q_topic,
                'question_type' => $q_type
            ],
            FALSE,
            FALSE
        ];

        $data['mc_blank_old'] = [
            1,
            [
                'name' => $q_question,
                'points' => -1,
                'reponses' => [[
                    'id' => &$mc_id,
                    'question' => $mc_question,
                    'answer' => $mc_answer
                ]],
                'nbAnswer' => 1,
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'id' => &$question_id
            ],
            FALSE,
            FALSE
        ];

        return $data;
    }
    /**
     * Provider for `test_add_type` with multiple answers
     *
     * @return array
     */
    public function provider_add_ma() : array
    {
        $this->resetInstance();
        $this->CI->load->model('question_model');

        $question_id =& self::$_dummy_ids['question'];
        $q_question = self::$_dummy_values['question']['question'];
        $q_points = self::$_dummy_values['question']['points'];
        $q_topic =& self::$_dummy_values['question']['topic'];
        $q_type = self::$_dummy_values['question']['type'];
        $ma_answer = self::$_dummy_values['multiple_answer']['answer'];
        $ma_id =& self::$_dummy_ids['multiple_answer'];

        $data = [];

        $data['ma_save_no_error_new'] = [
            2,
            [
                'save' => TRUE,
                'name' => $q_question,
                'points' => $q_points,
                'nb_desired_answers' => 1,
                'nbAnswer' => 1,
                'reponses' => [['answer' => $ma_answer]],
                'focus_topic' => &$q_topic,
                'question_type' => $q_type
            ],
            TRUE,
            FALSE
        ];

        $data['ma_save_no_error_old'] = [
            2,
            [
                'save' => TRUE,
                'name' => $q_question,
                'points' => $q_points,
                'nb_desired_answers' => 1,
                'nbAnswer' => 1,
                'reponses' => [[
                    'answer' => $ma_answer,
                    'id' => &$ma_id
                ]],
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'id' => &$question_id
            ],
            TRUE,
            FALSE
        ];

        $data['ma_save_error_new'] = [
            2,
            [
                'save' => TRUE,
                'name' => $q_question,
                'points' => -1,
                'nb_desired_answers' => 1,
                'nbAnswer' => 1,
                'reponses' => [['answer' => $ma_answer]],
                'focus_topic' => &$q_topic,
                'question_type' => $q_type
            ],
            FALSE,
            TRUE
        ];

        $data['ma_save_error_old'] = [
            2,
            [
                'save' => TRUE,
                'name' => $q_question,
                'points' => -1,
                'nb_desired_answers' => 1,
                'nbAnswer' => 1,
                'reponses' => [[
                    'answer' => $ma_answer,
                    'id' => &$ma_id
                ]],
                'focus_topic' => &$q_topic,
                'question_type' => $q_type
            ],
            FALSE,
            TRUE
        ];

        $data['ma_save_error_not_exist'] = [
            2,
            [
                'save' => TRUE,
                'name' => $q_question,
                'points' => -1,
                'nb_desired_answers' => 1,
                'nbAnswer' => 1,
                'reponses' => [[
                    'answer' => $ma_answer,
                    'id' => &$ma_id
                ]],
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'id' => $this->CI->question_model->get_next_id()+100
            ],
            FALSE,
            TRUE
        ];

        return $data;
    }
    /**
     * Provider for `test_add_type` with cloze texts
     *
     * @return array
     */
    public function provider_add_ct() : array
    {
        $this->resetInstance();
        $this->CI->load->model('question_model');

        $question_id =& self::$_dummy_ids['question'];
        $q_question = self::$_dummy_values['question']['question'];
        $q_points = self::$_dummy_values['question']['points'];
        $q_topic =& self::$_dummy_values['question']['topic'];
        $q_type = self::$_dummy_values['question']['type'];
        $ct_id =& self::$_dummy_ids['cloze_text'];
        $ct_text = self::$_dummy_values['cloze_text']['text'];
        $cta_id =& self::$_dummy_ids['cloze_text_answer'];
        $cta_answer = self::$_dummy_values['cloze_text_answer']['answer'];

        $data = [];

        $data['ct_cancel'] = [
            4,
            ['cancel' => TRUE],
            TRUE,
            FALSE
        ];

        $data['ct_save_no_error_new'] = [
            4,
            [
                'save' => TRUE,
                'name' => $q_question,
                'points' => $q_points,
                'cloze_text' => $ct_text,
                'reponses' => [['answer' => $cta_answer]],
                'nbAnswer' => 1,
                'focus_topic' => &$q_topic,
                'question_type' => $q_type
            ],
            TRUE,
            FALSE
        ];

        $data['ct_save_no_error_old'] = [
            4,
            [
                'save' => TRUE,
                'name' => $q_question,
                'points' => $q_points,
                'cloze_text' => $ct_text,
                'reponses' => [[
                    'id' => &$cta_id,
                    'answer' => $cta_answer
                ]],
                'nbAnswer' => 1,
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'id' => &$question_id,
                'id_cloze_text' => &$ct_id
            ],
            TRUE,
            FALSE
        ];

        $data['ct_save_error_new'] = [
            4,
            [
                'save' => TRUE,
                'name' => $q_question,
                'points' => -1,
                'cloze_text' => $ct_text,
                'reponses' => [['answer' => $cta_answer]],
                'nbAnswer' => 1,
                'focus_topic' => &$q_topic,
                'question_type' => $q_type
            ],
            FALSE,
            TRUE
        ];

        $data['ct_save_error_old'] = [
            4,
            [
                'save' => TRUE,
                'name' => $q_question,
                'points' => -1,
                'cloze_text' => $ct_text,
                'reponses' => [[
                    'id' => &$cta_id,
                    'answer' => $cta_answer
                ]],
                'nbAnswer' => 1,
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'id' => &$question_id,
                'id_cloze_text' => &$ct_id
            ],
            FALSE,
            TRUE
        ];

        $data['ct_save_error_not_exist'] = [
            4,
            [
                'save' => TRUE,
                'name' => $q_question,
                'points' => $q_points,
                'cloze_text' => $ct_text,
                'reponses' => [[
                    'id' => &$cta_id,
                    'answer' => $cta_answer
                ]],
                'nbAnswer' => 1,
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'id' => $this->CI->question_model->get_next_id()+100,
                'id_cloze_text' => &$ct_id
            ],
            FALSE,
            TRUE
        ];

        return $data;
    }
    /**
     * Provider for `test_add_type` with free answers
     *
     * @return array
     */
    public function provider_add_fa() : array
    {
        $this->resetInstance();
        $this->CI->load->model('question_model');

        $question_id =& self::$_dummy_ids['question'];
        $q_question = self::$_dummy_values['question']['question'];
        $q_points = self::$_dummy_values['question']['points'];
        $q_topic =& self::$_dummy_values['question']['topic'];
        $q_type = self::$_dummy_values['question']['type'];
        $fa_id =& self::$_dummy_ids['free_answer'];
        $fa_answer = self::$_dummy_values['free_answer']['answer'];

        $data = [];

        $data['fa_cancel'] = [
            6,
            ['cancel' => TRUE],
            TRUE,
            FALSE
        ];

        $data['fa_save_no_error_new'] = [
            6,
            [
                'save' => TRUE,
                'name' => $q_question,
                'points' => $q_points,
                'answer' => $fa_answer,
                'focus_topic' => &$q_topic,
                'question_type' => &$q_type
            ],
            TRUE,
            FALSE
        ];

        $data['fa_save_no_error_old'] = [
            6,
            [
                'save' => TRUE,
                'name' => $q_question,
                'points' => $q_points,
                'answer' => $fa_answer,
                'focus_topic' => &$q_topic,
                'question_type' => &$q_type,
                'id' => &$question_id,
                'id_answer' => &$fa_id
            ],
            TRUE,
            FALSE
        ];

        $data['fa_save_error_new'] = [
            6,
            [
                'save' => TRUE,
                'name' => $q_question,
                'points' => -1,
                'answer' => $fa_answer,
                'focus_topic' => &$q_topic,
                'question_type' => &$q_type
            ],
            FALSE,
            TRUE
        ];

        $data['fa_save_error_old'] = [
            6,
            [
                'save' => TRUE,
                'name' => $q_question,
                'points' => -1,
                'answer' => $fa_answer,
                'focus_topic' => &$q_topic,
                'question_type' => &$q_type,
                'id' => &$question_id,
                'id_answer' => &$fa_id
            ],
            FALSE,
            TRUE
        ];

        $data['fa_save_error_not_exist'] = [
            6,
            [
                'save' => TRUE,
                'name' => $q_question,
                'points' => -1,
                'answer' => $fa_answer,
                'focus_topic' => &$q_topic,
                'question_type' => &$q_type,
                'id' => $this->CI->question_model->get_next_id()+100,
                'id_answer' => &$fa_id
            ],
            FALSE,
            TRUE
        ];

        return $data;
    }
    /**
     * Provider for `test_add_type` with picture landmarks
     *
     * @return array
     */
    public function provider_add_pl() : array
    {
        $this->resetInstance();
        $this->CI->load->model('question_model');

        $question_id =& self::$_dummy_ids['question'];
        $q_question = self::$_dummy_values['question']['question'];
        $q_points = self::$_dummy_values['question']['points'];
        $q_topic =& self::$_dummy_values['question']['topic'];
        $q_type = self::$_dummy_values['question']['type'];
        $q_picture = self::$_dummy_values['question']['picture_name'];
        $pl_id =& self::$_dummy_ids['picture_landmark'];
        $pl_symbol = self::$_dummy_values['picture_landmark']['symbol'];
        $pl_answer = self::$_dummy_values['picture_landmark']['answer'];

        $data = [];

        $data['pl_1_cancel_no_id'] = [
            7,
            [
                'cancel' => TRUE,
                'nbAnswer' => 1,
                'reponses' => [[
                    'symbol' => $pl_symbol,
                    'answer' => $pl_answer
                ]],
            ],
            TRUE,
            FALSE,
            1
        ];

        $data['pl_1_cancel_id'] = [
            7,
            [
                'cancel' => TRUE,
                'id' => &$question_id,
                'nbAnswer' => 1,
                'reponses' => [[
                    'symbol' => $pl_symbol,
                    'answer' => $pl_answer
                ]],
                'question_type' => $q_type,
                'picture_name' => $q_picture
            ],
            FALSE,
            FALSE,
            1
        ];

        $data['pl_1_nothing'] = [
            7,
            [
                'nbAnswer' => 1,
                'reponses' => [[
                    'symbol' => $pl_symbol,
                    'answer' => $pl_answer
                ]],
            ],
            TRUE,
            FALSE,
            1
        ];

        $data['pl_2_cancel'] = [
            7,
            ['cancel' => TRUE],
            TRUE,
            FALSE,
            2
        ];

        $data['pl_2_save_no_error_new'] = [
            7,
            [
                'save' => TRUE,
                'name' => $q_question,
                'points' => $q_points,
                'nbAnswer' => 1,
                'reponses' => [[
                    'symbol' => $pl_symbol,
                    'answer' => $pl_answer
                ]],
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'upload_data' => ['file_name' => $q_picture]
            ],
            TRUE,
            FALSE,
            2
        ];

        $data['pl_2_save_no_error_old'] = [
            7,
            [
                'save' => TRUE,
                'name' => $q_question,
                'points' => $q_points,
                'nbAnswer' => 1,
                'reponses' => [[
                    'symbol' => $pl_symbol,
                    'answer' => $pl_answer,
                    'id' => &$pl_id
                ]],
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'upload_data' => ['file_name' => $q_picture],
                'id' => &$question_id
            ],
            TRUE,
            FALSE,
            2
        ];

        $data['pl_2_save_error_new'] = [
            7,
            [
                'save' => TRUE,
                'name' => $q_question,
                'points' => -1,
                'nbAnswer' => 1,
                'reponses' => [[
                    'symbol' => $pl_symbol,
                    'answer' => $pl_answer
                ]],
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'upload_data' => ['file_name' => $q_picture]
            ],
            FALSE,
            TRUE,
            2
        ];

        $data['pl_2_save_error_old'] = [
            7,
            [
                'save' => TRUE,
                'name' => $q_question,
                'points' => -1,
                'nbAnswer' => 1,
                'reponses' => [[
                    'symbol' => $pl_symbol,
                    'answer' => $pl_answer
                ]],
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'upload_data' => ['file_name' => $q_picture],
                'id' => &$question_id
            ],
            FALSE,
            TRUE,
            2
        ];

        $data['pl_2_save_error_not_exist'] = [
            7,
            [
                'save' => TRUE,
                'name' => $q_question,
                'points' => -1,
                'nbAnswer' => 1,
                'reponses' => [[
                    'symbol' => $pl_symbol,
                    'answer' => $pl_answer
                ]],
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'upload_data' => ['file_name' => $q_picture],
                'id' => $this->CI->question_model->get_next_id()+100
            ],
            FALSE,
            TRUE,
            2
        ];

        return $data;
    }
    /**
     * Provider for `test_add_type_da` with multiple choices
     *
     * @return array
     */
    public function provider_add_mc_da() : array
    {
        $question_id =& self::$_dummy_ids['question'];
        $q_question = self::$_dummy_values['question']['question'];
        $q_points = self::$_dummy_values['question']['points'];
        $q_topic = self::$_dummy_values['question']['topic'];
        $q_type = self::$_dummy_values['question']['type'];
        $mc_question = self::$_dummy_values['multiple_choice']['answer'];
        $mc_answer = self::$_dummy_values['multiple_choice']['valid'];

        $data = [];

        $data['mc_add_new'] = [
            1,
            [
                'name' => $q_question,
                'points' => $q_points,
                'reponses' => [[
                    'question' => $mc_question,
                    'answer' => $mc_answer
                ]],
                'nbAnswer' => 1,
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'add_answer' => TRUE
            ],
            1
        ];

        $data['mc_add_old'] = [
            1,
            [
                'name' => $q_question,
                'points' => $q_points,
                'reponses' => [[
                    'question' => $mc_question,
                    'answer' => $mc_answer
                ]],
                'nbAnswer' => 1,
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'id' => &$question_id,
                'add_answer' => TRUE
            ],
            1
        ];

        $data['mc_delete_new'] = [
            1,
            [
                'name' => $q_question,
                'points' => $q_points,
                'reponses' => [[
                    'question' => $mc_question,
                    'answer' => $mc_answer
                ]],
                'nbAnswer' => 1,
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'del_answer0' => TRUE
            ],
            -1
        ];

        $data['mc_delete_old'] = [
            1,
            [
                'name' => $q_question,
                'points' => $q_points,
                'reponses' => [[
                    'question' => $mc_question,
                    'answer' => $mc_answer
                ]],
                'nbAnswer' => 1,
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'id' => &$question_id,
                'del_answer0' => TRUE
            ],
            -1
        ];

        $data['mc_delete_not_exist'] = [
            1,
            [
                'name' => $q_question,
                'points' => $q_points,
                'reponses' => [[
                    'question' => $mc_question,
                    'answer' => $mc_answer
                ]],
                'nbAnswer' => 1,
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'id' => &$question_id,
                'del_answer5' => TRUE
            ],
            0
        ];

        return $data;
    }
    /**
     * Provider for `test_add_type_da` with multiple answers
     *
     * @return array
     */
    public function provider_add_ma_da() : array
    {
        $question_id =& self::$_dummy_ids['question'];
        $q_question = self::$_dummy_values['question']['question'];
        $q_points = self::$_dummy_values['question']['points'];
        $q_topic =& self::$_dummy_values['question']['topic'];
        $q_type = self::$_dummy_values['question']['type'];
        $ma_answer = self::$_dummy_values['multiple_answer']['answer'];
        $ma_id =& self::$_dummy_ids['multiple_answer'];

        $data = [];

        $data['ma_add_new'] = [
            2,
            [
                'add_answer' => TRUE,
                'name' => $q_question,
                'points' => $q_points,
                'nb_desired_answers' => 1,
                'nbAnswer' => 1,
                'reponses' => [['answer' => $ma_answer]],
                'focus_topic' => &$q_topic,
                'question_type' => $q_type
            ],
            1
        ];

        $data['ma_add_old'] = [
            2,
            [
                'add_answer' => TRUE,
                'name' => $q_question,
                'points' => $q_points,
                'nb_desired_answers' => 1,
                'nbAnswer' => 1,
                'reponses' => [[
                    'answer' => $ma_answer,
                    'id' => &$ma_id
                ]],
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'id' => &$question_id
            ],
            1
        ];

        $data['ma_delete_new'] = [
            2,
            [
                'del_answer0' => TRUE,
                'name' => $q_question,
                'points' => $q_points,
                'nb_desired_answers' => 1,
                'nbAnswer' => 1,
                'reponses' => [['answer' => $ma_answer]],
                'focus_topic' => &$q_topic,
                'question_type' => $q_type
            ],
            -1
        ];

        $data['ma_delete_old'] = [
            2,
            [
                'del_answer0' => TRUE,
                'name' => $q_question,
                'points' => $q_points,
                'nb_desired_answers' => 1,
                'nbAnswer' => 1,
                'reponses' => [[
                    'answer' => $ma_answer,
                    'id' => &$ma_id
                ]],
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'id' => &$question_id
            ],
            -1
        ];

        $data['ma_delete_not_exist'] = [
            2,
            [
                'del_answer5' => TRUE,
                'name' => $q_question,
                'points' => $q_points,
                'nb_desired_answers' => 1,
                'nbAnswer' => 1,
                'reponses' => [['answer' => $ma_answer]],
                'focus_topic' => &$q_topic,
                'question_type' => $q_type
            ],
            0
        ];

        return $data;
    }
    /**
     * Provider for `test_add_type_da` with cloze texts
     *
     * @return array
     */
    public function provider_add_ct_da() : array
    {
        $this->resetInstance();
        $this->CI->load->model('question_model');

        $question_id =& self::$_dummy_ids['question'];
        $q_question = self::$_dummy_values['question']['question'];
        $q_points = self::$_dummy_values['question']['points'];
        $q_topic =& self::$_dummy_values['question']['topic'];
        $q_type = self::$_dummy_values['question']['type'];
        $ct_id =& self::$_dummy_ids['cloze_text'];
        $ct_text = self::$_dummy_values['cloze_text']['text'];
        $cta_id =& self::$_dummy_ids['cloze_text_answer'];
        $cta_answer = self::$_dummy_values['cloze_text_answer']['answer'];

        $data = [];

        $data['ct_add_new'] = [
            4,
            [
                'add_answer' => TRUE,
                'name' => $q_question,
                'points' => $q_points,
                'cloze_text' => $ct_text,
                'reponses' => [['answer' => $cta_answer]],
                'nbAnswer' => 1,
                'focus_topic' => &$q_topic,
                'question_type' => $q_type
            ],
            1
        ];

        $data['ct_add_old'] = [
            4,
            [
                'add_answer' => TRUE,
                'name' => $q_question,
                'points' => $q_points,
                'cloze_text' => $ct_text,
                'reponses' => [[
                    'id' => &$cta_id,
                    'answer' => $cta_answer
                ]],
                'nbAnswer' => 1,
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'id' => &$question_id,
                'id_cloze_text' => &$ct_id
            ],
            1
        ];

        $data['ct_delete_new'] = [
            4,
            [
                'del_answer0' => TRUE,
                'name' => $q_question,
                'points' => $q_points,
                'cloze_text' => $ct_text,
                'reponses' => [['answer' => $cta_answer]],
                'nbAnswer' => 1,
                'focus_topic' => &$q_topic,
                'question_type' => $q_type
            ],
            -1
        ];

        $data['ct_delete_old'] = [
            4,
            [
                'del_answer0' => TRUE,
                'name' => $q_question,
                'points' => $q_points,
                'cloze_text' => $ct_text,
                'reponses' => [[
                    'id' => &$cta_id,
                    'answer' => $cta_answer
                ]],
                'nbAnswer' => 1,
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'id' => &$question_id,
                'id_cloze_text' => &$ct_id
            ],
            -1
        ];

        $data['ct_delete_not_exist'] = [
            4,
            [
                'del_answer5' => TRUE,
                'name' => $q_question,
                'points' => $q_points,
                'cloze_text' => $ct_text,
                'reponses' => [[
                    'id' => &$cta_id,
                    'answer' => $cta_answer
                ]],
                'nbAnswer' => 1,
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'id' => $this->CI->question_model->get_next_id()+100,
                'id_cloze_text' => &$ct_id
            ],
            0
        ];

        return $data;
    }
    /**
     * Provider for `test_add_type_da` with picture landmarks
     *
     * @return array
     */
    public function provider_add_pl_da() : array
    {
        $this->resetInstance();
        $this->CI->load->model('question_model');

        $q_points = self::$_dummy_values['question']['points'];
        $question_id =& self::$_dummy_ids['question'];
        $q_question = self::$_dummy_values['question']['question'];
        $q_topic =& self::$_dummy_values['question']['topic'];
        $q_type = self::$_dummy_values['question']['type'];
        $q_picture = self::$_dummy_values['question']['picture_name'];
        $pl_symbol = self::$_dummy_values['picture_landmark']['symbol'];
        $pl_answer = self::$_dummy_values['picture_landmark']['answer'];

        $data = [];

        $data['pl_2_add_new'] = [
            7,
            [
                'points' => $q_points,
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'name' => $q_question,
                'picture_name' => $q_picture,
                'nbAnswer' => 1,
                'reponses' => [[
                    'symbol' => $pl_symbol,
                    'answer' => $pl_answer
                ]],
                'add_answer' => TRUE
            ],
            1,
            2
        ];

        $data['pl_2_add_old'] = [
            7,
            [
                'points' => $q_points,
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'name' => $q_question,
                'picture_name' => $q_picture,
                'nbAnswer' => 1,
                'reponses' => [[
                    'symbol' => $pl_symbol,
                    'answer' => $pl_answer
                ]],
                'add_answer' => TRUE,
                'id' => &$question_id
            ],
            1,
            2
        ];

        $data['pl_2_delete_new'] = [
            7,
            [
                'points' => $q_points,
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'name' => $q_question,
                'picture_name' => $q_picture,
                'nbAnswer' => 1,
                'reponses' => [[
                    'symbol' => $pl_symbol,
                    'answer' => $pl_answer
                ]],
                'del_answer0' => TRUE
            ],
            -1,
            2
        ];

        $data['pl_2_delete_old'] = [
            7,
            [
                'points' => $q_points,
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'name' => $q_question,
                'picture_name' => $q_picture,
                'nbAnswer' => 1,
                'reponses' => [[
                    'symbol' => $pl_symbol,
                    'answer' => $pl_answer
                ]],
                'del_answer0' => TRUE,
                'id' => &$question_id
            ],
            -1,
            2
        ];

        $data['pl_2_delete_not_exist'] = [
            7,
            [
                'points' => $q_points,
                'focus_topic' => &$q_topic,
                'question_type' => $q_type,
                'name' => $q_question,
                'picture_name' => $q_picture,
                'nbAnswer' => 1,
                'reponses' => [[
                    'symbol' => $pl_symbol,
                    'answer' => $pl_answer
                ]],
                'del_answer5' => TRUE,
                'id' => $this->CI->question_model->get_next_id()+100
            ],
            0,
            2
        ];

        return $data;
    }

    /**************
     * MISC METHODS
     **************/
    /**
     * Creates/Obtains the id of the current dummy question
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
        $CI->load->model(['question_model', 'topic_model', 'question_type_model']);

        $question = $CI->question_model->with_deleted()->get(self::$_dummy_ids['question']);
        // The question does not exist, create it
        if(is_null($question)) {
            $dummy_values =& self::$_dummy_values['question'];
            if(is_null($CI->topic_model->get($dummy_values['topic']))) {
                $dummy_values['topic'] = $CI->topic_model->get_by('(Archive = 0 OR Archive IS NULL) AND FK_Parent_Topic IS NOT NULL');
            }
            if(is_null($CI->question_type_model->get($dummy_values['type']))) {
                self::$_dummy_values['question']['type'] = $CI->question_type_model->get_all()[0]->ID;
            }

            $question = array(
                'FK_Topic' => $dummy_values['topic'],
                'FK_Question_Type' => $dummy_values['type'],
                'Question' => $dummy_values['question'],
                'Nb_Desired_Answers' => $dummy_values['nb_answers'],
                'Picture_Name' => $dummy_values['picture_name'],
                'points' => $dummy_values['points']
            );

            self::$_dummy_ids['question'] = $CI->question_model->insert($question);
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
            // Question no longer exists, create a new one
            self::_dummy_question_get();
        } else {
            $dummy_values =& self::$_dummy_values['question'];

            $question = array(
                'FK_Topic' => $dummy_values['topic'],
                'Question' => $dummy_values['question'],
                'Nb_Desired_Answers' => $dummy_values['nb_answers'],
                'Picture_Name' => $dummy_values['picture_name'],
                'points' => $dummy_values['points'],
                'Archive' => 0
            );
            $CI->question_model->update(self::$_dummy_ids['question'], $question);
        }
    }
    /**
     * Destroys all the questions created during tests
     *
     * @return void
     */
    private static function _dummy_questions_wipe()
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model([
            'question_model', 'free_answer_model', 'multiple_answer_model',
            'picture_landmark_model', 'multiple_choice_model', 'answer_distribution_model',
            'table_cell_model', 'cloze_text_answer_model', 'cloze_text_model',
            'question_questionnaire_model', 'questionnaire_model', 'user_model'
        ]);

        $dummy_values = [
            self::$_dummy_values['question']['question'],
            self::$_dummy_values['question']['question_alt'],
            ''
        ];
        // List of models that can just invoke delete to get rid of them
        $no_depth_models = [
            'free_answer_model', 'multiple_answer_model', 'multiple_choice_model',
            'answer_distribution_model', 'table_cell_model', 'picture_landmark_model'
        ];

        foreach($dummy_values as $value) {
            // Fetch questions created
            $questions = $CI->question_model->with_deleted()->get_many_by(['Question' => $value]);
            foreach($questions as $question) {
                // Remove related answers
                foreach($no_depth_models as $model) {
                    $CI->{$model}->delete_by(['FK_Question' => $question->ID]);
                }

                // Remove related cloze texts and its answers
                $cloze_texts = $CI->cloze_text_model->get_many_by(['FK_Question' => $question->ID]);
                foreach($cloze_texts as $cloze_text) {
                    $CI->cloze_text_answer_model->delete_by(['FK_Cloze_Text' => $cloze_text->ID]);
                    $CI->cloze_text_model->delete($cloze_text->ID);
                }

                // Remove related questionnaires
                $linked_questionnaires = $CI->question_questionnaire_model->get_many_by(['FK_Question' => $question->ID]);
                foreach($linked_questionnaires as $link) {
                    $questionnaire = $CI->questionnaire_model->get($link->FK_Questionnaire);
                    // Remove questionnaire's pdfs
                    if(file_exists('pdf_files/questionnaires/'.$questionnaire->PDF)) {
                        unlink('pdf_files/questionnaires/'.$questionnaire->PDF);
                    }
                    if(file_exists('pdf_files/corriges/'.$questionnaire->Corrige_PDF)) {
                        unlink('pdf_files/corriges/'.$questionnaire->Corrige_PDF);
                    }
                    $CI->questionnaire_model->delete($questionnaire->ID);
                    $CI->question_questionnaire_model->delete($link->ID);
                }

                if(file_exists("uploads/pictures/{$question->Picture_Name}") && !empty($question->Picture_Name)) {
                    unlink("uploads/pictures/{$question->Picture_Name}");
                }

                $CI->question_model->delete($question->ID);
            }
        }
    }

    /**
     * Creates/Obtains the id of the current dummy cloze text
     *
     * @return integer = ID of the dummy cloze text
     */
    private static function &_dummy_cloze_text_get() : int
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('cloze_text_model');

        $cloze_text = $CI->cloze_text_model->get(self::$_dummy_ids['cloze_text']);
        // Cloze text does not exist, create it
        if(is_null($cloze_text)) {
            $dummy_values =& self::$_dummy_values['cloze_text'];
            $question_id = self::_dummy_question_get();

            $cloze_text = array(
                'FK_Question' => $question_id,
                'Cloze_Text' => $dummy_values['text']
            );

            self::$_dummy_ids['cloze_text'] = $CI->cloze_text_model->insert($cloze_text);
        }

        return self::$_dummy_ids['cloze_text'];
    }
    /**
     * Resets the dummy cloze text
     *
     * @return void
     */
    private static function _dummy_cloze_text_reset()
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('cloze_text_model');

        $cloze_text = $CI->cloze_text_model->get(self::$_dummy_ids['cloze_text']);
        if(is_null($cloze_text)) {
            self::_dummy_cloze_text_get();
        } else {
            $dummy_values =& self::$_dummy_values['cloze_text'];
            $question_id = self::_dummy_question_get();

            $cloze_text = array(
                'FK_Question' => $question_id,
                'Cloze_Text' => $dummy_values['text']
            );

            $CI->cloze_text_model->update(self::$_dummy_ids['cloze_text'], $cloze_text);
        }
    }
    /**
     * Destroys all the cloze texts created during tests
     *
     * @return void
     */
    private static function _dummy_cloze_texts_wipe()
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model(['cloze_text_answer_model', 'cloze_text_model']);

        $dummy_values = [
            self::$_dummy_values['cloze_text']['text'],
            self::$_dummy_values['cloze_text']['text_alt'],
            ''
        ];

        foreach($dummy_values as $value) {
            $cloze_texts = $CI->cloze_text_model->get_many_by(['Cloze_Text' => $value]);
            foreach($cloze_texts as $cloze_text) {
                $CI->cloze_text_answer_model->delete_by(['FK_Cloze_Text' => $cloze_text->ID]);
                $CI->cloze_text_model->delete($cloze_text->ID);
            }
        }
    }

    /**
     * Creates/Obtains the id of the current dummy cloze text answer
     *
     * @return integer = ID of the dummy cloze text answer
     */
    private static function &_dummy_cloze_text_answer_get() : int
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('cloze_text_answer_model');

        $cloze_text_answer = $CI->cloze_text_answer_model->get(self::$_dummy_ids['cloze_text_answer']);
        // Cloze text answer does not exist, create it
        if(is_null($cloze_text_answer)) {
            $dummy_values =& self::$_dummy_values['cloze_text_answer'];
            $cloze_text_id = self::_dummy_cloze_text_get();

            $cloze_text_answer = array(
                'FK_Cloze_Text' => $cloze_text_id,
                'Answer' => $dummy_values['answer'],
                'Answer_Order' => $dummy_values['order']
            );

            self::$_dummy_ids['cloze_text_answer'] = $CI->cloze_text_answer_model->insert($cloze_text_answer);
        }
        return self::$_dummy_ids['cloze_text_answer'];
    }
    /**
     * Resets the dummy cloze text answer
     *
     * @return void
     */
    private static function _dummy_cloze_text_answer_reset()
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('cloze_text_answer_model');

        $cloze_text_answer = $CI->cloze_text_answer_model->get(self::$_dummy_ids['cloze_text_answer']);
        if(is_null($cloze_text_answer)) {
            self::_dummy_cloze_text_get();
        } else {
            $dummy_values =& self::$_dummy_values['cloze_text_answer'];
            $cloze_text_id = self::_dummy_cloze_text_get();

            $cloze_text_answer = array(
                'FK_Cloze_Text' => $cloze_text_id,
                'Answer' => $dummy_values['answer'],
                'Answer_Order' => $dummy_values['order']
            );

            $CI->cloze_text_answer_model->update(self::$_dummy_ids['cloze_text_answer'], $cloze_text_answer);
        }
    }
    /**
     * Destroys all the cloze text answers created during tests
     *
     * @return void
     */
    private static function _dummy_cloze_text_answers_wipe()
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('cloze_text_answer_model');

        $dummy_values = [
            self::$_dummy_values['cloze_text_answer']['answer'],
            self::$_dummy_values['cloze_text_answer']['answer_alt'],
            ''
        ];

        foreach($dummy_values as $value) {
            $CI->cloze_text_answer_model->delete_by(['Answer' => $value]);
        }
    }

    /**
     * Creates/Obtains the id of the current dummy free answer
     *
     * @return integer = ID of the dummy free answer
     */
    private static function &_dummy_free_answer_get() : int
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('free_answer_model');

        $free_answer = $CI->free_answer_model->get(self::$_dummy_ids['free_answer']);
        // Free answer does not exist, create it
        if(is_null($free_answer)) {
            $dummy_values =& self::$_dummy_values['free_answer'];
            $question_id = self::_dummy_question_get();

            $free_answer = array(
                'FK_Question' => $question_id,
                'Answer' => $dummy_values['answer']
            );

            self::$_dummy_ids['free_answer'] = $CI->free_answer_model->insert($free_answer);
        }
        return self::$_dummy_ids['free_answer'];
    }
    /**
     * Resets the dummy free answer
     *
     * @return void
     */
    private static function _dummy_free_answer_reset()
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('free_answer_model');

        $free_answer = $CI->free_answer_model->get(self::$_dummy_ids['free_answer']);
        if(is_null($free_answer)) {
            self::_dummy_free_answer_get();
        } else {
            $dummy_values =& self::$_dummy_values['free_answer'];
            $question_id = self::_dummy_question_get();

            $free_answer = array(
                'FK_Question' => $question_id,
                'Answer' => $dummy_values['answer']
            );

            $CI->free_answer_model->update(self::$_dummy_ids['free_answer'], $free_answer);
        }
    }
    /**
     * Destroys all the free answers created during tests
     *
     * @return void
     */
    private static function _dummy_free_answers_wipe()
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('free_answer_model');

        $dummy_values = [
            self::$_dummy_values['free_answer']['answer'],
            self::$_dummy_values['free_answer']['answer_alt'],
            ''
        ];

        foreach($dummy_values as $value) {
            $CI->free_answer_model->delete_by(['Answer' => $value]);
        }
    }

    /**
     * Creates/Obtains the id of the current dummy multiple answer
     *
     * @return integer = ID of the dummy multiple answer
     */
    private static function &_dummy_multiple_answer_get() : int
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('multiple_answer_model');

        $multiple_answer = $CI->multiple_answer_model->get(self::$_dummy_ids['multiple_answer']);
        // Multiple answer does not exist, create it
        if(is_null($multiple_answer)) {
            $dummy_values =& self::$_dummy_values['multiple_answer'];
            $question_id = self::_dummy_question_get();

            $multiple_answer = array(
                'FK_Question' => $question_id,
                'Answer' => $dummy_values['answer']
            );

            self::$_dummy_ids['multiple_answer'] = $CI->multiple_answer_model->insert($multiple_answer);
        }
        return self::$_dummy_ids['multiple_answer'];
    }
    /**
     * Resets the dummy multiple answer
     *
     * @return void
     */
    private static function _dummy_multiple_answer_reset()
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('multiple_answer_model');

        $multiple_answer = $CI->multiple_answer_model->get(self::$_dummy_ids['multiple_answer']);
        if(is_null($multiple_answer)) {
            self::_dummy_multiple_answer_get();
        } else {
            $dummy_values =& self::$_dummy_values['multiple_answer'];
            $question_id = self::_dummy_question_get();

            $multiple_answer = array(
                'FK_Question' => $question_id,
                'Answer' => $dummy_values['answer']
            );

            $CI->multiple_answer_model->update(self::$_dummy_ids['multiple_answer'], $multiple_answer);
        }
    }
    /**
     * Destroys all the multiple answers created during tests
     *
     * @return void
     */
    private static function _dummy_multiple_answers_wipe()
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('multiple_answer_model');

        $dummy_values = [
            self::$_dummy_values['multiple_answer']['answer'],
            self::$_dummy_values['multiple_answer']['answer_alt'],
            ''
        ];

        foreach($dummy_values as $value) {
            $CI->multiple_answer_model->delete_by(['Answer' => $value]);
        }
    }

    /**
     * Creates/Obtains the id of the current dummy multiple choice answer
     *
     * @return integer = ID of the dummy multiple choice answer
     */
    private static function &_dummy_multiple_choice_get() : int
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('multiple_choice_model');

        $multiple_choice = $CI->multiple_choice_model->get(self::$_dummy_ids['multiple_choice']);
        // Multiple choice does not exist, create it
        if(is_null($multiple_choice)) {
            $dummy_values =& self::$_dummy_values['multiple_choice'];
            $question_id = self::_dummy_question_get();

            $multiple_choice = array(
                'FK_Question' => $question_id,
                'Answer' => $dummy_values['answer'],
                'Valid' => $dummy_values['valid']
            );

            self::$_dummy_ids['multiple_choice'] = $CI->multiple_choice_model->insert($multiple_choice);
        }
        return self::$_dummy_ids['multiple_choice'];
    }
    /**
     * Resets the dummy multiple choice answer
     *
     * @return void
     */
    private static function _dummy_multiple_choice_reset()
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('multiple_choice_model');

        $multiple_choice = $CI->multiple_choice_model->get(self::$_dummy_ids['multiple_choice']);
        if(is_null($multiple_choice)) {
            self::_dummy_multiple_choice_get();
        } else {
            $dummy_values =& self::$_dummy_values['multiple_choice'];
            $question_id = self::_dummy_question_get();

            $multiple_choice = array(
                'FK_Question' => $question_id,
                'Answer' => $dummy_values['answer'],
                'Valid' => $dummy_values['valid']
            );

            $CI->multiple_choice_model->update(self::$_dummy_ids['multiple_choice'], $multiple_choice);
        }
    }
    /**
     * Destroys all the multiple choice answers created during tests
     *
     * @return void
     */
    private static function _dummy_multiple_choices_wipe()
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('multiple_choice_model');

        $dummy_values = [
            self::$_dummy_values['multiple_choice']['answer'],
            self::$_dummy_values['multiple_choice']['answer_alt'],
            ''
        ];

        foreach($dummy_values as $value) {
            $CI->multiple_choice_model->delete_by(['Answer' => $value]);
        }
    }

    /**
     * Creates/Obtains the id of the current dummy picture landmark
     *
     * @return integer = ID of the dummy picture landmark
     */
    private static function &_dummy_picture_landmark_get() : int
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('picture_landmark_model');

        $picture_landmark = $CI->picture_landmark_model->get(self::$_dummy_ids['picture_landmark']);
        // Picture landmark does not exist, create it
        if(is_null($picture_landmark)) {
            $dummy_values = self::$_dummy_values['picture_landmark'];
            $question_id = self::_dummy_question_get();

            $picture_landmark = array(
                'FK_Question' => $question_id,
                'Symbol' => $dummy_values['symbol'],
                'Answer' => $dummy_values['answer']
            );

            self::$_dummy_ids['picture_landmark'] = $CI->picture_landmark_model->insert($picture_landmark);
        }
        return self::$_dummy_ids['picture_landmark'];
    }
    /**
     * Resets the dummy picture landmark
     *
     * @return void
     */
    private static function _dummy_picture_landmark_reset()
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('picture_landmark_model');

        $picture_landmark = $CI->picture_landmark_model->get(self::$_dummy_ids['picture_landmark']);
        if(is_null($picture_landmark)) {
            self::_dummy_picture_landmark_get();
        } else {
            $dummy_values = self::$_dummy_values['picture_landmark'];
            $question_id = self::_dummy_question_get();

            $picture_landmark = array(
                'FK_Question' => $question_id,
                'Symbol' => $dummy_values['symbol'],
                'Answer' => $dummy_values['answer']
            );

            $CI->picture_landmark_model->update(self::$_dummy_ids['picture_landmark'], $picture_landmark);
        }
    }
    /**
     * Destroys all the picture landmarks created during tests
     *
     * @return void
     */
    private static function _dummy_picture_landmarks_wipe()
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('picture_landmark_model');

        $dummy_values = [
            self::$_dummy_values['picture_landmark']['symbol'],
            ''
        ];

        foreach($dummy_values as $value) {
            $CI->picture_landmark_model->delete_by(['Symbol' => $value]);
        }
    }

    /**
     * Creates/Obtains the id of the current dummy answer distribution
     *
     * @todo Wait until answer distributions are implemented before usage
     * @return integer = ID of the dummy answer distribution
     */
    private static function &_dummy_answer_distribution_get() : int
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('answer_distribution_model');

        $answer_distribution = $CI->answer_distribution_model->get(self::$_dummy_ids['answer_distribution']);
        // Answer ditribution does not exist
        if(is_null($answer_distribution)) {
            $dummy_values =& self::$_dummy_values['answer_distribution'];
            $question_id = self::_dummy_question_get();

            $answer_distribution = array(
                'FK_Question' => $question_id,
                'Question_Part' => $dummy_values['question_part'],
                'Answer_Part' => $dummy_values['answer_part']
            );

            self::$_dummy_ids['answer_distribution'] = $CI->answer_distribution_model->insert($answer_distribution);
        }
        return self::$_dummy_ids['answer_distribution'];
    }
    /**
     * Resets the dummy answer distribution
     *
     * @todo Wait until answer distributions are implemented before usage
     * @return void
     */
    private static function _dummy_answer_distribution_reset()
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('answer_distribution_model');

        $answer_distribution = $CI->answer_distribution_model->get(self::$_dummy_ids['answer_distribution']);
        // Answer ditribution dos not exist
        if(is_null($answer_distribution)) {
            self::_dummy_answer_distribution_get();
        } else {
            $dummy_values =& self::$_dummy_values['answer_distribution'];
            $question_id = self::_dummy_question_get();

            $answer_distribution = array(
                'FK_Question' => $question_id,
                'Question_Part' => $dummy_values['question_part'],
                'Answer_Part' => $dummy_values['answer_part']
            );

            $CI->answer_distribution_model->update(self::$_dummy_ids['answer_distribution'], $answer_distribution);
        }
    }
    /**
     * Destroys all the answer distributions created in tests
     *
     * @todo Wait until answer distributions are implemented before usage
     * @return void
     */
    private static function _dummy_answer_distributions_wipe()
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('answer_distribution_model');

        $dummy_values = [
            self::$_dummy_values['answer_distribution']['answer_part'],
            self::$_dummy_values['answer_distribution']['answer_part_alt'],
            ''
        ];

        foreach($dummy_values as $value) {
            $CI->answer_distribution_model->delete_by(['Question_Part' => $value]);
        }
    }

    /**
     * Creates/Obtains the id of the current dummy table cell
     *
     * @todo Wait until table cells are implemented before usage
     * @return integer = ID of the dummy table cell
     */
    private static function &_dummy_table_cell_get() : int
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('table_cell_model');

        $table_cell = $CI->table_cell_model->get(self::$_dummy_ids['table_cell']);
        // Table cell does not exist
        if(is_null($table_cell)) {
            $dummy_values =& self::$_dummy_values['table_cell'];
            $question_id = self::_dummy_question_get();

            $table_cell = array(
                'FK_Question' => $question_id,
                'Content' => $dummy_values['content'],
                'Column_Nb' => $dummy_values['column'],
                'Row_Nb' => $dummy_values['row'],
                'Header' => $dummy_values['header'],
                'Display_In_Question' => $dummy_values['display']
            );

            self::$_dummy_ids['table_cell'] = $CI->table_cell_model->insert($table_cell);
        }
        return self::$_dummy_ids['table_cell'];
    }
    /**
     * Resets the current dummy table cell
     *
     * @todo Wait until table cells are implemented before usage
     * @return void
     */
    private static function _dummy_table_cell_reset()
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('table_cell_model');

        $table_cell = $CI->table_cell_model->get(self::$_dummy_ids['table_cell']);
        if(is_null($table_cell)) {
            self::_dummy_table_cell_get();
        } else {
            $dummy_values =& self::$_dummy_values['table_cell'];
            $question_id = self::_dummy_question_get();

            $table_cell = array(
                'FK_Question' => $question_id,
                'Content' => $dummy_values['content'],
                'Column_Nb' => $dummy_values['column'],
                'Row_Nb' => $dummy_values['row'],
                'Header' => $dummy_values['header'],
                'Display_In_Question' => $dummy_values['display']
            );

            $CI->table_cell_model->update(self::$_dummy_ids['table_cell'], $table_cell);
        }
    }
    /**
     * Detroys all the table cells created in tests
     *
     * @todo Wait until table cells are implemented before usage
     * @return void
     */
    private static function _dummy_table_cells_wipe()
    {
        $CI =& get_instance();
        if($CI instanceof CIPHPUnitTestNullCodeIgniter) {
            CIPHPUnitTest::createCodeIgniterInstance();
            $CI =& get_instance();
        }
        $CI->load->model('table_cell_model');

        $dummy_values = [
            self::$_dummy_values['table_cell']['content'],
            self::$_dummy_values['table_cell']['content_alt'],
            ''
        ];

        foreach($dummy_values as $value) {
            $CI->table_cell_model->delete_by(['Content' => $value]);
        }
    }
}