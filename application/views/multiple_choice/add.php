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
    echo form_open('Question/add_MultipleChoice', $attributes);
    ?>

		<!-- Hidden fields to put informations in $_POST -->
		<?php
		echo form_hidden('focus_topic', $focus_topic->ID);
		echo form_hidden('question_type', $question_type->ID);
		echo form_hidden('nbAnswer', $nbAnswer);
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
	        <div class="form-group col-md-10">
	            <?php echo form_label($this->lang->line('answers_list'), 'answer', 'for="answer"'); ?>
			</div>
			<div class="col-md-2">
				<?php echo form_label($this->lang->line('valid_answer'), 'valid_answer'); ?>
				<div class="row">
					<div class="col-md-6">
						<?php echo form_label($this->lang->line('oui'), 'oui'); ?>
					</div>
					<div class="col-md-6">
						<?php echo form_label($this->lang->line('non'), 'non'); ?>
	                </div>
	            </div>
	    	</div>
		</div>
		
		<?php for ($i = 1; $i <= $nbAnswer; $i++){ 
		$noQuestion = "question".$i;
		$noAnswer = "answer".$i;
		?>
			<div class="form-group row">
				<div class="col-md-10"><?php if(isset($$noQuestion)){echo form_input($noQuestion, $$noQuestion, 'class="form-control" id="question"');}else{echo form_input($noQuestion, '', 'class="form-control" id="question"');} ?></div>
				<div class="col-md-1"><?php if(isset($$noAnswer)){if($$noAnswer==1){echo form_radio($noAnswer, 1, TRUE);}else{echo form_radio('answer'.$i, 1);}}else{echo form_radio($noAnswer, 1);}?></div>
				<div class="col-md-1"><?php if(isset($$noAnswer)){if($$noAnswer==0){echo form_radio($noAnswer, 0, TRUE);}else{echo form_radio('answer'.$i, 0);}}else{echo form_radio($noAnswer, 0);}?></div>
			</div>
			
		<?php } ?>
		
		<div class="form-group row">
			<div class="col-md-2">
				<?php echo form_submit('add', $this->lang->line('btn_add'), 'class="btn btn-primary"'); ?>
			</div>
			<div class="col-md-2">
				<?php echo form_submit('delete', $this->lang->line('btn_del'), 'class="btn btn-primary"'); ?>
			</div>
		</div>
    <?php echo form_close(); ?>
</div>