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
        ?>
            <div class="form-group">
                <div class="row colbox">
                    <div class="col-md-4">
                        <?= form_label($this->lang->line('focus_topic'), 'focus_topic'); ?>
                    </div>
                    <div class="col-md-8"><?= form_dropdown('focus_topic', $topics, null, 'class="form-control" id="focus_topic"'); ?></div>
                </div>
            </div>
            <div class="form-group">
                <div class="row colbox">
                    <div class="col-md-4">
                        <?= form_label($this->lang->line('question_type'), 'question_type', 'for="question_type"'); ?>
                    </div>
                    <div class="col-md-8"><?= form_dropdown('question_type', $list_question_type, null, 'class="form-control" id="question_type"'); ?></div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-12 text-right">
                    <a id="btn_cancel" class="btn btn-default" href="<?= base_url('question'); ?>"><?= lang('btn_cancel'); ?></a>
                    <input id="btn_next" name="btn_next" type="submit" class="btn btn-primary" value="<?= lang('btn_next'); ?>" />
                </div>
            </div>
        <?= form_close(); ?>
    </div>
</div>