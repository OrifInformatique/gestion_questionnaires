<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * View for Excel file importation
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
?>

<div class="container">
    <h1 class="title-section"><?php echo $this->lang->line('title_import_questions'); ?></h1>

    <?php
    $attributes = array("class" => "form-group",
                        "id" => "importQuestionForm",
                        "name" => "importQuestionForm");

    echo form_open_multipart('Question/import', $attributes);
    ?>
    <div class="row">
        <div class="form-group">
            <div class="col-sm-8">
                <h4><?php echo $this->lang->line('focus_topic'); ?></h4>
                <?php echo form_dropdown('topic_selected', $topics, NULL, 'id="topic_selected" class="form-control"'); ?>
            </div>
            <div class="col-sm-4">
                
                <a href="../Topic" class="btn btn-info col-xs-12 xs-space"><?php echo $this->lang->line('btn_add_topic');?></a>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-xs-12 col-sm-8 xs-space"> 
            <input class="form-control-file" type="file" name="excelfile" id="excelfile" accept=".xlsx">
         </div>
        <div class="col-xs-12 col-sm-4 xs-space"> 
            <input class="btn btn-success col-xs-12" type="submit" value="<?php echo $this->lang->line('btn_import_question'); ?>" name="submitExcel">
        </div>
    </div>
    <h1 class="title-section"><?php echo $this->lang->line('title_import_pictures'); ?></h1>

    <?php
    $attributes = array("class" => "form-group",
        "id" => "updatePicturesForm",
        "name" => "updatePicturesForm",
        "enctype" => "multipart/form-data");
    echo form_open('Question/import', $attributes);
    ?>
    <div class="row"> 
        <div class="col-xs-12 col-sm-8 xs-space">
                <input class="form-control-file text-center" type="file" name="picturesfile[]" id="picturesfile" accept="image/*" multiple>
        </div> 
        <div class="col-xs-12 col-sm-4 xs-space">
                <input class="btn btn-success col-xs-12" type="submit" value="<?php echo $this->lang->line('btn_import_image'); ?>" name="submitPictures">
        </div>
    </div>
</div>