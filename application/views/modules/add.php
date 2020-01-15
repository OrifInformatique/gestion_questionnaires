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
    <h2 class="title-section"><?php echo $this->lang->line('title_module_add'); ?></h2>
    <?php
        $attributes = array("class" => "form-group",
            "id" => "addModuleForm",
            "name" => "addModuleForm");
        echo form_open('Topic/form_validate_module', $attributes);
            
        if($error == true) {
            echo "<p class='alert alert-danger'>" . $this->lang->line('add_module_form_err') . "</p>";
        }
    ?>
    <div class="row">
        <div class="form-group col-12">
            <h4 for="title"><?php echo $this->lang->line('add_title_module'); ?></h4>
            <input maxlength="<?=TOPIC_MAX_LENGTH?>" type="text" name="title" class="form-control" id="title" value="">
            <input type="hidden" name="action" id="action" value="<?php echo $action; ?>">
        </div>         
    </div>
    <div class="row">
        <div class="col-12 text-right">
            <a id="btn_cancel" class="btn btn-default" href="<?=base_url('/topic')?>"><?=$this->lang->line('btn_cancel')?></a>
            <?php
                echo form_submit('save', $this->lang->line('save'), 'class="btn btn-primary"'); 
            ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
