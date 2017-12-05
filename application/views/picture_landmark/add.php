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
        echo form_open('Question/add/7', $attributes);
			
			echo form_hidden('focus_topic', $focus_topic);
			echo form_hidden('question_type', $question_type);
			echo form_hidden('points', $points);
			echo form_hidden('nbAnswer', $nbAnswer);
			echo form_hidden('upload_data', $upload_data); ?>
			<div class="form-group row">
                <div class="col-md-4">
                    <?php echo form_label($this->lang->line('name_question_add'), 'title', 'for="title"'); ?>
                </div>
			</div>
	
			<div class="form-group row">
                <div class="col-md-12"><?php echo form_input('name', $name, 'class="form-control" id="name"'); ?></div>
            </div>
			<?php echo "<img src='/gestion_questionnaires/uploads/pictures/" . $upload_data['file_name'] . "' alt='" . $upload_data['file_name'] . "'>"; ?>
			<div class="form-group row">
                <div class="col-md-2">
                    <?php echo form_label($this->lang->line('landmark'), 'symbol', 'for="symbol"'); ?>
				</div>
				<div class="col-md-10">
					<?php echo form_label($this->lang->line('mutliple_answer'), 'answer', 'for="answer"'); ?>
				</div>
			</div>
			
			<?php for ($i = 1; $i <= $nbAnswer; $i++){ 
			$noSymbol = "symbol".$i;
			$noAnswer = "answer".$i;
			?>
				<div class="form-group row">
					<div class="col-md-2"><?php if(isset($$noSymbol)){echo form_input($noSymbol, $$noSymbol, 'class="form-control" id="symbol"');}else{echo form_input($noSymbol, '', 'class="form-control" id="symbol"');} ?></div>
					<div class="col-md-10"><?php if(isset($$noAnswer)){echo form_input($noAnswer, $$noAnswer, 'class="form-control" id="answer"');}else{echo form_input($noAnswer, '', 'class="form-control" id="answer"');} ?></div>
				</div>
				
				<span class="text-danger"><?php echo form_error($noSymbol);?></span>
				<span class="text-danger"><?php echo form_error($noAnswer);?></span>
			<?php } ?>
			
			<div class="form-group row">
				<div class="col-md-2">
					<?php echo form_submit('add', $this->lang->line('btn_add'), 'class="btn btn-primary"'); ?>
				</div>
				<div class="col-md-2">
					<?php echo form_submit('delete', $this->lang->line('btn_del'), 'class="btn btn-primary"'); ?>
				</div>
				<div class="col-md-4"></div>
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