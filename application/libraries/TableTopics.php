<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This object is used to load in all elements for futur questionnaire
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class TableTopics
{

    private $title = '';
    private $PDFName = '';
    private $arrayTopics = array();
    private $arrayNbQuestion = array();

    /**
     * TableTopics constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return array
     */
    public function getArrayNbQuestion()
    {
        return $this->arrayNbQuestion;
    }

    /**
     * @param array $arrayNbQuestion
     */
    public function setArrayNbQuestion($arrayNbQuestion)
    {
        array_push($this->arrayNbQuestion, $arrayNbQuestion);
    }

    /**
     * @param int $index
     */
    public function removeArrayNbQuestion($index)
    {
        array_splice($this->arrayNbQuestion, $index, 1);
    }

    /**
     * @return array
     */
    public function getArrayTopics()
    {
        return $this->arrayTopics;
    }

    /**
     * @param array $arrayTopics
     */
    public function setArrayTopics($arrayTopics)
    {
        array_push($this->arrayTopics, $arrayTopics);
    }

    /**
     * @param int $index
     */
    public function removeArrayTopics($index)
    {
        array_splice($this->arrayTopics, $index, 1);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getPDFName()
    {
        return $this->PDFName;
    }

    /**
     * @param string $title
     */
    public function setPDFName($title)
    {
        $this->PDFName = $PDFName;
    }
}