        <div class="row">
			<div class="col-sm-12 form-group">
				<?php echo form_label($this->lang->line('cloze_text'), 'cloze_text', array('class' => 'form-label')); ?>
				<p><?php echo $this->lang->line('cloze_text_tip'); ?></p>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12 form-group">
				<?php 
		        	if(isset($cloze_text)){
						echo form_textarea('cloze_text', $cloze_text, 'maxlength="65535" class="form-control" id="cloze_text"');
					} else {
						echo form_textarea('cloze_text', '', 'maxlength="65535" class="form-control" id="cloze_text"');
					}
				?>
			</div>
		</div>
		
		<!-- ANSWERS FIELDS -->
		
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th colspan="2"><?php echo form_label($this->lang->line('answers_list'), 'answer') ?></th>
					</tr>
				</thead>
				<tbody>
		<?php
		for ($i = 0; $i < $nbAnswer; $i++){ ?>
			<tr data-row-id="<?=$i?>">
				<td class="form-group col-10">
					<?php
						echo form_hidden('reponses['.$i.'][id]', $answers[$i]['id']);
						echo form_input('reponses['.$i.'][answer]', $answers[$i]['answer'], 'maxlength="65535" class="form-control" id="answer['.$i.']"');
					?>
				</td>
				<td class="form-group col-2">
					<button type="button" class="btn btn-default no-border" onclick="invertInputs(this, <?=$nbAnswer?>, -1);" data-button-id="<?=$i?>">▲</button>
					<button type="button" class="btn btn-default no-border" onclick="invertInputs(this, <?=$nbAnswer?>, +1);" data-button-id="<?=$i?>">▼</button>
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