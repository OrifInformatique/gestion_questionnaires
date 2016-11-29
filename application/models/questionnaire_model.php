<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Questionnaire model is used to give access to the tab 'Questionnaire' of the application
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class questionnaire_model extends MY_Model
{
    protected $_table = 't_questionnaire';
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