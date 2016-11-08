<?php
/**
 * Created by PhpStorm.
 * User: UlSi
 * Date: 08.11.2016
 * Time: 11:18
 */
?>
<div class="container jumbotron" style="background-color: lavender">
    <div class="row">
        <div class="col-lg-8">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Questionnaire</th>
                        <th>PDF</th>
                        <th>Corrigé</th>
                    </tr>
                </thead>
                <?php
                foreach ($questionnaires as $questionnaire){
                    ?>
                <tbody>
                    <tr>
                        <td><?php echo $questionnaire->Questionnaire_Name; ?></td>
                        <td><?php echo $questionnaire->PDF; ?></td>
                        <td><?php echo $questionnaire->Corrige_PDF; ?></td>
                    </tr>
                </tbody>
                <?php
                }
                ?>
            </table>
        </div>
    </div>
</div>
