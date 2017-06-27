<div class="row">
    <div class="col-lg-2"></div>
    <div class="col-lg-8">
		<div><H3><?php echo $this->lang->line('del_module_form_title') . '"' . $Topic; ?>" ?</H3></div>
		<div class="btn-group">
			<a href="<?php echo base_url().uri_string()."/confirmed";?>" class="btn btn-danger btn-lg">
				<?php echo $this->lang->line('oui');?>
			</a>
			<a href="<?php echo base_url()."Module";?>" class="btn btn-lg">
				<?php echo $this->lang->line('non');?>
			</a>
		</div>
	</div>
	<div class="col-lg-2"></div>
</div>
