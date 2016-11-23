<?php
/**
 * Created by PhpStorm.
 * User: UlSi
 * Date: 25.10.2016
 * Time: 15:23
 *
 *
 **/
?>
    <div class="container well" style="background-color: lavender">
        <h2 style="height:70px;" class="text-center"><?php echo $this->lang->line('title_question'); ?></h2>
        <div class="row">
            <div class="col-lg-2"></div>
            <div class="col-lg-4" style="height:110px;">
                <h4><?php echo $this->lang->line('topic'); ?></h4>
                <select onchange="changeselect()" id="topics" class="form-control">
                    <?php
                    foreach ($topics as $object => $module) {
                        if ($module->FK_Parent_Topic == 0) {
                            echo "<optgroup label='$module->Topic' >";

                            for ($i = 0; $i < count($topics); $i++) {
                                if ($module->ID == $topics[$i]->FK_Parent_Topic) {
                                    echo "<option>" . $topics[$i]->Topic . "</option>";
                                }
                            }
                            echo "</optgroup>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-lg-6"></div>
        </div>
        <div class="row">
            <div class="col-lg-2"></div>
            <div class="col-lg-8">
                <?php
                if(isset($_GET['param'])){
                    echo "<h3>" . $_GET['param'] . "</h3>";
                }
                ?>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th></th>
                        <th><?php echo $this->lang->line('question'); ?></th>
                        <th><?php echo $this->lang->line('question_type'); ?></th>
                        <th><?php echo $this->lang->line('points'); ?></th>
                    </tr>
                    </thead>
                    <?php
                    $compteur = 0;

                    foreach ($questions as $objet => $question) {
                    if (isset($_GET['param'])) {
                    if ($question->topic->Topic == $_GET['param']) {
                    $compteur += 1;
                    ?>
                    <tbody>
                    <tr>
                        <td>
                            <button type="button" class="btn btn-danger"
                                    onclick="deleteQuestionnaire(<?php echo $question->ID; ?>)">
                                <?php echo $this->lang->line('btn_del') ?>
                            </button>
                            <a class="btn btn-warning" href="./update/<?php echo $question->ID; ?>">
                                <?php echo $this->lang->line('btn_update') ?>
                            </a>
                        </td>
                        <?php
                        displayQuestion($question);
                        }
                        }
                        else{
                        $compteur += 1;
                        ?>
                    <tbody>
                    <tr>
                        <td>
                            <button type="button" class="btn btn-danger"
                                    onclick="deleteQuestionnaire(<?php echo $question->ID; ?>)">
                                <?php echo $this->lang->line('btn_del') ?>
                            </button>
                            <a class="btn btn-warning" href="./update/<?php echo $question->ID; ?>">
                                <?php echo $this->lang->line('btn_update') ?>
                            </a>
                        </td>
                        <?php
                        displayQuestion($question);
                        }
                        }

                        if($compteur == 0){
                            echo "<div class='well' style='background-color: mistyrose;'><h4>"
                                . $this->lang->line('no_question') . "</h4></div>";
                        }

                        ?>
                </table>
                <a href="" class="btn btn-info"><?php echo $this->lang->line('btn_add'); ?></a>
            </div>
            <div class="col-lg-2"></div>
        </div>
    </div>
    <script>
        function deleteQuestionnaire(id) {
            if (confirm("Voulez-vous supprimer cette question?")) {
                document.location.href = "../Question/delete/" + id;
            } else {

            }
        }

        function init() {
            document.getElementById("topics").selectedIndex = -1;
        }

        function changeselect() {
            var topic = document.getElementById("topics").value;

            window.location = '?param=' + topic;
        }

        window.onload = init();
    </script>
<?php
function displayQuestion($question)
{
    ?>

    <td><?php echo $question->Question; ?></td>
    <td><?php echo $question->question_type->Type_Name ?></td>
    <td><?php echo $question->Points; ?></td>
    </tr>
    </tbody>
    <?php
}
?>