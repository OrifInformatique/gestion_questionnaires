<?php
/**
 * Modify Questionnaire before generating from model
 *
 * @author        Orif Pomy, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright    Copyright (c) Orif section informatique, Switzerland (http://www.sectioninformatique.ch)
 */
?>
<div class="container">
	<h1 class="title-section"><?= $this->lang->line('finish_questionnaire'); ?></h1>
    <?php
    $attributes = array('id' => 'finishQuestionnaireForm',
                        'name' => 'finishQuestionnaireForm');
    echo form_open('Questionnaire/model_generate_pdf', $attributes, [
        'modelId' => $model->ID
    ]);
    ?>

    <!-- ERROR MESSAGES -->
    <?php
    if (!empty(validation_errors())) {
        echo '<div class="alert alert-danger">'.validation_errors().'</div>';}
    ?>

    <div class="row">
        <div class="form-group col-12">
            <?= form_label($this->lang->line('add_title_questionnaire'), 'title', array('class' => 'form-label')); ?>
            <?= form_input('title', $model->Questionnaire_Name, array(
                'maxlength' => 100, 'id' => 'title',
                'class' => 'form-control'
            )); ?>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-12">
            <?= form_label($this->lang->line('add_subtitle_questionnaire'), 'subtitle', array('class' => 'form-label')); ?>
            <?= form_input('subtitle', $model->Questionnaire_Subtitle, array(
                'maxlength' => 100, 'id' => 'subtitle',
                'class' => 'form-control'
            )); ?>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-12">
            <div class="col-sm-12 text-right">
                <input type="submit" class="btn btn-default" name="cancel" value="<?php echo $this->lang->line('cancel');?>">
                <input type="submit" class="btn btn-primary" name="generate" value="<?php echo $this->lang->line('generate_questionnaire');?>">
            </div>
        </div>
    </div>

    <?= form_close(); ?>
</div>