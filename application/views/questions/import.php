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
    <h1 style="padding-top: 12%; padding-bottom: 5%"
        class="text-center"><?php echo $this->lang->line('title_import_questions'); ?></h1>

    <?php
    $attributes = array("class" => "form-group",
                        "id" => "importQuestionForm",
                        "name" => "importQuestionForm");

    echo form_open_multipart('Question/import', $attributes);
    ?>
    <div class="row">
        <div class="col-lg-4"></div>
        <div class="form-group">
            <div class="col-lg-4" style="height:110px;">
                <h4><?php echo $this->lang->line('focus_topic'); ?></h4>

                <?php var_dump($topics) ?>
                
                <select class="form-control" name="topic_selected" id="topic_selected">
                    <?php
                        // List of available topics
                        foreach ($topics as $object => $module) {
                            if ($module->FK_Parent_Topic == 0) {
                                // Display first level topic
                                echo "<optgroup label='$module->Topic' >";

                                // Display all associated subtopics
                                for ($i = 0; $i < count($topics); $i++) {
                                    if ($module->ID == $topics[$i]->FK_Parent_Topic) {
                                        echo "<option>" . $topics[$i]->Topic . "</option>";
                                    }
                                }
                                echo "</optgroup>";
                            }
                        }
                    ?>
                </select>
            </div>
            <div class="col-lg-2">
                </br>
                </br>
                <div class="form-group">
                    <a href="../Topic" class="btn btn-info"><?php echo $this->lang->line('btn_add');?></a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4"></div>
        <div class="col-lg-8">
            <div class="form-group">
                <input class="form-control-file" type="file" name="excelfile" id="excelfile"
                       accept=".xlsx">
            </div>
            <div class="form-group">
                <input class="btn btn-success" type="submit"
                       value="<?php echo $this->lang->line('btn_import_questions'); ?>" name="submitExcel">
            </div>
            </form>

        </div>
        <div class="col-lg-2"></div>
    </div>
    <h1 style="padding-top: 12%; padding-bottom: 5%"
        class="text-center"><?php echo $this->lang->line('title_import_pictures'); ?></h1>

    <?php
    $attributes = array("class" => "form-group",
        "id" => "updatePicturesForm",
        "name" => "updatePicturesForm",
        "enctype" => "multipart/form-data");
    echo form_open('Question/import', $attributes);
    ?>
    <div class="row">
        <div class="col-lg-4"></div>
        <div class="col-lg-4">
            <div class="form-group">
                <input class="form-control-file text-center" type="file" name="picturesfile[]" id="picturesfile"
                       accept="image/*" multiple>
            </div>
            <div class="form-group">
                <input class="btn btn-success" type="submit"
                       value="<?php echo $this->lang->line('btn_import'); ?>" name="submitPictures">
            </div>
        </div>
    </div>
</div>