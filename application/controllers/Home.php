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
    	if($this->session->user_access >= $this->config->item('access_lvl_manager'))
            redirect('questionnaire');
        else
            $this->display_view("home/home_view");
    }
}