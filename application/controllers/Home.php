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

        $this->load->helper(array('form', 'url'));
    }

    /**
     * Redirect depending of user access level
     */
    public function index(){
    	switch($this->session->user_access){
            case ACCESS_LVL_USER :
            	$this->display_view("home/home_view");
                break;
            case ACCESS_LVL_ADMIN :
                redirect('Questionnaire');
                break;
        }
    }
}