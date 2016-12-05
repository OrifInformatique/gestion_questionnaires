<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Question Type model is used to get different types for each question.
 * question is used to get related question type for each question.
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */

/**
 * Test the pattern to find the active page
 * @param $pattern = related tab
 */
function test_regex($pattern)
{

    $subject = $_SERVER['REQUEST_URI'];

    if (preg_match($pattern, $subject)) {
        echo "class='active'";
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
function checkactive($page){
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

    if(isset($_SESSION['logged_in']))
    {
        ?>
        <nav class="navbar navbar-inverse navbar navbar-fixed-top" style="border-bottom: 0;">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a href="<?php echo base_url('Home');?>" class="navbar-brand"><?php echo $this->lang->line('nav_home');?></a>
                </div>
                <div class="collapse navbar-collapse" id="myNavbar">
                    <ul class="nav navbar-nav">
                        <li <?php checkactive(1); ?>><a href="<?php echo base_url('Questionnaire');?>">
                                <?php echo $this->lang->line('nav_questionnaire');?></a></li>
                        <li <?php checkactive(2); ?>><a href="<?php echo base_url('Question');?>">
                                <?php echo $this->lang->line('nav_question');?></a></li>
                        <li <?php checkactive(3); ?>><a href="<?php echo base_url('Module');?>">
                                <?php echo $this->lang->line('nav_module');?></a></li>
                        <li <?php checkactive(4); ?>><a href="<?php echo base_url('Topic');?>">
                                <?php echo $this->lang->line('nav_topic');?></a></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="<?php echo base_url('Auth/unlog');?>"><span class="glyphicon glyphicon-log-in"></span><?php echo $this->lang->line('unlog');?></a></li>
                    </ul>
                </div>
            </div>
        </nav>
<?php
    }
?>