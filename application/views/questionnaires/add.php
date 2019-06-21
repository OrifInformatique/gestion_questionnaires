<?php
/**
 * Add Questionnaire
 *
 * @author        Orif Pomy, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright    Copyright (c) Orif section informatique, Switzerland (http://www.sectioninformatique.ch)
 */
$nbMaxQuestion = 0;
?>
<script>
    $(document).ready(function(){
        $("#topic_selected").change(function(){

            var topic = $( "#topic_selected" ).val();

            topic = topic.replace("'", "_apostrophe_");

            $.post("./add/", {topic: topic}, function (nbQuestion) {

                $("#nb_questions")
                    .find('option')
                    .remove()
                    .end()

                var i;
                for (i = 0; i < nbQuestion; i++){
                    $("#nb_questions")
                        .append('<option>' + (i+1) + '</option>')

                }
            }).fail(function(xhr, status, error) {
                    alert(error);
                });
        });
    });
</script>

<div id="page-content-wrapper">
    <div class="container">
        <h1 class="title-section"><?php echo $this->lang->line('add_questionnaire_title'); ?></h1>
        <div class="row">
            <?php
            $attributes = array("class" => "form-group",
                "id" => "addQuestionnaireForm",
                "name" => "addQuestionnaireForm",
                "style" => "padding-bottom: 5%;");
            echo form_open('Questionnaire/form_add', $attributes);
            ?>
            <?php
            if($error == true)
            {
                echo "<p class='alert alert-warning'>" . $this->lang->line('update_questionnaire_form_err') . "</p>";
            }
            ?>
            <div class="form-group col-xs-12">
                <h4 for="title"><?php echo $this->lang->line('add_title_questionnaire'); ?></h4>
                <div class="row">
                    <div class="col-xs-12">
                        <input maxlength="100" type="text" name="title" class="form-control" id="title"
                        value="<?php echo $title;?>">
                    </div>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-4">
                <h4 for="topic_selected"><?php echo $this->lang->line('add_topic_questionnaire'); ?></h4>
                <div class="row">
                    <div class="col-xs-12">
                        <select class="form-control" id="topic_selected" name="topic_selected">
                            <option selected disabled hidden></option>
                            <?php

                            //Récupère chaque topics
                            foreach ($topicsList as $object => $topic) {

                                if ($topic->FK_Parent_Topic == 0) {
                                    //Affiche le topic parent
                                    echo "<optgroup label='$topic->Topic' >";

                                    //Récupère chaque topic associé au topic parent
                                    for ($i = 0; $i < count($topicsList); $i++) {
                                        if ($topic->ID == $topicsList[$i]->FK_Parent_Topic) {

                                            $disabled = false;
                                            foreach ($topics as $topic_selected) {
                                                if($topic_selected->ID == $topicsList[$i]->ID) {
                                                    $disabled = true;
                                                }
                                            }

                                            //Affiche les topics associés
                                            echo "<option value='" . $topicsList[$i]->ID . "'".($disabled?' disabled':'').">" . $topicsList[$i]->Topic . "</option>";
                                        }
                                    }
                                    echo "</optgroup>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group col-sm-4 colxs-12">
                <h4 for="nb_questions"><?php echo $this->lang->line('nb_questions'); ?></h4>
                <select class="form-control" id="nb_questions" name="nb_questions"></select>
            </div>
            <div class="col-sm-4 col-xs-12">
                <input type="submit" class="btn btn-success col-xs-12 xs-space"  value="<?php echo $this->lang->line('add_form')?>" name="<?php echo $this->lang->line('add_form')?>"/>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <table class="table">
                    <thead class="thead-inverse">
                    <tr>
                        <div class="row">
                            <th class="col-lg-1">
                                <span>#</span>
                            </th>
                            <th class="col-lg-9">
                                <span>Nom du sujet</span>
                            </th>
                            <th class="col-lg-2">
                                <span>Nb questions</span>
                            </th>
                        </div>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $compteur = 1;
                    //Display each row of topic and number answer asked
                    foreach($topics as $key => $topic)
                    {
                        ?>
                        <tr>
                            <td><?php echo $compteur?></td>
                            <td>
                                    <?php echo $topic->Topic ;?>
                            </td>
                            <td>
                                <?php echo $nbQuestions[$key];?>
                            </td>
                            <td>
                                <input type="submit" value="×" class="close" id="btn_del" name="delete_topic[<?=$key?>]">
                            </td>
                        </tr>
                    <?php
                        $compteur += 1;
                    }
                    ?>
                    </tbody>
                </table>
               

                
            </div>
            <div class="col-sm-4 col-xs-12" > 
            <input type="submit" class="btn btn-danger col-xs-12" name="<?php echo $this->lang->line('cancel');?>" value="<?php echo $this->lang->line('cancel');?>">

            </div>
            <div class="col-sm-offset-4 col-sm-4 col-xs-12">
                 <input type="submit" class="btn btn-success col-xs-12" name="<?php echo $this->lang->line('save');?>" value="<?php echo $this->lang->line('save');?>">
            </div>

            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<script>init();</script>
