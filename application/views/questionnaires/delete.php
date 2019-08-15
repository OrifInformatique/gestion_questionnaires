<div id="page-content-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div>
                    <h3><?= $this->lang->line('del_questionnaire'.($model ? '_model' : '').'_form_title') . '"' . ($model ? $Base_Name : $Questionnaire_Name); ?>" ?
                    </h3>
                </div>
                <div class="btn-group">
                    <a href="<?= base_url().uri_string()."/confirmed";?>" class="btn btn-danger btn-lg">
                        <?= $this->lang->line('yes');?>
                    </a>
                    <a href="<?= base_url()."Questionnaire";?>" class="btn btn-lg">
                        <?= $this->lang->line('no');?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
