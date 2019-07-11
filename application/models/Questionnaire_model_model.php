<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This model is used for accessing t_questionnaire_model
 * 
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class questionnaire_model_model extends MY_Model {
    /* SET MY_Model VARIABLES */
    protected $_table = 't_questionnaire_model';
    protected $primary_key = 'ID';
    protected $protected_attributes = ['ID'];
    protected $has_many = ['questionnaire_model_topic' => ['primary_key' => 'FK_Questionnaire_Model',
                                                           'model' => 'questionnaire_model_topic_model']];

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }
}