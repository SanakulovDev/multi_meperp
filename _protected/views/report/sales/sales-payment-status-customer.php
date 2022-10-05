<?php

use app\components\Helpers;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Payment status for customer');
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
	.tbl-plan {
		width: 70%;
		border-collapse: collapse;
	}

	.tbl-plan tr td {
		border: 1px solid #cccccc;
	}

	.tbl-plan tr td {
		padding: 2px 3px;
	}

	.tr_head td {
		font-weight: bold;
		text-align: center;
		background-color: #b6e8ff;
		height: 30px;
	}

	.tr_footer td {
		font-weight: bold;
		height: 30px;
		background-color: #b6e8ff;
	}

	.td_num {
		text-align: right !important;
		width: 150px;
	}

	.tbl-second {
		width: 60% !important;
	}

	.td_first {
		text-align: center;
		width: 30px;
	}

	.td_delimeter {
		width: 15px;
		background-color: #ffffff !important;
		border-top: 0px !important;
		border-bottom: 0px !important;
	}

	.tr_item td {
		text-align: center;
	}

	.tbl-first {
		width: 50% !important;
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
</style>
<?
// echo "<pre>";
// print_r($data);
// echo "</pre>";
// die;
?>
<div class="row">
	<div class="col-md-2 col-lg-2">
		<?= Html::a(Yii::t('app', 'btn-back'), ['sales-payment-status'], ['class' => 'btn btn-default btn-xs']) ?>
	</div>
	<div class="col-md-10 col-lg-10">
		<div class="pull-right">
			<?= Html::button(Yii::t('app', 'btn-download-delivery-plan'), ['class' => 'btn btn-info btn-xs', 'id' => 'download-xls']) ?>
		</div>
	</div>
</div>
<br>
<div class="content-index">

	<h3 style="margin-top: 10px;"><b><?=$data['customerName']?></b></h3>
	<h4 style="margin-top: 30px;"><b>Дебит</b></h4>

	<table class="tbl-plan tbl-first" id="fix_table1">
		<tr class="tr_head">
			<td>№</td>
			<td>№ счет фактуры</td>
			<td>Сумма</td>
			<td>Дата</td>
			<td>№ договора</td>
			<td>№ товарно транспортных накладных</td>
		</tr>

		<?php $i = 0;$sumInvAmt = 0; ?>
		<?php foreach ($data['debit'] as $row) { ?>
			<?php
				$i++;
				$classOdd = (($i % 2) == 0) ? 'tr_odd' : '';
				$sumInvAmt += $row['amt'];
			?>
			<tr class="tr_item <?= $classOdd ?>">
				<td class="td_first"><?= $i ?></td>
				<td><?= $row['invoice_no'] ?></td>
				<td class="td_num" data-value="<?=$row['amt']?>"><?= Helpers::numberFormatRemoveZero($row['amt']) ?></td>
				<td><?= $row['invoice_date'] ?></td>
				<td><?= $row['contract'] ?></td>
				<td><?= $row['waybill_no'] ?></td>
			</tr>
		<?php } ?>
		<tr class="tr_footer">
			<td colspan="2">Итого</td>
			<td class="td_num" data-value="<?=$sumInvAmt?>"><?= Helpers::numberFormatRemoveZero($sumInvAmt) ?></td>
			<td class="td_num"></td>
			<td class="td_num"></td>
			<td class="td_num"></td>
		</tr>
	</table>

	<h4 style="margin-top: 30px;"><b>Кредит</b></h4>
	<table class="tbl-plan tbl-second" id="fix_table2">
		<tr class="tr_head">
			<td>№</td>
			<td>№ платежного поручения</td>
			<td>Сумма</td>
			<td>Дата</td>
			<td>№ договора</td>
			<td>Сумма поставки</td>
			<td>Остаток</td>
		</tr>
		<?php $i = 0;$sumPayAmt = 0;$sumAllAmt = 0;$sumDiffAmt = 0; ?>
		<?php foreach ($data['credit'] as $row) { ?>
			<?php
				$i++;
				$classOdd = (($i % 2) == 0) ? 'tr_odd' : '';
				$sumPayAmt += $row['pay_amt'];
				$sumAllAmt += $row['amount'];
				$sumDiffAmt += $row['amt_diff'];
			?>
		<tr class="tr_item <?=$classOdd?>">
			<td class="td_first"><?=$i?></td>
			<td><?=$row['no']?></td>
			<td class="td_num" data-value="<?=$row['pay_amt']?>"><?= Helpers::numberFormatRemoveZero($row['pay_amt']) ?></td>
			<td><?=$row['pay_date']?></td>
			<td><?=$row['contract_no']?></td>
			<td><?= Helpers::numberFormatRemoveZero($row['amount'])?></td>
			<td><?= Helpers::numberFormatRemoveZero($row['amt_diff'])?></td>
		</tr>
		<?php } ?>




		<tr class="tr_footer">
			<td colspan="2">Итого</td>
			<td class="td_num" data-value="<?=$sumPayAmt?>" ><?= Helpers::numberFormatRemoveZero($sumPayAmt) ?></td>
			<td class="td_num"></td>
			<td class="td_num"></td>
			<td class="td_num" data-value="<?=$sumAllAmt?>"><?= Helpers::numberFormatRemoveZero($sumAllAmt) ?></td>
			<td class="td_num" data-value="<?=$sumDiffAmt?>"><?= Helpers::numberFormatRemoveZero($sumDiffAmt) ?></td>
		</tr>
	</table>

</div>

<?php
$docReadyJs = <<< JS
$(document).ready(function() {

	$('#download-xls').on('click', function (e) {
		html_xls_export('fix_table1', '$downloadFileName');
		html_xls_export('fix_table2', '$downloadFileName');
  });	

  $('.td_num').each(function() {
		var cellVal = parseInt($(this).attr('data-value'));    
		if(cellVal == 0){
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