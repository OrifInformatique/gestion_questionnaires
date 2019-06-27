<div id="page-content-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div>
                    <h3><?php echo $this->lang->line('del_user_form_title') . '"' . $User; ?>" ?</h3>
                </div>
                <div class="btn-group">
                    <a href="<?php echo base_url().uri_string()."/1";?>" class="btn btn-danger btn-lg">
                        <?php echo $this->lang->line('yes');?>
                    </a>
                    <a href="<?php echo base_url()."Module";?>" class="btn btn-lg">
                        <?php echo $this->lang->line('no');?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
