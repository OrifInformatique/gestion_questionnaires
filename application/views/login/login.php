<?php
/**
 * Created by PhpStorm.
 * User: UlSi
 * Date: 18.08.2016
 * Time: 13:19
 */
?>

<div class="container" style="background-color: lavender">
    <div class="jumbotron">
        <h1>Secretariat médical</h1>
        <p>Essayer de vous enregistrer pour acceder à l'administration</p>
    </div
    <?php
    $attributes = array("classe" => "form-group",
        "id" => "loginform",
        "name" => "loginform");
        echo form_open('auth/login', $attributes);
    ?>
    <form>
        <div class="well">
        <div class="form-group">
            <label for="username">Nom d'utilisateur</label>
            <div class="row">
                <div class="col-lg-4"><input type="text" class="form-control" id="username"
                                             aria-describedby="Entrez votre nom d'utilisateur"></div>
            </div>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <div class="row">
                <div class="col-lg-4"><input type="password" class="form-control" id="password"></div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>

    <?php echo form_close(); ?>
</div>