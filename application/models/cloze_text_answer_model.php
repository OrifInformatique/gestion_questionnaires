<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * TODO
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class cloze_text_answer_model extends MY_Model
{
    /* SET MY_Model VARIABLES */
    protected $_table = 't_cloze_text_answer';
    protected $primary_key = 'ID';
    protected $protected_attributes = ['ID'];
    protected $belongs_to = ['question' => ['primary_key' => 'FK_Cloze_Text',
        'model' => 'cloze_text_model']];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
}