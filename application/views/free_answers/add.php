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
    <h1 class="text-center"><?php echo $this->lang->line('title_question_add'); ?></h1>
	<?php
	$attributes = array("id" => "addQuestionForm",
						"name" => "addQuestionForm");
	echo form_open('Question/add_FreeAnswer', $attributes);
	?>

		<!-- Hidden fields to put informations in $_POST -->
		<?php
		echo form_hidden('focus_topic', $focus_topic->ID);
		echo form_hidden('question_type', $question_type->ID);
		?>
		
		<!-- Display buttons and display topic and question type as information -->
		<div class="row">
			<div class="form-group col-md-4">
				<?php echo form_submit('save', $this->lang->line('save'), 'class="btn btn-success"'); ?>
				<?php echo form_submit('cancel', $this->lang->line('cancel'), 'class="btn btn-danger"'); ?>
			</div>
	        <div class="form-group col-md-8 text-right">
				<h4><?php echo $this->lang->line('focus_topic').' : '.$focus_topic->Topic; ?></h4>
				<h4><?php echo $this->lang->line('question_type').' : '.$question_type->Type_Name; ?></h4>
			</div>
	    </div>

		<!-- ERROR MESSAGES -->
	    <?php
	    if (!empty(validation_errors())) {
	        echo '<div class="alert alert-danger">'.validation_errors().'</div>';}
	    ?>

	    <!-- QUESTION FIELDS -->
		<div class="row">
	        <div class="form-group col-md-12">
	        	<?php echo form_label($this->lang->line('question_text'), 'name'); ?>
	        	<?php echo form_input('name', set_value('name'), 'class="form-control" id="name"'); ?>
	        </div>
	    </div>

		<div class="row">
	        <div class="form-group col-md-1">
				<?php echo form_label($this->lang->line('points'), 'points'); ?>
	        </div>
	        <div class="form-group col-md-1">
	        	<?php echo form_input('points', set_value('points'), 'class="form-control" id="points"'); ?>
			</div>
	    </div>

		<!-- ANSWERS FIELDS -->
		
		<div class="row">
			<div class="form-group col-md-12">
				<?php echo form_label($this->lang->line('answer_question_add'), 'answer'); ?>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-12"><?php echo form_input('answer', set_value('answer'), 'class="form-control" id="answer"'); ?></div>
		</div>
	<?php echo form_close(); ?>
</div>