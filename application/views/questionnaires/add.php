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
<div class="container">
    <h1 class="title-section"><?php echo $this->lang->line('add_questionnaire_title'); ?></h1>
    <div class="row">
        <div class="col-lg-2"></div>
        <div class="col-lg-8">
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
            <div class="form-group">
                <label for="title"><?php echo $this->lang->line('add_title_questionnaire'); ?></label>
                <div class="row">
                    <div class="col-lg-4"><input type="text" name="title" class="form-control" id="title"
                        value="<?php echo $title;?>"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="topic_selected"><?php echo $this->lang->line('add_topic_questionnaire'); ?></label>
                <div class="row">
                    <div class="col-lg-4">
                        <select class="form-control" id="topic_selected" 
                                name="topic_selected">
                            <?php

                            //Récupère chaque topics
                            foreach ($topicsList as $object => $topic) {

                                if ($topic->FK_Parent_Topic == 0) {
                                    //Affiche le topic parent
                                    echo "<optgroup label='$topic->Topic' >";

                                    //Récupère chaque topic associé au topic parent
                                    for ($i = 0; $i < count($topicsList); $i++) {
                                        if ($topic->ID == $topicsList[$i]->FK_Parent_Topic) {
                                            //Affiche les topics associés
                                            echo "<option value='" . $topicsList[$i]->ID . "'>" . $topicsList[$i]->Topic . "</option>";
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
            <div class="form-group">
                <label for="nb_questions"><?php echo $this->lang->line('nb_questions'); ?></label>
                <div class="row">
                    <div class="col-lg-2">
                        <select class="form-control" id="nb_questions" name="nb_questions">
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <input type="submit" class="btn btn-primary" value="<?php echo $this->lang->line('add_form')?>"/>
                    </div>
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
                            foreach($topics as $topic)
                            {
                                ?>
                                <tr>
                                    <td><?php echo $compteur?></td>
                                    <td>
                                            <?php echo $topic->Topic ;?>
                                    </td>
                                    <td>
                                        <?php echo $nbQuestions[$compteur-1];?>
                                    </td>
                                </tr>
                            <?php
                                $compteur += 1;
                            }
                            ?>
                            </tbody>
                        </table>
                        <input type="submit" class="btn btn-primary" name="<?php echo $this->lang->line('generatePDF_btn');?>" value="<?php echo $this->lang->line('generatePDF_btn');?>">
                        <?php echo form_close(); ?>
                    </div>
                </div>
        </div>
        <div class="col-lg-2"></div>
        </div>
    </div>
    <script>init();</script>

