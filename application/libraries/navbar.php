<?php
/**
 * Created by PhpStorm.
 * User: UlSi
 * Date: 15.11.2016
 * Time: 14:19
 */

class Navbar {

    /**
     * @param $pattern = onglet title
     */
    public function test_regex($pattern)
    {

        $subject = $_SERVER['REQUEST_URI'];

        if (preg_match($pattern, $subject)) {
            echo "style='background-color: white'";
        }
    }
    /**
     * Test of the current active page
     * @param $page =
     * 1 => Questionnaire
     * 2 => Question
     * 3 => Module
     * 4 => Topic
     * **/
    public function checkactive($page){
        switch ($page){
            case 1;
                test_regex('/\/Questionnaire\//');
                break;
            case 2:
                test_regex('/\/Question\//');
                break;
            case 3:
                test_regex('/\/Module\//');
                break;
            case 4:
                test_regex('/\/Topic\//');
                break;
            default:
                break;
        }
    }
}