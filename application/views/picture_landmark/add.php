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
    <h1 class="title-section"><?php echo $this->lang->line('title_question_update'); ?></h1>
    <?php
    $attributes = array("id" => "addQuestionForm",
                        "name" => "addQuestionForm");
    echo form_open('Question/add_picture_landmark/2', $attributes);
    ?>
    
        
        <!-- Hidden fields to put informations in $_POST -->
        <?php
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
        ?>
        
        <div class="row">
            <div class="col-sm-12 text-right">
                <b class="form-header"><?php echo $this->lang->line('question_type').' : '.$question_type->Type_Name; ?></b>
            </div>
        </div>

        <!-- ERROR MESSAGES -->
        <?php
        if (!empty(validation_errors())) {
            echo '<div class="alert alert-danger">'.validation_errors().'</div>';}
        ?>
        
        <!-- QUESTION FIELDS -->
        <div class="row">
            <div class="col-sm-12 form-group">
                <?php echo form_label($this->lang->line('focus_topic'), 'focus_topic', array('class' => 'form-label')); ?>
                <?php 
                    if(isset($focus_topic)){
                        echo form_dropdown('focus_topic', $topics, $focus_topic->ID, 'class="form-control" id="focus_topic"');
                    } else {
                        echo form_dropdown('focus_topic', $topics, null, 'class="form-control" id="focus_topic"');
                    }
                ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 form-group">
                <?php echo form_label($this->lang->line('question_text'), 'name', array('class' => 'form-label')); ?>
                <?php 
                    if(isset($name)){
                        echo form_input('name', $name, 'maxlength="65535" class="form-control" id="name"');
                    } else {
                        echo form_input('name', '', 'maxlength="65535" class="form-control" id="name"');
                    }
                ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6 form-group">
                <?php echo form_label($this->lang->line('points'), 'points', array('class' => 'form-label')); ?>
            </div>
            <div class="col-sm-1 form-group">
                <?php 
                    if(isset($points)){
                        echo form_input('points', $points, 'maxlength="11" class="form-control" id="points"');
                    } else {
                        echo form_input('points', '', 'maxlength="11" class="form-control" id="points"');
                    }
                ?>
            </div>
        </div>

        <!-- PICTURE -->
        <div class="row">
            <div class="col-sm-12 form-group">
                <?php
                if(isset($upload_data)){
                    echo "<img src='".base_url('uploads/pictures/') . $upload_data['file_name'] . "' alt='" . $upload_data['file_name'] . "'>";
                } else {
                    echo "<img src='".base_url('uploads/pictures/') . $picture_name . "' alt='" . $picture_name . "'>";
                } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 form-group">
                <?php echo form_submit('change_picture', $this->lang->line('change_picture'), 'class="btn btn-secondary"'); ?>
            </div>
        </div>
  
        <!-- ANSWERS FIELDS -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th colspan="3"><?php echo form_label($this->lang->line('answers_list'), 'answer'); ?></th>
                    </tr>
                </thead>
                <tbody>
            
                <?php for ($i = 0; $i < $nbAnswer; $i++){ ?>
                    <tr>
                        <td class="form-group" width="80">
                            <?php
                                echo form_input('reponses['.$i.'][symbol]', $answers[$i]['symbol'], 'maxlength="2" class="form-control" id="answer['.$i.']"');
                            ?>
                        </td>
                        <td class="form-group">
                            <?php
                                echo form_hidden('reponses['.$i.'][id]', $answers[$i]['id']);
                                echo form_input('reponses['.$i.'][answer]', $answers[$i]['answer'], 'maxlength="50" class="form-control" id="answer['.$i.']"');
                            ?>
                        </td>
                        <td class="form-group">
                            <?php echo form_submit('del_answer'.$i, '-', 'class="btn btn-secondary no-border"');
                            ?>
                        </td>
                    </tr>
                <?php } ?>
                    <tr>
                        <td colspan="2">
                        <td><?php echo form_submit('add_answer', '+', 'class="btn btn-secondary no-border"'); ?></td>
                    </tr>
                </tbody>
            </table>
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