<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Users List View
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
?>
<div class="container">
    <h1 class="title-section"><?= $this->lang->line('title_users'); ?></h1>
    <div class="row">
        <div class="col-xs-12 col-sm-4">
            <a href="<?= base_url('Admin/user_add'); ?>" class="btn btn-success col-xs-12">
                <?= $this->lang->line('btn_add_user'); ?>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th><?= $this->lang->line('user_name'); ?></th>
                        <th><?= $this->lang->line('user_usertype'); ?></th>
                        <th><?= $this->lang->line('user_active'); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $user) { ?>
                        <tr>
                            <td><a href="<?= base_url('Admin/user_add/'.$user->ID); ?>"><?= $user->User; ?></a></td>
                            <td><?= $user_types[$user->FK_User_Type]; ?></td>
                            <td><?= $this->lang->line($user->Archive ? 'no' : 'yes'); ?></td>
                            <td><a href="<?= base_url('Admin/user_delete/'.$user->ID); ?>" class="close">×</a></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>