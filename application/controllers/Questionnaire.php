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
                                'question_model', 'question_type_model', 'answer_distribution_model',
                                'multiple_choice_model', 'cloze_text_model', 'table_cell_model', 
                                'picture_landmark_model'));
        $this->load->helper(array('url', 'form'));
        $this->load->library(array('TableTopics', 'form_validation', 'fpdf181/fpdf', 'upload'));
    }

    /**
     * Display questionnaire list
     */
    public function index($error = "")
    {
        $outputs['questionnaires'] = $this->questionnaire_model->get_all();
		$outputs['error'] = $error;
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
    public function delete($id = 0, $action = NULL)
    {
		if ($id != 0) {
			$questionnaire = $this->questionnaire_model->with("question_questionnaires")->get($id);
			if (is_null($action)) {
				$output = get_object_vars($this->questionnaire_model->get($id));
				$output["questionnaires"] = $this->questionnaire_model->get_all();
				$this->display_view("questionnaires/delete", $output);
			} else {
				if (count($questionnaire->question_questionnaires) > 0) {
					foreach ($questionnaire->question_questionnaires as $question_questionnaire) {
						$this->question_questionnaire_model->delete($question_questionnaire->ID);
					}
				}
				$this->questionnaire_model->delete($id);
				$this->index();
			}
        } else {
		  $this->index();
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
        $output['error'] = ($error == NULL ? NULL : true);
        $output['topicsList'] = $this->topic_model->get_all();
        $output['questions'] = $this->question_model->with_all()->get_all();
        $output['question_types'] = $this->question_type_model->get_all();
        $output['nbLines'] = 5;
        $output['title'] = $title;
        $output['topics'] = $topics;
        $output['nbQuestions'] = $nbQuestions;


        if(isset($_POST['topic']))
        {
            $Topic = urldecode($_POST['topic']);
            $Topic = str_replace("_apostrophe_", "\'", $Topic);
            $idTopic = $this->topic_model->get_by("Topic = '" .  $Topic . "'")->ID;
            $nbQuestion = $this->question_model->getNbQuestionByTopic($idTopic);
            echo $nbQuestion;
            
        }else
        {
            $this->display_view('questionnaires/add', $output);   
        }
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
                $this->add(NULL, $tableTopics->getTitle(), $tableTopics->getArrayTopics(), $tableTopics->getArrayNbQuestion());
            }
            else
            {
                $tableTopics = $this->saveTopicElements($tableTopics, 2);
                $this->generatePDF($tableTopics);
            }
        }
        else
        {
            $this->add(1);
        }
    }

    public function generatePDF($tableTopics)
    {
        $listRndQuestions = $this->InsertNewQuestionnaire($tableTopics);
        $totalpoints = 0;

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetTitle("Questionnaire", true);
        $pdf->MultiCell(0, 20, 'Questionnaire', 1, "C");
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->MultiCell(0, 10, 'Nom :                                                               Date :', 0, "L");
        $pdf->MultiCell(80, 10, iconv('UTF-8', 'windows-1252', 'Prénom : '), 0, "L");
        $pdf->SetFont('Arial', '', 16);


       // try
        //{
            foreach ($listRndQuestions as $object => $idQuestion) {

                $rndQuestion = $this->question_model->get($idQuestion);
                //var_dump($rndQuestion);
                $Question = $rndQuestion->Question;
                $Question = iconv('UTF-8', 'windows-1252', $Question);
                $totalpoints += $rndQuestion->Points;

                $pdf->MultiCell(0, 20, $Question, 0, "L");

                switch ($rndQuestion->FK_Question_Type) {
                    case 1:
                        $this->displayMultipleChoices($rndQuestion, $pdf);
                        break;
                    case 2:
                        $this->displayMultipleAnswers($rndQuestion, $pdf);
                        break;
                    case 3:
                        $this->displayAnswerDistribution($rndQuestion, $pdf);
                        break;
                    case 4:
                        $this->displayClozeText($rndQuestion, $pdf);
                        break;
                    case 5:
                        $this->displayTable($rndQuestion, $pdf);
                        break;
                    case 7:
                        $this->displayPictureLandmarks($rndQuestion, $pdf);
                        break;
                    default:
                        break;
                }

                $pdf->MultiCell(0, 20, ".../$rndQuestion->Points", 0, "R");

            }
            $error = false;
            /*
        }catch (Exception $e)
        {
            $error = true;
        }*/


        $pdf->MultiCell(0, 20, "Total : .../$totalpoints", 0, "R");
        if ($error) {
            echo $this->lang->line('pdf_error');
        }else{
            //$pdf->Output('I', 'Questionnaire', true);
			$pdf->Output('F', $tableTopics->getTitle().'.pdf', true);
			$this->index();
        }
    }


    private function InsertNewQuestionnaire($tableTopics)
    {
        $listIDQuestions = array();
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
                array_push($listIDQuestions, intval($rndQuestions[$number]["ID"]));

            }
        }
        return $listIDQuestions;
    }

    /**
     * Save each elements already seen on the topic list
     * @param $tableTopics = object of the Topics table
     * @param $indice = 2 if the user want to finish the table OR 1 if not
     * @return the table of Topic
     */
    private function saveTopicElements($tableTopics, $indice)
    {
        if($indice == 1)
        {
            $index = 11;
            while ($index <= ((count($this->input->post()) - $indice) * 5))
            {
                $tableTopics->setArrayTopics($this->input->post($index));
                $tableTopics->setArrayNbQuestion($this->input->post($index + 1));
                $index += 10;
            }

        }else if($indice == 2)
        {

            $index = 10;
            do
            {
                $tableTopics->setArrayTopics($this->input->post($index + 1));
                $tableTopics->setArrayNbQuestion($this->input->post($index + 2));
                $index += 10;
            }while ($index <= ((count($this->input->post()) - $indice) * 5));

        }
        return $tableTopics;
    }

    private function displayMultipleChoices($Question, $pdf)
    {
        try{

            $multi_Choice_Questions = $this->multiple_choice_model->get_many_by("FK_Question = $Question->ID");

            foreach ($multi_Choice_Questions as $m)
            {
                $pdf->Cell(20, 20, iconv('UTF-8', 'windows-1252', $m->Answer), 0, "L");

                $y = $pdf->Gety();
                $pdf->SetY($y + 7.7);
                $pdf->SetX(100);
                $pdf->MultiCell(5, 5, "", 1);
            }

            return false;

        }catch(Exception $e)
        {
            return true;
        }
    }

    private function displayMultipleAnswers($Question, $pdf)
    {
        $nbAskedAnswers = $Question->Nb_Desired_Answers;
        for($i = 0; $i < $nbAskedAnswers; $i++)
        {
            $pdf->MultiCell(80, 10, "________________________", 0);
        }
    }

    private function displayAnswerDistribution($Question, $pdf)
    {
        $An_Distrib_Questions = $this->answer_distribution_model->get_many_by("FK_Question = $Question->ID");

        foreach ($An_Distrib_Questions as $an_Distrib_Question)
        {
            $pdf->Cell(80,20,iconv('UTF-8', 'windows-1252',$an_Distrib_Question->Question_Part), 1, "C");
            $pdf->Cell(20,20, "", 0, "C");
            $pdf->MultiCell(80,20, "", 1, "C");

        }
    }

    //if error return true
    private function displayClozeText($Question, $pdf)
    {
        $ClozeText = $this->cloze_text_model->with_all()->get_by("FK_Question = $Question->ID");

        if(!isset($ClozeText->Cloze_Text))
        {
            return true;
        }else {
            $pdf->MultiCell(0, 10, iconv('UTF-8', 'windows-1252', $ClozeText->Cloze_Text), 0, "J");
            return false;
        }
    }

    private function displayTable($Question, $pdf)
    {
        $tableCells = $this->table_cell_model->get_many_by("FK_Question = $Question->ID");
        var_dump($tableCells);
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
                                    $pdf->SetFont('Arial','B',16);
                                    if($tableCellRow->Column_Nb < $maxColumn)
                                    {
                                        $pdf->Cell(40,10, iconv('UTF-8', 'windows-1252',$tableCellRow->Content), 1, "C");
                                    }else
                                    {
                                        $pdf->MultiCell(40,10, iconv('UTF-8', 'windows-1252',$tableCellRow->Content), 1, "C");
                                    }
                                    $pdf->SetFont('Arial', '',16);
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

    private function displayPictureLandmarks($Question, $pdf)
    {
        
        /*
        $url = base_url() .'uploads/pictures/';
        $width = getimagesize($fullPath)[0];
        $height = getimagesize($fullPath)[1];


        $image_p = imagecreatetruecolor(100, 100);
        $image = imagecreatefromjpeg($fullPath);


        imagecopyresampled($image_p, $image, 0, 0, 0, 0, 100, 100, $width, $height);
    
        imagedestroy($image);
        imagejpeg($image_p, $fullPath, 100);*/

        $pictureLandmarks = $this->picture_landmark_model->with_all()->get_many_by("FK_Question = $Question->ID");
        $picture = $Question->Picture_Name;

        $fullPath = base_url() .'uploads/pictures/'. $picture;

        $pdf->Image($fullPath, null, null, 100, 100);

        
        foreach ($pictureLandmarks as $pictureLandmark)
        {
            $pdf->Cell(7,10, "$pictureLandmark->Symbol: ", 0, "C");
            $pdf->MultiCell(40,10, '___________', 0, "L");
        }
    }

}