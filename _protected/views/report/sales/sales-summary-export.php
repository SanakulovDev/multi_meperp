<?php

use app\components\Helpers;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Sales summary export');
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
	.tbl-sales {
		width: 100%;
		border-collapse: collapse;
	}

	.tbl-sales,
	.tbl-sales tr,
	.tbl-sales tr td {
		border: 1px solid #cccccc;
	}

	.tbl-sales tr td {
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
	}

	.td_num {
		text-align: right;
		width: 90px;
	}

	.tbl-second {
		margin-top: 30px;
	}

	.td_zero{
		color: #bdbdbd !important;
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
		<?= Html::a(Yii::t('app', 'btn-back'), ['sales-summary'], ['class' => 'btn btn-default btn-xs']) ?>
	</div>
	<div class="col-md-10 col-lg-10">
		<div class="pull-right">
			<?= Html::button(Yii::t('app', 'btn-download-delivery-plan'), ['class' => 'btn btn-info btn-xs', 'id' => 'download-xls']) ?>
		</div>
	</div>
</div>
<br>
<div class="content-index">

	<table class="tbl-sales tbl-first" id="fix_table">
		<tr class="tr_head">
			<td>Покупатель</td>
			<td>Марка ГП</td>
			<td>Описание</td>
			<td>Всего на 2021</td>
			<?php foreach ($data['monthList'] as $month => $monthName) { ?>
				<td><?= $monthName ?></td>
			<? } ?>
		</tr>
		<?php
		$sum_total_uzs = 0;
		$sum_total_usd = 0;
		$sum_total_qty = 0;
		$custId = 0;
		?>
		<?php foreach ($data['data'] as $customerName => $arrCustomer) { ?>
			<?php $custId++; ?>
			<?php if ($customerName == 'total') continue; ?>
			<?php $j = 0; ?>
			<?php foreach ($arrCustomer as $type => $itemType) { ?>
				<?php $j++; ?>
				<?
				$sum[$custId][$type]['uzs'] = 0;
				$sum[$custId][$type]['usd'] = 0;
				$sum[$custId][$type]['qty'] = 0;
				?>
				<tr class="tr_item">
					<?php if($j == 1) {?>
						<td rowspan="<?=count($arrCustomer) * 3?>"><?= $customerName ?></td>
					<?}?>
					<td rowspan="3"><?= Yii::t('app', $type) ?></td>
					<td>Сумма (USD)</td>
					<td class="td_num total sum_<?= $custId ?>_<?= $type ?>_usd">0</td>
					<?php foreach ($data['monthList'] as $month => $monthName) { ?>
						<?php list($yyyymm,$quarter) = explode('|',$month); ?>
						<?php $val = $data['data'][$customerName][$type]['USD'][$yyyymm] ?? 0;
						$sum[$custId][$type]['usd'] += $val ?>
						<td class="td_num"><?= Helpers::numberFormatRemoveZero($val) ?></td>
					<? } ?>
				</tr>
				<tr class="tr_item">
					<td>Сумма (1000 UZS)</td>
					<td class="td_num total sum_<?= $custId ?>_<?= $type ?>_uzs">0</td>
					<?php foreach ($data['monthList'] as $month => $monthName) { ?>
						<?php list($yyyymm,$quarter) = explode('|',$month); ?>
						<?php $val = $data['data'][$customerName][$type]['UZS'][$yyyymm] ?? 0;
						$sum[$custId][$type]['uzs'] += $val ?>
						<td class="td_num"><?= Helpers::numberFormatRemoveZero($val / 100) ?></td>
					<? } ?>
				</tr>
				<tr class="tr_item">
					<td>Кг</td>
					<td class="td_num total sum_<?= $custId ?>_<?= $type ?>_qty">0</td>
					<?php foreach ($data['monthList'] as $month => $monthName) { ?>
						<?php list($yyyymm,$quarter) = explode('|',$month); ?>
						<?php $val = $data['data'][$customerName][$type]['qty'][$yyyymm] ?? 0;
						$sum[$custId][$type]['qty'] += $val ?>
						<td class="td_num"><?= Helpers::numberFormatRemoveZero($val) ?></td>
					<? } ?>
				</tr>
			<? } ?>
		<? } ?>



		<tr class="tr_footer">
			<td rowspan="3" colspan="2">Итого</td>
			<td>Сумма (USD)</td>
			<td class="td_num  total sum_total_usd">0</td>
			<?php foreach ($data['monthList'] as $month => $monthName) { ?>
				<?php list($yyyymm,$quarter) = explode('|',$month); ?>
				<?php $val = $data['data']['total']['USD'][$yyyymm] ?? 0;
				$sum_total_usd += $val ?>
				<td class="td_num"><?= Helpers::numberFormatRemoveZero($val) ?></td>
			<? } ?>
		<tr class="tr_footer">
			<td>Сумма (1000 UZS)</td>
			<td class="td_num total sum_total_uzs">0</td>
			<?php foreach ($data['monthList'] as $month => $monthName) { ?>
				<?php list($yyyymm,$quarter) = explode('|',$month); ?>
				<?php $val = $data['data']['total']['UZS'][$yyyymm] ?? 0;
				$sum_total_uzs += $val ?>
				<td class="td_num"><?= Helpers::numberFormatRemoveZero($val / 1000) ?></td>
			<? } ?>
		</tr>
		<tr class="tr_footer">
			<td>Кг</td>
			<td class="td_num  total sum_total_qty">0</td>
			<?php foreach ($data['monthList'] as $month => $monthName) { ?>
				<?php list($yyyymm,$quarter) = explode('|',$month); ?>
				<?php $val = $data['data']['total']['qty'][$yyyymm] ?? 0;
				$sum_total_qty += $val ?>
				<td class="td_num"><?= Helpers::numberFormatRemoveZero($val) ?></td>
			<? } ?>
		</tr>
	</table>

</div>

<?php

$custId = 0;
foreach ($data['data'] as $customerName => $arrCustomer) {
	$custId++;
	if ($customerName == 'total') continue;
	foreach ($arrCustomer as $type => $itemType) {
		$sum[$custId][$type]['uzs'] = Helpers::numberFormatRemoveZero($sum[$custId][$type]['uzs'] / 1000);
		$sum[$custId][$type]['usd'] = Helpers::numberFormatRemoveZero($sum[$custId][$type]['usd']);
		$sum[$custId][$type]['qty'] = Helpers::numberFormatRemoveZero($sum[$custId][$type]['qty']);
	}
}
$sum = json_encode($sum);

$sum_total_uzs = Helpers::numberFormatRemoveZero($sum_total_uzs / 1000);
$sum_total_usd = Helpers::numberFormatRemoveZero($sum_total_usd);
$sum_total_qty = Helpers::numberFormatRemoveZero($sum_total_qty);


$docReadyJs = <<< JS
$(document).ready(function() {

	var sum_types = '$sum';

	$('#download-xls').on('click', function (e) {
		html_xls_export('fix_table', '$downloadFileName');
	});	

	obj_sum_types = $.parseJSON(sum_types);
	console.log(obj_sum_types);
	var cust_id = 0;
	$.each(obj_sum_types, function(index,item){
		cust_id++;
		$.each(item, function(rtype,ritem){
			$('.sum_' + cust_id + '_' + rtype + '_uzs').html(ritem.uzs);	
			$('.sum_' + cust_id + '_' + rtype + '_usd').html(ritem.usd);	
			$('.sum_' + cust_id + '_' + rtype + '_qty').html(ritem.qty);	
		}); 
	}); 


	$('.sum_total_uzs').html('$sum_total_uzs');
	$('.sum_total_qty').html('$sum_total_qty');
	$('.sum_total_usd').html('$sum_total_usd');

	$('.td_num').each(function() {
		var cellText = $(this).html();    
		if(cellText == '0'){
			$(this).addClass('td_zero');
		}
	});


}) 
JS;
$this->registerJs($docReadyJs);
?>