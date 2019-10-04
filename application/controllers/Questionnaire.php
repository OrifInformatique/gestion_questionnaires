<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Questionnaire controller
 *
 * @author      Orif, section informatique (UlSi, ViDi, MeDa)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class Questionnaire extends MY_Controller
{
    CONST TITLEFONT_SIZE = 18;
    CONST QUESTIONFONT_SIZE = 12;

    /* MY_Controller variables definition */
    protected $access_level = ACCESS_LVL_MANAGER;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('questionnaire_model', 'topic_model', 'question_model',
            'questionnaire_model_topic_model', 'questionnaire_model_model'));
        $this->load->library(array('TableTopics', 'fpdf181/Fpdf', 'pdf'));
    }

    /**
     * Display questionnaire list
     *
     * @param string $error = Any error that occured
     */
    public function index($error = "")
    {
        $outputs = array(
            'title' => $this->lang->line('title_questionnaire'),
            'questionnaires' => $this->questionnaire_model->get_all(),
            'models' => $this->questionnaire_model_model->get_all(),
            'error' => $error
        );
        $this->display_view('questionnaires/index', $outputs);
    }

    /**
     * Display the update questionnaire view
     *
     * @param int $id = id of the selected questionnaire
     * @param bool $model = Whether or not it's updating a model
     * @param bool $error = Whether or not there has been an error
     */
    public function update($id = 0, $model = FALSE, $error = FALSE)
    {
        if($model) $object = $this->questionnaire_model_model->get($id);
        else $object = $this->questionnaire_model->get($id);
        if(is_null($object)) {
            redirect('Questionnaire');
            return;
        }

        $outputs = array(
            'title' => $this->lang->line('title_questionnaire_update'),
            'error' => $error,
            'id' => $id,
            'model' => $model,
            'quest_title' => $object->Questionnaire_Name,
            'subtitle' => $object->Questionnaire_Subtitle,
            'modelName' => $object->Base_Name ?? ''
        );
        $this->display_view("questionnaires/update", $outputs);
    }

    /**
     * Form validation to update a questionnaire
     */
    public function form_update()
    {
        $id = $this->input->post('id');
        $model = (bool) $this->input->post('model');

        $this->form_validation->set_rules('title', $this->lang->line('add_title_questionnaire'), 'required');
        $this->form_validation->set_rules(
            'id', 'Id',
            "callback_quest_or_model_exists[{$model}]",
            $this->lang->line('msg_err_'.($model ? 'model' : 'questionnaire').'_not_exist')
        );

        $object = array(
            'Questionnaire_Name' => $this->input->post('title'),
            'Questionnaire_Subtitle' => $this->input->post('subtitle')
        );

        if($model) $object['Base_Name'] = $this->input->post('modelName');

        if ($this->form_validation->run() == true) {
            if($model)
                $this->questionnaire_model_model->update($id, $object);
            else {
                $this->questionnaire_model->update($id, $object);
                $this->generatePDF($id);
            }
        } else {
            $this->update($id, $model, true);
        }
        
        redirect('Questionnaire');
    }

    public function quest_or_model_exists($id, $model) : bool
    {
        $object = NULL;
        if($model) {
            $object = $this->questionnaire_model_model->get($id);
        } else {
            $object = $this->questionnaire_model->get($id);
        }
        return !is_null($object);
    }

    /**
     * Delete selected questionnaire and redirect to questionnaire list
     * @param int $id = id of the selected questionnaire
     * @param any $action = If null, ask for confirmation, otherwise delete
     */
    public function delete($id = 0, $action = NULL)
    {
        if ($id != 0) {
            if (is_null($action)) {
                $output = get_object_vars($this->questionnaire_model->get($id));
                $output["model"] = false;
                $output["title"] = $this->lang->line('delete_questionnaire');
                $this->display_view("questionnaires/delete", $output);
            } else {
                $this->load->model('question_questionnaire_model');

                $questionnaire = $this->questionnaire_model->with("question_questionnaires")->get($id);
                if(!is_null($questionnaire)) {
                    if (count($questionnaire->question_questionnaires) > 0) {
                        foreach ($questionnaire->question_questionnaires as $question_questionnaire) {
                            $this->question_questionnaire_model->delete($question_questionnaire->ID);
                        }
                    }
                    unlink('pdf_files/questionnaires/'.$questionnaire->PDF);
                    unlink('pdf_files/corriges/'.$questionnaire->Corrige_PDF);
                    $this->questionnaire_model->delete($id);
                }
                redirect('Questionnaire');
            }
        } else {
            redirect('Questionnaire');
        }
    }

    /**
     * To add a new questionnaire
     *
     * @param boolean $model = Whether or not it's for models
     * @param int $error = Whether or not there has been an error
     * @param string $title = The title of the page
     * @param array $topics = The topics selected
     * @param array $nbQuestions = The amount of questions selected per topic
     * @param string $modelName = The name of the model
     */
    public function add($model = 0, $error = NULL, $title = '', $topics = array(), $nbQuestions = array(), $subtitle = '', $modelName = '')
    {
        // Removing this will make it so that you can't set the amount of questions
        if(isset($_POST['topic'])) {
            echo $this->question_model->getNbQuestionByTopic($this->input->post('topic'));
        } else {
            $output = array(
                'title' => $this->lang->line('add_questionnaire_title'),
                'error' => ($error == NULL ? NULL : true),
                'topicsList' => $this->topic_model->get_many_by('Archive = 0 OR Archive IS NULL'),
                'title' => $title,
                'modelName' => $modelName,
                'topics' => $topics,
                'nbQuestions' => $nbQuestions,
                'model' => (bool) $model,
                'subtitle' => $subtitle
            );
            $this->display_view('questionnaires/add', $output);
        }
    }

    /**
     * Adds a new topic to a questionnaire or saves it on the database
     */
    public function form_add($model = 0)
    {
        // If temporary tableTopics does not exist, create it
        // Used to store topics and number of questions list
        $model = (!empty($this->input->post('model'))) || $model == 1;
        $temp_table_name = 'temp_tableTopics'.($model ? '_model' : '');
        if (!isset($_SESSION[$temp_table_name]))
        {
            //Create a temporary TableTopics object
            $tableTopics = new TableTopics();
        }
        else
        {
            //Get TableTopics object from session
            $tableTopics = unserialize($_SESSION[$temp_table_name]);
            if(!($tableTopics instanceof TableTopics)) {
                $tableTopics = new TableTopics();
            }
        }

        //Store title if defined
        $tableTopics->setTitle($this->input->post('title'));
        $tableTopics->setModelName($this->input->post('modelName'));
        $tableTopics->setSubtitle($this->input->post('subtitle'));
        
        $title = $tableTopics->getTitle();
        $arrayTopics = $tableTopics->getArrayTopics();
        $arrayNbQuestion = $tableTopics->getArrayNbQuestion();
        $subtitle = $tableTopics->getSubtitle();
        $modelName = $tableTopics->getModelName();

        //If the user want to validate his pdf
        if (isset($_POST['cancel']))
        {
            unset($_SESSION[$temp_table_name]);
            redirect('/Questionnaire');
        }
        elseif (isset($_POST['delete_topic']))
        {
            foreach ($_POST['delete_topic'] as $key => $topic) {
                if($key < 0 || $key >= count($tableTopics->getArrayTopics())) {
                    $this->form_validation->set_rules(
                        "delete_topic[{$key}]", $this->lang->line('delete_topic'),
                        'callback_get_false',
                        $this->lang->line('topic_error_404_heading')
                    );
                } else {
                    $tableTopics->removeArrayTopics($key);
                    $tableTopics->removeArrayNbQuestion($key);
                }
            }
            $this->form_validation->run();
            $arrayTopics = $tableTopics->getArrayTopics();
            $arrayNbQuestion = $tableTopics->getArrayNbQuestion();

            $_SESSION[$temp_table_name] = serialize($tableTopics);
            $this->add($model, NULL, $title, $arrayTopics, $arrayNbQuestion, $subtitle, $modelName);
        }
        elseif (isset($_POST['add_form']))
        {
            $this->form_validation->set_rules('topic_selected', $this->lang->line('add_topic_questionnaire'), 'required|callback_cb_topic_exists');
            $this->form_validation->set_rules('nb_questions', $this->lang->line('nb_questions'), 'required|is_natural_no_zero');
            if($this->form_validation->run() == true)
            {
                //Get last inputs
                $id_topic = $this->input->post('topic_selected');
                $topic = $this->topic_model->get($id_topic);
                $nbQuestions = $this->input->post('nb_questions');

                //Save each elements already seen on the topic list
                //$tableTopics = $this->saveTopicElements($tableTopics, 1);

                $arrayTopics = $tableTopics->getArrayTopics();
                for($i = 0; $i < count($arrayTopics); $i++) {
                    if($arrayTopics[$i]->ID == $topic->ID) {
                        $nbQuestions--;
                        $tableTopics->removeArrayTopics($i);
                    }
                }

                //And take with him the last inputs
                $tableTopics->setArrayTopics($topic);
                $tableTopics->setArrayNbQuestion($nbQuestions);
                $_SESSION[$temp_table_name] = serialize($tableTopics);
            }
            $arrayTopics = $tableTopics->getArrayTopics();
            $arrayNbQuestion = $tableTopics->getArrayNbQuestion();

            $this->add($model, NULL, $title, $arrayTopics, $arrayNbQuestion, $subtitle, $modelName);
        }
        elseif(isset($_POST['save']))
        {
            //Set form validation rules
            $this->form_validation->set_rules('title', 'Title', 'required');
            if($model) $this->form_validation->set_rules('modelName', 'Name', 'required');

            //Check form validation
            if($this->form_validation->run() == true && isset($tableTopics->getArrayTopics()[0]))
            {
                $tableTopics->setTitle($this->input->post('title'));
                $tableTopics->setSubtitle($this->input->post('subtitle'));

                unset($_SESSION[$temp_table_name]);
                if(!$model) $this->generatePDF(-1, $tableTopics);
                else $this->generateModel($tableTopics);

            } else {
                // Form validation error
                $this->add($model, 1, $title, $arrayTopics, $arrayNbQuestion, $subtitle, $modelName);
            }
        } else {
            $this->add($model, NULL, $title, $arrayTopics, $arrayNbQuestion, $subtitle, $modelName);
        }
    }

    /**
     * For codeigniter callback on something already found to be wrong
     *
     * @return boolean
     */
    public function get_false() : bool
    {
        return FALSE;
    }

    /**
     * Loads the id of the questionnaire to create
     * If it does not exist yet, create it from $tableTopics
     *
     * @param integer $idQuestionnaire = The questionnaire's id
     * @param TableTopics $tableTopics = The topics
     */
    public function generatePDF($idQuestionnaire = -1, $tableTopics = NULL) {
        if($idQuestionnaire == -1) {
            if(is_null($tableTopics)) redirect('Questionnaire');
            $idQuestionnaire = $this->InsertNewQuestionnaire($tableTopics);
        }
        if(is_null($this->questionnaire_model->get($idQuestionnaire))) redirect('Questionnaire');

        $this->generateQuestions($idQuestionnaire);
        $this->generateAnswers($idQuestionnaire);

        redirect('Questionnaire');
    }

    /**
     * (Re)generates the PDF for the questions
     *
     * @param integer $idQuestionnaire = The id of the questionnaire to create
     */
    public function generateQuestions($idQuestionnaire) {
        $this->load->model(['multiple_choice_model', 'picture_landmark_model']);

        $listRndQuestions = $this->findQuestionByQuestionnaire($idQuestionnaire);
        $questionnaire = $this->questionnaire_model->get($idQuestionnaire);
        $title = $questionnaire->Questionnaire_Name;
        $subtitle = $questionnaire->Questionnaire_Subtitle;
        $totalpoints = $questionNum = 0;

        $pdf = new PDF();
        $pdf->SetFooterStrings($this->lang->line('pdf_page'), $this->lang->line('pdf_page_of'));
        $pdf->SetFont('Arial', '', self::QUESTIONFONT_SIZE);
        $pdf->SetTitle($title, true);
        // Title of the questions document
        $pdf->AddPage();
        $pdf->SetMargins(20, 20);
        $pdf->Image('assets/images/Logo_Orif_Grand.jpg', NULL, NULL, 60, 20);
        $pdf->Cell(20);
        $pdf->Cell(60, 20, iconv('UTF-8', 'windows-1252', $this->lang->line('pdf_last_name')), 0, "L");
        $pdf->Cell(60, 20, iconv('UTF-8', 'windows-1252', $this->lang->line('pdf_first_name')), 0, "L");
        $pdf->MultiCell(60, 20, iconv('UTF-8', 'windows-1252', $this->lang->line('pdf_date')), 0, "L");
        $pdf->SetFont('Arial', '', self::TITLEFONT_SIZE);
        $pdf->MultiCell(0, 10, iconv('UTF-8', 'windows-1252', $title), 0, "L");
        $pdf->SetFont('Arial', '', self::QUESTIONFONT_SIZE);
        $pdf->MultiCell(0, 10, iconv('UTF-8', 'windows-1252', $subtitle), 0, 'L');
        $pdf->Cell(140);
        $pdf->MultiCell(20, 10, iconv('UTF-8', 'windows-1252', $this->lang->line('pdf_points')), 0, "R");
        $pdf->AliasNbPages();

        foreach($listRndQuestions as $idQuestion) {
            $rndQuestion = $this->question_model->with_deleted()->get($idQuestion);
            $question = iconv('UTF-8', 'windows-1252', $rndQuestion->Question);
            $totalpoints += $rndQuestion->Points;
            $questionNum++;
            // A page is ~300 high, so create a new one after 225
            $maxHeight = 225;
            $amount = 0;
            // Lower the bound for landmarks so the answers and image are on a single page
            switch($rndQuestion->FK_Question_Type) {
                case 1:
                    $amount = $this->multiple_choice_model->count_by("FK_Question = ".$rndQuestion->ID)-2;
                    break;
                case 2:
                    $amount = ceil($rndQuestion->Nb_Desired_Answers / 2)-2;
                    break;
                case 7:
                    $amount = max($this->picture_landmark_model->count_by("FK_Question = ".$rndQuestion->ID)+2, 10);
                    break;
                default:
            }

            if($amount > 0) $maxHeight -= $amount*10;
            if($pdf->GetY() > $maxHeight) {
                $pdf->addPage();
            }

            $stringLines = $this->string_to_small_array($rndQuestion->Question);

            // Display question header
            if(count($stringLines) == 1)    //Keep line spacing large enough between the question and the answer line
            {
                $pdf->SetFont('', 'B');
                $pdf->Cell(10, 7, $questionNum.'.');
                $pdf->SetFont('', '');
                $pdf->Cell(139, 7, iconv('UTF-8', 'windows-1252', $stringLines[0]));
            } else 
            {
                $pdf->SetFont('', 'B');
                $pdf->Cell(10, 10, $questionNum.'.');
                $pdf->SetFont('', '');
                $pdf->Cell(139, 10, iconv('UTF-8', 'windows-1252', $stringLines[0]));
            }
            $pdf->MultiCell(20, 7, '.../'.$rndQuestion->Points);
            for($i = 1; $i < count($stringLines); $i++) {
                $pdf->Cell(10, 10);
                $pdf->Cell(140, 10, iconv('UTF-8', 'windows-1252', $stringLines[$i]));
                
                //Avoid the answer line to be closer of the question
                if((count($stringLines) - 1) == $i)
                {
                    $pdf->MultiCell(10, 10, '');
                } else 
                {
                   $pdf->MultiCell(10, 7, '');
                }
            }
            switch ($rndQuestion->FK_Question_Type) {
                case 1:
                    $this->multipleChoicesPdf($rndQuestion, $pdf);
                    break;
                case 2:
                    $this->multipleAnswersPdf($rndQuestion, $pdf);
                    break;
                case 3: // /!\ The function is incomplete /!\ \\
                    $this->answerDistributionPdf($rndQuestion, $pdf);
                    break;
                case 4:
                    $this->clozeTextPdf($rndQuestion, $pdf);
                    break;
                case 5: // /!\ The function is incomplete /!\ \\
                    $this->tablePdf($rndQuestion, $pdf);
                    break;
                case 6:
                    $this->simpleQuestionPdf($rndQuestion, $pdf);
                    break;
                case 7:
                    $this->pictureLandmarkPdf($rndQuestion, $pdf);
                    break;
                default:
                    break;
            }
            $pdf->Ln();
        }
        if($pdf->GetY() > 225) {
            $pdf->addPage();
        }
        // Finish last page
        $pdf->SetTextColor(128,128,128);
        $pdf->SetFont('Arial','', self::TITLEFONT_SIZE);
        $pdf->Cell(30); // Padding
        $pdf->Cell(90, 20, iconv('UTF-8', 'windows-1252', $this->lang->line('pdf_totals_points').' .../'.$totalpoints), 0, "R");
        $pdf->Cell(90, 20, iconv('UTF-8', 'windows-1252', $this->lang->line('pdf_note')), 0, "R");
        $pdf->Ln();
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('Arial','', self::QUESTIONFONT_SIZE);

        $questionnaire = $this->questionnaire_model->get($idQuestionnaire);
        $pdf->Output('F', "pdf_files/questionnaires/" . $questionnaire->PDF, true);
    }

    /**
     * (Re)generates the PDF for the answers
     *
     * @param integer $idQuestionnaire = The id of the questionnaire to create
     */
    public function generateAnswers($idQuestionnaire) {
        $this->load->model(['multiple_choice_model', 'picture_landmark_model']);

        $listRndQuestions = $this->findQuestionByQuestionnaire($idQuestionnaire);
        $questionnaire = $this->questionnaire_model->get($idQuestionnaire);
        $title = $questionnaire->Questionnaire_Name;
        $subtitle = $questionnaire->Questionnaire_Subtitle;
        $totalpoints = $questionNum = 0;

        $answers = new PDF();
        $answers->SetFooterStrings($this->lang->line('pdf_page'), $this->lang->line('pdf_page_of'));
        $answers->SetFont('Arial', '', self::QUESTIONFONT_SIZE);
        $answers->SetTitle($title.' '.$this->lang->line('pdf_corrige'), true);
        // Title of the questions document
        $answers->AddPage();
        $answers->SetMargins(20, 20);
        $answers->Image('assets/images/Logo_Orif_Grand.jpg', NULL, NULL, 60, 20);
        $answers->Cell(20);
        $answers->Cell(60, 20, iconv('UTF-8', 'windows-1252', $this->lang->line('pdf_last_name')), 0, "L");
        $answers->Cell(60, 20, iconv('UTF-8', 'windows-1252', $this->lang->line('pdf_first_name')), 0, "L");
        $answers->MultiCell(60, 20, iconv('UTF-8', 'windows-1252', $this->lang->line('pdf_date')), 0, "L");
        $answers->SetFont('Arial', '', self::TITLEFONT_SIZE);
        $answers->MultiCell(0, 10, iconv('UTF-8', 'windows-1252', $title.' '.$this->lang->line('pdf_corrige')), 0, "L");
        $answers->SetFont('Arial', '', self::QUESTIONFONT_SIZE);
        $answers->Cell(140);
        $answers->MultiCell(20, 10, iconv('UTF-8', 'windows-1252', $this->lang->line('pdf_points')), 0, "R");
        $answers->AliasNbPages();
        foreach($listRndQuestions as $idQuestion) {
            $rndQuestion = $this->question_model->with_deleted()->get($idQuestion);
            $question = iconv('UTF-8', 'windows-1252', $rndQuestion->Question);
            $totalpoints += $rndQuestion->Points;
            $questionNum++;
            // A page is ~300 high, so create a new one after 225
            $maxHeight = 225;
            $amount = 0;
            // Lower the bound for landmarks so the answers and image are on a single page
            switch($rndQuestion->FK_Question_Type) {
                case 1:
                    $amount = $this->multiple_choice_model->count_by("FK_Question = ".$rndQuestion->ID)-2;
                    break;
                case 2:
                    $amount = ceil($rndQuestion->Nb_Desired_Answers / 2)-2;
                    break;
                case 7:
                    $amount = max($this->picture_landmark_model->count_by("FK_Question = ".$rndQuestion->ID)+2, 10);
                    break;
                default:
            }
            if($amount > 0) $maxHeight -= $amount*10;
            if($answers->GetY() > $maxHeight) {
                $answers->addPage();
            }
            // Display question header
            $stringLines = $this->string_to_small_array($rndQuestion->Question);
            $answers->SetFont('', 'B');
            $answers->Cell(10, 10, $questionNum.'.');
            $answers->SetFont('', '');
            $answers->Cell(139, 10, iconv('UTF-8', 'windows-1252', $stringLines[0]));
            $answers->MultiCell(20, 7, '.../'.$rndQuestion->Points);
            for($i = 1; $i < count($stringLines); $i++) {
                $answers->Cell(10, 10);
                $answers->Cell(140, 10, iconv('UTF-8', 'windows-1252', $stringLines[$i]));
                $answers->MultiCell(10, 10, '');
            }
            switch ($rndQuestion->FK_Question_Type) {
                case 1:
                    $this->multipleChoicesPdf($rndQuestion, $answers, true);
                    break;
                case 2:
                    $this->multipleAnswersPdf($rndQuestion, $answers, true);
                    break;
                case 3: // /!\ The function is incomplete /!\ \\
                    $this->answerDistributionPdf($rndQuestion, $answers, true);
                    break;
                case 4:
                    $this->clozeTextPdf($rndQuestion, $answers, true);
                    break;
                case 5: // /!\ The function is incomplete /!\ \\
                    $this->tablePdf($rndQuestion, $answers, true);
                    break;
                case 6:
                    $this->simpleQuestionPdf($rndQuestion, $answers, true);
                    break;
                case 7:
                    $this->pictureLandmarkPdf($rndQuestion, $answers, true);
                    break;
                default:
                    break;
            }
            $answers->Ln();
        }
        // Finish last page
        $answers->SetTextColor(128,128,128);
        $answers->SetFont('Arial','', self::TITLEFONT_SIZE);
        $answers->Cell(30); // Padding
        $answers->Cell(90, 20, iconv('UTF-8', 'windows-1252', $this->lang->line('pdf_totals_points').' .../'.$totalpoints), 0, "R");
        $answers->Cell(90, 20, iconv('UTF-8', 'windows-1252', $this->lang->line('pdf_note')), 0, "R");
        $answers->Ln();
        $answers->SetTextColor(0,0,0);
        $answers->SetFont('Arial','', self::QUESTIONFONT_SIZE);

        $questionnaire = $this->questionnaire_model->get($idQuestionnaire);
        $answers->Output('F', "pdf_files/corriges/" . $questionnaire->Corrige_PDF, true);
        redirect('Questionnaire');
    }

    /**
     * Adds a new model to the database
     *
     * @param TableTopics $tableTopics = The object containing all the data
     */
    public function generateModel($tableTopics = NULL) {
        if(!is_null($tableTopics)) { // Prevent users from going in here
            $nbQuestions = $tableTopics->getArrayNbQuestion();
            $arrayTopics = $tableTopics->getArrayTopics();
            $modelName = $tableTopics->getModelName();
            $title = $tableTopics->getTitle();
            $subtitle = $tableTopics->getSubtitle();
            $newQuestionnaireModel = array(
                'Base_Name' => $modelName,
                'Questionnaire_Name' => $title,
                'Questionnaire_Subtitle' => $subtitle
            );
            $idQuestionnaireModel = $this->questionnaire_model_model->insert($newQuestionnaireModel);
            $count = count($nbQuestions);
            for($i = 0; $i < $count; $i++) {
                $newQuestionnaireModelTopic = array(
                    'FK_Questionnaire_Model' => $idQuestionnaireModel,
                    'FK_Topic' => $arrayTopics[$i]->ID,
                    'Nb_Topic_Questions' => $nbQuestions[$i]
                );
                $this->questionnaire_model_topic_model->insert($newQuestionnaireModelTopic);
            }
        }
        redirect('Questionnaire');
    }

    /**
     * Displays a page to modify a last time the pdf title and subtitle
     *
     * @param integer $modelId = The id of the model to generate
     */
    public function generate_pdf_from_model($modelId) {
        $questionnaireModel = $this->questionnaire_model_model->get($modelId);
        $output = array(
            'model' => $questionnaireModel
        );
        $this->display_view('questionnaires/generate', $output);
    }

    /**
     * Generates a pdf and answer sheet based on the questionnaire model
     * Actually just prepares a TableTopics object before passing it to generatePDF()
     */
    public function model_generate_pdf() {
        $modelId = $this->input->post('modelId');

        $this->form_validation->set_rules('title', $this->lang->line('add_title_questionnaire'), 'required');
        $this->form_validation->set_rules('subtitle', $this->lang->line('add_subtitle_questionnaire'));

        if($this->form_validation->run()) {
            $title = $this->input->post('title');
            $subtitle = $this->input->post('subtitle');
            $questmodtops = $this->questionnaire_model_topic_model->get_many_by('FK_Questionnaire_Model = '.$modelId);

            $tableTopics = new TableTopics();
            $tableTopics->setTitle($title);
            $tableTopics->setSubtitle($subtitle);
            foreach($questmodtops as $questmodtop) {
                $topic = $this->topic_model->get($questmodtop->FK_Topic);
                $tableTopics->setArrayTopics($topic);
                $tableTopics->setArrayNbQuestion($questmodtop->Nb_Topic_Questions);
            }
            $this->generatePDF(-1, $tableTopics);
        } else {
            $this->generate_pdf_from_model($modelId);
        }
    }

    /**
     * Deletes a questionnaire model
     *
     * @param integer $modelId = The id of the questionnaire model to delete
     * @param any $action = Keep NULL to show confirmation, otherwise delete
     */
    public function model_delete($modelId, $action = NULL) {
        $questionnaireModel = $this->questionnaire_model_model->get($modelId);
        if(is_null($questionnaireModel)) redirect('Questionnaire');
        if(is_null($action)) {
            $output = get_object_vars($questionnaireModel);
            $output['model'] = true;
            $this->display_view('questionnaires/delete', $output);
        } else {
            $questmodtops = $this->questionnaire_model_topic_model->get_many_by('FK_Questionnaire_Model = '.$modelId);
            foreach($questmodtops as $questmodtop) {
                $this->questionnaire_model_topic_model->delete($questmodtop->ID);
            }
            $this->questionnaire_model_model->delete($modelId);
            redirect('Questionnaire');
        }
    }

    /**
     * Checks that provided topic id exists in the database
     *
     * @param int $idTopic = ID of the topic
     * @return boolean = Whether or not it exists
     */
    public function cb_topic_exists($idTopic) : bool
    {
        return !is_null($this->topic_model->get($idTopic));
    }

    /**
     * Creates a new questionnaire in pdf format
     *
     * @param TableTopics $tableTopics = The questionnaire topics
     * @return integer = The id of the newly added questionnaire
     */
    private function InsertNewQuestionnaire($tableTopics)
    {
        $this->load->model('question_questionnaire_model');

        // Prepare the questionnaire name and save the original
        $title_original = trim($tableTopics->getTitle());

        // Replace all non alphanum characters with dashes
        // This would only fail on less than 1% of all operating systems
        $title = preg_replace('/[^a-z0-9]+/', '-', strtolower($title_original));

        // In case the questionnaire's pdf already exists, change the file's name
        if(file_exists("pdf_files/questionnaires/".$title.".pdf") || file_exists("pdf_files/corriges/".$title."_corrige.pdf")) {
            $i = 1;
            while(file_exists("pdf_files/questionnaires/".$title.'-'.$i.".pdf") || file_exists("pdf_files/corriges/".$title.'-'.$i."_corrige.pdf")){
                $i++;
            }
            $title .= '-'.$i;
        }

        // Insert new questionnaire
        $newQuestionnaire = array(
            "Questionnaire_Name" => $title_original,
            "Questionnaire_Subtitle" => $tableTopics->getSubtitle(),
            "PDF" => $title.".pdf",
            "Corrige_PDF" => $title."_corrige.pdf"
        );

        $idQuestionnaire = $this->questionnaire_model->insert($newQuestionnaire);

        //Insert each question about the questionnaire
        $arrayNbQuestion = $tableTopics->getArrayNbQuestion();
        $arrayTopics = $tableTopics->getArrayTopics();
        for ($index = 0; $index < count($arrayTopics); $index++)
        {
            //Get ID for each topic asked
            $nbQuestion = $arrayNbQuestion[$index];
            $idTopic = $arrayTopics[$index]->ID;

            //load randoms questions about the topic
            $rndQuestions = $this->question_model->getRNDQuestions($idTopic, $nbQuestion);

            foreach($rndQuestions as $rndQuestion) {
                $row = array(
                    'FK_Question' => $rndQuestion['ID'],
                    "FK_Questionnaire" => $idQuestionnaire
                );
                $this->question_questionnaire_model->insert($row);
            }
        }

        return $idQuestionnaire;
    }

    /**
     * Obtains all the questions in a questionnaire
     *
     * @param integer $idQuestionnaire = The questionnaire's id
     */
    private function findQuestionByQuestionnaire($idQuestionnaire){
        $this->load->model('question_questionnaire_model');

        $listIDQuestions = array();
        $rndQuestions = array();

        //load randoms questions about the topic
        $rndQuestions = $this->question_questionnaire_model->get_many_by("FK_Questionnaire  = '" .  $idQuestionnaire . "'");

        foreach($rndQuestions as $rndQuestion) {
            $listIDQuestions[] = $rndQuestion->FK_Question;
        }

        return $listIDQuestions;
    }

    /**
     * Adds a multiple choices question to a questionnaire's pdf
     *
     * @param Question $question = The question to add
     * @param FPDF $pdf = The pdf to update
     * @param boolean $answerPdf = Whether or not the pdf is for correction
     */
    private function multipleChoicesPdf($question, $pdf, $answerPdf = FALSE) {
        $this->load->model('multiple_choice_model');
        // Prepare variables
        $multiChoiceQuestions = $this->multiple_choice_model->get_many_by("FK_Question = ".$question->ID);

        foreach($multiChoiceQuestions as $question) {
            $answer = $question->Answer;
            if($answer === '1')
                $answer = $this->lang->line('yes');
            elseif($answer === '0')
                $answer = $this->lang->line('no');
            $stringLines = $this->string_to_small_array($answer);

            for($i = 0; $i < count($stringLines); $i++) {
                // Padding / Left box
                $pdf->Cell(10, 10);
                $y = $pdf->Gety();
                $x = $pdf->GetX();

                // Place the checkbox
                $pdf->SetXY($x + 2.5, $y + 2.5);
                $cross = ($answerPdf && $question->Valid ? "x" : " ");
                if($answerPdf) $pdf->SetTextColor(255, 0, 0);
                $pdf->Cell(5, 5, $cross, 1);
                $pdf->SetTextColor(0, 0, 0);

                // Place the question
                $pdf->SetXY($x, $y);
                $pdf->Cell(10, 10);
                $pdf->Cell(140, 10, iconv('UTF-8', 'windows-1252', $stringLines[$i]));
                $pdf->Ln();
            }
        }
    }

    /**
     * Adds a multiple answers question to a questionnaire's pdf
     *
     * @param Question $question = The question to add
     * @param FPDF $pdf = The pdf to update
     * @param boolean $answerPdf = Whether or not the pdf is for correction
     */
    private function multipleAnswersPdf($question, $pdf, $answerPdf = FALSE) {
        if($answerPdf) {
            $this->load->model('multiple_answer_model');

            $pdf->SetTextColor(255, 0, 0);
            $possible_answers = $this->multiple_answer_model->get_many_by("FK_Question = ".$question->ID);
            $answer = '';
            foreach($possible_answers as $possible_answer) {
                $answer .= ' / '.$possible_answer->Answer;
            }
            $answer = substr($answer, 2);
            $answers = $this->string_to_small_array($answer);
            for($i = 0; $i < count($answers); $i++) {
                $pdf->Cell(10, 10);
                // Write the answer
                $pdf->MultiCell(150, 10, iconv('UTF-8', 'windows-1252', $answers[$i]));
            }
        } else {
            $pdf->Cell(10, 10);
            $nbAskedAnswers = $question->Nb_Desired_Answers;
            $pdf->MultiCell(150, 10, iconv('UTF-8', 'windows-1252', '('.$nbAskedAnswers.' '.$this->lang->line('pdf_multiple_answer_1').($nbAskedAnswers > 1 ? 's' : '').' '.$this->lang->line('pdf_multiple_answer_2').($nbAskedAnswers > 1 ? 's' : '').')'));
            for($i = 0; $i < $nbAskedAnswers; $i++) {
                $pdf->Cell(10, 10);
                // Place the line to answer
                $pdf->MultiCell(150, 10, str_repeat('_', 60));
            }
        }
        $pdf->SetTextColor(0, 0, 0);
    }

    /**
     * @todo The logic for the answer part
     *
     * Adds a distribution question to a questionnaire's pdf
     *
     * @param Question $question = The question to add
     * @param FPDF $pdf = The pdf to update
     * @param boolean $answerPdf = Whether or not the pdf is for correction
     */
    private function answerDistributionPdf($question, $pdf, $answerPdf = FALSE) {
        $this->load->model('answer_distribution_model');

        if($answerPdf) {
            /**
             * @todo
             * The function did not exist,
             * there are no distribution questions and
             * the page to create them has yet to be coded
             */
        } else {
            $anDistributionQuestions = $this->answer_distribution_model->get_many_by("FK_Question = $question->ID");

            foreach ($anDistributionQuestions as $anDistributionQuestion) {
                $pdf->Cell(80,20,iconv('UTF-8', 'windows-1252',$anDistributionQuestion->Question_Part), 1, "C");
                $pdf->Cell(20,20, "", 0, "C");
                $pdf->MultiCell(80,20, "", 1, "C");
            }
        }
        $pdf->SetTextColor(0, 0, 0);
    }

    /**
     * Adds a cloze text question to a questionnaire's pdf
     *
     * @param Question $question = The question to add
     * @param FPDF $pdf = The pdf to update
     * @param boolean $answerPdf = Whether or not the pdf is for correction
     */
    private function clozeTextPdf($question, $pdf, $answerPdf = FALSE) {
        $this->load->model('cloze_text_model');

        $clozeText = $this->cloze_text_model->get_by("FK_Question = ".$question->ID);
        // Sometimes you can find … mixed with .
        $outputText = preg_split("/\[[…\.]*\]/", $clozeText->Cloze_Text, -1, PREG_SPLIT_DELIM_CAPTURE);
        $result = '';
        $nbrAnswer = count($outputText)-1;
        // How the question is written, after processing
        foreach ($outputText as $key => $value) {
            $result .= $value . '[ '.($key+1).' ]';
        }
        // Remove the last [number] from the result
        $lastC = strrpos($result, '[');
        $result = substr($result, 0, $lastC);
        // Keep [ x ] on the same line
        $stringLines = $this->string_to_small_array($result, '/ (?!\?|(\[ [0-9] \])|([0-9])|\])/');
        // Display the question
        $pdf->MultiCell(10, 1, '');
        for($i = 0; $i < count($stringLines); $i++) {
            $pdf->Cell(10, 10);
            $pdf->Cell(140, 10, iconv('UTF-8', 'windows-1252', $stringLines[$i]));

            //Avoid the answer line to be closer of the question
            if((count($stringLines) - 1) == $i)
            {
                $pdf->MultiCell(10, 10, '');
            } else 
            {
                $pdf->MultiCell(10, 7, '');
            }
        }

        if($answerPdf) {
            $this->load->model('cloze_text_answer_model');

            $allAnswers = $this->cloze_text_answer_model->get_many_by("FK_Cloze_Text = ".$clozeText->ID);
            $answers = array();
            foreach($allAnswers as $a) {
                $answers[] = $this->string_to_small_array($a->Answer, NULL, 30);
            }

            $pdf->SetTextColor(255, 0, 0);
            for($i = 1; $i <= count($answers); $i++) {
                $pdf->Cell(10, 10);
                // Write the answer
                $pdf->Cell(75, 10, iconv('UTF-8', 'windows-1252', '[ '.$i.' ] '.$answers[$i-1][0]));
                $i++;
                // Write the second answer if it exists
                $pdf->MultiCell(75, 10, ($i > count($answers) ? '' : iconv('UTF-8', 'windows-1252', '[ '.$i.' ] '.$answers[$i-1][0])));
            }
        } else {
            // Create a short line for answering
            $line = str_repeat('_', 20);

            for($i = 1; $i <= $nbrAnswer; $i++) {
                $pdf->Cell(10, 10);
                // Display the first line
                $pdf->Cell(75, 10, iconv('UTF-8', 'windows-1252', "[ $i ] $line"));
                $i++;
                // Display the second line if it exists
                $pdf->MultiCell(75, 10, ($i > $nbrAnswer ? '' : "[ $i ] $line"));
            }
        }
        $pdf->SetTextColor(0, 0, 0);
    }

    /**
     * @todo The logic for the answer part
     *
     * Adds a table question to a questionnaire's pdf
     * Might work as expected
     *
     * @param Question $question = The question to add
     * @param FPDF $pdf = The pdf to update
     * @param boolean $answerPdf = Whether or not the pdf is for correction
     */
    private function tablePdf($question, $pdf, $answerPdf = FALSE) {
        $this->load->model('table_cell_model');
        if($answerPdf) {
            /**
             * @todo
             * The function did not exist,
             * there are no table questions and
             * the page to create them has yet to be coded
             */
        } else {
            $tcCol = $tcRow = $tableCells = $this->table_cell_model->get_many_by("FK_Question = ".$question->ID);

            // Sort tableCells by amount of columns
            usort($tcCol, function($a, $b) {
                return $a->Column_Nb <=> $b->Column_Nb;
            });
            // Sort tableCells by amount of rows
            usort($tcRow, function($a, $b) {
                return $a->Row_Nb <=> $b->Row_Nb;
            });

            foreach($tcCol as $col) {
                foreach($tcRow as $row) {
                    if($row->Header) {
                        $pdf->SetFont('Arial', 'B', self::TITLEFONT_SIZE);
                        $pdf->Cell(40, 10, iconv('UTF-8', 'windows-1252', $row->Content), 1, "C");
                        $pdf->SetFont('Arial', '', self::TITLEFONT_SIZE);
                    } elseif($row->Display_In_Question)
                        $pdf->Cell(40, 10, iconv('UTF-8', 'windows-1252', $row->Content), 1, "C");
                    else
                        $pdf->Cell(40, 10, '', 1, "C");
                }
                // New line at the end of the table
                $pdf->Ln();
            }
        }
        $pdf->SetTextColor(0, 0, 0);
    }

    /**
     * @deprecated use tablePdf($Question, $pdf, false)
     * This is kept as a reference in case there is a table question that appears
     *
     * I don't recommend looking at this one
     */
    private function displayTable($Question, $pdf)
    {
        $this->load->model('table_cell_model');
        $tableCells = $this->table_cell_model->get_many_by("FK_Question = ".$Question->ID);
        $maxColumn = 0;
        $maxRow = 0;

        for($i = 0;$i < count($tableCells); $i++)
        {
            if($maxColumn < $tableCells[$i]->Column_Nb) $maxColumn = $tableCells[$i]->Column_Nb;
            if($maxRow < $tableCells[$i]->Row_Nb) $maxRow = $tableCells[$i]->Row_Nb;
        }

        for($j = 1;$j <= $maxColumn; $j++)
        {
            foreach($tableCells as $tableCellColumn)
            {
                if($tableCellColumn->Column_Nb == $j)
                {
                    for($j = 1;$j <= $maxRow; $j++)
                    {
                        foreach($tableCells as $tableCellRow)
                        {
                            if($tableCellRow->Row_Nb == $j)
                            {
                                if($tableCellRow->Header)
                                {
                                    $pdf->SetFont('Arial','B', self::TITLEFONT_SIZE);
                                    if($tableCellRow->Column_Nb < $maxColumn)
                                    {
                                        $pdf->Cell(40,10, iconv('UTF-8', 'windows-1252',$tableCellRow->Content), 1, "C");
                                    }else
                                    {
                                        $pdf->MultiCell(40,10, iconv('UTF-8', 'windows-1252',$tableCellRow->Content), 1, "C");
                                    }
                                    $pdf->SetFont('Arial', '', self::TITLEFONT_SIZE);
                                }
                                else if($tableCellRow->Display_In_Question)
                                {
                                    if($tableCellRow->Column_Nb < $maxColumn)
                                    {
                                        $pdf->Cell(40,10, iconv('UTF-8', 'windows-1252',$tableCellRow->Content), 1, "C");
                                    }else
                                    {
                                        $pdf->MultiCell(40,10, iconv('UTF-8', 'windows-1252',$tableCellRow->Content), 1, "C");
                                    }
                                }else
                                {
                                    if($tableCellRow->Column_Nb < $maxColumn)
                                    {
                                        $pdf->Cell(40,10, '', 1, "C");
                                    }else
                                    {
                                        $pdf->MultiCell(40,10, '', 1, "C");
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Adds a text question to a questionnaire's pdf
     *
     * @param string $answer = The answer of the question
     * @param FPDF $pdf = The pdf to update
     * @param boolean $answerPdf = Whether or not the pdf is for correction
     */
    private function simpleQuestionPdf($question, $pdf, $answerPdf = FALSE) {
        $this->load->model('free_answer_model');
        // Prepare variables
        $maxlength = 60;
        $answer = $this->free_answer_model->get_by('FK_Question', $question->ID)->Answer;
        $numberOfLines = 1;

        if(strlen($answer) > 50 && strlen($answer) < 100)
        {
            $numberOfLines = 2;
        } else if(strlen($answer) >= 100)
        {
            $numberOfLines = 3;
        }

        if($answerPdf) {
            $stringLines = $this->string_to_small_array($answer);
            $pdf->SetTextColor(255, 0, 0);
            for($i = 0; $i < count($stringLines); $i++) {
                $pdf->Cell(10, 10);
                // Write each answer
                $pdf->Cell(150, 10, iconv('UTF-8', 'windows-1252', $stringLines[$i]));
                $pdf->Ln();
            }
            $pdf->SetTextColor(0, 0, 0);
        } else {
            $line = str_repeat("_", $maxlength);
            for($i = 0; $i < $numberOfLines; $i++) {
                $pdf->Cell(10, 10);
                // Display each line for answering
                $pdf->MultiCell(150, 10, $line);
            }
        }
    }

    /**
     * Adds a picture landmark question to a questionnaire's pdf
     *
     * @param Question $question = The question to add
     * @param FPDF $pdf = The pdf to update
     * @param boolean $answerPdf = Whether or not the pdf is for correction
     */
    private function pictureLandmarkPdf($question, $pdf, $answerPdf = FALSE) {
        $this->load->model('picture_landmark_model');

        $pictureLandmarks = $this->picture_landmark_model->with_all()->get_many_by("FK_Question = ".$question->ID);
        $picture = $question->Picture_Name;
        $fullPath = base_url((is_file('uploads/pictures/'.$picture) ? 'uploads/pictures/'.$picture : 'assets/images/not-found.png'));
        $size = getimagesize($fullPath);
        $size = (["width" => $size[0], "height" => $size[1]]);

        // Image should not be more than 80 pixels tall
        // To prevent overflowing onto another page
        if($size['height'] > 80) {
            $size['width'] /= ($size['height'] / 80);
            $size['height'] = 80;
        }
        // Prevents image from being more than half of the available width
        if($size["width"] > 80) {
            $size["height"] /= ($size["width"] / 80);
            $size["width"] = 80;
        }

        // Inserts image and then returns to point before
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->Image($fullPath, NULL, NULL, $size['width'], $size['height']);
        $pdf->SetXY($x, $y);

        // Counts the amount of lines to add,
        // in case not all of them are added,
        // they will be later on
        $i_max = max(count($pictureLandmarks), round($size['height'], -1)/10);
        $pdf->Cell(10, $i_max*10);
        $pdf->Cell($size['width'], $i_max*10);
        $x = $pdf->GetX();
        $i = 0;
        foreach($pictureLandmarks as $pictureLandmark) {
            $text = $pictureLandmark->Symbol.'. ';
            // Write answer / line depending on the type of sheet we are writing
            if($answerPdf) {
                $text .= iconv('UTF-8', 'windows-1252', $pictureLandmark->Answer);
                $pdf->SetTextColor(255, 0, 0);
            } else {
                $text .= str_repeat('_', 20);
            }
            $pdf->MultiCell(150-$size['width'], 10, $text);
            // Go back to the left side of answers
            $pdf->SetX($x);
            $i++;
        }
        // Go back a last time and write the last chunk
        $pdf->MultiCell(150-$size['width'], 10*($i_max-$i), '');
        $pdf->SetTextColor(0, 0, 0);
    }

    /**
     * Returns a string cut in smaller pieces
     *
     * @param string $string = The string to cut
     * @param string $pattern = The pattern to use to cut the string
     * @param integer $max_string_length = The maximum length of the string
     * @return array = An array containing the string in pieces
     */
    private function string_to_small_array(string $string, ?string $pattern = '/ (?!\?)/', int $max_string_length = 60) {
        // In case only the max_string_length is changed
        $pattern = $pattern ?: '/ (?!\?)/';
        // Stores the different lines
        $stringLines = [];
        // Stores the "words"
        $stringSplit = preg_split($pattern, $string);
        // Current index in $stringLines
        $stringIndex = 0;
        for($i = 0; $i < count($stringSplit); $i++) {
            // Reassign variable for simpler usage
            $s = $stringSplit[$i];
            if(empty($stringLines[$stringIndex]))
                // Empty line? Not anymore
                $stringLines[$stringIndex] = $s;
            elseif(strlen($stringLines[$stringIndex]) < $max_string_length)
                // Add word to line which has space
                $stringLines[$stringIndex] .= ' '.$s;
            else
                // New line with the word
                $stringLines[++$stringIndex] = $s;
        }
        return $stringLines;
    }
}
