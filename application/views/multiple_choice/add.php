        <!-- ANSWERS FIELDS -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th><?php echo form_label($this->lang->line('answers_list'), 'answer', array('class' => 'form-label')); ?></th>
                        <th colspan="2"><?php echo form_label($this->lang->line('valid_answer'), 'valid_answer', array('class' => 'form-label')); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    for ($i = 0; $i < $nbAnswer; $i++){ ?>
                        <tr>
                            <td class="form-group">
                                <?php
                                    echo form_hidden('reponses['.$i.'][id]', $answers[$i]['id']);
                                    echo form_input('reponses['.$i.'][question]', $answers[$i]['question'], 'maxlength="250" class="form-control" id="question"');
                                ?>
                            </td>
                            <td class="form-group">
                                <!-- YES radio button -->
                                <?php 
                                    if ($answers[$i]['answer']==1){
                                        echo form_radio('reponses['.$i.'][answer]', 1, TRUE);
                                    }else{
                                        echo form_radio('reponses['.$i.'][answer]', 1);
                                    }
                                    echo form_label($this->lang->line('yes'), $answers[$i]['id'], null, true);
                                ?>
                            </td>
                            <td class="form-group">
                                <!-- NO radio button -->
                                <?php 
                                    if ($answers[$i]['answer']==0){
                                        echo form_radio('reponses['.$i.'][answer]', 0, TRUE);
                                    }else{
                                        echo form_radio('reponses['.$i.'][answer]', 0);

                                    }
                                    echo form_label($this->lang->line('no'), $answers[$i]['id'], null, true);
                                ?>
                            </td>
                            <td class="form-group">
                                <?php echo form_submit('del_answer'.$i, '-', 'class="btn btn-secondary no-border"');
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="3">
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
