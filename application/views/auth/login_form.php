<div class="container">
    <div class="row">
        <div class="col-md-6 col-sm-10 well">
            <?php 
                $attributes = array("class" => "form-horizontal",
                                    "id" => "loginform",
                                    "name" => "loginform");
                echo form_open("auth/login", $attributes);
            ?>
            <fieldset>
                <legend><?= $this->lang->line('page_login'); ?></legend>
                
                <div class="form-group">
                    <div class="row colbox">
                        <div class="col-sm-4">
                            <?= form_label($this->lang->line('field_username'), 'username', [
                                'class' => 'control-label'
                            ]); ?>
                        </div>
                        <div class="col-sm-8">
                            <?= form_input('username', '', [
                                'id' => 'username', 'placeholder' => $this->lang->line('field_username'),
                                'class' => 'form-control'
                            ]); ?>
                            <span class="text-danger"><?= form_error('username'); ?></span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row colbox">
                        <div class="col-sm-4">
                            <?= form_label($this->lang->line('field_password'), 'username', [
                                'class' => 'control-label'
                            ]); ?>
                        </div>
                        <div class="col-sm-8">
                            <?= form_password('password', '', [
                                'id' => 'password', 'placeholder' => $this->lang->line('field_password'),
                                'class' => 'form-control'
                            ]); ?>
                            <span class="text-danger"><?= form_error('password'); ?></span>
                        </div>
                    </div>
                </div>
                                  
                <div class="form-group">
                    <div class="col-sm-12 text-center">
                        <?= form_submit('btn_login', $this->lang->line('btn_login'), [
                            'id' => 'btn_login', 'class' => 'btn btn-primary'
                        ]); ?>
                        <a id="btn_cancel" class="btn btn-default" href="<?= base_url(); ?>"><?= $this->lang->line('btn_cancel'); ?></a>
                    </div>
                </div>
            </fieldset>
            <?= form_close(); ?>

            <!-- Status messages -->
            <?php if(!is_null($this->session->flashdata('message-danger'))){ ?>
                <div class="alert alert-danger text-center"><?= $this->session->flashdata('message-danger'); ?></div>
            <?php } ?>
        </div>
    </div>
</div>