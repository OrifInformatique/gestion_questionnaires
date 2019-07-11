<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * View of question's list
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
?>
    <div id="wrapper">

        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <li class="sidebar-brand">
                    <a href="#">
                        <?php echo $this->lang->line('nav_question');?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo base_url('Question/add');?>"><?php echo $this->lang->line('btn_add');?></a>
                </li>
                <li>
                    <a href="<?php echo base_url('Question/import');?>"><?php echo $this->lang->line('btn_import');?></a>
                </li>
                <li>
                    <a id="btn_update"><?php echo $this->lang->line('btn_update');?></a>
                </li>
                <li>
                    <a id="btn_del"><?php echo $this->lang->line('btn_del');?></a>
                </li>
            </ul>
        </div>
    </div>
    <div id="page-content-wrapper">
        <div class="container">
            <div class="row">
                <h2><?php echo $this->lang->line('delete_questions_questionnaire_list'); ?></h2>
            </div>
            <div class="row">
                <div class="col-lg-2"></div>
                <div class="col-lg-8">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('title_questionnaire'); ?></th>
                        </tr>
                        </thead>
                        <?php
                        $compteur = 0;

                        foreach ($questionnaires as $objet => $questionnaire) {
                            $compteur += 1;
                            displayQuestion($questionnaire);
                        }

                        if($compteur == 0){
                            echo "<div class='well' style='border: solid 2px red;'><h4>"
                                . $this->lang->line('no_question') . "</h4></div>";
                        }

                        ?>
                    </table>
                </div>
                <div class="col-lg-2"></div>
            </div>
        </div>
    </div>
<?php
function displayQuestion($questionnaire)
{
    ?>
    <tr>
        <td><?php echo $questionnaire->Questionnaire_Name; ?></td>
    </tr>
    <?php
}
?>