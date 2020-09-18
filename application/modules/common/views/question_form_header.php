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
    echo form_open('Question/add_free_answer', $attributes);
    ?>

        <!-- Hidden fields to put informations in $_POST -->
        <?php
        echo form_hidden('question_type', $question_type->ID);
        if(isset($id)){
            echo form_hidden('id', $id);
        }
        if(isset($id_answer)){
            echo form_hidden('id_answer', $id_answer);
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
                    if(isset($name)){
                        echo form_input('points', $points, 'maxlength="11" class="form-control" id="points"');
                    } else {
                        echo form_input('points', '', 'maxlength="11" class="form-control" id="points"');
                    }
                ?>
            </div>
        </div>