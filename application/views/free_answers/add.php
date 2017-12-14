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
        echo form_open('Question/add_FreeAnswer', $attributes);
			
			echo form_hidden('focus_topic', $focus_topic);
			echo form_hidden('question_type', $question_type); ?>
			<div class="form-group row">
                <div class="col-md-4">
                    <?php echo form_label($this->lang->line('question_text'), 'title', 'for="title"'); ?>
                </div>
			</div>
	
			<div class="form-group row">
                <div class="col-md-12"><?php if(isset($name)){echo form_input('name', $name, 'class="form-control" id="name"');}else{echo form_input('name', '', 'class="form-control" id="name"');} ?></div>
            </div>
			<span class="text-danger"><?php echo form_error('name');?></span>

			<div class="form-group row">
                <div class="col-md-4">
					<?php echo form_label($this->lang->line('points'), 'points', 'for="points"'); ?>
                </div>
                <div class="col-md-2"><?php if(isset($points)){echo form_input('points', $points, 'class="form-control" id="points"');}else{echo form_input('points', '', 'class="form-control" id="points"');}?></div>
            </div>
			<span class="text-danger"><?php echo form_error('points');?></span>
			
			<div class="form-group row">
                <div class="col-md-12">
                    <?php echo form_label($this->lang->line('answer_question_add'), 'answer', 'for="answer"'); ?>
                </div>
			</div>
			<div class="form-group row">
                <div class="col-md-12"><?php if(isset($answer)){echo form_input('answer', $answer, 'class="form-control" id="answer"');}else{echo form_input('answer', '', 'class="form-control" id="answer"');} ?></div>
            </div>
		
			<span class="text-danger"><?php echo form_error('answer');?></span>
			
			<div class="form-group row">
				<div class="col-md-8"></div>
				<div class="col-md-2">
					<?php echo form_button('annuler', $this->lang->line('cancel'), 'class="btn btn-primary" onclick="location.href=\'/gestion_questionnaires/Question\'"'); ?>
				</div>
				<div class="col-md-2">
					<?php echo form_submit('enregistrer', $this->lang->line('save'), 'class="btn btn-primary"'); ?>
				</div>
			</div>
        <?php echo form_close(); ?>
    </div>
</div>