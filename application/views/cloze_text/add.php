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
	echo form_open('Question/add_ClozeText', $attributes);
	?>
	
		
		<!-- Hidden fields to put informations in $_POST -->
		<?php
		echo form_hidden('question_type', $question_type->ID);
		echo form_hidden('nbAnswer', $nbAnswer);
		if(isset($id)){
			echo form_hidden('id', $id);
		}
		if(isset($id_cloze_text)){
			echo form_hidden('id_cloze_text', $id_cloze_text);
		}
		?>
		
		<!-- Display buttons and display topic and question type as information -->
		<div class="row">
			<div class="form-group">
				<a name="cancel" class="btn btn-danger col-xs-12 col-sm-4" href="<?=base_url('/Question')?>"><?=$this->lang->line('cancel')?></a>
                <?php
					echo form_submit('save', $this->lang->line('save'), 'class="btn btn-success col-xs-12 col-sm-4 col-sm-offset-4"'); 
					echo form_submit('test', '', 'style="visibility: hidden; height:0;"');//for cancel "Enter" key in form 
				?>
			</div>
			<div class="form-group col-md-8 text-right">
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
                <?php echo form_label($this->lang->line('focus_topic'), 'focus_topic'); ?>
                <?php 
                    if(isset($focus_topic)){
                        echo form_dropdown('focus_topic', $topics, $focus_topic->ID, 'class="form-control"');
                    } else {
                        echo form_dropdown('focus_topic', $topics, null, 'class="form-control"');
                    }
                ?>
            </div>
        </div>

		<div class="row">
			<div class="form-group col-md-12">
				<?php echo form_label($this->lang->line('cloze_text_consign'), 'name'); ?>
				<?php
					if(isset($name)){
						echo form_input('name', $name, 'maxlength="65535" class="form-control" id="name"');
					} else {
						echo form_input('name', '', 'maxlength="65535" class="form-control" id="name"');
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
						echo form_input('points', $points, 'maxlength="11" class="form-control" id="name"');
					} else {
						echo form_input('points', '', 'maxlength="11" class="form-control" id="name"');
					}
				?>
			</div>
		</div>

		<div class="row">
			<div class="form-group col-md-12">
				<?php echo form_label($this->lang->line('cloze_text'), 'cloze_text'); ?>
				<p><?php echo $this->lang->line('cloze_text_tip'); ?></p>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-12">
				<?php 
		        	if(isset($cloze_text)){
						echo form_textarea('cloze_text', $cloze_text, 'maxlength="65535" class="form-control" id="cloze_text"');
					} else {
						echo form_textarea('cloze_text', '', 'maxlength="65535" class="form-control" id="cloze_text"');
					}
				?>
			</div>
		</div>
		
		<!-- ANSWERS FIELDS -->
		
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th colspan="2"><?php echo form_label($this->lang->line('answers_list'), 'answer') ?></th>
					</tr>
				</thead>
				<tbody>
		<?php
		for ($i = 0; $i < $nbAnswer; $i++){ ?>
			<tr data-row-id="<?=$i?>">
				<td class="form-group col-xs-10">
					<?php
						echo form_hidden('reponses['.$i.'][id]', $answers[$i]['id']);
						echo form_input('reponses['.$i.'][answer]', $answers[$i]['answer'], 'maxlength="65535" class="form-control" id="answer['.$i.']"');
					?>
				</td>
				<td class="form-group col-xs-2">
					<button type="button" class="btn btn-default no-border" onclick="invertInputs(this, <?=$nbAnswer?>, -1);" data-button-id="<?=$i?>">▲</button>
					<button type="button" class="btn btn-default no-border" onclick="invertInputs(this, <?=$nbAnswer?>, +1);" data-button-id="<?=$i?>">▼</button>
					<?php echo form_submit('del_answer'.$i, '-', 'class="btn btn-default no-border"'); ?>
				</td>
			</tr>
		<?php } ?>
			</table>
		</div>
		<div class="row">
			<div class="col-md-2">
				<?php echo form_submit('add_answer', '+', 'class="btn btn-default"'); ?>
			</div>
		</div>
	<?php echo form_close(); ?>
</div>