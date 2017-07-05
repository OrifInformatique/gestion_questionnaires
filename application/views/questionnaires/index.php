<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Questionnaire List View
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
?>
<div id="wrapper">

    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <ul class="sidebar-nav">
            <li class="sidebar-brand">
                <a href="#">
                    <?php echo $this->lang->line('nav_questionnaire');?>
                </a>
            </li>
            <li>
                <a href="<?php echo base_url('Questionnaire/add');?>"><?php echo $this->lang->line('btn_add');?></a>
            </li>
            <li>
                <a id="btn_update"><?php echo $this->lang->line('btn_update');?></a>
            </li>
        </ul>
    </div>
</div>
<div id="page-content-wrapper">
    <div class="container">
        <h1 style="padding-top: 12%; padding-bottom: 5%" class="text-center"><?php echo $this->lang->line('title_questionnaire');?></h1>
        <div class="row">
            <div class="col-lg-2"></div>
            <div class="col-lg-8">
                <table class="table table-hover" id="table">
                    <thead>
                    <tr>
                        <th><?php echo $this->lang->line('questionnaire');?></th>
                        <th><?php echo $this->lang->line('pdf');?></th>
                        <th><?php echo $this->lang->line('corrige');?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($questionnaires as $questionnaire){
                        ?>
                        <tr onclick="getID(<?php echo $questionnaire->ID; ?>, 1);"
                            id="<?php echo $questionnaire->ID; ?>">
                                <td><?php echo $questionnaire->Questionnaire_Name; ?></td>
                                <td><a href="<?php echo base_url().$questionnaire->Questionnaire_Name.'.pdf'?>" target="_blank" href="">
                                        <?php echo $this->lang->line('redirect_pdf');?></a></td>
                                <td><a target="_blank" href="">
                                        <?php echo $this->lang->line('redirect_corrige');?></a></td>
								<td><a href="<?php echo base_url(); ?>questionnaire/delete/<?php echo $questionnaire->ID; ?>" class="close">×</a></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-2"></div>
        </div>
    </div>
</div>
