<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
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
            test_regex('/\/Questionnaire/');
            break;
        case 2:
            test_regex('/\/(Question[^n]|Question$)/');
            break;
        case 3:
            test_regex('/\/Module/');
            break;
        case 4:
            test_regex('/\/Topic/');
            break;
        default:
            break;
    }
}

// Display nav menu only if user is logged in
if(isset($_SESSION['logged_in']) && ($_SESSION['logged_in']==TRUE))
{
?>
    <div id="myNavbar" class="container">
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
    </div>
<?php
    }
?>