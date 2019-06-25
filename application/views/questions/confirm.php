<div id="page-content-wrapper">
    <div class="container">
        <div class="row">
            <h3><?= '"'.$Question.'"'; ?></h3>
            <?php if($Archive) { ?>
                <div class="alert alert-danger">
                    <?= $this->lang->line('question_already_deleted'); ?>
                </div>
            <?php } else {?>
                <div class="col-xs-12">
                    <div>
                        <h4 class="alert alert-warning"><?= $this->lang->line('del_question_form_title')?></h4>
                    </div>
                    <div class="btn-group">
                        <a href="<?= base_url().uri_string()."/confirmed";?>" class="btn btn-danger btn-lg">
                            <?= $this->lang->line('yes');?>
                        </a>
                        <a href="<?= base_url()."Question";?>" class="btn btn-lg">
                            <?= $this->lang->line('no');?>
                        </a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
