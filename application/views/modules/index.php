<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * View of module's list
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
                        <?php echo $this->lang->line('nav_module');?>
</a>
</li>
<li>
    <a href="#"><?php echo $this->lang->line('btn_add');?></a>
</li>
<li>
    <a id="btn_update"><?php echo $this->lang->line('btn_update');?></a>
</li>
<li>
    <a id="btn_del"><?php echo $this->lang->line('btn_del');?></a>
</li>
</ul>
</div>
</div>
<div id="page-content-wrapper">
    <div class="container">
        <h1 style="padding-top: 12%; padding-bottom: 5%" class="text-center"><?php echo $this->lang->line('title_module'); ?></h1>
        <div class="row">
            <div class="col-lg-2"></div>
            <div class="col-lg-8">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nom du module</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        foreach ($modules as $module)
                        {
                    ?>
                            <tr onclick="getID(<?php echo $module->ID; ?>, 3);" id="<?php echo $module->ID; ?>">
                                <td><?php echo $module->Topic; ?></td>
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