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
        <div class="well">
            <?php
            $attributes = array("class" => "form-group",
                "id" => "addTopicForm",
                "name" => "addTopicForm");
            echo form_open('Topic/form_add', $attributes);
            ?>
            <?php
            if($error == true) {
                echo "<p class='alert alert-warning'>" . $this->lang->line('add_topic_form_err') . "</p>";
            }
            ?>
            <div class="form-group">
			
				<label for="module"><?php echo $this->lang->line('focus_module'); ?></label>
				<select  class="form-control" name="module_selected" id="module_selected">
					<?php
					//Récupère chaque module
					foreach ($topics as $object => $topic) {
						var_dump($topic);
						if($topic->FK_Parent_Topic == 0)
						{
							//Affiche les modules
							echo "<option value=". $topic->ID .">" . $topic->Topic . "</option>";
						}
					}
					?>
				</select>
				<h2></h2>
			
                <label for="title"><?php echo $this->lang->line('update_title_topic'); ?></label>
                <div class="row">
                    <div class="col-lg-4"><input type="text" name="title" class="form-control" id="title" value=""></div>
                </div>
            </div>
            <input type="submit" class="btn btn-primary" />
            <?php echo form_close(); ?>
        </div>
    </div>
