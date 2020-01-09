<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Support controller
 *
 * @author      Orif, section informatique (UlSi, ViDi, MeDa, BuYa)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class Support extends MY_Controller {
    /* MY_Controller variables definition */

    /**
     * Constructor
     */
    public function __construct() {
        $this->access_level = $this->config->item('access_lvl_registered');
        parent::__construct();
        $this->form_validation->CI =& $this;
    }

    public function index($sumbitted = false) {
        if($sumbitted){
            $this->display_view('support/problem_sumbitted');
        } else {
            $this->display_view('support/report_problem');
        }
    }

    public function form_report_problem() {
        $this->form_validation->set_rules('issue_title', $this->lang->line('field_issue_title'), 'required');
        $this->form_validation->set_rules('issue_body', $this->lang->line('field_issue_body'), 'required');

        if($this->form_validation->run() == true){
            

            $url = 'https://api.github.com/repos/'.$this->config->item('github_repo').'/issues';
            $ch = curl_init($url);

            if (isset($_SESSION['username'])) {
                // Add the user name in the issue title
                $title = '['.$_SESSION['username'].'] '.$this->input->post('issue_title');
            } else {
                $title = $this->input->post('issue_title');
            }
            
            $body = array(
                'title' => $title,
                'body' => $this->input->post('issue_body'),
                'labels' => array('user report')
            );
            $json = json_encode($body);

            $header = array(
                'Content-Type: application/json; charset=utf-8',
                'Authorization: Basic '.base64_encode($this->config->item('github_username').':'.$this->config->item('github_token')),
                'User-Agent: PHP-Server'
            );

            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($ch);
            curl_close($ch);

            $this->index(true);
        }else{
            $this->index();
        }
    }

}