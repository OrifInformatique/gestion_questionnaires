
<div id="page-content-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div>
                    <span class="form-header"><?php echo $this->lang->line('del_module_form_title') . '"' . $Topic; ?>" ?</span>
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
