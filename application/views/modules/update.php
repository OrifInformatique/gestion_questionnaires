<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Module (Parent topic) update form
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_modules
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
?>
<div class="container jumbotron well" style="background-color: lavender">
    <h2 class="text-center"><?php echo $this->lang->line('title_module_update'); ?></h2>
    <div class="row">
        <div class="col-lg-2"></div>
        <div class="col-lg-8">
            <div class="well">
                <?php
                $attributes = array("class" => "form-group",
                    "id" => "updateModuleForm",
                    "name" => "updateModuleForm");
                echo form_open('Module/form_validate', $attributes);
                ?>
                <?php
                if($error == true) {
                    echo "<p class='alert alert-warning'>" . $this->lang->line('update_module_form_err') . "</p>";
                }
                ?>
                <div class="form-group">
                    <label for="title"><?php echo $this->lang->line('update_title_module'); ?></label>
                    <div class="row">
                        <div class="col-lg-4"><input type="text" name="title" class="form-control" id="title" value="<?php echo $title; ?>"></div>
                    </div>
                    <input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
					<input type="hidden" name="action" id="action" value="<?php echo $action; ?>">
                </div>
                <input type="submit" class="btn btn-primary" />
                <?php echo form_close(); ?>
            </div>
        </div>
        <div class="col-lg-2"></div>
    </div>
