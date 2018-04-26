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
    <div class="row">
        <?php
        $attributes = array("class" => "form-group",
                            "id" => "addQuestionForm",
                            "name" => "addQuestionForm");
        echo form_open('Question/add/2', $attributes);
        ?>
            <div class="form-group row">
                <div class="col-md-4">
                    <?php echo form_label($this->lang->line('focus_topic'), 'topic', 'for="topic"'); ?>
                </div>
                <div class="col-md-8"><?php echo form_dropdown('focus_topic', $topics); ?></div>
            </div>
			<div class="form-group row">
                <div class="col-md-4">
                    <?php echo form_label($this->lang->line('question_type'), 'list_question_type', 'for="question_type"'); ?>
                </div>
                <div class="col-md-8"><?php echo form_dropdown('question_type', $list_question_type); ?></div>
            </div>
			<div class="form-group row" style="text-align: right;">
				<div class=" col-sm-4 col-xs-12" >
					<?php echo form_button('annuler', $this->lang->line('cancel'), 'class="btn btn-danger col-xs-12" onclick="location.href=\'/gestion_questionnaires/Question\'"'); ?>
				</div>
				<div class="col-sm-offset-4 col-sm-4 col-xs-12">
					<?php echo form_submit('suivant', $this->lang->line('btn_next'), 'class="btn btn-success col-xs-12"'); ?>
				</div>
			</div>
        <?php echo form_close(); ?>
    </div>
</div>