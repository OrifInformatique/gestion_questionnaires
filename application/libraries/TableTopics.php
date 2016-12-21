<?php

/**
 * Created by PhpStorm.
 * User: UlSi
 * Date: 19.12.2016
 * Time: 11:06
 */
class TableTopics
{

    private $title = '';
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
    
}