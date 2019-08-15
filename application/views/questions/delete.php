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
                        <?= $this->lang->line('nav_question');?>
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('Question/add');?>"><?= $this->lang->line('btn_add');?></a>
                </li>
                <li>
                    <a href="<?= base_url('Question/import');?>"><?= $this->lang->line('btn_import');?></a>
                </li>
                <li>
                    <a id="btn_update"><?= $this->lang->line('btn_update');?></a>
                </li>
                <li>
                    <a id="btn_del"><?= $this->lang->line('btn_del');?></a>
                </li>
            </ul>
        </div>
    </div>
    <div id="page-content-wrapper">
        <div class="container">
            <div class="row">
                <h2><?= $this->lang->line('delete_questions_questionnaire_list'); ?></h2>
            </div>
            <div class="row">
                <div class="col-lg-2"></div>
                <div class="col-lg-8">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><?= $this->lang->line('title_questionnaire'); ?></th>
                            </tr>
                        </thead>
                        <?php
                        foreach ($questionnaires as $questionnaire) {
                            displayQuestion($questionnaire);
                        }

                        if(!count($questionnaires)){
                            echo "<div class='well' style='border: solid 2px red;'><h4>".$this->lang->line('no_question')."</h4></div>";
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
{ ?>
    <tr>
        <td><?= $questionnaire->Questionnaire_Name; ?></td>
    </tr>
<?php } ?>