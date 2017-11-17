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
        echo form_open('Question/add/3', $attributes);
        ?>
			<div class="form-group">
                <div class="col-md-4">
                    <?php echo form_label($this->lang->line('name_question_add'), 'title', 'for="title"'); ?>
                </div>
			</div>
			<div class="form-group">
                <div class="col-md-12"><?php echo form_input('name', $name, 'class="form-control" id="name"'); ?></div>
            </div>
			<div class="form-group">
                <div class="col-md-4">
					<?php echo form_label($this->lang->line('points'), 'points', 'for="points"'); ?>
                </div>
                <div class="col-md-2"><?php echo form_input('points', $points, 'class="form-control" id="points"'); ?></div>
            </div>
			<div class="form-group">
                <div class="col-md-12">
                    <?php echo form_label($this->lang->line('answer_question_add'), 'answer', 'for="answer"'); ?>
                </div>
			</div>
			<div class="form-group">
                <div class="col-md-12"><?php echo form_input('answer', '', 'class="form-control" id="answer"'); ?></div>
            </div>
			<div class="form-group">
				<div class="col-md-8"></div>
				<div class="col-md-2">
					<?php //echo form_submit('annuler', 'Annuler', 'class="btn btn-primary"'); ?>
				</div>
				<div class="col-md-2">
					<?php echo form_submit('enregistrer', 'Enregistrer', 'class="btn btn-primary"'); ?>
				</div>
			</div>
        <?php echo form_close(); ?>
    </div>
</div>