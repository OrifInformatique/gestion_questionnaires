<?php
/**
 * Created by PhpStorm.
 * User: UlSi
 * Date: 08.11.2016
 * Time: 11:18
 */
?>
<div class="container jumbotron well" style="background-color: lavender">
    <h2 class="text-center"><?php echo $this->lang->line('title');?></h2>
    <div class="row">
        <div class="col-lg-2"></div>
        <div class="col-lg-8">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th><?php echo $this->lang->line('questionnaire');?></th>
                        <th><?php echo $this->lang->line('pdf');?></th>
                        <th><?php echo $this->lang->line('corrige');?></th>
                    </tr>
                </thead>
                <?php
                foreach ($questionnaires as $questionnaire){
                    ?>
                <tbody>
                    <tr>
                        <td></td>
                        <td><?php echo $questionnaire->Questionnaire_Name; ?></td>
                        <td><a href=""><?php echo $this->lang->line('redirect_pdf');?></a></td>
                        <td><a href=""><?php echo $this->lang->line('redirect_corrige');?></a></td>
                    </tr>
                </tbody>
                <?php
                }
                ?>
            </table>
            <p id="demo">A Paragraph</p>
            <button type="button" onclick="myFunction()">Try</button>
            <a href="" class="btn btn-info"><?php echo $this->lang->line('btn_add');?></a>
        </div>
        <div class="col-lg-2"></div>
    </div>
</div>
