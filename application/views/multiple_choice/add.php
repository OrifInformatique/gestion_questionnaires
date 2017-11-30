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
        echo form_open('Question/add/4', $attributes);
			
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
                <div class="col-md-10">
                    <?php echo form_label($this->lang->line('title_question'), 'answer', 'for="answer"'); ?>
				</div>
				<div class="col-md-1">
					<?php echo form_label($this->lang->line('oui'), 'oui'); ?>
				</div>
				<div class="col-md-1">
					<?php echo form_label($this->lang->line('non'), 'non'); ?>
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
				
			<?php }
			for($i=1; $i <= $nbAnswer; $i++){
					$noQuestion = "question".$i;
					$noAnswer = "answer".$i; ?>
					<span class="text-danger"><?php echo form_error($noQuestion);?></span>
					<span class="text-danger"><?php echo form_error($noAnswer);?></span>
			<?php } ?>
			
			<div class="form-group row">
				<div class="col-md-2">
					<?php echo form_submit('add', $this->lang->line('btn_add'), 'class="btn btn-primary"'); ?>
				</div>
				<div class="col-md-6"></div>
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