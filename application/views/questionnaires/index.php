<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Questionnaire List View
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
?>
<div id="page-content-wrapper">
    <div class="container">
        <h1><?= $this->lang->line('title_model'); ?></h1>
        <div class="row">
            <div class="col-sm-3">
                <a href="<?= base_url('Questionnaire/form_add/1'); ?>" class="btn btn-primary col-12"><?= $this->lang->line('btn_add_questionnaire_model'); ?></a>
            </div>
        </div>
        <div class="row">
            <div class="col-12 table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><?= $this->lang->line('questionnaire_model_name'); ?></th>
                            <th><?= $this->lang->line('questionnaire_model_titles');?></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($models as $model) { ?>
                        <tr>
                            <td><a href="<?= base_url('Questionnaire/update/'.$model->ID.'/1'); ?>"><?= $model->Base_Name; ?></a></td>
                            <td><?= $model->Questionnaire_Name; ?></td>
                            <td><a class="btn btn-secondary btn-sm" href="<?= base_url('Questionnaire/generate_pdf_from_model/'.$model->ID) ?>"><?= $this->lang->line('generate_questionnaire'); ?></a></td>
                            <td><a href="<?= base_url('Questionnaire/model_delete/'.$model->ID); ?>" class="close">×</a></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <h1 class="title-section"><?php echo $this->lang->line('title_questionnaire');?></h1>
        <?php
            if($error == true) {
                echo "<p class='alert alert-danger'>" . $error . "</p>";
            }
        ?>
        <div class="row">
            <div class="col-sm-3">
                <a href="<?php echo base_url(); ?>Questionnaire/form_add/" class="btn btn-primary col-12"><?php echo $this->lang->line('btn_add_questionnaire');?></a>
            </div>
        </div>
        <div class="row">
            <div class="col-12 table-responsive">
                <table class="table table-hover" >
                    <thead>
                    <tr>
                        <th><?php echo $this->lang->line('questionnaire');?></th>
                        <th><?php echo $this->lang->line('pdf');?></th>
                        <th><?php echo $this->lang->line('corrige');?></th>
                        <th><?php echo $this->lang->line('regenerate');?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($questionnaires as $questionnaire){
                        ?>
                        <tr>
                            <td><a href="<?= base_url('Questionnaire/update/'.$questionnaire->ID); ?>"><?php echo $questionnaire->Questionnaire_Name; ?></a></td>
                            <td><a class="btn btn-secondary btn-sm" href="<?php echo base_url()."pdf_files/questionnaires/".$questionnaire->PDF; ?>" target="_blank">
                                    <?php echo $this->lang->line('redirect_pdf');?></a></td>
                            <td><a class="btn btn-secondary btn-sm" href="<?php echo base_url()."pdf_files/corriges/".$questionnaire->Corrige_PDF; ?>" target="_blank">
                                    <?php echo $this->lang->line('redirect_corrige');?></a></td>
                            <td><a class="btn btn-secondary btn-sm" href="<?php echo base_url(); ?>Questionnaire/generate_pdf/<?php echo $questionnaire->ID; ?>">
                                    <?php echo $this->lang->line('regenerate_questionnaire');?></a></td>
                            <td><a href="<?php echo base_url(); ?>questionnaire/delete/<?php echo $questionnaire->ID; ?>" class="close">×</a></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
