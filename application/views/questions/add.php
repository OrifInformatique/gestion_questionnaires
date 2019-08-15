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
    <h1 class="title-section"><?= $this->lang->line('title_question_add'); ?></h1>
    <div class="row">
        <?php
        $attributes = array("class" => "form-group",
                            "id" => "addQuestionForm",
                            "name" => "addQuestionForm");
        echo form_open('Question/add/2', $attributes);
        if(!empty(validation_errors())) {
            echo "<p class='alert alert-warning'>".validation_errors()."</p>";
        }
        ?>
            <div class="form-group row">
                <div class="col-md-4">
                    <?= form_label($this->lang->line('focus_topic'), 'focus_topic'); ?>
                </div>
                <div class="col-md-8"><?= form_dropdown('focus_topic', $topics, '', [
                    'class'=>'form-control', 'id' => 'focus_topic'
                ]); ?></div>
            </div>
            <div class="form-group row">
                <div class="col-md-4">
                    <?= form_label($this->lang->line('question_type'), 'question_type'); ?>
                </div>
                <div class="col-md-8"><?= form_dropdown('question_type', $list_question_type, '', [
                    'class'=>'form-control', 'id' => 'question_type'
                ]); ?></div>
            </div>
            <div class="form-group row" style="text-align: right;">
                <div class=" col-sm-4 col-xs-12">
                    <a href="<?= base_url('Question'); ?>"class="btn btn-danger col-xs-12">
                        <?= $this->lang->line('cancel'); ?>
                    </a>
                </div>
                <div class="col-sm-offset-4 col-sm-4 col-xs-12">
                    <?= form_submit('suivant', $this->lang->line('btn_next'), 'class="btn btn-success col-xs-12"'); ?>
                </div>
            </div>
        <?= form_close(); ?>
    </div>
</div>