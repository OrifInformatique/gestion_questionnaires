<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Questionnaire model is used to give access to the tab 'Questionnaire' of the application
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class temp_questionnaire extends MY_Model{

    /* SET MY_Model VARIABLES */
    protected $_table = 't_temp_questionnaire';
    protected $primary_key = 'ID';
    protected $protected_attributes = ['ID'];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
}