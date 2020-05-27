<?php
require_once(__DIR__.'/../../third_party/Test_Trait.php');

/**
 * Class for tests for question model
 * 
 * @author      Orif, section informatique (ViDi, MeSa, BuYa)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class Question_model_test extends TestCase {
    use Test_Trait;

    /**
     * List of dummy values
     *
     * @var array
     */
    private $_dummy_values = [
        'question' => 'dummy_question',
        'topic' => 0,
        'question_type' => 0
    ];

    public function setUp()
    {
        $this->resetInstance();
        $this->CI->load->model('question_model');
    }

    /*******
     * TESTS
     *******/
    /**
     * Test for `question_model::getNbQuestionByTopic`
     * 
     * @dataProvider provider_getnbquestionbytopic
     *
     * @param int $topic_id = ID of the topic to use
     * @param int $expected_amount = Expected amount of questions in the topic
     * @return void
     */
    public function test_getnbquestionbytopic($topic_id, $expected_amount)
    {
        $this->_db_errors_save();

        $found_amount = $this->CI->question_model->getNbQuestionByTopic($topic_id);
        $this->assertEquals($expected_amount, $found_amount);

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }
    /**
     * Test for `question_model::getRNDQuestions`
     * 
     * @dataProvider provider_getrndquestions
     *
     * @param int $topic_id = ID of the topic to use
     * @param int $amount = Amoutn of questions to request
     * @param int $expected_amount = Amount of questions expected
     * @return void
     */
    public function test_getrndquestions($topic_id, $amount, $expected_amount)
    {
        $this->_db_errors_save();

        $questions = $this->CI->question_model->getRNDQuestions($topic_id, $amount);
        $this->assertEquals($expected_amount, count($questions));

        $this->assertFalse(
            $this->_db_errors_diff(),
            'One or more error occured in an SQL statement'
        );
    }

    /****************
     * DATA PROVIDERS
     ****************/
    /**
     * Provider for test_getnbquestionbytopic
     *
     * @return array
     */
    public function provider_getnbquestionbytopic() : array
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
     * Provider for test_getrndquestions
     *
     * @return array
     */
    public function provider_getrndquestions() : array
    {
        $this->resetInstance();
        $this->CI->load->model(['question_model', 'topic_model']);

        $data = [];

        $topic_id = $this->CI->topic_model->get_by('FK_Parent_Topic IS NOT NULL')->ID;
        $expected_amount = min(5, $this->CI->question_model->count_by("FK_Topic = {$topic_id} AND Archive = 0"));
        $data['no_error'] = [
            $topic_id,
            5,
            $expected_amount
        ];

        $topic_id = $this->CI->topic_model->get_next_id()+1;
        $expected_amount = min(5, $this->CI->question_model->count_by("FK_Topic = {$topic_id} AND Archive = 0"));
        $data['not_exist'] = [
            $topic_id,
            5,
            $expected_amount
        ];

        $topic_id = $this->CI->topic_model->get_by('FK_Parent_Topic IS NULL')->ID;
        $expected_amount = min(5, $this->CI->question_model->count_by("FK_Topic = {$topic_id} AND Archive = 0"));
        $data['not_topic'] = [
            $topic_id,
            5,
            $expected_amount
        ];

        $topic_id = $this->CI->topic_model->get_by('FK_Parent_Topic IS NOT NULL')->ID;
        $expected_amount = min(0, $this->CI->question_model->count_by("FK_Topic = {$topic_id} AND Archive = 0"));
        $data['negative_amount'] = [
            $topic_id,
            -1,
            $expected_amount
        ];

        return $data;
    }
}