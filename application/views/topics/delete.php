<div id="page-content-wrapper">
    <div class="container">
        <div class="row">
        	<div class="col-xs-12">
				<div>
					<span class="form-header"><?php echo $this->lang->line('del_topic_form_title') . '"' . $Topic; ?>" ?</span>
					<?php
						if(!empty($topics)) {
							echo "<p class='alert alert-danger'>".$this->lang->line('delete_module_topics_list')[0].$topics.$this->lang->line('delete_module_topics_list')[1].'<br><a href="'.base_url('Question?module='.$ID).'">'.$this->lang->line('view_questions_list').'</a></p>';
						}
					?>
				</div>
				<div class="btn-group">
					<a href="<?php echo base_url().uri_string()."/confirmed";?>" class="btn btn-danger btn-lg">
						<?php echo $this->lang->line('yes');?>
					</a>
					<a href="<?php echo base_url()."Topic";?>" class="btn btn-lg">
						<?php echo $this->lang->line('no');?>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>