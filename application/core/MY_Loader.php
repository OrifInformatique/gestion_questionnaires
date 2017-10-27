<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Question controller
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class MY_Loader extends CI_Loader
{

    public function __construct()
    {
        parent::__construct();
    }

    public function iface($strInterface)
    {
        require_once APPPATH . '/interfaces/' . $strInterface . '.php';
    }
}