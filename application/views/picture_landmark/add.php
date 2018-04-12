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
	echo form_open('Question/add_PictureLandmark/2', $attributes);
	?>
	
		
		<!-- Hidden fields to put informations in $_POST -->
		<?php
		echo form_hidden('focus_topic', $focus_topic->ID);
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
				<?php
					if(isset($name)){
		        		echo form_long_input('name', $name, 'class="form-control" id="name"');
		        	} else {
		        		echo form_long_input('name', '', 'class="form-control" id="name"');
		        	}
		        ?>
			</div>
		</div>

		<div class="row">
			<div class="form-group col-md-1">
				<?php echo form_label($this->lang->line('points'), 'points'); ?>
			</div>
			<div class="form-group col-md-1">
				<?php 
		        	if(isset($name)){
		        		echo form_input('points', $points, 'class="form-control" id="name"');
		        	} else {
		        		echo form_input('points', '', 'class="form-control" id="name"');
		        	}
		        ?>
			</div>
		</div>

		<!-- PICTURE -->
		<div class="row">
			<div class="form-group col-md-12">
				<?php
				if(isset($upload_data)){
					echo "<img src='/gestion_questionnaires/uploads/pictures/" . $upload_data['file_name'] . "' alt='" . $upload_data['file_name'] . "'>";
				} else {
					echo "<img src='/gestion_questionnaires/uploads/pictures/" . $picture_name . "' alt='" . $picture_name . "'>";
				} ?>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-4">
				<?php echo form_submit('change_picture', $this->lang->line('change_picture'), 'class="btn btn-secondary"'); ?>
			</div>
		</div>

		<!-- ANSWERS FIELDS -->
		
		<div class="row">
			<div class="form-group col-md-12">
				<?php echo form_label($this->lang->line('answers_list'), 'answer'); ?>
			</div>
		</div>
		
		<?php
		for ($i = 0; $i < $nbAnswer; $i++){ ?>
			<div class="row">
				<div class="col-md-1">
					<?php
						echo form_input('reponses['.$i.'][symbol]', $answers[$i]['symbol'], 'class="form-control" id="answer"');
					?>
				</div>
				<div class="col-md-10">
					<?php
						echo form_hidden('reponses['.$i.'][id]', $answers[$i]['id']);
						echo form_input('reponses['.$i.'][answer]', $answers[$i]['answer'], 'class="form-control" id="answer"');
					?>
				</div>
				<div class="form-group col-md-1">
					<?php echo form_submit('del_answer'.$i, '-', 'class="btn btn-danger"');
					?>
				</div>
			</div>
		<?php } ?>
		
		<div class="row">
			<div class="col-md-1">
				<?php echo form_submit('add_answer', '+', 'class="btn btn-success"'); ?>
			</div>
		</div>
	<?php echo form_close(); ?>
</div>