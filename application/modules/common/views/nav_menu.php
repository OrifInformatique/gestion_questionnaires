<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
if(!function_exists('test_regex')) {
    /**
     * Test the pattern to find the active page
     * @param $pattern = related tab
     */
    function test_regex($pattern)
    {
        $subject = $_SERVER['REQUEST_URI'];
        
        if (preg_match($pattern, $subject)) {
            echo "active";
        }
    }
}
if(!function_exists('checkactive')) {
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
            case 0;
                test_regex('/\/Home/i');
                test_regex('/\//');
                break;
            case 1;
                test_regex('/\/Questionnaire/i');
                break;
            case 2:
                test_regex('/\/(Question[^n]|Question$)/i');
                break;
            case 3:
                test_regex('/\/Module/i');
                break;
            case 4:
                test_regex('/\/Topic/i');
                break;
            case 5:
                test_regex('/\/Admin/i');
                break;
            case 6:
                test_regex('/\/Support/i');
                break;
            default:
                break;
        }
    }
}

// Display nav menu only if user is logged in
if(isset($_SESSION['logged_in']) && ($_SESSION['logged_in']==TRUE))
{
?>
    <div class="container" >
        <nav class="navbar navbar-expand-lg navbar-light bg-secondary">
            <ul class="navbar-nav mr-auto">
                <?php if($_SESSION['user_access'] >= $this->config->item('access_lvl_manager')) { ?>
                    <li class="nav-item <?php checkactive(1); ?>">
                        <a class="nav-link" href="<?php echo base_url('questionnaire');?>">
                            <?= $this->lang->line('nav_questionnaire');?>
                        </a>
                    </li>
                    <li class="nav-item <?php checkactive(2); ?>">
                        <a class="nav-link" href="<?php echo base_url('question');?>">
                            <?= $this->lang->line('nav_question');?>
                        </a>
                    </li>
                    <li class="nav-item <?php checkactive(4); ?>">
                        <a class="nav-link" href="<?php echo base_url('topic');?>">
                            <?= $this->lang->line('nav_topic');?>
                        </a>
                    </li>
                    <?php if($_SESSION['user_access'] >= $this->config->item('access_lvl_admin')) { ?>
                        <li class="nav-item <?php checkactive(5); ?>">
                            <a class="nav-link" href="<?php echo base_url('admin');?>">
                                <?= $this->lang->line('nav_admin');?>
                            </a>
                        </li>
                    <?php } ?>
                <?php } else { ?>
                    <li class="nav-item <?php checkactive(0); ?>">
                        <a class="nav-link" href="<?php echo base_url('home');?>">
                            <?php echo $this->lang->line('nav_home');?>
                        </a>
                    </li>
               <?php } ?>
            </ul>
            
            <!-- Button to submit a problem -->
            <?php if($_SESSION['user_access'] >= $this->config->item('access_lvl_manager')) { ?>
                <a class="btn btn-outline-warning pull-right" href="<?php echo base_url('support');?>">
                    <?= $this->lang->line('nav_support');?>
                </a>
            <?php } ?>
        </nav>
    </div>
<?php } ?>
