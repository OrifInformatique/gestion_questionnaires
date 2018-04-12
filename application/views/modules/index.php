<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * View of module's list
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
?>
<div id="page-content-wrapper">
	<div class="container">
		<h1 class="title-section"><?php echo $this->lang->line('title_module'); ?></h1>
		<?php
			if($error == true) {
				echo "<p class='alert alert-danger'>" . $error . "</p>";
			}
		?>
		<div class="row">
			<div class="col-xs-12 col-sm-4">
				<a href="<?php echo base_url(); ?>Module/add/" class="btn btn-success col-xs-12"><?php echo $this->lang->line('btn_add_module'); ?></a>
			</div>
			<div class="col-xs-12 table-responsive">
				<table class="table table-hover">
					<thead>
						<tr>
							<th><?php echo $this->lang->line('module');?></th>
						</tr>
					</thead>
					<tbody>
					<?php
						foreach ($modules as $module)
						{
						?>
						<tr>
							<td>	
							<a href="<?php echo base_url(); ?>Module/update/<?php echo $module->ID; ?>"><?php echo $module->Topic; ?></a>
							</td>
							<td>
							<a href="<?php echo base_url(); ?>Module/delete/<?php echo $module->ID; ?>" class="close">×</a>
							</td>
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