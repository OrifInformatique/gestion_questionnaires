<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * View of question's details
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */

?>
<div class="container">
	<div class="row">
		<div class="col-xs-12">
			<table class="table table-hover" id="table">
				<tbody>
					<tr>
						<td><h4 for="title">Type de question:&nbsp</h4><?php echo $question->question_type->Type_Name ?></td>
						<td><h4 for="title">Date de création:&nbsp</h4><?php echo $question->Creation_Date ?></td>
						<td><h4 for="title">ID:&nbsp</h4><?php echo $question->ID ?></td>
					</tr>
					<tr>
						<td colspan=2><h4 for="title">Sujet:&nbsp</h4><?php echo $question->topic->Topic ?></td>
						<td><h4 for="title">Nb points:&nbsp</h4><?php echo $question->Points ?></td>
					</tr>
					<tr><td colspan=3><h4 for="title">Question:&nbsp</h4><?php echo $question->Question; ?></td></tr>
					<tr><td colspan=3><h4 for="title">Réponse:&nbsp</h4><?php echo $reponse; ?></td></tr>
				</tbody>
			</table>
			<?php if ($question->FK_Question_Type == 7) {
			echo "<img src=".base_url('uploads/pictures/'.$image).">";
			}?>
			 <?php echo form_button('retour', $this->lang->line('return'), 'class="btn btn-default col-xs-12 col-sm-4 col-md-2" onclick="location.href=\'/gestion_questionnaires/Question\'"'); ?>
          
                 
		</div>
	</div>
</div>