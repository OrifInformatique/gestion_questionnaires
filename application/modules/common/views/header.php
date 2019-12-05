<!DOCTYPE html>
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
    
    <!-- Bootstrap -->
    <link rel="stylesheet" href="<?php echo base_url("assets/css/bootstrap.min.css"); ?>" />
    <script type="text/javascript" src="<?php echo base_url("assets/js/jquery.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/bootstrap.min.js"); ?>"></script>

    <!-- Application styles -->
    <link rel="stylesheet" href="<?=base_url()?>assets/css/sidebar.css" />
    <link rel="stylesheet" href="<?php echo base_url("assets/css/MY_styles.css"); ?>" />
  </head>
  <body>
  <script src="<?=base_url()?>assets/js/javascript.js"></script>