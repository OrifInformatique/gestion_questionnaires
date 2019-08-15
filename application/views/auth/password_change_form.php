<div class="container">
    <div class="row">
        <div class="col-md-6 col-sm-10 well">
            <?php 
                $attributes = array("class" => "form-horizontal",
                                    "id" => "change_password",
                                    "name" => "change_password");
                echo form_open("auth/change_password", $attributes);
            ?>
            <fieldset>
                <legend><?= $this->lang->line('page_password_change'); ?></legend>
                
                <div class="form-group">
                    <div class="row colbox">
                        <div class="col-sm-4">
                            <?= form_label($this->lang->line('field_old_password'), 'old_password', [
                                'class' => 'control-label'
                            ]); ?>
                        </div>
                        <div class="col-sm-8">
                            <?= form_password('old_password', '', [
                                'id' => 'old_password', 'class' => 'form-control',
                                'placeholder' => $this->lang->line('field_old_password')
                            ]); ?>
                            <span class="text-danger"><?= form_error('old_password'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row colbox">
                        <div class="col-sm-4">
                            <?= form_label($this->lang->line('field_new_password'), 'new_password', [
                                'class' => 'control-label'
                            ]); ?>
                        </div>
                        <div class="col-sm-8">
                            <?= form_password('new_password', '', [
                                'id' => 'new_password', 'class' => 'form-control',
                                'placeholder' => $this->lang->line('field_new_password')
                            ]); ?>
                            <span class="text-danger"><?= form_error('new_password'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row colbox">
                        <div class="col-sm-4">
                            <?= form_label($this->lang->line('field_password_confirm'), 'confirm_password', [
                                'class' => 'control-label'
                            ]); ?>
                        </div>
                        <div class="col-sm-8">
                            <?= form_password('confirm_password', '', [
                                'id' => 'confirm_password', 'class' => 'form-control',
                                'placeholder' => $this->lang->line('field_password_confirm')
                            ]); ?>
                            <span class="text-danger"><?= form_error('confirm_password'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12 text-center">
                        <?= form_submit('btn_change_password', $this->lang->line('btn_change_password'), [
                            'id' => 'btn_change_password', 'class' => 'btn btn-primary'
                        ]); ?>
                        <a id="btn_cancel" class="btn btn-default" href="<?= base_url(); ?>"><?= $this->lang->line('btn_cancel'); ?></a>
                    </div>
                </div>
            </fieldset>
            <?= form_close(); ?>
        </div>
    </div>
</div>