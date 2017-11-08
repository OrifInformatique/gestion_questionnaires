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
    /* SET MY_Model VARIABLES */
    protected $_table = 't_topic';
    protected $primary_key = 'ID';
    protected $protected_attributes = ['ID'];
    protected $belongs_to = ['Parent_Topic' => ['primary_key' => 'FK_Parent_Topic',
                                                'model' => 'topic_model']];
    protected $has_many = ['questions' => ['primary_key' => 'FK_Topic',
                                           'model' => 'question_model'],
                           'child_topics' => ['primary_key' => 'FK_Parent_Topic',
                                              'model' => 'topic_model']];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Return all topics in a bidimensional array (parent topics, subtopics)
     * ATTENTION : this does not work with more than 2 topic's levels
     *
     * $with_archived : if true, return all topics, if false return non archived topics
     */
    public function get_tree($with_archived = false)
    {
        if ($with_archived)
        {
            // Return all topics even if they are archived
            $topics = $this->topic_model->get_all();
        }
        else
        {
            // Return only non archived topics
            $topics = $this->topic_model->get_many_by("Archive = false OR Archive IS NULL");
        }

        foreach ($topics as $topic) {
            if ($topic->FK_Parent_Topic == 0)
            {
                // This is a first level topic
                $topics_tree[$topic->ID] = $topic;

                // Add subtopics
                for ($i = 0; $i < count($topics); $i++) {
                    if ($topic->ID == $topics[$i]->FK_Parent_Topic) {
                        //$topics_tree[$topic->ID][] = $topics[$i];
                    }
                }
            }
        }

        return $topics_tree;
    }
}