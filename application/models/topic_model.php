<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Topic model is used to give access to the tab 'Topic' of the application
 * Parent_Topic is used to give a module name to all topics.
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class topic_model extends MY_Model
{
    protected $_table = 't_topic';
    protected $primary_key = 'ID';
    protected $protected_attributes = ['ID'];
    protected $belongs_to = ['Parent_Topic' => ['primary_key' => 'FK_Parent_Topic',
                                                'model' => 'topic']];
    protected $has_many = ['questions'];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
}