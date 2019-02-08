<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Login View (Form to login).
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
?>

<div class="container">
    <div class="jumbotron">
        <h1><?php echo $this->lang->line('app_title'); ?></h1>
        <p><?php echo $this->lang->line('indic_login'); ?></p>
    </div>
    <div class="well">
        <?php
        $attributes = array("class" => "form-group",
            "id" => "loginform",
            "name" => "loginform");
        echo form_open('Auth/login', $attributes);
        ?>
        <div class="row">
            <div class="col-lg-4">
                <?php
                    switch ($error){
                        case 1:
                            echo "<p class='alert alert-warning'>" . $this->lang->line('invalid_id') . "</p>";
                            break;
                        case 2:
                            echo "<p class='alert alert-danger'>" . $this->lang->line('no_id') . "</p>";
                            break;
                        default:
                            break;
                    }
                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="username"><?php echo $this->lang->line('field_username'); ?></label>
            <div class="row">
                <div class="col-lg-4"><input type="text" name="username" class="form-control" id="username"
                                             aria-describedby="Entrez votre nom d'utilisateur"></div>
            </div>
        </div>
        <div class="form-group">
            <label for="password"><?php echo $this->lang->line('field_password'); ?></label>
            <div class="row">
                <div class="col-lg-4"><input type="password" class="form-control" name="password" id="password"></div>
            </div>
        </div>

        <input type="submit" class="btn btn-primary" />
        
        <?php echo form_close(); ?>
    </div>
</div>