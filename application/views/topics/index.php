<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * View of topic's list
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
?>
<div id="page-content-wrapper">
    <div class="container">
        <h1 class="title-section"><?php echo $this->lang->line('title_topic'); ?></h1>			
		<?php
			if($error == true) {
				echo "<p class='alert alert-danger'>" . $error . "</p>";
			}
		?>

        <div class="row">
            <div class="col-12">
                <div class="accordion" id="accordion">
                    <?php foreach ($modules as $module) { ?>
                        <div class="card">
                            <div class="card-header" id="heading<?=$module->ID?>">
                                <h5 class="mb-0">
                                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse<?=$module->ID?>" aria-expanded="false" aria-controls="collapse<?=$module->ID?>">
                                        <?=$module->Topic;  ?> ▼
                                    </button>
                                    <a href="<?php echo base_url(); ?>Topic/delete_module/<?php echo $module->ID; ?>" class="close">×</a>
                                    <a href="<?=base_url()?>Topic/update_module/<?php echo $module->ID ?>" class="close">✎</a>
                                </h5>
                            </div>

                            <div id="collapse<?=$module->ID?>" class="collapse" aria-labelledby="heading<?=$module->ID?>" data-parent="#accordion">
                                <div class="card-body">
                                    <table class="table table-hover">
                                        <tr><td><a class="btn btn-primary" href="<?=base_url('Topic/add_topic/'.$module->ID)?>"><?=$this->lang->line('btn_add_topic_in_module')?></a></td></tr>
                                        <?php foreach ($topics as $topic) {
                                            if($topic->FK_Parent_Topic == $module->ID){ ?>
                                                <tr>
                                                    <td><a href="<?= base_url('Topic/update_topic/'.$topic->ID)?>"><?=$topic->Topic?></a></td>
                                                    <td><a href="<?= base_url('Topic/delete_topic/'.$topic->ID)?>" class="close">×</a></td>
                                                </tr>
                                            <?php }
                                        } ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="text-right"></div>
                    <a class="btn btn-primary" href="<?=base_url('Topic/add_module/')?>"><?=$this->lang->line('btn_add_module')?></a>
                </div>
            </div>
        </div>
    </div>
</div>
