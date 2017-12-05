<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * View of question's details to update
 *
 * @author      Orif, section informatique (BuYa, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
?>

<?php

if(isset($error)){
	echo $error;
}?>

<div class="container">
    <h1 style="padding-top: 12%; padding-bottom: 5%" class="text-center"><?php echo $this->lang->line('title_question_add'); ?></h1>
    <div class="row">
        <?php
        $attributes = array("class" => "form-group",
                            "id" => "addQuestionForm",
                            "name" => "addQuestionForm");
        echo form_open_multipart('Question/add/6', $attributes);
			
			echo form_hidden('focus_topic', $focus_topic);
			echo form_hidden('question_type', $question_type);
			echo form_hidden('points', $points);
			echo form_hidden('nbAnswer', $nbAnswer); ?>
			<div class="form-group row">
                <div class="col-md-4">
                    <?php echo form_label($this->lang->line('name_question_add'), 'title', 'for="title"'); ?>
                </div>
			</div>
			<div class="form-group row">
                <div class="col-md-12"><?php echo form_input('name', $name, 'class="form-control" id="name"'); ?></div>
            </div>
			
			<div class="form-group row">
                <div class="col-md-12"><?php echo form_upload('picture', '', 'id="picture"'); ?></div>
            </div>
			 
			<div class="form-group row">
				<div class="col-md-10"></div>
				<div class="col-md-2">
					<?php echo form_submit('enregistrer', $this->lang->line('save'), 'class="btn btn-primary"'); ?>
				</div>
			</div>
			
        <?php echo form_close(); ?>
    </div>
</div>