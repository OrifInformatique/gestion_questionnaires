<?php
/**
 * Add/modify User
 *
 * @author        Orif Pomy, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright    Copyright (c) Orif section informatique, Switzerland (http://www.sectioninformatique.ch)
 */
$update = !is_null($user);
?>
<div class="container">
    <h1 class="title-section"><?= $this->lang->line('title_user'.($update ? '_update' : '_new')); ?></h1>
    <?php
    $attributes = array('id' => 'addUserForm',
                        'name' => 'addUserForm');
    echo form_open('Admin/user_form', $attributes, array(
        'id' => $user->id ?? 0
    ));
    ?>

        <!-- ERROR MESSAGES -->
        <?php
        if (!empty(validation_errors())) {
            echo '<div class="alert alert-danger">'.validation_errors().'</div>';}
        ?>

        <!-- USER FIELDS -->
        <div class="row">
            <div class="form-group col-md-12">
                <?= form_label($this->lang->line('user_name'), 'user_name', array('class' => 'form-label')); ?>
                <?= form_input('user_name', $user->username ?? '', array(
                    'maxlength' => 45, 'class' => 'form-control', 'id' => 'user_name'
                )); ?>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-12">
                <?= form_label($this->lang->line('user_usertype'), 'user_usertype', array('class' => 'form-label')); ?>
                <?= form_dropdown('user_usertype', $user_types, $user->fk_user_type ?? null, array(
                    'class' => 'form-control'
                )); ?>
            </div>
        </div>

        <?php if(!$update) { ?>
        <div class="row">
            <div class="form-group col-md-12">
                <?= form_label($this->lang->line('user_password'), 'user_password', array('class' => 'form-label')); ?>
                <?= form_password('user_password', '', array(
                    'class' => 'form-control', 'id' => 'user_password'
                )); ?>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-12">
                <?= form_label($this->lang->line('user_password_again'), 'user_password_again', array('class' => 'form-label')); ?>
                <?= form_password('user_password_again', '', array(
                    'class' => 'form-control', 'id' => 'user_password_again'
                )); ?>
            </div>
        </div>
        <?php } else { ?>
        <div class="row">
            <div class="form-group col-md-12">
                <?php
                if($user->archive) {
                    echo form_submit('reactivate', $this->lang->line('btn_reactivate'), 'class="btn btn-info col-12 col-sm-4"');
                } else {
                    echo form_submit('disactivate', $this->lang->line('btn_desactivate'), 'class="btn btn-warning col-12 col-sm-4"');
                }
                ?>
                <a href="<?= base_url('Admin/user_change_password/'.$user->id); ?>"
                    class="btn btn-default col-12 col-sm-4 col-sm-offset-4">
                    <?= $this->lang->line("title_user_change_password"); ?>
                </a>
            </div>
        </div>
        <?php } ?>

        <div class="row">
            <div class="col-12 text-right">
                <a name="cancel" class="btn btn-default" href="<?=base_url('/Admin/user_index')?>"><?=$this->lang->line('cancel')?></a>
                <?php
                    echo form_submit('save', $this->lang->line('save'), 'class="btn btn-primary"'); 
                ?>
            </div>
        </div>

    <?= form_close(); ?>
</div>