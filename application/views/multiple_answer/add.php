        <!-- ANSWERS FIELDS -->
        <div class="row">
            <div class="col-sm-6 form-group">
                <?php echo form_label($this->lang->line('nb_desired_answers'), 'nb_desired_answers', array('class' => 'form-label')); ?>
            </div>
            <div class="col-sm-1 form-group">
                <?php 
                    if(isset($name)){
                        echo form_input('nb_desired_answers', $nb_desired_answers, 'maxlength="11" class="form-control" id="nb_desired_answers"');
                    } else {
                        echo form_input('nb_desired_answers', '', 'maxlength="11" class="form-control" id="nb_desired_answers"');
                    }
                ?>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th colspan="2"><?php echo form_label($this->lang->line('valid_answers_list'), 'answer') ?></th>
                    </tr>
                </thead>
                <tbody>
        <?php
        for ($i = 0; $i < $nbAnswer; $i++){ ?>
            <tr>
                <td class="form-group">
                    <?php
                        echo form_hidden('reponses['.$i.'][id]', $answers[$i]['id']);
                        echo form_input('reponses['.$i.'][answer]', $answers[$i]['answer'], 'maxlength="250" class="form-control" id="answer"');
                    ?>
                </td>
                <td class="form-group">
                    <?php echo form_submit('del_answer'.$i, '-', 'class="btn btn-secondary no-border"'); ?>
                </td>
            </tr>
        <?php } ?>
                    <tr>
                        <td>
                        <td><?php echo form_submit('add_answer', '+', 'class="btn btn-secondary no-border"'); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Display buttons and display topic and question type as information -->
        <div class="row">
            <div class="col-12 text-right">
                <a id="btn_cancel" class="btn btn-default" href="<?=base_url('/Question')?>"><?=$this->lang->line('btn_cancel')?></a>
                <?php
                    echo form_submit('save', $this->lang->line('save'), 'class="btn btn-primary"'); 
                ?>
            </div>
        </div>
    <?php echo form_close(); ?>
</div>