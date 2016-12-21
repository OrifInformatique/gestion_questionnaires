<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Question Questionnaire model is used to set up a new questionnaire.
 * This table is a relation between questions and questionnaire to generate a clean questionnaire
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class question_questionnaire_model extends MY_Model
{
    protected $_table = 't_question_questionnaire';
    protected $primary_key = 'ID';
    protected $protected_attributes = ['ID'];
    protected $belongs_to = ['Question' => ['primary_key' => 'FK_Question',
                            'model' => 'question'], 'Questionnaire' =>
                            ['primary_key' => 'FK_Questionnaire', 'model'
                            => 'Questionnaire']];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
}