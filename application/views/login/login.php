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
        <h1><?php echo $this->lang->line('page_login'); ?></h1>
        <p><?php echo $this->lang->line('indic_login'); ?></p>
    </div
        <div class="well">
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
</div>