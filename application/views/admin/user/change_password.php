<?php
/**
 * Add/modify User
 *
 * @author        Orif Pomy, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright    Copyright (c) Orif section informatique, Switzerland (http://www.sectioninformatique.ch)
 */
?>
<div class="container">
	<h1 class="title-section"><?= $this->lang->line('title_user_change_password').'"'.$user->User; ?>"</h1>
    <?php
    $attributes = array('id' => 'addUserForm',
                        'name' => 'addUserForm');
    echo form_open('Admin/user_change_password_form', $attributes, array(
        'id' => $user->ID ?? 0
    ));
    ?>

        <div class="row">
            <div class="form-group">
                <a name="cancel" class="btn btn-danger col-xs-12 col-sm-4" href="<?=base_url('/Admin/user_index')?>"><?=$this->lang->line('cancel')?></a>
                <?php
                    echo form_submit('save', $this->lang->line('save'), 'class="btn btn-success col-xs-12 col-sm-4 col-sm-offset-4"'); 
                ?>
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <div class="col-md-6">
                    <h4><?= form_label($this->lang->line('user_password_force'), 'password_force'); ?></h4>
                </div>
                <div class="col-md-6">
                    <h4><?= form_checkbox('password_force', 'password_force', FALSE, array(
                        'id' => 'password_force', 'class' => 'form-control',
                        'onchange' => 'toggleOldPassword()'
                    )); ?></h4>
                </div>
            </div>
        </div>

        <div class="row" id="userPasswordOld">
            <div class="form-group col-md-12">
                <h4><?= form_label($this->lang->line('field_old_password'), 'user_password_old'); ?></h4>
                <?= form_password('user_password_old', '', array(
                    'class' => 'form-control', 'id' => 'user_password_old'
                )); ?>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-12">
                <h4><?= form_label($this->lang->line('field_new_password'), 'user_password_new'); ?></h4>
                <?= form_password('user_password_new', '', array(
                    'class' => 'form-control', 'id' => 'user_password_new'
                )); ?>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-12">
                <h4><?= form_label($this->lang->line('field_password_confirm'), 'user_password_again'); ?></h4>
                <?= form_password('user_password_again', '', array(
                    'class' => 'form-control', 'id' => 'user_password_again'
                )); ?>
            </div>
        </div>

    <?= form_close(); ?>
</div>
<script type="text/javascript">
    let oldPasswordInput = '',
    userPasswordOld = document.getElementById('userPasswordOld'),
    forcePasswordCheckbox = document.getElementById('password_force');

    function toggleOldPassword() {
        userPasswordOld.hidden = forcePasswordCheckbox.checked;
    }
</script>