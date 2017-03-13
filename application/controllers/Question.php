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
        $this->load->model(array('question_model', 'question_type_model', 'topic_model', 'multiple_choice_model'));
        $this->load->helper(array('url', 'form'));
        $this->load->library(array('PHPExcel-1.8/Classes/PHPExcel', 'upload'));
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
        $id = $this->input->post('id');

        if ($this->form_validation->run() == true) {

            $this->index();
        } else {
            ;
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
            $output['question_types'] = $this->question_type_model->get_all();

            $this->display_view('questions/update', $output);
        } else {

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
    public function importExcel()
    {

        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'xlsx';
        $config['max_size'] = 100;
        $config['max_width'] = 1024;
        $config['max_height'] = 768;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if (!$this->upload->do_upload('excelfile')) {
            $error = array('error' => $this->upload->display_errors());

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
            //Import_MultipleAnswers();

            ?>
            <script>alert("Importation terminée !");</script>
            <?php
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

    }


    /**
     * Import 'Multiple_Choice' question type by Excel sheet 'ChoixMultiple'
     * @param $idTopic = the topic select on the select attribute
     * @param $objReader = the object that allow we to read the excel sheet
     * @param $inputFileName = the name of sheet 'ChoixMultiple'
     */
    private function Import_MultipleChoices($idTopic, $objReader, $inputFileName)
    {
        $sheetname = "ChoixMultiples";
        define("QUESTION_TYPE", 1);
        define("ID_TOPIC", $idTopic);

        /**  Advise the Reader of which WorkSheets we want to load  **/
        $objReader->setLoadSheetsOnly($sheetname);
        $objReader->setReadDataOnly(true);
        /**  Load $inputFileName to a PHPExcel Object  **/
        $objPHPExcel = $objReader->load($inputFileName);

        //$chunkFilter = new chunkReadFilter(1,18);
        /**  Tell the Reader that we want to use the new Read Filter that we've just Instantiated
        $objReader->setReadFilter($chunkFilter);**/

        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
        {

            $column = 0;
            $row = 3;

            //Browse the sheet
            while($worksheet->getCellByColumnAndRow(0, $row)->getValue() != NULL)
            {
                //Current question
                $question = $worksheet->getCellByColumnAndRow(0, $row)->getValue();

                //Data to insert to the table 'T_Question'
                $inputQuestion = array(
                                "FK_Topic" => ID_TOPIC,
                                "FK_Question_Type" => QUESTION_TYPE,
                                "Question" => $question
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
                        "Valid" => $valid
                    );

                    $this->multiple_choice_model->insert($inputMultipleChoice);
                    $column += 1;
                }
                $row += 2;
                $column = 0;
            }
        }
    }
}