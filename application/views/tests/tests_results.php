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
    <table class="table">
        <tr>
            <th>Nom du test</th>
            <th>Error</th>
            <th>DB</th>
        </tr>
        <?php foreach ($test_results as $target_name => $target) { ?>
            <tr><th colspan="3"><?=$target_name?></th></tr>
            <?php foreach ($target as $operation_name => $operation) { ?>
                <tr><th colspan="3"> - <?=$operation_name?></th></tr>
                <?php foreach ($operation as $test => $result) { ?>
                    <tr>
                        <td><?=$test?></td>
                        <td class="<?=$result->errors == $result->errors_expected ? 'success' : 'danger' ?>">
                            <?=$result->errors == $result->errors_expected ? 'OK' : 'échoué'?>
                        </td>
                        <td class="<?=$result->db_update == $result->db_update_expected ? 'success' : 'danger' ?>">
                            <?=$result->db_update == $result->db_update_expected ? 'OK' : 'échoué'?>
                        </td>
                    </tr>
        <?php } } } ?>
    </table>
</div>
