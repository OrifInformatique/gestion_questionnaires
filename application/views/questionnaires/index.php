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
        <h1 class="title-section"><?php echo $this->lang->line('title_questionnaire');?></h1>
        <?php
            if($error == true) {
                echo "<p class='alert alert-danger'>" . $error . "</p>";
            }
        ?>
        <div class="row">
            <div class="col-xs-12 col-sm-4">
                <a href="<?php echo base_url(); ?>Questionnaire/add/" class="btn btn-success col-xs-12" style="margin-bottom:10px;"><?php echo $this->lang->line('btn_add_questionnaire');?></a>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 table-responsive">
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
                            <td><?php echo $questionnaire->Questionnaire_Name; ?></td>
                            <td><a href="<?php echo base_url()."pdf_files/questionnaires/".$questionnaire->PDF; ?>" target="_blank">
                                    <?php echo $this->lang->line('redirect_pdf');?></a></td>
                            <td><a target="_blank" href="<?php echo base_url()."pdf_files/corriges/".$questionnaire->Corrige_PDF; ?>">
                                    <?php echo $this->lang->line('redirect_corrige');?></a></td>
                            <td><a href="<?php echo base_url(); ?>Questionnaire/generatePDF/<?php echo $questionnaire->ID; ?>">
                                    <?php echo $this->lang->line('regenerate');?></a></td>
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
