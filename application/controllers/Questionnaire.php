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
    protected $access_level = "2";

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('question_questionnaire_model', 'questionnaire_model', 'topic_model',
            'question_model', 'question_type_model', 'answer_distribution_model',
            'multiple_choice_model', 'cloze_text_model', 'cloze_text_answer_model', 'table_cell_model',
            'picture_landmark_model', 'free_answer_model', 'multiple_answer_model'));
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
            $idTopic = $this->topic_model->get_by("ID = '" .  $Topic . "'")->ID;
            $nbQuestion = $this->question_model->getNbQuestionByTopic($idTopic);
            echo $nbQuestion;

        }else
        {
            $this->display_view('questionnaires/add', $output);
        }
    }

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
        if (!isset($_POST[$this->lang->line('generatePDF_btn')]))
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
            if($this->form_validation->run() == true)
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

    public function generatePDF($idQuestionnaire = -1, $tableTopics = null)
    {

            
    
        if($idQuestionnaire == -1){
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
        $pdf->MultiCell(0, 10, 'Nom :                                                               Date :', 0, "L");
        $pdf->MultiCell(80, 10, iconv('UTF-8', 'windows-1252', 'Prénom : '), 0, "L");
        $pdf->SetFont('Arial', '', self::QUESTIONFONT_SIZE);
        $pdf->Ln();


        // Title of the answered document
        $answers->AddPage();
        $answers->SetFont('Arial', 'B',  self::TITLEFONT_SIZE);
        $answers->SetTitle('Corrigé', true);
        $answers->MultiCell(0, 20, iconv('UTF-8', 'windows-1252', 'Corrigé : '), 1, "C");
        $answers->SetFont('Arial', '', self::QUESTIONFONT_SIZE);
        $answers->MultiCell(0, 10, "\n\n\n", 0, "C");

        foreach ($listRndQuestions as $object => $idQuestion) {

            $rndQuestion = $this->question_model->get($idQuestion);
            $Question = $rndQuestion->Question;
            $Question = iconv('UTF-8', 'windows-1252', $Question);
            $totalpoints += $rndQuestion->Points;

            //une page fait environ 300 on oblige a créer une nouvelle page si on est déja aprés 225
            if($pdf->getY()> 225){
                $pdf->addPage();
            }
            if($answers->getY()> 225){
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
                $this->displayMultipleChoices($rndQuestion, $pdf);
                $this->answerMultipleChoices($rndQuestion, $answers);
                break;
                case 2:
                $this->displayMultipleAnswers($rndQuestion, $pdf);
                $this->answerMultipleAnswers($rndQuestion, $answers);
                break;
                case 3:
                $this->displayAnswerDistribution($rndQuestion, $pdf);
                $this->answerAnswerDistribution($rndQuestion, $answers);
                break;
                case 4:
                $this->displayClozeText($rndQuestion, $pdf);
                $this->answerClozeText($rndQuestion, $answers);
                break;
                case 5:
                $this->displayTable($rndQuestion, $pdf);
                $this->answerTable($rndQuestion, $answers);
                break;
                case 6:
                $this->displaySimpleQuestion($this->free_answer_model->get_by("FK_Question", $idQuestion)->Answer, $pdf);
                $this->answerSimpleQuestion($this->free_answer_model->get_by("FK_Question", $idQuestion)->Answer, $answers);
                break;
                case 7:
                $this->displayPictureLandmarks($rndQuestion, $pdf);
                $this->answerPictureLandmarks($rndQuestion, $answers);
                break;
                default:
                break;
            }
            $pdf->MultiCell(0, 20, ".../$rndQuestion->Points", 0, "R");
            $answers->MultiCell(0, 20, ".../$rndQuestion->Points", 0, "R");

        }
        $error = false;
        
        /*
        }catch (Exception $e)
        {
            $error = true;
        }*/

        $pdf->setTextColor(128,128,128);
        $pdf->SetFont('Arial','', self::TITLEFONT_SIZE);
        $pdf->MultiCell(0, 20, "Total des points : .../$totalpoints             Note :            ", 0, "C");
        $answers->setTextColor(128,128,128);
        $answers->SetFont('Arial','', self::TITLEFONT_SIZE);
        $answers->MultiCell(0, 20, "Total des points : $totalpoints                                   ", 0, "C");
        if ($error) {
            echo $this->lang->line('pdf_error');
        }else{
            if($idQuestionnaire == -1){
                $pdf->Output('I', 'Questionnaire', true);
                $title = $tableTopics->getTitle();
                $title_const = $title;
                $i = 1;
                while(file_exists("pdf_files/questionnaires/".$title.".pdf") || file_exists("pdf_files/corriges/".$title.".pdf")){
                    $title = $title_const .'-'. $i;
                    $i++;
                }

                $pdf->Output('F', "pdf_files/questionnaires/" .$title.'.pdf', true);
                $answers->Output('F', "pdf_files/corriges/" . $title.'_corrige.pdf', true);
            } else {
                $pdf->Output('F', "pdf_files/questionnaires/" . $this->getQuestionnaireName($idQuestionnaire)['PDF'], true);
                $answers->Output('F', "pdf_files/corriges/" . $this->getQuestionnaireName($idQuestionnaire)['corrige'], true);
            }
            $this->index();
        }
    }


    private function InsertNewQuestionnaire($tableTopics)
    {
        $listIDQuestions = array();
        //Insert new Questionnaire
        $title = $tableTopics->getTitle();
        $newQuestionnaire = array("Questionnaire_Name" => $title);

        $title_const = $title;
        $i = 1;

        while(file_exists("pdf_files/questionnaires/".$title.".pdf") || file_exists("pdf_files/corriges/".$title.".pdf")){
            $title = $title_const .'-'. $i;
            $i++;
        }
        $newQuestionnaire['PDF'] = $title.".pdf";
        $newQuestionnaire['Corrige_PDF'] = $title."_corrige.pdf";

        $this->questionnaire_model->insert($newQuestionnaire);
        $idQuestionnaire = $this->db->insert_id();

        //Insert each question about the questionnaire
        for ($index = 0; $index < count($tableTopics->getArrayTopics()); $index++)
        {
            //Get ID for each topic asked
            $topic = $tableTopics->getArrayTopics()[$index];
            $nbQuestion = $tableTopics->getArrayNbQuestion()[$index];
            $idTopic = $topic->ID;

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

    private function findQuestionByQuestionnaire($idQuestionnaire){
        $listIDQuestions = array();
        $rndQuestions = array();

        //load randoms questions about the topic
        $rndQuestions = $this->question_questionnaire_model->get_many_by("FK_Questionnaire  = '" .  $idQuestionnaire . "'");

        for($number = 0; $number < count($rndQuestions);$number++)
        {
            array_push($listIDQuestions, $rndQuestions[$number]->FK_Question);
        }

        return $listIDQuestions;
    }

    private function getQuestionnaireName($idQuestionnaire){
        $PDF['name'] = $this->questionnaire_model->get($idQuestionnaire)->Questionnaire_Name;
        $PDF['PDF'] = $this->questionnaire_model->get($idQuestionnaire)->PDF;
        $PDF['corrige'] = $this->questionnaire_model->get($idQuestionnaire)->Corrige_PDF;
        return $PDF;
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
                $pdf->MultiCell(5, 5, " ", 1);
                $pdf->SetY($y + 7.7);
            }

            return false;

        }catch(Exception $e)
        {
            return true;
        }
    }

    private function answerMultipleChoices($Question, $pdf)
    {
        try{
            $multi_Choice_Questions = $this->multiple_choice_model->get_many_by("FK_Question = $Question->ID");

            foreach ($multi_Choice_Questions as $m)
            {
                $pdf->Cell(20, 20, iconv('UTF-8', 'windows-1252', $m->Answer), 0, "L");

                $y = $pdf->Gety();
                $pdf->SetY($y + 7.7);
                $pdf->SetX(100);

                //Cross if the answer is true
                $cross = "";
                if ($m->Valid == 1) {
                    $cross = "x";
                }
                $pdf->setTextColor(255,0,0);
                $pdf->MultiCell(5, 5, $cross, 1);
                $pdf->SetY($y + 7.7);
                $pdf->setTextColor(0,0,0);
            }

            return false;

        }
        catch(Exception $e)
        {
            return true;
        }
    }

    private function displayMultipleAnswers($Question, $pdf)
    {
        $nbAskedAnswers = $Question->Nb_Desired_Answers;
        $pdf->Ln();
        for($i = 0; $i < $nbAskedAnswers; $i++)
        {
            $pdf->MultiCell(80, 8, "___________________________", 0);
        }
    }

    private function answerMultipleAnswers($Question, $pdf)
    {
        $pdf->setTextColor(255,0,0);
        $pdf->Ln();
        $possible_answers = $this->multiple_answer_model->get_many_by("FK_Question = $Question->ID");
        foreach ($possible_answers as $possible_answer)
        {
            $pdf->MultiCell(80, 8, iconv('UTF-8', 'windows-1252', $possible_answer->Answer), 0);
        }

        $pdf->setTextColor(0,0,0);
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

    private function displayClozeText($Question, $pdf)
    {
        try{
            $ClozeText = $this->cloze_text_model->get_by("FK_Question = $Question->ID");
            $outputText = preg_split("#\[\.*\]#", $ClozeText->Cloze_Text,-1,PREG_SPLIT_DELIM_CAPTURE);
            $nbrAnswer = count($outputText)-1;
            $result='';

            foreach ($outputText as $key => $value) {
               $result=$result.$value.(($key!=$nbrAnswer)?'['.($key+1).']':'');
            }
            $pdf->SetFont('','',self::QUESTIONFONT_SIZE);
            $pdf->MultiCell(0, 10, iconv('UTF-8', 'windows-1252', $result), 0, "J");

            for ($i=0; $i < $nbrAnswer; $i++) {
                $pdf->MultiCell(0, 10, '['.($i+1).'] : ________________________', 0);
            }

            return false;
        }
        catch(Exception $e){
            return true;
        }
    }


    //if error return true
    private function answerClozeText($Question, $pdf)
    {
        try{
            $ClozeText = $this->cloze_text_model->get_by("FK_Question = $Question->ID");
            $outputText = preg_split("#\[\.*\]#", $ClozeText->Cloze_Text,-1,PREG_SPLIT_DELIM_CAPTURE);
            $allAnswers = $this->cloze_text_answer_model->get_many_by("FK_Cloze_Text = $ClozeText->ID");
            $answers = array(); 

            foreach ($allAnswers as $key) {
                array_push($answers, $key->Answer);
            }


            $nbrAnswer = count($outputText)-1;
            $result='';
            foreach ($outputText as $key => $value) {
               $result=$result.$value.(($key!=$nbrAnswer)?'['.($key+1).']':'');
            }
            $pdf->SetFont('Arial','',self::QUESTIONFONT_SIZE);
            $pdf->MultiCell(0, 10, iconv('UTF-8', 'windows-1252', $result), 0, "J");

                $pdf->setTextColor(255,0,0);
            if ($nbrAnswer == count($answers)){
                for ($i=0; $i < $nbrAnswer; $i++) {
                    $pdf->Cell(10, 10, '['.($i+1).'] : ', 0);
                    $pdf->MultiCell(0, 10, iconv('UTF-8', 'windows-1252', $answers[$i]) , 0);
                }
            }else{
                foreach ($answers as $value) {
                    $pdf->MultiCell(0, 10, iconv('UTF-8', 'windows-1252',$value), 0);
                }
            }
            $pdf->setTextColor(0,0,0);
            return false;
        }
        catch(Exception $e){
            return true;
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
    private function displaySimpleQuestion($Answer, $pdf)
    {
        
        $pdf->Ln();
        //calculation of the number of lines granted according to the expected answer.
        for ($i=0; $i < ceil(strlen($Answer)/60); $i++) { 
            $pdf->MultiCell(0, 10, iconv('UTF-8', 'windows-1252', "____________________________________________________________________________"), 0, "L");     
        } 
        
    }
    private function answerSimpleQuestion($Answer, $pdf){
        
        // answer in red for a simple kind of question
        $pdf->setTextColor(255,0,0);
        //Answer put
        $pdf->MultiCell(0, 7.5, iconv('UTF-8', 'windows-1252', "\n".$Answer), 0, "L");
        $pdf->setTextColor(0,0,0);
    }
    private function displayPictureLandmarks($Question, $pdf)
    {
        $pictureLandmarks = $this->picture_landmark_model->with_all()->get_many_by("FK_Question = $Question->ID");
        $picture = $Question->Picture_Name;

        (is_file('uploads/pictures/'. $picture))?($fullPath='uploads/pictures/'.$picture):($fullPath=base_url().'application/img/not-found.png');
        $pdf->Ln();

        $w = getimagesize($fullPath)[0];
        $h = getimagesize($fullPath)[1];

        //l'image ne dépasse pas 80 en largeur
        if($w>80) {
            $h=$h/($w/80);
            $w=80;
        }
        $pdf->Image($fullPath, null, null, $w, $h);
        $y1 = $pdf->getY();
        $pagenum1 = $pdf->PageNo();
        $pdf->SetY($y1-$h);

        foreach ($pictureLandmarks as $pictureLandmark)
        {
            $pdf->Cell(100);
            $pdf->Cell(7,5, "$pictureLandmark->Symbol: ", 0, "C");
            $pdf->MultiCell(100,5, '___________________', 0, "L");
            $pdf->Ln();
        }
        $y2 = $pdf->getY();
        $pagenum2 = $pdf->PageNo();
        //prend le point le plus bas entre la liste ou l'image pour définir le point y de la suite
        
        if($pagenum1==$pagenum2){
            ($y1>$y2)?$pdf->SetY($y1):$pdf->SetY($y2);
        }else{
            ($pagenum1>$pagenum2)?$pdf->SetY($y1):$pdf->SetY($y2);
        }
    }

    private function answerPictureLandmarks($Question, $pdf)
    {
        $pictureLandmarks = $this->picture_landmark_model->with_all()->get_many_by("FK_Question = $Question->ID");
        $picture = $Question->Picture_Name;
        $imageUrl = 'uploads/pictures/'. $picture;

        (is_file('uploads/pictures/'. $picture))?($fullPath='uploads/pictures/'.$picture):($fullPath=base_url().'application/img/not-found.png');
        $pdf->Ln();
        $w = getimagesize($fullPath)[0];
        $h = getimagesize($fullPath)[1];

        //l'image ne dépasse pas 80 en largeur
        if($w>80) {
            $h=$h/($w/80);
            $w=80;
        }

        $pdf->Image($fullPath, null, null, $w, $h);
        $y1 = $pdf->getY();
        $pagenum1 = $pdf->PageNo();
        $pdf->SetY($y1-$h);

        foreach ($pictureLandmarks as $pictureLandmark)
        {
            $pdf->Cell(100);
            $pdf->Cell(7,10, "$pictureLandmark->Symbol: ", 0, "C");
            $pdf->setTextColor(255,0,0);
            $pdf->MultiCell(40,10, iconv('UTF-8', 'windows-1252', $pictureLandmark->Answer), 0, "L");
            $pdf->setTextColor(0,0,0);
        }
        $y2 = $pdf->getY();
        $pagenum2 = $pdf->PageNo();
        //prend le point le plus bas entre la liste ou l'image pour définir le point y de la suite
        if($pagenum1==$pagenum2){
            ($y1>$y2)?$pdf->SetY($y1):$pdf->SetY($y2);
        }else{
            ($pagenum1>$pagenum2)?$pdf->SetY($y1):$pdf->SetY($y2);
        }
    }
}