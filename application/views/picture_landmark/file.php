<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * View of question's details to update
 *
 * @author      Orif, section informatique (BuYa, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
?>

<div class="container">
    <h1 class="title-section"><?php echo $this->lang->line('title_question_add'); ?></h1>
    <?php
    $attributes = array("id" => "addQuestionForm",
                        "name" => "addQuestionForm");
    echo form_open_multipart('Question/add_picture_landmark', $attributes);
    ?>

        <!-- Hidden fields to put informations in $_POST -->
        <?php
        echo form_hidden('focus_topic', $focus_topic->ID);
        echo form_hidden('question_type', $question_type->ID);
        echo form_hidden('nbAnswer', $nbAnswer);

        if(isset($id)){
            echo form_hidden('id', $id);
        }
        
        if(isset($upload_data)){
            echo form_hidden('upload_data', $upload_data);
        }
        if(isset($picture_name)){
            echo form_hidden('picture_name', $picture_name);
        }
        if(isset($name)){
            echo form_hidden('name', $name);
        }
        if(isset($points)){
            echo form_hidden('points', $points);
        }

        if(isset($answers)){
            for ($i = 0; $i < $nbAnswer; $i++){
                echo form_hidden('reponses['.$i.'][symbol]', $answers[$i]['symbol']);
                echo form_hidden('reponses['.$i.'][id]', $answers[$i]['id']);
                echo form_hidden('reponses['.$i.'][answer]', $answers[$i]['answer']);
            }
        }
        ?>
    
        <div class="row">
            <div class="col-sm-12 text-right">
                <b class="form-header"><?php echo $this->lang->line('question_type').' : '.$question_type->Type_Name; ?></b>
            </div>
        </div>

        <!-- ERROR MESSAGES -->
        <?php
            if(isset($error)){
                echo $error;
            }
        ?>
        
        <!-- PICTURE UPLOAD -->
        <div class="row">
            <div class="form-group col-md-12"><?php echo form_upload('picture', '', 'id="picture" class="form-control"'); ?></div>
        </div>

        <!-- Display buttons and display topic and question type as information -->
        <div class="row">
            <div class="col-12 text-right">
                <a id="btn_cancel" class="btn btn-default" href="<?=base_url('/Question')?>"><?=$this->lang->line('btn_cancel')?></a>
                <?php
                    echo form_submit('save', $this->lang->line('save'), 'class="btn btn-primary"'); 
                ?>
            </div>
        </div>
        
    <?php echo form_close(); ?>
</div>