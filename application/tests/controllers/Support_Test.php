<?php
require_once(__DIR__.'/../../third_party/Test_Trait.php');

/**
 * Class for tests for Support controller
 * 
 * @author      Orif, section informatique (ViDi, MeSa, BuYa)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class Support_Test extends TestCase {
    use Test_Trait;

    /*******************
     * START/END METHODS
     *******************/
    public function setUp()
    {
        $this->resetInstance();
        $this->_login_as(ACCESS_LVL_ADMIN);
    }

    /*******
     * TESTS
     *******/
    /**
     * Test for `Support::index`
     * 
     * @dataProvider provider_index
     * 
     * @covers Support::index
     * @covers Support::form_report_problem
     *
     * @param boolean $submitted = Whether it was submitted
     * @param string $text = Expected text
     * @return void
     */
    public function test_index(bool $submitted, string $text)
    {
        $output = $this->request('GET', "support/index/{$submitted}");

        $this->assertContains($text, $output);
    }
    /**
     * Test for `Support::form_report_problem`
     * 
     * @depends Some way to use cURL without cURL
     * 
     * @todo Create some way to use cURL without cURL before testing.
     *
     * @return void
     */
    public function test_form_report_problem_good()
    { }
    /**
     * Test for `Support::form_report_problem` with wrong data
     *
     * @return void
     */
    public function test_form_report_problem_bad()
    {
        $title_html = $this->CI->lang->line('title_report_a_problem');

        $output = $this->request('POST', 'support/form_report_problem', []);

        $this->assertContains($title_html, $output);
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

        $data = [];

        $data['not_submitted'] = [
            FALSE,
            $this->CI->lang->line('title_report_a_problem')
        ];

        $data['submitted'] = [
            TRUE,
            $this->CI->lang->line('title_problem_submitted')
        ];

        return $data;
    }
}