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
        echo form_open('Topic/form_validate_module', $attributes);
        ?>
        <?php
        if($error == true) {
            echo "<p class='alert alert-warning'>" . $this->lang->line('update_module_form_err') . "</p>";
        }
        ?>
        <div class="col-xs-12">
            <h4 for="title"><?php echo $this->lang->line('update_title_module'); ?></h4>
            
            <input maxlength="<?=TOPIC_MAX_LENGTH?>" type="text" name="title" class="form-control" id="title" value="<?php echo $title; ?>">
            <input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
            <input type="hidden" name="action" id="action" value="<?php echo $action; ?>">
        </div>
        <div class="col-xs-12">

            <?php echo form_button('annuler', $this->lang->line('cancel'), 'class="btn btn-danger col-xs-12 col-sm-4" onclick="location.href=\''.base_url('Topic').'\'"'); ?>
            <input type="submit" class="btn btn-success col-xs-12 col-sm-4 col-sm-offset-4" value="<?php echo $this->lang->line('save') ?>"/>
                 
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
