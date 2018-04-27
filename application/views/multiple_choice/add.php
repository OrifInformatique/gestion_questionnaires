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
			
	        <div class="form-group col-md-8 text-right">
				<h4><?php echo $this->lang->line('focus_topic').' : '.$focus_topic->Topic; ?></h4>
				<h4><?php echo $this->lang->line('question_type').' : '.$question_type->Type_Name; ?></h4>
			</div>
	    </div>
		<div class="row">	
			<div class="form-group col-xs-12 ">
				<?php echo form_submit('cancel', $this->lang->line('cancel'), 'class="btn btn-danger col-xs-12 col-sm-4"'); ?>
				<?php echo form_submit('save', $this->lang->line('save'), 'class="btn btn-success col-xs-12 col-sm-4 col-sm-offset-4"'); ?>
				
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
		        		echo form_long_input('name', $name, 'maxlength="65535" class="form-control" id="name"');
		        	} else {
		        		echo form_long_input('name', '', 'maxlength="65535" class="form-control" id="name"');
		        	}
		        ?>
	        </div>
	    </div>

		<div class="row">
	        <div class="form-group col-md-1 col-xs-3">
				<?php echo form_label($this->lang->line('points'), 'points'); ?>
	        </div>
	        <div class="form-group col-md-1 col-xs-3">
	        	<?php 
		        	if(isset($name)){
		        		echo form_input('points', $points, 'maxlength="11" class="form-control" id="name"');
		        	} else {
		        		echo form_input('points', '', 'maxlength="11" class="form-control" id="name"');
		        	}
		        ?>
			</div>
	    </div>
		
		<!-- ANSWERS FIELDS -->
		<div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th><?php echo form_label($this->lang->line('answers_list'), 'answer'); ?></th>
                        <th><?php echo form_label($this->lang->line('valid_answer'), 'valid_answer'); ?></th>

                   	</tr>
                </thead>
                <tbody>
				<?php
		for ($i = 0; $i < $nbAnswer; $i++){ ?>
			<tr>
				<td class="form-group col-xs-9">
					<?php
						//echo form_hidden('reponses['.$i.'][id]', $answers[$i]['id']);
						echo form_input('reponses['.$i.'][question]', $answers[$i]['question'], 'maxlength="250" class="form-control" id="question"');
					?>
				</td>
				
				<td class="form-group col-xs-2" >
					<!-- YES radio button -->
					<?php 
						if ($answers[$i]['answer']==1){
							echo form_radio('reponses['.$i.'][answer]', 1, TRUE);
						}else{
							echo form_radio('reponses['.$i.'][answer]', 1);
						}
						echo form_label($this->lang->line('yes'), $answers[$i]['id'], null, true);
					?>
				    
					<!-- NO radio button -->
					<?php 
						if ($answers[$i]['answer']==0){
							echo form_radio('reponses['.$i.'][answer]', 0, TRUE);
						}else{
							echo form_radio('reponses['.$i.'][answer]', 0);

						}echo form_label($this->lang->line('no'), $answers[$i]['id'], null, true);
					?>
				</td>
				<td class="form-group col-xs-1">
					<?php echo form_submit('del_answer'.$i, '-', 'class="btn btn-secondary no-border"');
					?>
				</td>
			</tr>
		<?php } ?>
		<tr>
			<td><?php echo form_submit('add_answer', '+', 'class="btn btn-secondary"'); ?></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>

	</tbody>

		
    <?php echo form_close(); ?>
</div>