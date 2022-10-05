<?php
	use yii\helpers\Url;

?>
<div class="qrcode_smalldiv mtb-1">
	<table class="tbl_qrcode_small" align="center">
		<tr>
			<td class="w45 right_border_none"><img class="h28" style="margin-left:5px" src="<?=Url::home();?>img/logo.jpg">
			</td>
			<td colspan="2" class="left_border_none txt_right f_bold" style="margin-right:5px">Uz Dong Yang Co.</td>
		</tr>
		<tr>
			<td colspan="2" class="txt_center">
				<?php if(strlen($model->part->part_no) >= 8): ?>
					<span class="f30"><?=substr($model->part->part_no, 0, 4);?></span>
					<span class="f45 f_bold"><?=substr($model->part->part_no, -4, 4);?></span>
				<?php else: ?>
					<span class="f45 bold"><?=$model->part->part_no?></span>
				<?php endif ?>
			</td>
			<td rowspan="2" class="qrcode_small txt_center">
				<img class="qrcode_small" src="<?='data:image/png;base64, '.$model->generateQrcode()?>">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="txt_center">
				<?=substr($model->part->part_name, 0, 18);?>
			</td>
		</tr>
		<tr>
			<td class="f35 txt_left f_bold">
				<?=$model->part->side;?>
			</td>
			<td colspan="2" class="txt_center">
				<?=$model->serial_number;?>
			</td>
		</tr>
		<tr>
			<td colspan="3" class="f10 txt_center">
				MADE IN UZBEKISTAN
			</td>
		</tr>
	</table>
</div>
<div class='break_page'></div>
