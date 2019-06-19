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
    protected $access_level = ACCESS_LVL_ADMIN;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('question_questionnaire_model', 'questionnaire_model', 'topic_model',
            'question_model', 'answer_distribution_model', 'multiple_choice_model',
            'cloze_text_model', 'cloze_text_answer_model', 'table_cell_model',
            'picture_landmark_model', 'free_answer_model', 'multiple_answer_model'));
        $this->load->helper(array('url', 'form'));
        $this->load->library(array('TableTopics', 'form_validation', 'fpdf181/Fpdf', 'upload'));
    }

    /**
     * Display questionnaire list
     */
    public function index($error = "")
    {
        $outputs = array(
            'questionnaires' => $this->questionnaire_model->get_all(),
            'error' => $error
        );
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
    public function update($id = 0, $error = FALSE)
    {
        if ($id != 0) {
            $outputs = array(
                'error' => $error,
                'id' => $id
            );
            $this->display_view("questionnaires/update", $outputs);
        } else {
            redirect('Questionnaire');
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
            redirect('Questionnaire');
        } else {
            $this->update($id, true);
        }
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
                $output["questionnaires"] = $this->questionnaire_model->get_all();
                $this->display_view("questionnaires/delete", $output);
            } else {
                $questionnaire = $this->questionnaire_model->with("question_questionnaires")->get($id);
                if (count($questionnaire->question_questionnaires) > 0) {
                    foreach ($questionnaire->question_questionnaires as $question_questionnaire) {
                        $this->question_questionnaire_model->delete($question_questionnaire->ID);
                    }
                }
                $this->questionnaire_model->delete($id);
                redirect('Questionnaire');
            }
        } else {
            redirect('Questionnaire');
        }
    }

    /**
     * To add a new questionnaire
     * @param int $error
     * @param string $title
     * @param array $topics
     * @param array $nbQuestions
     */
    public function add($error = NULL, $title = '', $topics = array(), $nbQuestions = array())
    {
        $output = array(
            'error' => ($error == NULL ? NULL : true),
            'topicsList' => $this->topic_model->get_all(),
            'title' => $title,
            'topics' => $topics,
            'nbQuestions' => $nbQuestions
        );

        // Removing this will make it so that you can't set the amount of questions
        if(isset($_POST['topic'])) {
            $Topic = urldecode($_POST['topic']);
            $Topic = str_replace("_apostrophe_", "\'", $Topic);
            $idTopic = $this->topic_model->get_by("ID = '" .  $Topic . "'")->ID;
            $nbQuestion = $this->question_model->getNbQuestionByTopic($idTopic);
            echo $nbQuestion;
        } else {
            $this->display_view('questionnaires/add', $output);
        }
    }

    /**
     * Adds a new topic to a questionnaire or saves it on the database
     */
    public function form_add()
    {
        // If temporary tableTopics does not exist, create it
        // Used to store topics and number of questions list
        if (!isset($_SESSION['temp_tableTopics']))
        {
            //Create a temporary TableTopics object
            $tableTopics = new TableTopics();
        }
        else
        {
            //Get TableTopics object from session
            $tableTopics = unserialize($_SESSION['temp_tableTopics']);
        }

        //Store title if defined
        $tableTopics->setTitle($this->input->post('title'));

        //If the user want to validate his pdf
        if (!isset($_POST[$this->lang->line('save')]))
        {
            $this->form_validation->set_rules('topic_selected', $this->lang->line('add_topic_questionnaire'), 'required');
            $this->form_validation->set_rules('nb_questions', $this->lang->line('nb_questions'), 'required');
            if($this->form_validation->run() == true)
            {
                //Get last inputs
                $id_topic = $this->input->post('topic_selected');
                $topic = $this->topic_model->get($id_topic);
                $nbQuestions = $this->input->post('nb_questions');

                //Save each elements already seen on the topic list
                //$tableTopics = $this->saveTopicElements($tableTopics, 1);

                //And take with him the last inputs
                $tableTopics->setArrayTopics($topic);
                $tableTopics->setArrayNbQuestion($nbQuestions);
                $_SESSION['temp_tableTopics'] = serialize($tableTopics);
            }
            $this->add(NULL, $tableTopics->getTitle(), $tableTopics->getArrayTopics(), $tableTopics->getArrayNbQuestion());
        }
        else
        {
            //Set form validation rules
            $this->form_validation->set_rules('title', 'Title', 'required');

            //Check form validation
            if($this->form_validation->run() == true && isset($tableTopics->getArrayTopics()[0]))
            {
                $tableTopics->setTitle($this->input->post('title'));
                unset($_SESSION['temp_tableTopics']);
                $this->generatePDF(-1, $tableTopics);
            } else {
                // Form validation error
                $this->add(1);
            }
        }
    }

    /**
     * Generates a PDF from a questionnaire
     *
     * @param integer $idQuestionnaire = The questionnaire's id
     * @param TableTopics $tableTopics = The topics
     */
    public function generatePDF($idQuestionnaire = -1, $tableTopics = NULL)
    {
        if($idQuestionnaire == -1){
            // Prevent calling the function without any argument
            if(is_null($tableTopics)) redirect('Questionnaire');
            $listRndQuestions = $this->InsertNewQuestionnaire($tableTopics);
        } else {
            $listRndQuestions = $this->findQuestionByQuestionnaire($idQuestionnaire);
        }

        $totalpoints = 0;

        $pdf = new FPDF();
        $answers = new FPDF();

        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', self::TITLEFONT_SIZE);
        $pdf->SetTitle("Questionnaire", true);
        $pdf->MultiCell(0, 20, 'Questionnaire', 1, "C");
        $pdf->SetFont('Arial', '', self::QUESTIONFONT_SIZE);
        $pdf->Cell(80, 10, "Nom :", 0, "L");
        $pdf->Cell(80, 10, "Date :", 0, "C");
        $pdf->Ln();
        $pdf->MultiCell(80, 10, iconv('UTF-8', 'windows-1252', 'Prénom : '), 0, "L");
        $pdf->Ln();


        // Title of the answered document
        $answers->AddPage();
        $answers->SetFont('Arial', 'B',  self::TITLEFONT_SIZE);
        $answers->SetTitle('Corrigé', true);
        $answers->MultiCell(0, 20, iconv('UTF-8', 'windows-1252', 'Corrigé : '), 1, "C");
        $answers->SetFont('Arial', '', self::QUESTIONFONT_SIZE);
        $answers->MultiCell(0, 10, "\n\n\n", 0, "C");

        foreach ($listRndQuestions as $idQuestion) {

            $rndQuestion = $this->question_model->get($idQuestion);
            $Question = $rndQuestion->Question;
            $Question = iconv('UTF-8', 'windows-1252', $Question);
            $totalpoints += $rndQuestion->Points;

            //une page fait environ 300 on oblige a créer une nouvelle page si on est déja aprés 225
            if($pdf->getY() > 225){
                $pdf->addPage();
            }
            if($answers->getY() > 225){
                $answers->addPage();
            }

            $pdf->SetFont('Arial','B',self::QUESTIONFONT_SIZE);
            $answers->SetFont('Arial','B',self::QUESTIONFONT_SIZE);

            $pdf->MultiCell(0, 5, $Question, 0, "L");
            $answers->MultiCell(0, 5, $Question, 0, "L");

            $pdf->SetFont('', '', 12);
            $answers->SetFont('', '', 12);

            switch ($rndQuestion->FK_Question_Type) {
                case 1:
                    $this->multipleChoicesPdf($rndQuestion, $pdf);
                    $this->multipleChoicesPdf($rndQuestion, $answers, true);
                    break;
                case 2:
                    $this->multipleAnswersPdf($rndQuestion, $pdf);
                    $this->multipleAnswersPdf($rndQuestion, $answers, true);
                    break;
                case 3:
                    $this->answerDistributionPdf($rndQuestion, $pdf);
                    $this->answerDistributionPdf($rndQuestion, $answers, true); // /!\ This part of the function does nothing /!\
                    break;
                case 4:
                    $this->clozeTextPdf($rndQuestion, $pdf);
                    $this->clozeTextPdf($rndQuestion, $answers, true);
                    break;
                case 5:
                    $this->tablePdf($rndQuestion, $pdf);
                    $this->tablePdf($rndQuestion, $answers, false); // /!\ This part of the function does nothing /!\
                    break;
                case 6:
                    $this->simpleQuestionPdf($this->free_answer_model->get_by("FK_Question", $idQuestion)->Answer, $pdf);
                    $this->simpleQuestionPdf($this->free_answer_model->get_by("FK_Question", $idQuestion)->Answer, $answers, true);
                    break;
                case 7:
                    $this->pictureLandmarkPdf($rndQuestion, $pdf);
                    $this->pictureLandmarkPdf($rndQuestion, $answers, true);
                    break;
                default:
                    break;
            }
            $pdf->MultiCell(0, 20, ".../".$rndQuestion->Points, 0, "R");
            $answers->MultiCell(0, 20, ".../".$rndQuestion->Points, 0, "R");

        }

        $pdf->setTextColor(128,128,128);
        $pdf->SetFont('Arial','', self::TITLEFONT_SIZE);
        $pdf->Cell(30); // Padding
        $pdf->Cell(90, 20, "Total des points : .../".$totalpoints, 0, "R");
        $pdf->Cell(90, 20, "Note :", 0, "R");
        $pdf->Ln();
        $answers->setTextColor(128,128,128);
        $answers->SetFont('Arial','', self::TITLEFONT_SIZE);
        $answers->MultiCell(0, 20, "Total des points : ".$totalpoints, 0, "C");

        if($idQuestionnaire == -1){
            //$pdf->Output('I', 'Questionnaire', true);
            $title = $tableTopics->getTitle();

            if(file_exists("pdf_files/questionnaires/".$title.".pdf") || file_exists("pdf_files/corriges/".$title."_corrige.pdf")) {
                $i = 1;
                while(file_exists("pdf_files/questionnaires/".$title.'-'.$i.".pdf") || file_exists("pdf_files/corriges/".$title.'-'.$i."_corrige.pdf")){
                    $i++;
                }
                $title .= '-'.$i;
            }

            $pdf->Output('F', "pdf_files/questionnaires/" .$title.'.pdf', true);
            $answers->Output('F', "pdf_files/corriges/" . $title.'_corrige.pdf', true);
        } else {
            $questionnaire = $this->questionnaire_model->get($idQuestionnaire);
            $pdf->Output('F', "pdf_files/questionnaires/" . $questionnaire->PDF, true);
            $answers->Output('F', "pdf_files/corriges/" . $questionnaire->Corrige_PDF, true);
        }
        redirect('Questionnaire');
    }

    /**
     * Creates a new questionnaire in pdf format
     *
     * @param TableTopics $tableTopics = The questionnaire topics
     */
    private function InsertNewQuestionnaire($tableTopics)
    {
        $listIDQuestions = array();
        // Prepare the questionnaire name and save the original
        $title_original = $title = $tableTopics->getTitle();

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
            "PDF" => $title.".pdf",
            "Corrige_PDF" => $title."_corrige.pdf"
        );

        $idQuestionnaire = $this->questionnaire_model->insert($newQuestionnaire);

        //Insert each question about the questionnaire
        for ($index = 0; $index < count($tableTopics->getArrayTopics()); $index++)
        {
            //Get ID for each topic asked
            $nbQuestion = $tableTopics->getArrayNbQuestion()[$index];
            $idTopic = $tableTopics->getArrayTopics()[$index]->ID;

            //load randoms questions about the topic
            $rndQuestions = $this->question_model->getRNDQuestions($idTopic, $nbQuestion);

            for($number = 0; $number < count($rndQuestions); $number++)
            {
                $row = array(
                    "FK_Question" => intval($rndQuestions[$number]["ID"]),
                    "FK_Questionnaire" => $idQuestionnaire
                );
                $this->question_questionnaire_model->insert($row);
                $listIDQuestions[] = intval($rndQuestions[$number]["ID"]);

            }
        }

        return $listIDQuestions;
    }

    /**
     * Obtains all the questions in a questionnaire
     *
     * @param integer $idQuestionnaire = The questionnaire's id
     */
    private function findQuestionByQuestionnaire($idQuestionnaire){
        $listIDQuestions = array();
        $rndQuestions = array();

        //load randoms questions about the topic
        $rndQuestions = $this->question_questionnaire_model->get_many_by("FK_Questionnaire  = '" .  $idQuestionnaire . "'");

        for($number = 0; $number < count($rndQuestions); $number++)
        {
            $listIDQuestions[] = $rndQuestions[$number]->FK_Question;
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
    private function multipleChoicesPdf($question, $pdf, $answerPdf = false) {
        $multiChoiceQuestions = $this->multiple_choice_model->get_many_by("FK_Question = ".$question->ID);

        foreach($multiChoiceQuestions as $question) {
            $pdf->Cell(20, 20, iconv('UTF-8', 'windows-1252', $question->Answer), 0, 'L');
            $y = $pdf->Gety() + 7.7;
            $pdf->SetY($y);
            $pdf->SetX(100);

            // Only show answers for the answer sheet
            $cross = ($answerPdf && $question->Valid ? "x" : " ");
            if($answerPdf) $pdf->setTextColor(255, 0, 0);


            $pdf->MultiCell(5, 5, $cross, 1);
            $pdf->SetY($y);
            $pdf->setTextColor(0,0,0);
        }
    }

    /**
     * Adds a multiple answers question to a questionnaire's pdf
     *
     * @param Question $question = The question to add
     * @param FPDF $pdf = The pdf to update
     * @param boolean $answerPdf = Whether or not the pdf is for correction
     */
    private function multipleAnswersPdf($question, $pdf, $answerPdf = false) {
        $possible_answers = $this->multiple_answer_model->get_many_by("FK_Question = ".$question->ID);
        $pdf->Ln();

        if($answerPdf) {
            $pdf->setTextColor(255, 0, 0);

            foreach($possible_answers as $possible_answer) {
                $pdf->MultiCell(80, 8, iconv('UTF-8', 'windows-1252', $possible_answer->Answer), 0);
            }
        } else {
            $nbAskedAnswers = $question->Nb_Desired_Answers;
            // Get the longest answer so that the person who answers has enough room to write the correct answer
            $longest_answer = array_reduce($possible_answers, function($a, $b) {
                return strlen($a->Answer) > strlen($b->Answer) ? $a : $b;
            }, (object) ['Answer' => '']);
            $line = str_repeat("_", 30); // Repeating it any more will result in the line going on the next line
            for($i = 0; $i < $nbAskedAnswers; $i++) {
                $pdf->MultiCell(80, 8, $line, 0);
            }
        }
        $pdf->setTextColor(0, 0, 0);
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
    private function answerDistributionPdf($question, $pdf, $answerPdf = false) {
        if($answerPdf) {
            /**
             * @todo
             * The function did not exist,
             * there are no distribution questions and
             * the page to create them has yet to be coded
            */
        } else {
            $anDistributionQuestions = $this->answer_distribution_model->get_many_by("FK_Question = $Question->ID");

            foreach ($anDistributionQuestions as $anDistributionQuestion) {
                $pdf->Cell(80,20,iconv('UTF-8', 'windows-1252',$anDistributionQuestion->Question_Part), 1, "C");
                $pdf->Cell(20,20, "", 0, "C");
                $pdf->MultiCell(80,20, "", 1, "C");
            }
        }
        $pdf->setTextColor(0, 0, 0);
    }

    /**
     * Adds a cloze text question to a questionnaire's pdf
     *
     * @param Question $question = The question to add
     * @param FPDF $pdf = The pdf to update
     * @param boolean $answerPdf = Whether or not the pdf is for correction
     */
    private function clozeTextPdf($question, $pdf, $answerPdf = false) {
        $clozeText = $this->cloze_text_model->get_by("FK_Question = ".$question->ID);
        // Sometimes you can find … mixed with .
        $outputText = preg_split("/\[[…\.]*\]/", $clozeText->Cloze_Text, -1, PREG_SPLIT_DELIM_CAPTURE);
        $nbrAnswer = count($outputText)-1;
        $result = '';
        $allAnswers = $this->cloze_text_answer_model->get_many_by("FK_Cloze_Text = ".$clozeText->ID);

        // How the question is written, after processing
        foreach ($outputText as $key => $value) {
            $result .= $value . '['.($key+1).']';
        }
        // Remove the last [number] from the result
        $lastC = strrpos($result, '[');
        $result = substr($result, 0, $lastC);

        $pdf->SetFont(($answerPdf ? 'Arial' : ''), '', self::QUESTIONFONT_SIZE);
        $pdf->MultiCell(0, 10, iconv('UTF-8', 'windows-1252', $result), 0, "J");

        if($answerPdf) {
            // Put all answers in an array
            $answers = array();
            foreach($allAnswers as $a) {
                $answers[] = $a->Answer;
            }

            $pdf->setTextColor(255, 0, 0);
            if($nbrAnswer == count($answers)) {
                for($i = 0; $i < $nbrAnswer; $i++) {
                    $pdf->Cell(10, 10, '['.($i + 1).'] : ', 0);
                    $pdf->MultiCell(0, 10, iconv('UTF-8', 'windows-1252', $answers[$i]), 0);
                }
            } else {
                foreach($answers as $a) {
                    $pdf->MultiCell(0, 10, iconv('UTF-8', 'windows-1252', $a));
                }
            }
        } else {
            // Get the longest answer so that the person who answers has enough room to write the correct answer
            $longest_answer = array_reduce($allAnswers, function($a, $b) {
                return strlen($a->Answer) > strlen($b->Answer) ? $a : $b;
            }, (object) ['Answer' => '']); // Put an object with the only value we request
            $line = str_repeat("_", strlen($longest_answer->Answer) + 10);

            for($i = 1; $i <= $nbrAnswer; $i++) {
                $pdf->MultiCell(0, 10, '['.$i.'] : '.$line, 0);
            }
        }
        $pdf->setTextColor(0, 0, 0);
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
    private function tablePdf($question, $pdf, $answerPdf = false) {
        if($answerPdf) {
            /**
             * @todo
             * The function did not exist,
             * there are no table questions and
             * the page to create them has yet to be coded
            */
        } else {
            $tcCol = $tcRow = $tableCells = $this->table_cell_model->get_many_by("FK_Question = ".$Question->ID);

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
                        $pdf->Cell(40, 10, iconv('Used', 'windows-1252', $row->Content), 1, "C");
                        $pdf->SetFont('Arial', '', self::TITLEFONT_SIZE);
                    } elseif($row->Display_In_Question)
                        $pdf->Cell(40, 10, iconv('Used', 'windows-1252', $row->Content), 1, "C");
                    else
                        $pdf->Cell(40, 10, '', 1, "C");
                }
                // New line at the end of the table
                $pdf->Ln();
            }
        }
        $pdf->setTextColor(0, 0, 0);
    }

    /**
     * @deprecated use tablePdf($Question, $pdf, false)
     * This is kept as a reference in case there is a table question that appears
     *
     * I don't recommend looking at this one
     */
    private function displayTable($Question, $pdf)
    {
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
    private function simpleQuestionPdf($answer, $pdf, $answerPdf = false) {
        if($answerPdf) {
            $pdf->setTextColor(255, 0, 0);
            $pdf->MultiCell(0, 7.5, iconv('UTF-8', 'windows-1252', $answer), 0, "L");
        } else {
            // Put enough lines to answer
            for ($i = 0; $i < ceil(strlen($answer) / 60); $i++) {
                $pdf->MultiCell(0, 10, iconv('UTF-8', 'windows-1252', str_repeat("_", 75)), 0, "L");
            }
        }
        $pdf->setTextColor(0, 0, 0);
    }

    /**
     * Adds a picture landmark question to a questionnaire's pdf
     *
     * @param Question $question = The question to add
     * @param FPDF $pdf = The pdf to update
     * @param boolean $answerPdf = Whether or not the pdf is for correction
     */
    private function pictureLandmarkPdf($question, $pdf, $answerPdf = false) {
        $pictureLandmarks = $this->picture_landmark_model->with_all()->get_many_by("FK_Question = ".$question->ID);
        $picture = $question->Picture_Name;
        $fullPath = base_url((is_file('uploads/pictures/'.$picture) ? 'uploads/pictures/'.$picture : 'assets/images/not-found.png'));
        $pdf->Ln();
        $size = getimagesize($fullPath);
        $size = (["width" => $size[0], "height" => $size[1]]);

        // Image should not be more than 80 pixels tall
        // Most likely to prevent overflowing onto another page
        if($size["width"] > 80) {
            $size["height"] /= ($size["width"] / 80);
            $size["width"] = 80;
        }

        $pdf->Image($fullPath, null, null, $size["width"], $size["height"]);
        $yOld = $pdf->getY();
        $pageNumOld = $pdf->PageNo();
        $pdf->SetY($yOld - $size["height"]);

        // Add answers to the pdf
        foreach($pictureLandmarks as $pictureLandmark) {
            $pdf->Cell(100);
            $pdf->Cell(7, 5, $pictureLandmark->Symbol.": ", 0, "C");
            if($answerPdf) $pdf->setTextColor(255, 0, 0);
            $line = ($answerPdf ? iconv('UTF-8', 'windows-1252', $pictureLandmark->Answer) : str_repeat("_", 20));
            $pdf->MultiCell(100, 5, $line, 0, "L");
            $pdf->Ln();
            $pdf->setTextColor(0, 0, 0);
        }

        $yNew = $pdf->getY();
        $pageNumNew = $pdf->PageNo();
        // Set current Y to low enough to add new things
        if($pageNumNew == $pageNumOld) {
            $pdf->setY($yOld > $yNew ? $yOld : $yNew);
        } else {
            $pdf->setY($pageNumOld > $pageNumNew ? $yOld : $yNew);
        }
    }
}