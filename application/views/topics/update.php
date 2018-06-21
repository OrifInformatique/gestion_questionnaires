<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Topic update form
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_topics
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
?>
<div class="container">
    <h2 class="title-section"><?php echo $this->lang->line('title_topic_update'); ?></h2>
    <div class="row">
   
            <?php
            $attributes = array("class" => "form-group",
                "id" => "updateTopicForm",
                "name" => "updateTopicForm");
            echo form_open('Topic/form_update', $attributes);
            ?>
            <?php
            if($error == true) {
                echo "<p class='alert alert-warning'>" . $this->lang->line('update_topic_form_err') . "</p>";
            }
            ?>
            <div class="col-xs-12">
                <h4 for="title"><?php echo $this->lang->line('update_title_topic'); ?></h4>
                <input maxlength="70" type="text" name="title" class="form-control col-xs-12" id="title" value="<?php echo $title; ?>">
            </div>
            <input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
            <div class="col-xs-12">
                <?php echo form_button('annuler', $this->lang->line('cancel'), 'class="btn btn-danger col-xs-12 col-sm-4" onclick="location.href=\'/gestion_questionnaires/Topic\'"'); ?>
                <input type="submit" class="btn btn-success col-xs-12 col-sm-4 col-sm-offset-4" value="<?php echo $this->lang->line('save') ?>"/>
            </div>

            <?php echo form_close(); ?>
    </div>
</div>
