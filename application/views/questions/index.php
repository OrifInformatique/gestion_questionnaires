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
            <h1 style="padding-top: 12%; padding-bottom: 5%" class="text-center"><?php echo $this->lang->line('title_question'); ?></h1>
            <div class="row">
                <div class="col-lg-2"></div>
                <div class="col-lg-4" style="height:110px;">
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
                <div class="col-lg-4" style="height:110px;">
                    <a href="Question?"><button type="button"><?php echo $this->lang->line('clear_filters'); ?></button></a>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-2"></div>
                <div class="col-lg-4" style="height:110px;">
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
                <div class="col-lg-2">
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
            </div>
            <div class="row">
                <div class="col-lg-2"></div>
                <div class="col-lg-8">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('question'); ?></th>
                            <th><?php echo $this->lang->line('question_type'); ?></th>
                            <th><?php echo $this->lang->line('points'); ?></th>
                        </tr>
                        </thead>
                        <?php
                        $compteur = 0;

                        foreach ($questions as $objet => $question) {
                            $compteur += 1;
                            displayQuestion($question);
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
    <script>
        window.onload = init();
    </script>
<?php
function displayQuestion($question)
{
    ?>
    <tr id="<?php echo $question->ID; ?>" onclick="getID(<?php echo $question->ID;?>, 2)">
        <td><a href="./Question/detail/<?php echo $question->ID;?>"><?php echo $question->Question; ?></a></td>
        <td><?php echo $question->question_type->Type_Name ?></td>
        <td><?php echo $question->Points; ?></td>
    </tr>
    <?php
}
?>