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
        <div class="row">
            <div class="col-md-4">
                <?php
                $attributes = array("class" => "form-group",
                                    "id" => "loginform",
                                    "name" => "loginform");
                echo form_open('Auth/login', $attributes);

                switch ($error){
                    case 1:
                        echo "<p class='alert alert-danger'>" . $this->lang->line('invalid_id') . "</p>";
                        break;
                    case 2:
                        echo "<div class='alert alert-danger'>" . validation_errors() . "</div>";
                        break;
                    default:
                        break;
                }
                ?>

                <div class="form-group">
                    <?php
                    echo form_label($this->lang->line('field_username'), 'username');
                    echo form_input('username', '', 'id="username" class="form-control"');
                    ?>
                </div>
                <div class="form-group">
                    <?php
                    echo form_label($this->lang->line('field_password'), 'password');
                    echo form_password('password', '', 'id="password" class="form-control"');
                    ?>
                </div>
                
                <?php
                echo form_submit('submit', $this->lang->line('login'), 'class="btn btn-primary"');
                echo form_close();
                ?>
            </div>
        </div>
    </div>
</div>