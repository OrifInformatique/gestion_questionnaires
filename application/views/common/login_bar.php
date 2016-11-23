<?php
/**
 * Created by PhpStorm.
 * User: UlSi
 * Date: 26.10.2016
 * Time: 08:50
 */
    if(isset($_SESSION['logged_in']))
    {
        ?>
        <nav class="navbar navbar-default" style="background-color: lavender">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand"><?php echo $this->lang->line('nav_home');?></a>
                </div>
                <ul class="nav navbar-nav">
                    <li <?php checkactive(1); ?>><a href="<?php echo base_url('Questionnaire/Questionnaires_list');?>">
                            <?php echo $this->lang->line('nav_questionnaire');?></a></li>
                    <li <?php checkactive(2); ?>><a href="<?php echo base_url('Question/list_questions');?>">
                            <?php echo $this->lang->line('nav_question');?></a></li>
                    <li <?php checkactive(3); ?>><a href="#"><?php echo $this->lang->line('nav_module');?></a></li>
                    <li <?php checkactive(4); ?>><a href="#"><?php echo $this->lang->line('nav_topic');?></a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="../../index.php/Auth/unlog"><span class="glyphicon glyphicon-log-in"></span><?php echo $this->lang->line('unlog');?></a></li>
                </ul>
            </div>
        </nav>
<?php
    }

function test_regex($pattern)
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
?>