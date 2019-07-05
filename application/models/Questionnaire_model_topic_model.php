<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This model is used for accessing t_questionnaire_model_topic
 * 
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class questionnaire_model_topic_model extends My_Model {
    /* SET MY_Model VARIABLES */
    protected $_table = 't_questionnaire_model_topic';
    protected $primary_key = 'ID';
    protected $protected_attributes = ['ID'];
    protected $belongs_to = ['questionnaire_model' => ['primary_key' => 'FK_Questionnaire_Model',
                                                       'model' => 'questionnaire_model_model'],
                             'topic' => ['primary_key' => 'FK_Topic',
                                         'model' => 'topic_model']];

    public function __construct() {
        parent::__construct();
    }
}