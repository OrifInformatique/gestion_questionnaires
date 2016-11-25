<?php
/**
 * Created by PhpStorm.
 * User: UlSi
 * Date: 23.11.2016
 * Time: 15:51
 */
?>
<div class="container jumbotron well" style="background-color: lavender">
    <h2 class="text-center"><?php echo $this->lang->line('title_question_update'); ?></h2>
<div class="row">
    <div class="col-lg-2"></div>
    <div class="col-lg-8">
        <div class="well">
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
                    <div class="col-lg-4"><input type="text" name="name" class="form-control" id="name"></div>
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
    </div>
    <div class="col-lg-2"></div>
</div>