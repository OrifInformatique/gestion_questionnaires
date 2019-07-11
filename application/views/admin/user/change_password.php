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
    $attributes = array('id' => 'user_change_password_form',
                        'name' => 'user_change_password_form');
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
