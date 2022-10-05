<?php

use app\components\Helpers;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Sales summary by customer');
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
		<?= Html::a(Yii::t('app', 'btn-back'), ['sales-summary-domestic'], ['class' => 'btn btn-default btn-xs']) ?>
	</div>
	<div class="col-md-10 col-lg-10">
		<div class="pull-right">
			<?= Html::button(Yii::t('app', 'btn-download-delivery-plan'), ['class' => 'btn btn-info btn-xs', 'id' => 'download-xls']) ?>
		</div>
	</div>
</div>
<br>
<div class="content-index">

	<h3><?=$data['customerName']?></h3>

	<table class="tbl-sales tbl-second" id="fix_table">
		<tr class="tr_head">
			<td>Марка ГП</td>
			<td>Описание</td>
			<td>Всего на 2021</td>
			<?php foreach ($data['monthList'] as $month => $monthName) {?>
				<td><?=$monthName?></td>
			<? } ?>
		</tr>
		<?php
		$sum_total_uzs = 0;
		$sum_total_usd = 0;
		$sum_total_qty = 0;
		?>
		<?php foreach ($data['types'] as $type) {?>
			<?
			$sum[$type]['uzs'] = 0;
			$sum[$type]['usd'] = 0;
			$sum[$type]['qty'] = 0;
			?>
			<tr class="tr_item">
				<td rowspan="2"><?=Yii::t('app', $type)?></td>
				<td>Сумма (1000 UZS)</td>
				<td class="td_num total sum_<?=$type?>_uzs">0</td>
				<?php foreach ($data['monthList'] as $month => $monthName) {?>
					<?php list($yyyymm,$quarter) = explode('|',$month); ?>
					<?php $val = $data['data'][$type]['UZS'][$yyyymm] ?? 0; $sum[$type]['uzs'] += $val ?>
					<td class="td_num"><?=Helpers::numberFormatRemoveZero($val / 1000)?></td>
				<? } ?>
			</tr>
			<tr class="tr_item">
				<td>Кг</td>
				<td class="td_num total sum_<?=$type?>_qty">0</td>
				<?php foreach ($data['monthList'] as $month => $monthName) {?>
					<?php list($yyyymm,$quarter) = explode('|',$month); ?>
					<?php $val = $data['data'][$type]['qty'][$yyyymm] ?? 0; $sum[$type]['qty'] += $val ?>
					<td class="td_num"><?=Helpers::numberFormatRemoveZero($val)?></td>
				<? } ?>
			</tr>
		<?}?>


		<tr class="tr_footer">
			<td rowspan="2">Итого</td>
			<td>Сумма (1000 UZS)</td>
			<td class="td_num total sum_total_uzs">0</td>
			<?php foreach ($data['monthList'] as $month => $monthName) {?>
				<?php list($yyyymm,$quarter) = explode('|',$month); ?>
				<?php $val = $data['data']['total']['UZS'][$yyyymm] ?? 0; $sum_total_uzs += $val ?>
				<td class="td_num"><?=Helpers::numberFormatRemoveZero($val / 1000)?></td>
			<? } ?>
		</tr>
		<tr class="tr_footer">
			<td>Кг</td>
			<td class="td_num  total sum_total_qty">0</td>
			<?php foreach ($data['monthList'] as $month => $monthName) {?>
				<?php list($yyyymm,$quarter) = explode('|',$month); ?>
				<?php $val = $data['data']['total']['qty'][$yyyymm] ?? 0; $sum_total_qty += $val ?>
				<td class="td_num"><?=Helpers::numberFormatRemoveZero($val)?></td>
			<? } ?>
		</tr>
	</table>

</div>

<?php


foreach ($data['types'] as $type) {
	$sum[$type]['uzs'] = Helpers::numberFormatRemoveZero($sum[$type]['uzs'] / 1000);	
	$sum[$type]['usd'] = Helpers::numberFormatRemoveZero($sum[$type]['usd']);	
	$sum[$type]['qty'] = Helpers::numberFormatRemoveZero($sum[$type]['qty']);	
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
	$.each(obj_sum_types, function(index,item){
		console.log(item);
		$('.sum_' + index + '_uzs').html(item.uzs);	
		$('.sum_' + index + '_usd').html(item.usd);	
		$('.sum_' + index + '_qty').html(item.qty);	
	}); 


	$('.sum_total_uzs').html('$sum_total_uzs');
	$('.sum_total_qty').html('$sum_total_qty');
	$('.sum_total_usd').html('$sum_total_usd');

	$('.td_num').each(function() {
		var cellText = $(this).html();    
		console.log(cellText);
		if(cellText == '0'){
			$(this).addClass('td_zero');
		}
	});
}) 
JS;
$this->registerJs($docReadyJs);
?>