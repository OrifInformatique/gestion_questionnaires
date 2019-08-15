<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Module (Parent topic) update form
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_modules
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
?>
<div class="container">
    <h2 class="title-section"><?php echo $this->lang->line('title_module_update'); ?></h2>
    <div class="row">
        <?php
        $attributes = array("class" => "form-group",
            "id" => "updateModuleForm",
            "name" => "updateModuleForm");
        echo form_open('Topic/form_validate_module', $attributes, [
            'id' => $id, 'action' => $action
        ]);
        if (!empty(validation_errors())) {
            echo '<div class="alert alert-danger">'.validation_errors().'</div>';
        }
        ?>
        <div class="col-xs-12">
            <h4><?= form_label($this->lang->line('update_title_module'), 'title'); ?></h4>

            <?= form_input('title', $title, [
                'id' => 'title', 'maxlength' => TOPIC_MAX_LENGTH,
                'class' => 'form-control'
            ]); ?>
        </div>
        <div class="col-xs-12">
            <a href="<?= base_url('Topic'); ?>" class="btn btn-danger col-xs-12 col-sm-4"><?= $this->lang->line('cancel'); ?></a>
            <?= form_submit('submit', $this->lang->line('save'), [
                'class' => 'btn btn-success col-xs-12 col-sm-4 col-sm-offset-4'
            ]); ?>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
