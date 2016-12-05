<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Module controller
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */

class Module extends MY_Controller
{
    /* MY_Controller variables definition */
    protected $access_level = "2";

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('topic_model');
        $this->load->helper(array('form', 'url'));
    }

    /**
     * Display the module view
     */
    public function index()
    {
        $output['topics_modules'] = $this->topic_model->get_all();
        $this->display_view("modules/index", $output);
    }
}