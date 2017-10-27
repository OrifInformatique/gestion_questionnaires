<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * View of question's details
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
?>
<div class="col-lg-2"></div>
<div class="col-lg-8">
<table class="table table-hover" id="table">
<tbody>
	<tr>
		<td><label for="title">Type de question:&nbsp</label><?php echo $question->question_type->Type_Name ?></td>
		<td><label for="title">Date de création:&nbsp</label><?php echo $question->Creation_Date ?></td>
		<td><label for="title">ID:&nbsp</label><?php echo $question->ID ?></td>
	</tr>
	<tr>
		<td colspan=2><label for="title">Sujet:&nbsp</label><?php echo $question->topic->Topic ?></td>
		<td><label for="title">Nb points:&nbsp</label><?php echo $question->Points ?></td>
	</tr>
	<tr><td colspan=3><label for="title">Question:&nbsp</label><?php echo $question->Question; ?></td></tr>
	<tr><td colspan=3><label for="title">Réponse:&nbsp</label><?php echo $reponse; ?></td></tr>
</tbody>
</table>
<?php if ($question->FK_Question_Type == 7) {
echo "<img src=".base_url('uploads/pictures/'.$image)." height='400' width='400'>";
}?>
</div>
<div class="col-lg-2"></div>