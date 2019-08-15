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
                    <h4><?= form_label($this->lang->line('focus_module'), 'module_selected'); ?></h4>
                    <select onchange="changeselect()" class="form-control" id="module_selected">
                        <?php
                        echo "<option selected disabled hidden></option>";
                        echo '<option value="">'.$this->lang->line('clear_filter')."</option>";

                        // Loads every topic
                        foreach ($topics as $module) {
                            if($module->FK_Parent_Topic) continue;
                            ?>
                                <option value='<?= $module->ID; ?>'
                                <?php if($module->ID == $this->input->get('module')) {
                                    echo"selected";
                                }?>><?= $module->Topic; ?>
                                </option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="col-lg-4">
                    <h4><?= form_label($this->lang->line('focus_topic'), 'topic_selected'); ?></h4>
                    <select onchange="changeselect()" class="form-control" id="topic_selected">
                        <?php

                        echo "<option selected disabled hidden></option>";
                        echo '<option value="">'.$this->lang->line('clear_filter')."</option>";

                        // Loads every topic
                        if(empty($_GET['module'])){
                            foreach ($topics as $module) {
                                // Skip children topics
                                if($module->FK_Parent_Topic) continue;

                                // Displays the parent topic
                                echo "<optgroup label='$module->Topic' >";

                                // Selects the parent topic's children
                                foreach ($topics as $topic) {
                                    if ($module->ID == $topic->FK_Parent_Topic) {
                                        // Displays the children ?>
                                        <option value='<?= $topic->ID; ?>'
                                            <?php if($topic->ID == $this->input->get('topic')) {
                                                echo "selected";
                                            }?>><?= $topic->Topic; ?>
                                        </option>
                                        <?php
                                    }
                                }

                                echo "</optgroup>";
                                
                            }
                        } else {
                            foreach ($topics as $module) {
                                if ($module->FK_Parent_Topic == $_GET['module']) {
                                    // Display the parent topic ?>
                                    <option value='<?= $module->ID; ?>' <?php 
                                    if($module->ID == $this->input->get('topic')){
                                        echo "selected";
                                    }?>><?= $module->Topic; ?>
                                    </option>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-lg-4">
                    <h4><?= form_label($this->lang->line('question_type'), 'question_type_selected'); ?></h4>
                    <select onchange="changeselect()" class="form-control" id="question_type_selected">
                        <?php
                        
                        echo "<option selected disabled hidden></option>";
                        echo '<option value="">'.$this->lang->line('clear_filter')."</option>";


                        //Récupère chaque topics
                        foreach ($questionTypes as $module) {
                                ?>
                                <option value='<?= $module->ID; ?>' <?php if($module->ID == $this->input->get('type')){
                                    echo"selected";
                                }?>><?= $module->Type_Name; ?></option>
                                <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8">
                    <h4><?= form_label($this->lang->line('search'), 'search'); ?></h4>
                    <?= form_input('search', ($this->input->get('search') ?? ''), [
                        'class' => 'form-control', 'id' => 'search'
                    ]); ?>
                </div>
                <div class="col-lg-4">
                    <?= form_button('', $this->lang->line('filter'), [
                        'class' => 'col-xs-12 button-align btn btn-primary xs-space',
                        'onclick' => 'changeselect()'
                    ]); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <a href="<?= base_url('Question/resetFilters');?>" class="col-xs-12 button-align btn btn-default xs-space" >
                        <?= $this->lang->line('clear_filters'); ?>

                    </a>
                </div>
            </div>
                <hr>
            <div class="row">
                <div class="col-xs-12 col-sm-4">
                    <a class="col-xs-12 btn btn-success" style="margin-bottom: 10px;" href="<?= base_url('Question/add');?>">
                        <?= $this->lang->line('btn_add_question');?>
                    </a>
                </div>
                <div class="col-xs-12 col-sm-offset-4 col-sm-4">
                    <a class="col-xs-12 btn btn-info" href="<?= base_url('Question/import');?>">
                        <?= $this->lang->line('btn_import_question');?>
                    </a>
                </div>
            </div>
            <div id="pagination_top"><?=$pagination?></div>
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
                                        echo '<a onclick=\'sortClick("'.((string)$this->input->get('sort')).'", "question")\'';
                                        echo ' class="sorted_btn btn btn-default">';
                                        echo $question_sort.'</a>';
                                    ?>
                                </th>
                                <th><?php 
                                        echo $this->lang->line('question_type');
                                        echo '<a onclick=\'sortClick("'.((string)$this->input->get('sort')).'", "question_type")\'';
                                        echo ' class="sorted_btn btn btn-default">';
                                        echo $question_sort.'</a>';
                                    ?>  
                                    </th>
                                <th>
                                    <?php   
                                        echo $this->lang->line('points');
                                        echo '<a onclick=\'sortClick("'.((string)$this->input->get('sort')).'", "points")\'';
                                        echo ' class="sorted_btn btn btn-default">';
                                        echo $question_sort.'</a>';
                                    ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($questions as $question) {
                                displayQuestion($question);
                            }
                            ?>
                        </tbody>
                    </table>
                    <?php
                    if(!count($questions)){
                        echo "<div class='well' style='border: solid 2px red;'><h4>".$this->lang->line('no_question')."</h4></div>";
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
    <tr id="question_<?= $question->ID; ?>" >
        <td><a href="<?=base_url('Question/detail/').$question->ID;?>">
            <?php
            //cut and add "..." if number of letters exceeds 300
            echo substr($question->Question, 0,300);
            echo (strlen($question->Question)>=300)?"...":"";
            ?></a><?php
            if(count($question->cloze_text)){
                //if it's a "Cloze_text" question type, show the 75 first character of cloze_text
                $cloze_text = str_replace('…', '...', $question->cloze_text[0]->Cloze_Text);

                if(strlen($cloze_text) > 75){
                    $cloze_text = substr($cloze_text, 0, 75) . "...";
                }
                echo "<br>".$cloze_text;
            }
            ?>
        </td>
        <td><?= $question->question_type->Type_Name; ?></td>
        <td style="text-align: center;"><?= $question->Points; ?></td>
        <td style="text-align: center;"><a class="close" href="<?=base_url('Question/update/').$question->ID; ?>">✎</a></td>
        <td style="text-align: center;"><a class="close" href="<?=base_url('Question/delete/').$question->ID; ?>">×</a></td>
    </tr>
<?php } ?>
