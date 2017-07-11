<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * View of question's details to update
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
?>
<div class="container">
    <h1 style="padding-top: 12%; padding-bottom: 5%" class="text-center"><?php echo $this->lang->line('title_question_update'); ?></h1>
<div class="row">
    <div class="col-lg-2"></div>
    <div class="col-lg-8">
            <?php
            $attributes = array("class" => "form-group",
                "id" => "updateQuestionForm",
                "name" => "updateQuestionForm");
            echo form_open('Question/form_update', $attributes);
            ?>
            <?php
            if($error == true) {
                echo "<p class='alert alert-warning'>" . $this->lang->line('update_question_form_err') . "</p>";
            }
            ?>
            <div class="form-group">
                <label for="title"><?php echo $this->lang->line('update_name_question'); ?></label>
                <div class="row">
                    <div class="col-lg-12"><input type="text" name="name" class="form-control" id="name" value="<?php echo $question->Question; ?>"></div>
                </div>
                <input type="hidden" name="id" id="id" value="<?php //echo $id; ?>">
            </div>
            <div class="form-group">
                <label for="title"><?php echo $this->lang->line('update_question_type'); ?></label>
                <select onchange="changeselect()" id="t_question" class="form-control">
                    <?php
                    foreach ($question_types as $object => $question_type){
                        echo "<option>$question_type->Type_Name</option>";
                    }
                    ?>
                </select>
            </div>
            <input type="submit" class="btn btn-primary" />
            <?php echo form_close(); ?>
    </div>
    <div class="col-lg-2"></div>
</div>