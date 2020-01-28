<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <title>
        <?php
        echo $this->lang->line('app_title');
        if (isset($title) && $title!='') {
          echo " - ".$title;
        }
        ?>
    </title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?=base_url()?>assets/images/logo/favicon.ico" />
    
    <!-- Bootstrap  -->
    <!-- Orif Bootstrap CSS personalized with https://bootstrap.build/app -->
    <link rel="stylesheet" href="<?= base_url("assets/css/bootstrap.min.css"); ?>" />
    <!-- jquery, popper and Bootstrap javascript -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <!-- Application styles -->
    <link rel="stylesheet" href="<?=base_url()?>assets/css/sidebar.css" />
    <link rel="stylesheet" href="<?php echo base_url("assets/css/MY_styles.css"); ?>" />
  </head>
  <body>
    <script src="<?=base_url()?>assets/js/javascript.js"></script>
    <?php
        if (ENVIRONMENT != 'production') {
            echo '<div class="alert alert-warning text-center">CodeIgniter environment variable is set to '.strtoupper(ENVIRONMENT).'. You can change it in .htaccess file.</div>';
        }
    ?>
  