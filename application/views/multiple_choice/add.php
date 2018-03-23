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
		if(isset($id)){
    		echo form_hidden('id', $id);
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
		        		echo form_input('name', $name, 'class="form-control" id="name"');
		        	} else {
		        		echo form_input('name', '', 'class="form-control" id="name"');
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
		
		<!-- ANSWERS FIELDS -->
		<div class="row">
	        <div class="form-group col-md-10">
	            <?php echo form_label($this->lang->line('answers_list'), 'answer'); ?>
			</div>
			<div class="form-group col-md-2">
				<?php echo form_label($this->lang->line('valid_answer'), 'valid_answer'); ?>
	    	</div>
		</div>

		<?php
		for ($i = 0; $i < $nbAnswer; $i++){ ?>
			<div class="row">
				<div class="form-group col-md-9">
					<?php
						echo form_hidden('reponses['.$i.'][id]', $answers[$i]['id']);
						echo form_input('reponses['.$i.'][question]', $answers[$i]['question'], 'class="form-control" id="question"');
					?>
				</div>
				<div class="form-group col-md-1">
					<?php echo form_submit('del_answer'.$i, '-', 'class="btn btn-secondary"');
					?>
				</div>
				<div class="form-group col-md-1">
					<!-- YES radio button -->
					<?php echo form_label($this->lang->line('yes'), $answers[$i]['id']); ?>
					<?php
						if ($answers[$i]['answer']==1){
							echo form_radio('reponses['.$i.'][answer]', 1, TRUE);
						}else{
							echo form_radio('reponses['.$i.'][answer]', 1);
						}
					?>
				</div>
				<div class="form-group col-md-1">
					<!-- NO radio button -->
					<?php echo form_label($this->lang->line('no'), $answers[$i]['id']); ?>
					<?php
						if ($answers[$i]['answer']==0){
							echo form_radio('reponses['.$i.'][answer]', 0, TRUE);
						}else{
							echo form_radio('reponses['.$i.'][answer]', 0);
						}
					?>
				</div>
			</div>
		<?php } ?>
		
		<div class="form-group row">
			<div class="col-md-2">
				<?php echo form_submit('add_answer', '+', 'class="btn btn-secondary"'); ?>
			</div>
		</div>
    <?php echo form_close(); ?>
</div>