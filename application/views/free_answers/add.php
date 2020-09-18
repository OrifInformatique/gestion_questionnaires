       <!-- ANSWERS FIELDS -->
        
        <div class="row">
            <div class="col-sm-12 form-group">
                <?php echo form_label($this->lang->line('answer_question_add'), 'answer', array('class' => 'form-label')); ?>
                <?php 
                    if(isset($name)){
                        echo form_input('answer', $answer, 'maxlength="65535" class="form-control" id="answer"');
                    } else {
                        echo form_input('answer', '', 'maxlength="65535" class="form-control" id="answer"');
                    }
                ?>
            </div>
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