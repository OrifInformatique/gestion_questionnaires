<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="container">
    <h1 class="title-section"><?php echo $this->lang->line('title_report_a_problem'); ?></h1>
    <?php
    $attributes = array("id" => "reportProbleme",
                        "name" => "reportProbleme");
    echo form_open('support/form_report_problem', $attributes);
    ?>
        <!-- ERROR MESSAGES -->
        <?php
        if (!empty(validation_errors())) {
            echo '<div class="alert alert-danger">'.validation_errors().'</div>';}
        ?>

        <div class="row">
            <div class="form-group col-md-12">
                <?php echo form_label($this->lang->line('field_issue_title'), 'issue_title'); ?>
                <?php echo form_input('issue_title', set_value('issue_title'), 'maxlength="200" class="form-control" id="issue_title"'); ?>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-12">
                <?php echo form_label($this->lang->line('field_issue_body'), 'issue_body'); ?>
                <?php echo form_textarea('issue_body', set_value('issue_body'), 'maxlength="65535" class="form-control" rows="8" id="issue_body"'); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-12 text-right">
                <a name="cancel" class="btn btn-default" href="<?=base_url()?>"><?=$this->lang->line('cancel')?></a>
                <?php
                    echo form_submit('post', $this->lang->line('post'), 'class="btn btn-primary"');
                ?>
            </div>
        </div>
    <?php echo form_close(); ?>
</div>
