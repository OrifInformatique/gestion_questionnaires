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
    <h2 class="title-section"><?php echo $this->lang->line('title_topic_add'); ?></h2>


    <div class="row">
        <?php
        $attributes = array("class" => "form-group",
            "id" => "addTopicForm",
            "name" => "addTopicForm");
        echo form_open('Topic/form_add', $attributes);
        if($error == true) {
            echo "<p class='alert alert-warning'>" . $this->lang->line('add_topic_form_err') . "</p>";
        }
        ?>

        <div class="col-xs-12">
            <h4 for="module"><?php echo $this->lang->line('focus_module'); ?></h4>
            <select  class="form-control" name="module_selected" id="module_selected">
             <?php
    			//Récupère chaque module
                foreach ($topics as $object => $topic) {
                    var_dump($topic);
                    if($topic->FK_Parent_Topic == 0) {
    				    //Affiche les modules
                        echo "<option value=". $topic->ID .">" . $topic->Topic . "</option>";
                    }
                }
            ?>
            </select>
        </div>
        <div class="col-xs-12">
            <h4 for="title"><?php echo $this->lang->line('update_title_topic'); ?></h4>
            <input type="text" name="title" class="form-control" id="title" value="">
        </div>
        <div class="col-xs-12">
              <?php echo form_button('annuler', $this->lang->line('cancel'), 'class="btn btn-danger col-xs-12 col-sm-4" onclick="location.href=\'/gestion_questionnaires/Topic\'"'); ?>
            <input type="submit" class="btn btn-success col-xs-12 col-sm-4 col-sm-offset-4" value="<?php echo $this->lang->line('save') ?>"/>
                 
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
