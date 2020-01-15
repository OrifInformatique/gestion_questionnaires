<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author      Orif, section informatique (BuYa, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */

?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <br>
            <?= "<p class='alert alert-warning'>" . $this->lang->line('no_implemented_question_type') . "</p>" ; ?>
            <a href="<?=base_url('Question/add')?>"><button class="btn btn-default"><?=$this->lang->line('return')?></button></a>
        </div>
    </div>
</div>