<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * View of question's details
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */

?>
<div class="container">
    <?php if($question->Archive) { ?>
        <div class="row alert alert-danger">
            <?= $this->lang->line('question_deleted'); ?>
        </div>
    <?php } ?>
    <div class="row">
        <div class="col-xs-12">
            <table class="table table-hover" id="table">
                <tbody>
                    <tr>
                        <td><h4>Type de question:&nbsp</h4><?= $question->question_type->Type_Name ?></td>
                        <td><h4>Date de création:&nbsp</h4><?= $question->Creation_Date ?></td>
                        <td><h4>ID:&nbsp</h4><?= $question->ID ?></td>
                    </tr>
                    <tr>
                        <td colspan=2><h4>Sujet:&nbsp</h4><?= $question->topic->Topic ?></td>
                        <td><h4>Nb points:&nbsp</h4><?= $question->Points ?></td>
                    </tr>
                    <tr><td colspan=3><h4>Question:&nbsp</h4><?= $question->Question; ?></td></tr>
                    <tr><td colspan=3><h4>Réponse:&nbsp</h4><?= $reponse; ?></td></tr>
                </tbody>
            </table>
            <?php if ($question->FK_Question_Type == 7) {
                echo "<img src=".base_url('uploads/pictures/'.$image).">";
            } ?>
            <a href="<?= base_url('Question'); ?>">$this->lang->line('return')</a>
        </div>
    </div>
</div>