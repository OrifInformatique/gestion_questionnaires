<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Questionnaire controller
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class Questionnaire extends MY_Controller
{

    /* MY_Controller variables definition */
    protected $access_level = "2";

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('question_questionnaire_model', 'questionnaire_model', 'topic_model',
                                'question_model', 'question_type_model'));
        $this->load->helper(array('url', 'form'));
        $this->load->library(array('TableTopics', 'form_validation'));

    }

    /**
     * Display questionnaire list
     */
    public function index()
    {
        $outputs['questionnaires'] = $this->questionnaire_model->get_all();
        $this->display_view('questionnaires/index', $outputs);
    }

    /**
     * @param int $id = id of the selected questionnaire
     * @param int $error = Type of error :
     * 0 = no error
     * 1 = wrong identifiers
     * 2 = field(s) empty
     * Display the update questionnaire view
     */
    public function update($id = 0, $error = 0)
    {
        $outputs['error'] = $error;
        if ($id != 0) {
            $outputs["id"] = $id;
            $this->display_view("questionnaires/update", $outputs);
        } else {
            $this->index();
        }
    }

    /**
     * Form validation to update a questionnaire
     */
    public function form_update()
    {
        $this->form_validation->set_rules('title', 'Title', 'required');

        $id = $this->input->post('id');
        $title = array('Questionnaire_Name' => $this->input->post('title'));

        if ($this->form_validation->run() == true) {

            $this->questionnaire_model->update($id, $title);
            $this->index();
        } else {
            ;
            $this->update($id, true);
        }
    }

    /**
     * @param int $id = id of the selected questionnaire
     * Delete selected questionnaire and redirect to questionnaire list
     */
    public function delete($id = 0)
    {
        if ($id != 0) {
            $this->questionnaire_model->delete($id);
        }
        $this->index();
    }

    /**
     * To add a new questionnaire
     * @param string $title
     * @param array $topics
     * @param array $nbQuestions
     */
    public function add($title = '', $topics = array(), $nbQuestions = array())
    {
        $output['topicsList'] = $this->topic_model->get_all();
        $output['questions'] = $this->question_model->with_all()->get_all();
        $output['nbLines'] = 5;
        $output['title'] = $title;
        $output['topics'] = $topics;
        $output['nbQuestions'] = $nbQuestions;
        $this->display_view('questionnaires/add', $output);
    }

    public function form_add()
    {
        //Get the title
        $this->form_validation->set_rules('title', 'Title', 'required');

        if($this->form_validation->run() == true)
        {
            //Get last inputs
            $title = $this->input->post('title');
            $topic = $this->input->post('topic_selected');
            $nbQuestions = $this->input->post('nb_questions');

            //Create an object to get the title and two arrays for topics and number of questions asked
            $tableTopics = new TableTopics();
            $tableTopics->setTitle($title);


            //If the user want to validate his pdf
            if (!isset($_POST[$this->lang->line('generatePDF_btn')]))
            {
                //Save each elements already seen on the topic list
                $tableTopics = $this->saveTopicElements($tableTopics, 1);

                //And take with him the last inputs
                $tableTopics->setArrayTopics($topic);
                $tableTopics->setArrayNbQuestion($nbQuestions);
                $this->add($tableTopics->getTitle(), $tableTopics->getArrayTopics(), $tableTopics->getArrayNbQuestion());
            }
            else
            {
                $tableTopics = $this->saveTopicElements($tableTopics, 2);
                $this->generatePDF($tableTopics);
            }
        }
        else
        {
            //Message d'erreur
        }
    }

    public function generatePDF($tableTopics)
    {
        $this->InsertNewQuestionnaire($tableTopics);
        /**
         * Pour construire un pdf :
         * https://secure.php.net/manual/fr/intro.pdf.php
         */
        echo "PDF en construction.. veuillez patienter une duréé indeterminée..";

    }


    private function InsertNewQuestionnaire($tableTopics)
    {
        //Insert new Questionnaire
        $title = $tableTopics->getTitle();
        $newQuestionnaire = array("Questionnaire_Name" => $title,
            "PDF" => $title . "pdf",
            "Corrige_PDF" => $title . "pdf_corrige");

        $this->questionnaire_model->insert($newQuestionnaire);
        $idQuestionnaire = $this->db->insert_id();

        //Insert each question about the questionnaire
        for ($index = 0; $index < count($tableTopics->getArrayTopics()); $index++)
        {
            //Get ID for each topic asked
            $topic = $tableTopics->getArrayTopics()[$index];
            $nbQuestion = $tableTopics->getArrayNbQuestion()[$index];
            $topicLine = $this->topic_model->get_by("Topic = '$topic'");
            $idTopic = $topicLine->ID;

            //load randoms questions about the topic
            $rndQuestions = $this->question_model->getRNDQuestions($idTopic, $nbQuestion);
            for($number = 0; $number < count($rndQuestions);$number++)
            {
                $row = array("FK_Question" => intval($rndQuestions[$number]["ID"]), "FK_Questionnaire" => $idQuestionnaire);
                $this->question_questionnaire_model->insert($row);
            }
        }
    }

    /**
     * Save each elements already seen on the topic list
     * @param $tableTopics = object of the Topics table
     * @param $indice = 2 if the user want to finish the table OR 1 if not
     * @return the table of Topic
     */
    private function saveTopicElements($tableTopics, $indice)
    {
        $index = 11;
        while ($index <= ((count($this->input->post()) - $indice) * 5)) {
            $tableTopics->setArrayTopics($this->input->post($index));
            $tableTopics->setArrayNbQuestion($this->input->post($index + 1));
            $index += 10;
        }

        return $tableTopics;
    }
}