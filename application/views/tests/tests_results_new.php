<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Tests results view
 *
 * @author      Orif, section informatique (BuYa)
 * @link        https://github.com/OrifInformatique/gestion_modules
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
?>
<div class="container">
    <div class="row table">
        <div class="col-sm-3"><b>Nom du test</b></div>
        <div class="col-sm-3"><b>Réussite</b></div>
        <div class="col-sm-6"><b>Erreurs</b></div>
    </div>
    <?php foreach($test_results as $target_name => $target) { ?>
        <div class="row col-sm-12"><b><?=$target_name;?></b></div>
        <?php foreach($target as $operation_name => $operation) { ?>
            <div class="row col-sm-12"><b> - <?=$operation_name;?></b></div>
            <?php foreach($operation as $test => $result) { ?>
                <div class="row">
                    <div class="col-sm-3"><?=$test;?></div>
                    <div class="col-sm-3 alert-<?=($result->success?'success':'danger');?>">
                        <?=($result->success?'✔️':'❌');?>
                    </div>
                    <table class="col-sm-6 alert-danger table-striped">
                    <?php foreach($result->errors as $error) { ?>
                        <tr><td><?=$error;?></td></tr>
                    <?php } ?>
                    </table>
                </div>
    <?php } } } ?>
</div>