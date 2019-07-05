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
                                              'model' => 'topic_model'],
                           'questionnaire_models' => ['primary_key' => 'FK_Topic',
                                                     'model' => 'questionnaire_model_model']];

    // Uncomment to enable soft deletion
    //protected $soft_delete_key = 'Archive';
    //protected $soft_delete = TRUE;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Recursive function to return all topics in a multidimensional array (parent topics, subtopics, ...).
     * This function is made to fit with CodeIgniter's form_dropdown function.
     *
     * $with_archived : if true, return all topics, if false return non archived topics
     * $parent_topic : the parent topic on wich we want to get all the children topics (used for recursivity)
     */
    public function get_tree($with_archived = false, $parent_topic = NULL)
    {
        if (is_null($parent_topic))
        {
            if ($with_archived)
            {
                // Get all first level topics even if they are archived
                $topics = $this->topic_model->get_many_by("FK_Parent_Topic IS NULL");
            }
            else
            {
                // Get only non archived first level topics
                $topics = $this->topic_model->get_many_by("(FK_Parent_Topic IS NULL) AND (Archive = false OR Archive IS NULL)");
            }
        }
        else
        {
            if ($with_archived)
            {
                // Get all child topics of parent_topic, even if they are archived
                $topics = $this->topic_model->get_many_by("FK_Parent_Topic = ".$parent_topic->ID);
            }
            else
            {
                // Get only non archived child topics of parent_topic
                $topics = $this->topic_model->get_many_by("(FK_Parent_Topic = ".$parent_topic->ID.") AND (Archive = false OR Archive IS NULL)");
            }
        }

        if (count($topics) > 0)
        {
            foreach ($topics as $topic)
            {
                if ($with_archived)
                {
                    // Get all child topics of current topic, even if they are archived
                    $child_topics = $this->topic_model->get_many_by("FK_Parent_Topic = ".$topic->ID);
                }
                else
                {
                    // Get only non archived child topics of current topic
                    $child_topics = $this->topic_model->get_many_by("(FK_Parent_Topic = ".$topic->ID.") AND (Archive = false OR Archive IS NULL)");
                }


                if (count($child_topics) > 0)
                {
                    // Get the children of current topic.
                    // The name of the current topic is used as array's key, to fit with CodeIgniter's form_dropdown function
                    $topics_tree[$topic->Topic] = $this->topic_model->get_tree($with_archived, $topic);
                }
                else
                {
                    // This is a "leaf" topic. The ID of the current topic is used as array's key.
                    if(!is_null($parent_topic)){
                        $topics_tree[$topic->ID] = $topic->Topic;
                    }
                }
            }
        }
        else
        {
            // There is no topic to return
            $topics_tree = NULL;
        }

        return $topics_tree;
    }
}