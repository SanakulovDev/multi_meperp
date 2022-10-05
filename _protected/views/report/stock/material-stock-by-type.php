<?php

use app\components\Helpers;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Material stock at {date}',['date' => date('d.m.Y')]);
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
.tbl-plan{
	width: 100%;
	border-collapse: collapse;
}
.tbl-plan tr td{
	border: 1px solid #cccccc;
} 
.tbl-plan tr td{
	padding: 2px 3px;
}

.tr_head td{
	font-weight: bold;
	text-align: center;
	background-color: #b6e8ff;
	height: 30px;
}

.tr_head .td_sum {
	background-color: #efe2bd;
	text-align: right;
}

.tr_footer td{
	font-weight: bold;
	height: 30px;
	background-color: #b6e8ff;
}

.td_num{
	text-align: right;
	width: 200px;
}

.tbl-second{
	margin-top: 30px;
}

.td_first{
	text-align: center;
	width: 30px;
}

.td_delimeter{
	width: 15px;
	background-color: #ffffff !important;
	border-top: 0px !important;
	border-bottom: 0px !important;
}

.td_zero{
	color: #bdbdbd !important;
}
.td_negative{
	color: red !important;
	background-color: #ffcfcf !important;
}
.tr_odd{
	background-color: #eceeef;
}

.mini_header {
    text-align: right;
    font-size: 12px;
    color: #4e4848;
    font-style: italic;
}

</style>
<?
    // echo "<pre>";
    // print_r($data);
    // echo "</pre>";
    // die;
?>
<div class="row">
	<div class="col-md-2 col-lg-2">
		<?=Html::a(Yii::t('app', 'btn-back'), ['material-stock','state' => $_GET['state'] ?? 0], ['class' => 'btn btn-default btn-xs'])?>
	</div>
	<div class="col-md-10 col-lg-10">
		<div class="pull-right">
			<?=Html::button(Yii::t('app', 'btn-download-delivery-plan'), ['class' => 'btn btn-info btn-xs', 'id' => 'download-xls'])?>
		</div>
	</div>
</div>
	<br>
<div class="content-index">
	<div class="row">
		<div class="col-lg-8 col-lg-offset-2">
			<div class="mini_header">млн Сум</div>
			<table class="tbl-plan tbl-first" id="fix_table">
				<tr class="tr_head">
					<td rowspan="2">№</td>
					<td rowspan="2">№ материал</td>
					<td rowspan="2">Полимерные компаунды<br><b>Тип <?=$data['data'][0]['type_name']?></b></td>
					<td rowspan="2">Цвет</td>
					<td>Cклад сырья (Кол-во)</td>
					<td>Cклад сырья (Сумма)</td>
				</tr>
				<tr class="tr_head">
					<td class="td_sum" id="sum-qty">0</td>
					<td class="td_sum" id="sum-amt">0</td>
				</tr>
				<?php $i = 0; $sumQty = 0; $sumAmt = 0;?>
				<?php foreach ($data['data'] as $row) {?>
					<?php
						$i++;
						$classOdd = (($i % 2) == 0) ? 'tr_odd' : '';
						$sumQty += $row['qty'];
						$sumAmt += $row['amt'];
					?>

					<tr class="tr_item <?=$classOdd?>">
						<td class="td_first"><?=$i?></td>
						<td><?=$row['no']?></td>
						<td><?=$row['name']?></td>
						<td><?=$row['color']?></td>
						<td class="td_num" data-value="<?=$row['qty']?>"><?=Helpers::numberFormatRemoveZero($row['qty'])?></td>
						<td class="td_num" data-value="<?=$row['amt']?>"><?=Helpers::numberFormatRemoveZero($row['amt'])?></td>
					</tr>

				<?php } ?>
			</table>
		</div>
	</div>
</div>

<?php
$sumQty = Helpers::numberFormatRemoveZero($sumQty);
$sumAmt = Helpers::numberFormatRemoveZero($sumAmt);
$docReadyJs = <<< JS
$(document).ready(function() {

	$('#download-xls').on('click', function (e) {
		html_xls_export('fix_table', '$downloadFileName');
  	});	

	$('#sum-qty').html('$sumQty');
	$('#sum-amt').html('$sumAmt');

	$('.td_num').each(function() {
		var cellVal = $(this).attr('data-value');    
		if(cellVal == "0"){
			$(this).addClass('td_zero');
		}
		if(cellVal < 0){
			$(this).addClass('td_negative');
		}
	});


}) 
JS;
$this->registerJs($docReadyJs);
?>