<?php
/**
 * Created by PhpStorm.
 * User: UlSi
 * Date: 04.10.2016
 * Time: 09:21
 */
?>
<div class="container" style="background-color: lavender">
    <div class="jumbotron">
        <h2>Modifier la question</h2>
    </div
    <?php
    $attributes = array("class" => "form-group",
        "id" => "loginform",
        "name" => "loginform");
    echo form_open('question/update', $attributes);
    ?>
    <form>
        <div class="well">
            <div class="form-group">
                <label for="topic">Topic</label>
                <div class="row">
                    <div class="col-lg-2"><input type="number" class="form-control" name="topic"
                                                 id="topic" value="<?php echo $question->FK_Topic?>"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="typequestion">Type de question</label>
                <div class="row">
                    <div class="col-lg-2"><input type="number" class="form-control" name="typequestion"
                                                 id="typequestion" value="<?php echo $question->FK_Question_Type?>"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="question">Question</label>
                <div class="row">
                    <div class="col-lg-4"><input type="text" class="form-control" id="question"
                                                 name="question" value="<?php echo $question->Question?>"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="nbrep">Nb de rep désirées</label>
                <div class="row">
                    <div class="col-lg-2"><input type="number" class="form-control" id="nbrep"
                                                 name="nbrep"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="table">Tableau de définition ?</label>
                <div class="row">
                    <div class="col-lg-2"><input type="checkbox" class="form-control" id="table"
                                                 name="table"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="img">Nom d'image</label>
                <div class="row">
                    <div class="col-lg-2"><input type="text" class="form-control" id="img"
                                                 name="img"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="pts">Points</label>
                <div class="row">
                    <div class="col-lg-2"><input type="number" class="form-control" id="pts"
                                                 name="pts"></div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
    <?php echo form_close(); ?>
</div>
