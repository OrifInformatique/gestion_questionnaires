<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Update Questionnaire
 *
 * @author		Orif Pomy, section informatique (UlSi, ViDi)
 * @copyright	Copyright (c) Orif section informatique, Switzerland (http://www.sectioninformatique.ch)
 */

?>
<div class="container">
    <h1 style="padding-top: 12%; padding-bottom: 5%" class="text-center"><?php echo $this->lang->line('title_questionnaire_update'); ?></h1>
    <div class="row">
        <div class="col-lg-2"></div>
        <div class="col-lg-8">
                <?php
                $attributes = array("class" => "form-group",
                    "id" => "updateQuestionnaireForm",
                    "name" => "updateQuestionnaireForm");
                echo form_open('Questionnaire/form_update', $attributes);
                ?>
                <?php
                if($error == true) {
                    echo "<p class='alert alert-warning'>" . $this->lang->line('update_questionnaire_form_err') . "</p>";
                }
                ?>
                <div class="form-group">
                    <label for="title"><?php echo $this->lang->line('update_title_questionnaire'); ?></label>
                    <div class="row">
                        <div class="col-lg-4"><input type="text" name="title" class="form-control" id="title"></div>
                    </div>
                    <input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
                </div>
                <input type="submit" class="btn btn-primary" />
                <?php echo form_close(); ?>
            
        </div>
        <div class="col-lg-2"></div>
    </div>

    

