<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @author      Orif, section informatique (BuYa)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */

class Tests extends MY_Controller {
	/* MY_Controller variables definition */
    protected $access_level = "@";

    public function __construct()
    {
        parent::__construct();
    }

    public function index(){
    	$this->display_view('tests/index');
    }
}