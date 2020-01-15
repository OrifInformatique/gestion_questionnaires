<?php
/**
 * Add Questionnaire
 *
 * @author        Orif Pomy, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright    Copyright (c) Orif section informatique, Switzerland (http://www.sectioninformatique.ch)
 */
?>

<div id="page-content-wrapper">
    <div class="container">
        <h1 class="title-section"><?php echo $this->lang->line('title_questionnaire'.($model ? '_model' : '').'_update'); ?></h1>
        <?php
        $attributes = array("id" => "addQuestionnaireForm",
            "name" => "addQuestionnaireForm",
            "style" => "padding-bottom: 5%;");
        echo form_open('Questionnaire/form_update', $attributes,
            ['id' => $id, 'model' => (bool) $model]);
        ?>

            <?php if(!empty(validation_errors())) { ?>
                <div class="alert alert-danger"><?= validation_errors(); ?></div>
            <?php } ?>

            <?php if($model) { ?>
                <div class="form-group col-12">
                    <?= form_label($this->lang->line('add_title_questionnaire_model'), 'modelName', array('class' => 'form-label')); ?>
                    <div class="row">
                        <div class="col-12">
                            <?= form_input('modelName', $modelName, array(
                                'maxlength' => 100, 'class' => 'form-control', 'id' => 'modelName'
                            )); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <div class="form-group col-12">
                <?= form_label($this->lang->line('add_title_questionnaire'), 'title', array('class' => 'form-label')); ?>
                <div class="row">
                    <div class="col-12">
                        <?= form_input('title', $quest_title, array(
                            'maxlength' => 100, 'id' => 'title', 'class' => 'form-control'
                        )); ?>
                    </div>
                </div>
            </div>

            <div class="form-group col-12">
                <?= form_label($this->lang->line('add_subtitle_questionnaire'), 'subtitle', array('class' => 'form-label')); ?>
                <div class="row">
                    <div class="col-12">
                        <?= form_input('subtitle', $subtitle, array(
                            'maxlength' => 100, 'id' => 'subtitle', 'class' => 'form-control'
                        )); ?>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <div class="col-sm-12 text-right">
                    <input type="submit" class="btn btn-default" name="cancel" value="<?php echo $this->lang->line('cancel');?>">
                    <input type="submit" class="btn btn-primary" name="save" value="<?php echo $this->lang->line('save');?>">
                </div>
            </div>

        <?= form_close(); ?>
    </div>
</div>
