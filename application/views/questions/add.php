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
    <h1 style="padding-top: 12%; padding-bottom: 5%" class="text-center"><?php echo $this->lang->line('title_question_add'); ?></h1>
    <div class="row">
        <?php
        $attributes = array("class" => "form-group",
                            "id" => "addQuestionForm",
                            "name" => "addQuestionForm");
        echo form_open('Question/add/2', $attributes);
        ?>
            <div class="form-group">
                <div class="col-md-4">
                    <?php echo form_label($this->lang->line('focus_topic'), 'topic', 'for="topic"'); ?>
                </div>
                <div class="col-md-8"><p>Liste déroulante des sujets</p></div>
            </div>

            <?php echo form_submit('test', 'test', 'class="btn btn-primary"'); ?>
        <?php echo form_close(); ?>
    </div>
</div>