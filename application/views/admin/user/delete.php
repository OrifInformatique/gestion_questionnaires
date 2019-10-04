<div id="page-content-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div>
                    <span class="form-header"><?= $this->lang->line('del_user_form_title') . '"' . $User; ?>" ?</span>
                </div>
                <div class="btn-group">
                    <?php if(!$Archive) { ?>
                    <a href="<?= base_url().uri_string()."/1";?>" class="btn btn-warning btn-lg">
                        <?= $this->lang->line('btn_desactivate');?>
                    </a>
                    <?php } ?>
                    <a href="<?= base_url().uri_string()."/2";?>" class="btn btn-danger btn-lg">
                        <?= $this->lang->line('btn_del');?>
                    </a>
                    <a href="<?= base_url()."Admin/user_index";?>" class="btn btn-lg">
                        <?= $this->lang->line('btn_cancel');?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
