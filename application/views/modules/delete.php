<div class="col-xs-12">
	<div>
		<h3><?php echo $this->lang->line('del_module_form_title') . '"' . $Topic; ?>" ?</h3>
	</div>
	<div class="btn-group">
		<a href="<?php echo base_url().uri_string()."/confirmed";?>" class="btn btn-danger btn-lg">
			<?php echo $this->lang->line('yes');?>
		</a>
		<a href="<?php echo base_url()."Module";?>" class="btn btn-lg">
			<?php echo $this->lang->line('no');?>
		</a>
	</div>
</div>
