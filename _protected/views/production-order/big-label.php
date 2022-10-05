<?php
	use yii\helpers\Url;

?>
<div class="qrcode_div mtb-1">
	<table class="tbl_qrcode" align="center">
		<tr>
			<td class="w45 right_border_none"><img class="h28" src="<?=Url::home();?>img/logo.jpg"></td>
			<td colspan="3" class="left_border_none txt_right f_bold"><?=Yii::$app->params['comp_name'];?> </td>
		</tr>

		<tr>
			<td rowspan="<?=$model->part->state == 1 ? 3 : 2?>" class="txt_center f10 txt_rotate90">ПАНЕЛ</td>
			<td colspan="3" class="f30 txt_center f_bold"><?=$model->part->part_no;?></td>
		</tr>
		<?php if($model->part->state == 1): ?>
			<tr>
				<td colspan="3" class="f16 txt_center f_bold"><?=$model->part->part_color;?></td>
			</tr>
		<?php endif; ?>
		<tr>
			<td colspan="3" class="f12 txt_center f_bold"><?=$model->part->part_name?></td>
		</tr>
		<tr>
			<td class="f10 txt_right">МОДЕЛ:&nbsp;</td>
			<td class="f16 txt_center f_bold"></td>
			<td rowspan="5" colspan="2" class="qrcode4_4 txt_center">
				<img class="qrcode4_4" src="<?='data:image/png;base64, '.$model->generateQrcode()?>">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="f10 txt_center"><?=$model->part->stateText?></td>
		</tr>
		<tr>
			<td colspan="2" class="f10 txt_center">СОНИ</td>
		</tr>
		<tr>
			<td colspan="2" class="f45 txt_center f_bold"><?=app\components\Helpers::numberFormatRemoveZero($model->quantity,6)?></td>
		</tr>
		<tr class="qc40">
			<td class="f16 txt_center f_bold"><?=$model->part->side;?></td>
			<td class="f16 txt_center f_bold"><?=$model->current_seq;?></td>
		</tr>
		<tr>
			<td colspan="2" class="f10 txt_right">Сериал №:&nbsp;</td>
			<td colspan="2" class="f10 txt_center f_bold"><?=$model->serial_number?></td>
		</tr>
		<tr>
			<td class="f10 txt_right">Маъсул:&nbsp;</td>
			<td colspan="2" class="f12 txt_center"><?=$model->createdBy->fullname?></td>
			<td class="f10 txt_center w80"><?=date('d.m.Y (H:i:s)', $model->created_at)?></td>
		</tr>
	</table>
</div>
<div class='break_page'></div>
