<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Home controller is used to display a homepage depending on user's
 * access level.
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */

class Home extends MY_Controller {
	/* MY_Controller variables definition */
    protected $access_level = "@";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Redirect depending of user access level
     */
    public function index(){
        $this->config->load(to_test_path('user/MY_user_config'));
    	if($this->session->user_access >= $this->config->item('access_lvl_registered'))
            redirect('questionnaire');
        else
            $this->display_view("home/home_view");
    }
}