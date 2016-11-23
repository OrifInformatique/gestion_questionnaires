<?php
/**
 * Created by PhpStorm.
 * User: UlSi
 * Date: 08.11.2016
 * Time: 11:18
 */
?>
<div class="container jumbotron well" style="background-color: lavender">
    <h2 class="text-center"><?php echo $this->lang->line('title_questionnaire');?></h2>
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
                        <td>
                            <button type="button" class="btn btn-danger"
                                    onclick="deleteQuestionnaire(<?php echo $questionnaire->ID; ?>)">
                                <?php echo $this->lang->line('btn_del')?></button>
                            <a class="btn btn-warning" href="./update/<?php echo $questionnaire->ID;?>">
                                <?php echo $this->lang->line('btn_update')?>
                            </a>
                        </td>
                        <td><?php echo $questionnaire->Questionnaire_Name; ?></td>
                        <td><a href="" class="btn btn-success"><?php echo $this->lang->line('redirect_pdf');?></a></td>
                        <td><a href="" class="btn btn-success"><?php echo $this->lang->line('redirect_corrige');?></a></td>
                    </tr>
                </tbody>
                <?php
                }
                ?>
            </table>
            <a href="" class="btn btn-info"><?php echo $this->lang->line('btn_add');?></a>
        </div>
        <div class="col-lg-2"></div>
    </div>
</div>
<script>
    function deleteQuestionnaire(id){
        if(confirm("Voulez-vous supprimer ce questionnaire ?")){
            document.location.href = "../Questionnaire/delete/" + id;
        }else{

        }
    }
</script>
