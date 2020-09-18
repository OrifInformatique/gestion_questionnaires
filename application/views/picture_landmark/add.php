        <!-- PICTURE -->
        <div class="row">
            <div class="col-sm-12 form-group">
                <?php
                if(isset($upload_data)){
                    echo "<img src='".base_url('uploads/pictures/') . $upload_data['file_name'] . "' alt='" . $upload_data['file_name'] . "'>";
                } else {
                    echo "<img src='".base_url('uploads/pictures/') . $picture_name . "' alt='" . $picture_name . "'>";
                } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 form-group">
                <?php echo form_submit('change_picture', $this->lang->line('change_picture'), 'class="btn btn-secondary"'); ?>
            </div>
        </div>
  
        <!-- ANSWERS FIELDS -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th colspan="3"><?php echo form_label($this->lang->line('answers_list'), 'answer'); ?></th>
                    </tr>
                </thead>
                <tbody>
            
                <?php for ($i = 0; $i < $nbAnswer; $i++){ ?>
                    <tr>
                        <td class="form-group" width="80">
                            <?php
                                echo form_input('reponses['.$i.'][symbol]', $answers[$i]['symbol'], 'maxlength="2" class="form-control" id="answer"');
                            ?>
                        </td>
                        <td class="form-group">
                            <?php
                                echo form_hidden('reponses['.$i.'][id]', $answers[$i]['id']);
                                echo form_input('reponses['.$i.'][answer]', $answers[$i]['answer'], 'maxlength="50" class="form-control" id="answer"');
                            ?>
                        </td>
                        <td class="form-group">
                            <?php echo form_submit('del_answer'.$i, '-', 'class="btn btn-secondary no-border"');
                            ?>
                        </td>
                    </tr>
                <?php } ?>
                    <tr>
                        <td colspan="2">
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