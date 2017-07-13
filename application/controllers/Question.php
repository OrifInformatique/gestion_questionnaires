<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Question controller
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */

class Question extends MY_Controller
{
    /* MY_Controller variables definition */
    protected $access_level = "2";


    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('question_model', 'question_type_model', 'topic_model', 'multiple_choice_model',
                            'multiple_answer_model', 'answer_distribution_model', 'cloze_text_model',
                            'cloze_text_answer_model', 'table_cell_model', 'free_answer_model', 'picture_landmark_model'));
        $this->load->helper(array('url', 'form'));
        $this->load->library(array('PHPExcel-1.8/Classes/PHPExcel', 'upload', 'form_validation'));
    }

    /**
     * Display question list
     */
    public function index()
    {
        $output['questions'] = $this->question_model->with_all()->get_all();
        $output['topics'] = $this->topic_model->get_all();
        $this->display_view('questions/index', $output);
    }

    /**
     * @param int $id = id of the question
     * Delete selected question
     */
    public function delete($id = 0)
    {
        if ($id != 0) {
            $this->question_model->delete($id);

            $this->index();
        }
    }

    /**
     * Form validation to update question
     */
    public function form_update()
    {
		$this->form_validation->set_rules('name', 'Title', 'required');
		
        $id = $this->input->post('id');
		$title = array('Question' => $this->input->post('name'));
		
        if ($this->form_validation->run() == true) {
			$this->question_model->update($id, $title);
            $this->index();
        } else {
            $this->update($id, 1);
        }
    }

    /**
     * @param int $id = selected question id
     * @param int $error = Type of error :
     * 0 = no error
     * 1 = wrong identifiers
     * 2 = field(s) empty
     * Display the detailed view to update a question
     */
    public function update($id = 0, $error = 0)
    {
        $output['error'] = $error;
        $output['id'] = $id;

        if ($id != 0) {
            $output['question'] = $this->question_model->get_by('ID = ' . $id);
            $this->display_view('questions/update', $output);
        } else {
			$this->index();
        }
    }

    public function detail($id = 0, $error = 0)
    {
        $output['error'] = $error;
        $output['id'] = $id;

        if ($id != 0) {
			$question = $this->question_model->with_all()->get_by('ID = ' . $id);
            $output['question'] = $question;
			$output['image'] = "";
			$output['reponse'] = "";
			if ($question->FK_Question_Type == 7) {
				$output['image'] = $question->Picture_Name;
				$reponses = $this->picture_landmark_model->get_many_by('FK_Question = ' . $id);
				foreach ($reponses as $reponse) {
					$output['reponse'] = $output['reponse']."<br>".$reponse->Symbol."/".$reponse->Answer;
				}				
			} elseif ($question->FK_Question_Type == 1) {
				$reponses = $this->multiple_choice_model->get_many_by('FK_Question = ' . $id);
				foreach ($reponses as $reponse) {
					if ($reponse->Valid == '1'){
						$cocher = $this->lang->line('oui');
					} else {
						$cocher = $this->lang->line('non');
					}
					$output['reponse'] = $output['reponse']."<br>".$reponse->Answer.":".$cocher;
				}
			} elseif ($question->FK_Question_Type == 2) {
				$reponses = $this->multiple_answer_model->get_many_by('FK_Question = ' . $id);
				foreach ($reponses as $reponse) {
					$output['reponse'] = $output['reponse']."<br>".$reponse->Answer;
				}				
			} elseif ($question->FK_Question_Type == 3) {
				$reponses = $this->answer_distribution_model->get_many_by('FK_Question = ' . $id);
				foreach ($reponses as $reponse) {
					$output['reponse'] = $output['reponse']."<br>".$reponse->Question_Part."/".$reponse->Answer_Part;
				}				
			} elseif ($question->FK_Question_Type == 4) {
				$question = $this->cloze_text_model->get_by('FK_Question = ' . $id);
				$output['question']->Question = $output['question']->Question.': '.$question->Cloze_Text;
				$reponses = $this->cloze_text_answer_model->get_many_by('FK_Cloze_Text = ' . $question->ID);
				foreach ($reponses as $reponse) {
					$output['reponse'] = $output['reponse']."<br>".$reponse->Answer_Order."/".$reponse->Answer;
				}				
			} elseif ($question->FK_Question_Type == 5) {
				$reponses = $this->table_cell_model->get_many_by('FK_Question = ' . $id);
			} elseif ($question->FK_Question_Type == 6) {
				$reponses = $this->free_answer_model->get_many_by('FK_Question = ' . $id);
				foreach ($reponses as $reponse) {
					$output['reponse'] = $output['reponse']." ".$reponse->Answer;
				}				
			}
			//var_dump($reponses);
			$this->display_view('questions/detail', $output);
        } else {
			$this->index();
        }
    }
	
	
    /**
     * Display form to add a question
     * Not build
     */
    public function add()
    {

    }

    /**
     * ON BUILDING
     * Useful to import all questions already write on Excel
     */
    public function import()
    {

        if(isset($_POST['submitExcel']))
        {

            $config['upload_path'] = './uploads/excel';
            $config['allowed_types'] = 'xlsx';
            $config['max_size'] = 100;
            $config['max_width'] = 1024;
            $config['max_height'] = 768;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if (!$this->upload->do_upload('excelfile')) {
                $error = array('error' => $this->upload->display_errors());
                var_dump($error);

            } else {

                $data = array('upload_data' => $this->upload->data());

                $inputFileName = $data['upload_data']['full_path'];

                $topic = $this->input->post('topic_selected');
                $topic = str_replace("'", "''", $topic);
                $idTopic = $this->topic_model->get_by("Topic = '" . $topic . "'")->ID;

                $inputFileType = 'Excel2007';

                /**  Create a new Reader of the type defined in $inputFileType  **/
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);

                $this->Import_MultipleChoices($idTopic, $objReader, $inputFileName);
                $this->Import_MultipleAnswers($idTopic, $objReader, $inputFileName);
                $this->Import_AnswerDistribution($idTopic, $objReader, $inputFileName);
                $this->Import_ClozeText($idTopic, $objReader, $inputFileName);
                $this->Import_TableCell($idTopic, $objReader, $inputFileName);
                $this->Import_FreeAnswer($idTopic, $objReader, $inputFileName);
                $this->Import_PictureLandmark($idTopic, $objReader, $inputFileName);

                redirect("./Question");

            }


            if (isset($_FILES['excelfile'])) {
                if ($_FILES['excelfile']['error'] == 0 &&
                    $_FILES['excelfile']['type'] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                ) {

                } else {
                    $output['error'] = true;
                    $output['questions'] = $this->question_model->with_all()->get_all();
                    $output['topics'] = $this->topic_model->get_all();
                    $this->display_view('questions/import', $output);
                }
            } else {
                $output['questions'] = $this->question_model->with_all()->get_all();
                $output['topics'] = $this->topic_model->get_all();
                $this->display_view('questions/import', $output);
            }

        }else if(isset($_POST['submitPictures']))
        {

            $files = $_FILES;
            $countfile = count($_FILES['picturesfile']['name']);

            for($i = 0; $i < $countfile; $i++)
            {
                //More optimal object
                $_FILES['picturesfile']['name'] = $files['picturesfile']['name'][$i];
                $_FILES['picturesfile']['type'] = $files['picturesfile']['type'][$i];
                $_FILES['picturesfile']['tmp_name'] = $files['picturesfile']['tmp_name'][$i];
                $_FILES['picturesfile']['error'] = $files['picturesfile']['error'][$i];
                $_FILES['picturesfile']['size'] = $files['picturesfile']['size'][$i];

                $config['upload_path'] = './uploads/pictures';
                $config['allowed_types'] = 'png|jpg|jpeg';
                $config['max_size'] = 0;

                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                if (!$this->upload->do_upload('picturesfile'))
                {
                    $error = array('error' => $this->upload->display_errors());
                }
            }

            if(!isset($error))
            {
                redirect("./Question/import");
            }
        }else
        {
            $output['questions'] = $this->question_model->with_all()->get_all();
            $output['topics'] = $this->topic_model->get_all();
            $this->display_view('questions/import', $output);
        }

    }


    /**
     * Import 'Multiple_Choice' question type by Excel sheet 'ChoixMultiple'
     * @param $idTopic = the topic select on the select attribute
     * @param $objReader = the object that allow we to read the excel sheet
     * @param $inputFileName = the name of sheet 'ChoixMultiple'
     */
    private function Import_MultipleChoices($idTopic, $objReader, $inputFileName)
    {
        $sheetName = 'ChoixMultiples';
        $questionType = 1;

        /**  Advise the Reader of which WorkSheets we want to load  **/
        $objReader->setLoadSheetsOnly($sheetName);
        $objReader->setReadDataOnly(true);
        /**  Load $inputFileName to a PHPExcel Object  **/
        $objPHPExcel = $objReader->load($inputFileName);

        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
        {

            $column = 0;
            $row = 3;

            //Browse the sheet
            //0 is the start column
            while($worksheet->getCellByColumnAndRow($column, $row)->getValue() != NULL)
            {
                //Current question
                $question = $worksheet->getCellByColumnAndRow($column, $row)->getValue();

                //Data to insert to the table 'T_Question'
                $inputQuestion = array(
                                "FK_Topic" => $idTopic,
                                "FK_Question_Type" => $questionType,
                                "Question" => $question,
                                "Creation_Date" => date("Y-m-d H:i:s")
                );

                $idQuestion = $this->question_model->insert($inputQuestion);

                //Take next to the question the data to insert to 'T_Multiple_Choice'
                while($worksheet->getCellByColumnAndRow($column + 1, $row)->getValue() != NULL)
                {
                    $answerField = $worksheet->getCellByColumnAndRow($column + 1, $row)->getValue();
                    $validField = $worksheet->getCellByColumnAndRow($column + 1, $row + 1)->getValue();

                    if($validField == "x")$valid = true;
                    else $valid = false;
                    
                    $inputMultipleChoice = array(
                        "FK_Question" => $idQuestion,
                        "Answer" => $answerField,
                        "Valid" => $valid,
                        "Creation_Date" => date("Y-m-d H:i:s")
                    );

                    $this->multiple_choice_model->insert($inputMultipleChoice);
                    $column += 1;
                }
                $row += 2;
                $column = 0;
            }
        }
    }

    /**
     * Import 'Multiple_Answer' question type by Excel sheet 'ReponsesMultiple'
     * @param $idTopic = the topic select on the select attribute
     * @param $objReader = the object that allow we to read the excel sheet
     * @param $inputFileName = the name of sheet 'ReponsesMultiple'
     */
    private function Import_MultipleAnswers($idTopic, $objReader, $inputFileName)
    {
        $sheetName = 'ReponsesMultiples';
        $questionType = 2;

        /**  Advise the Reader of which WorkSheets we want to load  **/
        $objReader->setLoadSheetsOnly($sheetName);
        $objReader->setReadDataOnly(true);
        /**  Load $inputFileName to a PHPExcel Object  **/
        $objPHPExcel = $objReader->load($inputFileName);

        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
        {
            $column = 0;
            $row = 3;

            //Browse the sheet
            //0 is the start column
            while($worksheet->getCellByColumnAndRow($column, $row)->getValue() != NULL)
            {
                //Current question
                $question = $worksheet->getCellByColumnAndRow($column, $row)->getValue();
                //Nb of answer
                $nbAnswerDesired = $worksheet->getCellByColumnAndRow($column + 1, $row)->getValue();

                //Data to insert to the table 'T_Question'
                $inputQuestion = array(
                    "FK_Topic" => $idTopic,
                    "FK_Question_Type" => $questionType,
                    "Question" => $question,
                    "Nb_Desired_Answers" => $nbAnswerDesired,
                    "Creation_Date" => date("Y-m-d H:i:s")
                );

                $idQuestion = $this->question_model->insert($inputQuestion);

                $column += 2;

                //Take next to the question the data to insert to 'T_Multiple_Answer'
                while($worksheet->getCellByColumnAndRow($column, $row)->getValue() != NULL)
                {
                    $answer = $worksheet->getCellByColumnAndRow($column, $row)->getValue();

                    $inputMultipleAnswer = array(
                        "FK_Question" => $idQuestion,
                        "Answer" => $answer,
                        "Creation_Date" => date("Y-m-d H:i:s")
                    );

                    $this->multiple_answer_model->insert($inputMultipleAnswer);

                    $column += 1;
                }

                $column = 0;
                $row++;
            }

        }

    }

    /**
     * Import 'Answer_Distribution' question type by Excel sheet 'DistributionReponses'
     * @param $idTopic = the topic select on the select attribute
     * @param $objReader = the object that allow we to read the excel sheet
     * @param $inputFileName = the name of sheet 'DistributionReponses'
     */
    private function Import_AnswerDistribution($idTopic, $objReader, $inputFileName)
    {
        $sheetName = 'DistributionReponses';
        $questionType = 3;

        /**  Advise the Reader of which WorkSheets we want to load  **/
        $objReader->setLoadSheetsOnly($sheetName);
        $objReader->setReadDataOnly(true);
        /**  Load $inputFileName to a PHPExcel Object  **/
        $objPHPExcel = $objReader->load($inputFileName);

        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
        {
            $column = 1;
            $row = 3;

            while($worksheet->getCellByColumnAndRow($column, $row)->getValue() != NULL)
            {
                if($worksheet->getCellByColumnAndRow($column - 1, $row)->getValue() != NULL)
                {
                    //Current question
                    $question = $worksheet->getCellByColumnAndRow($column - 1, $row)->getValue();

                    //Data to insert to the table 'T_Question'
                    $inputQuestion = array(
                        "FK_Topic" => $idTopic,
                        "FK_Question_Type" => $questionType,
                        "Question" => $question,
                        "Creation_Date" => date("Y-m-d H:i:s")
                    );

                    $idQuestion = $this->question_model->insert($inputQuestion);
                }

                $questionElement = $worksheet->getCellByColumnAndRow($column, $row)->getValue();
                $answerElement = $worksheet->getCellByColumnAndRow($column + 1, $row)->getValue();

                $inputAnswerDistribution = array(
                    "FK_Question" => $idQuestion,
                    "Question_Part" => $questionElement,
                    "Answer_Part" => $answerElement,
                    "Creation_Date" => date("Y-m-d H:i:s")
                );

                $this->answer_distribution_model->insert($inputAnswerDistribution);

                $row++;
            }

        }
    }

    /**
     * Import 'Cloze_Text' question type by Excel sheet 'TexteATrous'
     * @param $idTopic = the topic select on the select attribute
     * @param $objReader = the object that allow we to read the excel sheet
     * @param $inputFileName = the name of sheet 'TexteATrous'
     */
    private function Import_ClozeText($idTopic, $objReader, $inputFileName)
    {
        $sheetName = 'TexteATrous';
        $questionType = 4;

        /**  Advise the Reader of which WorkSheets we want to load  **/
        $objReader->setLoadSheetsOnly($sheetName);
        $objReader->setReadDataOnly(true);
        /**  Load $inputFileName to a PHPExcel Object  **/
        $objPHPExcel = $objReader->load($inputFileName);

        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
        {
            $column = 1;
            $row = 3;
            $answerOrder = 0;

            while($worksheet->getCellByColumnAndRow($column, $row)->getValue() != NULL)
            {
                if($worksheet->getCellByColumnAndRow($column - 1, $row)->getValue() != NULL)
                {
                    //Current question
                    $question = $worksheet->getCellByColumnAndRow($column - 1, $row)->getValue();

                    //Data to insert to the table 'T_Question'
                    $inputQuestion = array(
                        "FK_Topic" => $idTopic,
                        "FK_Question_Type" => $questionType,
                        "Question" => $question,
                        "Creation_Date" => date("Y-m-d H:i:s")
                    );

                    $idQuestion = $this->question_model->insert($inputQuestion);
                }

                $clozeText = $worksheet->getCellByColumnAndRow($column, $row)->getValue();

                $inputClozeText = array(
                    "FK_Question" => $idQuestion,
                    "Cloze_Text" => $clozeText,
                    "Creation_Date" => date("Y-m-d H:i:s")
                );

                $idClozeText = $this->cloze_text_model->insert($inputClozeText);

                $column = 2;

                while($worksheet->getCellByColumnAndRow($column, $row)->getValue() != NULL)
                {
                    $answerOrder++;
                    $answer = $worksheet->getCellByColumnAndRow($column, $row)->getValue();

                    $inputClozeTextAnswer = array(
                        "FK_Cloze_Text" => $idClozeText,
                        "Answer" => $answer,
                        "Answer_Order" => $answerOrder,
                        "Creation_Date" => date("Y-m-d H:i:s")
                    );

                    $this->cloze_text_answer_model->insert($inputClozeTextAnswer);

                    $column++;
                }

                $answerOrder = 0;
                $row++;
                $column = 1;
            }


        }
    }

    /**
     * Import 'Table_Cell' question type by Excel sheet 'Tableaux'
     * @param $idTopic = the topic select on the select attribute
     * @param $objReader = the object that allow we to read the excel sheet
     * @param $inputFileName = the name of sheet 'Tableaux'
     */
    private function Import_TableCell($idTopic, $objReader, $inputFileName)
    {
        $sheetName = 'Tableaux';
        $questionType = 5;
        $tableDefinition = false;

        /**  Advise the Reader of which WorkSheets we want to load  **/
        $objReader->setLoadSheetsOnly($sheetName);

        /**  Load $inputFileName to a PHPExcel Object  **/
        $objPHPExcel = $objReader->load($inputFileName);

        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
        {
            $column = 1;
            $row = 3;
            $regPattern = '/^\[.*\]$/';

            while($worksheet->getCellByColumnAndRow($column, $row)->getValue() != NULL)
            {
                if($worksheet->getCellByColumnAndRow($column - 1, $row)->getValue() != NULL &&
                    $worksheet->getCellByColumnAndRow($column - 1, $row + 1)->getValue() != NULL)
                {
                    //Current question
                    $question = $worksheet->getCellByColumnAndRow($column - 1, $row)->getValue();
                    $tableType = $worksheet->getCellByColumnAndRow($column - 1, $row + 1)->getValue();

                    if($tableType == 'simple')
                        $tableDefinition = false;
                    else
                        $tableDefinition = true;


                    //Data to insert to the table 'T_Question'
                    $inputQuestion = array(
                        "FK_Topic" => $idTopic,
                        "FK_Question_Type" => $questionType,
                        "Question" => $question,
                        "Table_With_Definition" => $tableDefinition,
                        "Creation_Date" => date("Y-m-d H:i:s")
                    );

                    $idQuestion = $this->question_model->insert($inputQuestion);

                    $nbColumn = 1;
                    $nbRow = 1;
                    $d = 1;
                }

                while($worksheet->getCellByColumnAndRow($column, $row)->getValue() != NULL) {

                    $tableCell = $worksheet->getCellByColumnAndRow($column, $row)->getValue();

                    //Test if the field is in bold
                    if ($worksheet->getCellByColumnAndRow($column, $row)->getStyle()->getFont()->getBold())
                    {
                        $header = true;
                    }else
                    {
                        $header = false;
                    }

                    //Test if the field need to be displayed
                    if (preg_match($regPattern, $tableCell))
                        $displayOnQuestion = true;
                    else
                        $displayOnQuestion = false;

                    $inputTableCell = array(
                        "FK_Question" => $idQuestion,
                        "Content" => $tableCell,
                        "Column_Nb" => $nbColumn,
                        "Row_Nb" => $nbRow,
                        "Header" => $header,
                        "Display_In_Question" => $displayOnQuestion,
                        "Creation_Date" => date("Y-m-d H:i:s")
                    );

                    $this->table_cell_model->insert($inputTableCell);

                    $nbColumn++;
                    $column++;
                }

                $nbColumn = 1;
                $nbRow++;
                $row++;
                $column = 1;
            }
        }
    }

    /**
     * Import 'Free_Answer' question type by Excel sheet 'ReponsesLibre'
     * @param $idTopic = the topic select on the select attribute
     * @param $objReader = the object that allow we to read the excel sheet
     * @param $inputFileName = the name of sheet 'ReponsesLibre'
     */
    private function Import_FreeAnswer($idTopic, $objReader, $inputFileName)
    {
        $sheetName = 'ReponsesLibre';
        $questionType = 6;


        /**  Advise the Reader of which WorkSheets we want to load  **/
        $objReader->setLoadSheetsOnly($sheetName);
        $objReader->setReadDataOnly(true);
        /**  Load $inputFileName to a PHPExcel Object  **/
        $objPHPExcel = $objReader->load($inputFileName);

        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
            $column = 0;
            $row = 3;


            while ($worksheet->getCellByColumnAndRow($column, $row)->getValue() != NULL) {

                $question = $worksheet->getCellByColumnAndRow($column, $row)->getValue();

                if($worksheet->getCellByColumnAndRow($column + 2, $row)->getValue() != NULL)
                {
                    $nbPoints = $worksheet->getCellByColumnAndRow($column + 2, $row)->getValue();
                }else
                {
                    $nbPoints = 1;
                }

                $inputQuestion = array(
                    "FK_Topic" => $idTopic,
                    "FK_Question_Type" => $questionType,
                    "Question" => $question,
                    "Points" => $nbPoints,
                    "Creation_Date" => date("Y-m-d H:i:s")
                );

                $idQuestion = $this->question_model->insert($inputQuestion);
                $answer = $worksheet->getCellByColumnAndRow($column + 1, $row)->getValue();

                $inputFreeAnswer = array(
                    "FK_Question" => $idQuestion,
                    "Answer" => $answer,
                    "Creation_Date" => date("Y-m-d H:i:s")
                );

                $this->free_answer_model->insert($inputFreeAnswer);

                $row++;
            }
        }
    }

    /**
     * Import 'Picture_Landmark' question type by Excel sheet 'ImageReperes'
     * @param $idTopic = the topic select on the select attribute
     * @param $objReader = the object that allow we to read the excel sheet
     * @param $inputFileName = the name of sheet 'ImageReperes'
     */
    private function Import_PictureLandmark($idTopic, $objReader, $inputFileName)
    {
        $sheetName = 'ImageReperes';
        $questionType = 7;
        $salt = 'f56ih58g0e';


        /**  Advise the Reader of which WorkSheets we want to load  **/
        $objReader->setLoadSheetsOnly($sheetName);
        $objReader->setReadDataOnly(true);
        /**  Load $inputFileName to a PHPExcel Object  **/
        $objPHPExcel = $objReader->load($inputFileName);

        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
            $column = 0;
            $row = 3;

            while ($worksheet->getCellByColumnAndRow($column, $row)->getValue() != NULL) {
                $question = $worksheet->getCellByColumnAndRow($column, $row)->getValue();
                //$pictureName = uniqid($salt).$worksheet->getCellByColumnAndRow($column + 1, $row)->getValue();
				$pictureName = $worksheet->getCellByColumnAndRow($column + 1, $row)->getValue();

                $inputQuestion = array(
                    "FK_Topic" => $idTopic,
                    "FK_Question_Type" => $questionType,
                    "Question" => $question,
                    "Picture_Name" => $pictureName,
                    "Creation_Date" => date("Y-m-d H:i:s")
                );

                $idQuestion = $this->question_model->insert($inputQuestion);

                $column = 2;

                while ($worksheet->getCellByColumnAndRow($column, $row)->getValue() != NULL)
                {
                    $symbol = $worksheet->getCellByColumnAndRow($column, $row)->getValue();
                    $answer = $worksheet->getCellByColumnAndRow($column, $row + 1)->getValue();

                    $inputPictureLandmarks = array(
                        "FK_Question" => $idQuestion,
                        "Symbol" => $symbol,
                        "Answer" => $answer,
                        "Creation_Date" => date("Y-m-d H:i:s")
                    );

                    $this->picture_landmark_model->insert($inputPictureLandmarks);

                    $column++;
                }

                $column = 0;
                $row += 2;
            }
        }
    }
}