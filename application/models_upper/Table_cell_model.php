<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * TODO
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class table_cell_model extends MY_Model
{
    /* SET MY_Model VARIABLES */
    protected $_table = 't_table_cell';
    protected $primary_key = 'ID';
    protected $protected_attributes = ['ID'];
    protected $belongs_to = ['question' => ['primary_key' => 'FK_Question', 'model' => 'question_model']];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
}