<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * View of question's list
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
?>
    <div id="page-content-wrapper">
        <div class="container">
            <h1 class="title-section"><?php echo $this->lang->line('title_question'); ?></h1>
            <div class="row">
               <div class="col-lg-4">
                    <h4><?php echo $this->lang->line('focus_module'); ?></h4>
                    <select onchange="changeselect()" class="form-control" id="module_selected">
                        <?php
                        echo "<option selected disabled hidden></option>";
                        echo '<option value="">'.$this->lang->line('clear_filter')."</option>";

                        //Récupère chaque topics
                        foreach ($topics as $object => $module) {
                            if ($module->FK_Parent_Topic == 0) {
                                ?>
                                    <option value='<?php echo $module->ID; ?>' <?php if(isset($_GET['module'])){if($module->ID==$_GET['module']){echo"selected";}}?>><?php echo $module->Topic; ?>
                                    </option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-lg-8">
                    <h4><?php echo $this->lang->line('focus_topic'); ?></h4>
                    <select onchange="changeselect()" class="form-control" id="topic_selected">
                        <?php

                        echo "<option selected disabled hidden></option>";
                        echo '<option value="">'.$this->lang->line('clear_filter')."</option>";

                        //Récupère chaque topics
                        if(empty($_GET['module'])){
                            foreach ($topics as $object => $module) {
                                if ($module->FK_Parent_Topic == 0) {
                                    //Affiche le topic parent
                                    echo "<optgroup label='$module->Topic' >";

                                    //Récupère chaque topic associé au topic parent
                                    for ($i = 0; $i < count($topics); $i++) {
                                        if ($module->ID == $topics[$i]->FK_Parent_Topic) {
                                            //Affiche les topics associés ?>
                                            <option value='<?php echo $topics[$i]->ID; ?>' <?php if(isset($_GET['topic'])){if($topics[$i]->ID==$_GET['topic']){echo"selected";}}?>><?php echo $topics[$i]->Topic; ?>
                                            </option>
                                            <?php
                                        }
                                    }

                                    echo "</optgroup>";
                                }
                                
                            }
                        } else {
                            foreach ($topics as $object => $module) {
                                if ($module->FK_Parent_Topic == $_GET['module']) {
                                    //Affiche le topic parent
                                     ?>
                                    <option value='<?php echo $module->ID; ?>' <?php if(isset($_GET['topic'])){if($module->ID==$_GET['topic']){echo"selected";}}?>><?php echo $module->Topic; ?>
                                    </option>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-lg-8 col-sm-6">
                    <h4><?php echo $this->lang->line('question_type'); ?></h4>
                    <select onchange="changeselect()" class="form-control" id="question_type_selected">
                        <?php
                        
                        echo "<option selected disabled hidden></option>";
                        echo '<option value="">'.$this->lang->line('clear_filter')."</option>";


                        //Récupère chaque topics
                        foreach ($questionTypes as $object => $module) {
                                ?>
                                <option value='<?php echo $module->ID; ?>' <?php if(isset($_GET['type'])){if($module->ID==$_GET['type']){echo"selected";}}?>><?php echo $module->Type_Name; ?></option>
                                <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-4 col-sm-offset-2 col-lg-offset-0 col-xs-12">
                    <a href="Question?" class="col-xs-12 button-align btn btn-default xs-space" ><?php echo $this->lang->line('clear_filters'); ?></a>
                </div>

            </div>
                <hr></hr>
            <div class="row">
                <div class="col-xs-12 col-sm-4">
                    <a class="col-xs-12 btn btn-success" style="margin-bottom: 10px;" href="<?php echo base_url('Question/add');?>"><?php echo $this->lang->line('btn_add_question');?></a>
                </div>
                <div class="col-xs-12 col-sm-offset-4 col-sm-4">
                    <a class="col-xs-12 btn btn-info"  href="<?php echo base_url('Question/import');?>"><?php echo $this->lang->line('btn_import_question');?></a>
                </div>
            </div>

            <div class="row">

                <br>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><?= $this->lang->line('question'); ?></th>
                                <th><?= $this->lang->line('question_type'); ?></th>
                                <th><?= $this->lang->line('points'); ?></th>
                            </tr>
                        </thead>
                        <?php
                        $compteur = 0;

                        foreach ($questions as $objet => $question) {
                            $compteur ++;
                            displayQuestion($question);
                        }

                        

                        ?>
                    </table>
                    <?php
                    if($compteur == 0){
                        echo "<div class='well' style='border: solid 2px red;'><h4>"
                        . $this->lang->line('no_question') . "</h4></div>";
                    }
                    ?>
                </div>
            </div>
        </div> 
    </div>
    <script>
        window.onload = init();
    </script>

<?php
function displayQuestion($question)
{
    ?>
    <tr id="<?php echo $question->ID; ?>" >
        <td><a href="./Question/detail/<?php echo $question->ID;?>">
            <?php 
            //cut and add "..." if number of letters exceeds 300
            echo substr($question->Question, 0,300);
            echo (strlen($question->Question)>=300)?"...":"";
            ?>
        </a></td>
        <td><?php echo $question->question_type->Type_Name ?></td>
        <td style="text-align: right;"><?php echo $question->Points; ?></td>
        <td style="text-align: center;"><a class="close" id="btn_update" onclick="updateItem(<?=$question->ID?>,2)">✎</a></td>
        <td style="text-align: center;"><a class="close" id="btn_del" onclick="deleteItem(<?=$question->ID?>,2)">×</a></td>
    </tr>
    <?php
}
?>