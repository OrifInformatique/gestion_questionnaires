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
    <h1 class="title-section"><?php echo $this->lang->line('question_type'); ?></h1>
    <div style="background:red;" class="row">
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
				<label for="points">Point(s) :</label>
                <div class="row">
                    <div class="col-lg-12"><input type="number" name="points" class="form-control" id="points" value="<?php echo $question->Points; ?>"></div>
                </div>
                <input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
            </div>
            <input type="submit" class="btn btn-primary" />
            <?php echo form_close(); ?>
        </div>
    </div>  
</div>  