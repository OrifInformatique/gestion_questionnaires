<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * View of question's details to update
 *
 * @author      Orif, section informatique (BuYa, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
?>
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
		echo form_close(); ?>
</div>