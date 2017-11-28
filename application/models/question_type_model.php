<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Question Type model is used to get different types for each question.
 * question is used to get related question type for each question.
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class question_type_model extends MY_Model
{
    /* SET MY_Model VARIABLES */
    protected $_table = 't_question_type';
    protected $primary_key = 'ID';
    protected $protected_attributes = ['ID'];
    protected $has_many = ['questions' => ['primary_key' => 'FK_Question_Type',
                                           'model' => 'question_model']];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
	
	
	public function get_array()
    {
		$question_types = $this->question_type_model->get_all();
		
		
		foreach($question_types as $question_type)
		{
			$array[] = (array)$question_type;
			
			$types_array[end($array)['ID']] = end($array)['Type_Name'];
		}
		
		return $types_array;
	}
}