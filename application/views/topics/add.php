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

    <?php
    $attributes = array("class" => "form-group",
        "id" => "addTopicForm",
        "name" => "addTopicForm");
    echo form_open('Topic/add_topic', $attributes);
    if(count($topics) == 0){
        echo '<div class="alert alert-warning">'.$this->lang->line('topic_no_module_error').'</div>';
    }
    if($error == true) {
        echo '<div class="alert alert-danger">';
        echo validation_errors();
        echo '</div>';
        //echo "<p class='alert alert-danger'>" . $this->lang->line('add_topic_form_err') . "</p>";
    }
    ?>
    <div class="row">
        <div class="col-12 form-group">
            <h4 for="module"><?php echo $this->lang->line('focus_module'); ?></h4>
            <select  class="form-control" name="module_selected" id="module_selected">
             <?php
    			//Récupère chaque module
                foreach ($topics as $object => $topic) {
				    //Affiche les modules
                    echo "<option value='". $topic->ID ."'".($selected_module == $topic->ID?' selected':'').">" . $topic->Topic . "</option>";
                }
            ?>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-12 form-group">
            <h4 for="title"><?php echo $this->lang->line('update_title_topic'); ?></h4>
            <input maxlength="<?=TOPIC_MAX_LENGTH?>" type="text" name="title" class="form-control" id="title" value="">
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
