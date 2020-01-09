<div class="container" >
  <div class="row xs-center">
    <a href="<?= base_url(); ?>" style="color:inherit">
      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 ">
        <img src="<?= base_url("assets/images/logo/logo_64.png"); ?>" >
      </div>
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <h1><?= $this->lang->line('app_title'); ?></h1>
      </div>
    </a>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12" >
      <div class="nav nav-pills text-right" style="margin-top:20px;">
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) { ?>

          <!-- Password change -->
          <a href="<?= base_url("auth/change_password"); ?>"><?= $this->lang->line('btn_change_password') ?></a><br />
          
          <!-- Logged in, display a "logout" button -->
          <a href="<?= base_url("auth/logout"); ?>" ><?= $this->lang->line('btn_logout'); ?></a>

        <?php } else { ?>
          <!-- Not logged in, display a "login" link -->
          <form action="<?= base_url("auth/login"); ?>" method="post" >
              <input type="hidden" id="after_login_redirect" name="after_login_redirect" value="<?= current_url() ?>">
              <input type="submit" class="btn btn-link"
                     value="<?= $this->lang->line('btn_login'); ?>" >
          </form>
        <?php } ?>
      </div>
    </div>
  </div>
</div>