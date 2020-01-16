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
    <?php
    $attributes = array("class" => "form-group",
        "id" => "updateTopicForm",
        "name" => "updateTopicForm");
    echo form_open('Topic/update_topic', $attributes);
    ?>
    <?php
    if($error == true) {
        echo "<p class='alert alert-warning'>" . $this->lang->line('update_topic_form_err') . "</p>";
    }
    ?>
    <div class="row">
        <div class="form-group col-12">
            <h4 for="title"><?php echo $this->lang->line('update_title_topic'); ?></h4>
            <input maxlength="<?=TOPIC_MAX_LENGTH?>" type="text" name="title" class="form-control" id="title" value="<?php echo $title_topic; ?>">
        </div>
    </div>
    <input type="hidden" name="id" id="id" value="<?php echo $id; ?>">

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
