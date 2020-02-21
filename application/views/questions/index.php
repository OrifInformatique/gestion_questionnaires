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

        <form onsubmit="return changeselect()">
            <div class="row">
                <div class="col-lg-4 form-group">
                    <b class="form-label"><?php echo $this->lang->line('focus_module'); ?></b>
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
                <div class="col-lg-4 form-group">
                    <b class="form-label"><?php echo $this->lang->line('focus_topic'); ?></b>
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
                <div class="col-lg-4 form-group">
                    <b class="form-label"><?php echo $this->lang->line('question_type'); ?></b>
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
                <div class="col-8 form-group">
                    <b class="form-label"><?php echo $this->lang->line('text_search'); ?></b>
                    <?php
                        if(isset($_GET['search'])){
                            echo form_input('search', set_value('search', $_GET['search']), ' class="form-control" id="search"');
                        } else {
                            echo form_input('search', '', ' class="form-control" id="search"');
                        }
                    ?>
                </div>
                <div class="col-lg-4 form-group">
                    <br>
                    <a href="<?php echo base_url('Question/reset_filters');?>" class="col-12 utton-align btn btn-secondary no-border" ><?php echo $this->lang->line('clear_filters'); ?></a>
                </div>
            </div>
        </form>
        <hr>
        <div class="row">
            <div class="col-12 col-sm-4 form-group">
                <a class="col-12 btn btn-primary" style="margin-bottom: 10px;" href="<?php echo base_url('Question/add');?>"><?php echo $this->lang->line('btn_add_question');?></a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div id="pagination_top"><?=$pagination?></div>
            </div>
            <div class="offset-2 col-lg-2 text-right">
                <b class="form-label"><?=$this->lang->line('field_limit')?> :</b>
            </div>
            <div class="col-lg-2 text-right">
                <?=form_dropdown('limit', $limit_options, $limit_per_page, 'class="form-control" id="limit" onchange="changeselect()"')?>
            </div>
        </div>
        <div class="row">
            <?php 
                $question_sort='▲▼';
                $question_type_sort='▲▼';
                $points_sort='▲▼';
                if (isset($_GET['sort'])){
                    switch ($_GET['sort']){
                        case 'question_asc':
                            $question_sort='▼';
                            break;
                        case 'question_desc':
                            $question_sort='▲';
                            break;
                        case 'question_type_asc':
                            $question_type_sort='▼';
                            break;
                        case 'question_type_desc':
                            $question_type_sort='▲';
                            break;
                        case 'points_asc':
                            $points_sort='▼';
                            break;
                        case 'points_desc':
                            $points_sort='▲';
                            break;
                    }
                }
            ?>
            <br>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>
                                <?php 
                                    echo $this->lang->line('question'); 
                                    echo "<a onclick='sortClick(\"".(isset($_GET['sort'])?$_GET['sort']."\"":"\"").", \"question\")' class='sorted_btn btn btn-default'>$question_sort</a>" 
                                ?>
                            </th>
                            <th><?php 
                                    echo $this->lang->line('question_type');
                                    echo "<a onclick='sortClick(\"".(isset($_GET['sort'])?$_GET['sort']."\"":"\"").", \"question_type\")' class='sorted_btn btn btn-default'>$question_type_sort</a>" 
                                ?>  
                            </th>
                            <th>
                                <?php   
                                    echo $this->lang->line('points'); 
                                    echo "<a onclick='sortClick(\"".(isset($_GET['sort'])?$_GET['sort']."\"":"\"").", \"points\")' class='sorted_btn btn btn-default'>$points_sort</a>" 
                                ?>
                            </th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $compteur = 0;
                        foreach ($questions as $objet => $question) {

                            $compteur ++;
                            displayQuestion($question);
                        }
                        ?>
                    </tbody>
                </table>
                <?php
                if($compteur == 0){
                    echo "<div class='alert alert-danger'>"
                    . $this->lang->line('no_question') . "</div>";
                }
                ?>
            </div>
        </div>
        <div id="pagination_top"><?=$pagination?></div>
    </div>
</div>

<?php
function displayQuestion($question)
{
    ?>
    <tr id="<?php echo $question->ID; ?>" >
        <td id="question"><a href="<?=base_url()?>Question/update/<?php echo $question->ID;?>">
            <?php 
            //cut and add "..." if number of letters exceeds 300
            echo substr($question->Question, 0,300);
            echo (strlen($question->Question)>=300)?"...":"";
            echo "</a>";

            if(count($question->cloze_text) > 0){
                //if it's a "Cloze_text" question type, show the 75 first character of cloze_text
                $cloze_text = str_replace('…', '...', $question->cloze_text[0]->Cloze_Text);

                if(strlen($cloze_text) > 75){
                    $cloze_text = substr($cloze_text, 0, 75) . "...";
                }
                echo "<br>".$cloze_text;
            }
            ?>
        </td>
        <td><?php echo $question->question_type->Type_Name ?></td>
        <td style="text-align: center;"><?php echo $question->Points; ?></td>
        <td style="text-align: center;"><a class="close" id="btn_update" href="<?=base_url()?>Question/update/<?php echo $question->ID ?>">✎</a></td>
        <td style="text-align: center;"><a class="close" id="btn_del" href="<?=base_url()?>Question/delete/<?php echo $question->ID ?>">×</a></td>
    </tr>
    <?php
}
?>
