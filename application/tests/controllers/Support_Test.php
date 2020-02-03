<?php
// Load user controller for callback tests
require_once(__DIR__.'/../../modules/support/controllers/Support.php');

/**
 * Class for tests for Support controller
 * 
 * @author      Orif, section informatique (ViDi, BuYa)
 * @link        https://github.com/OrifInformatique/
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class Support_Test extends TestCase {

    /**
     * Called before a test
     *
     * @return void
     */
    public function setUp()
    {
        $this->resetInstance();
        // Required to load the correct url_helper
        $this->CI->load->helper('url');
    }

    /***********
     * PROVIDERS
     ***********/
    /**
     * Provider for `test_fields_not_empty`
     *
     * @return array
     */
    public function provider_fields_not_empty() {
        $this->resetInstance();

        $data = [];    
        $data['no_error'] = [
            [
                'issue_title' => 'Test PHPUnit',
                'issue_body' => date("d.m.Y H:i:s")
            ],
            FALSE
        ];
        $data['no_title'] = [
            [
                'issue_title' => '',
                'issue_body' => 'Test'
            ],
            TRUE
        ];
        $data['no_body'] = [
            [
                'issue_title' => 'Test',
                'issue_body' => ''
            ],
            TRUE
        ];
        $data['nothing'] = [
            [
                'issue_title' => '',
                'issue_body' => ''
            ],
            TRUE
        ];
        return $data;
    }

    /*******
     * TESTS
     *******/
    /**
     * Test for `Support::index` without being logged
     *
     * @return void
     */
    public function test_index_not_logged()
    {
        $this->request('GET', 'support');

        $this->assertRedirect('user/auth/login');
    }

    /**
     * Test for `Support::form_report_problem`
     * 
     * @dataProvider provider_fields_not_empty
     *
     * @param array $post_params = Parameters to pass to $_POST
     * @param boolean $error_expected = Whether an error is expected
     * @return void
     */
    public function test_fields_not_empty(array $post_params, bool $error_expected){

        self::_login_as_admin();

        // https://github.com/kenjis/ci-phpunit-test/issues/31
        $this->request->setCallable(
            function ($CI) {
                $CI->config->set_item('github_repo', 'OrifInformatique/test');
                $CI->config->load('../modules/support/config/MY_support_token');
            }
        );


        $output = $this->request('POST', 'support/form_report_problem', $post_params);

        if ($error_expected) {
            $this->assertNotEmpty(validation_errors());
        }
        else {
            $this->CI->lang->load('../../modules/support/language/french/MY_support');
            $this->assertContains($this->CI->lang->line('title_problem_submitted'), $output);

            $issueNumber = $_SESSION['last_issue_json']->number;
            $this->delete_issue($issueNumber);
        }
    }

    /**
     * Test for `Support::form_report_problem`
     * 
     * @return void
     */
    public function test_repository_does_not_exist(){

        self::_login_as_admin();

        // https://github.com/kenjis/ci-phpunit-test/issues/31
        $this->request->setCallable(
            function ($CI) {
                $CI->config->set_item('github_repo', 'OrifInformatique/notexist');
            }
        );


        $output = $this->request('POST', 'support/form_report_problem', ['issue_title' => 'Test', 'issue_body' => 'test']);

        $this->CI->lang->load('../../modules/support/language/french/MY_support');
        $this->assertContains($this->CI->lang->line('msg_error_occurred'), $output);
    }

    /**
     * Test for `Support::form_report_problem`
     * 
     * @return void
     */
    public function test_bad_token(){

        self::_login_as_admin();

        // https://github.com/kenjis/ci-phpunit-test/issues/31
        $this->request->setCallable(
            function ($CI) {
                $CI->config->set_item('github_repo', 'OrifInformatique/test');
                $CI->config->set_item('github_token', 'badtoken');
            }
        );


        $output = $this->request('POST', 'support/form_report_problem', ['issue_title' => 'Test', 'issue_body' => 'test']);

        $this->CI->lang->load('../../modules/support/language/french/MY_support');
        $this->assertContains($this->CI->lang->line('msg_error_occurred'), $output);
    }

    /**************
     * MISC METHODS
     **************/
    /**
     * Tricks the server to think there is an admin logged in
     *
     * @return void
     */
    private static function _login_as_admin()
    {
        $_SESSION['logged_in'] = TRUE;
        // We can't know the current configuration for admin access, so we max it
        $_SESSION['user_access'] = PHP_INT_MAX;
        $_SESSION['user_id'] = 0;
    }

    /**
     * Delete a created issue
     *
     * @param int $issueNumber = issue number on GitHub
     * @return void
     */
    public function delete_issue(int $issueNumber) {
        $this->CI->config->load('../modules/support/config/MY_support_token');

        $url = 'https://api.github.com/repos/'.$this->CI->config->item('github_repo').'/issues/'.$issueNumber;
        $ch = curl_init($url);

        $body = ['state' => 'closed'];
        $json = json_encode($body);

        $header = array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic '.base64_encode($this->CI->config->item('github_username').':'.$this->CI->config->item('github_token')),
            'User-Agent: PHP-Server'
        );

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        curl_close($ch);
    }
}
