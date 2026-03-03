<?php

use app\components\Helpers;
use Symfony\Component\Console\Helper\Helper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Sales report');
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
		background-color: #b6e8ff;
	}

	.td_num {
		text-align: right;
		width: 90px;
		white-space: nowrap;
	}

	.tbl-second {
		margin-top: 30px;
	}

	.total {
		width: 140px !important;
	}

	.td_zero {
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
		<?= Html::a(Yii::t('app', 'btn-back'), ['index'], ['class' => 'btn btn-default btn-xs']) ?>
	</div>
	<div class="col-md-10 col-lg-10">
		<div class="pull-right">
			<?= Html::button(Yii::t('app', 'btn-download-delivery-plan'), ['class' => 'btn btn-info btn-xs', 'id' => 'download-xls']) ?>
		</div>
	</div>
</div>
<br>
<div class="content-index">

	<?php
	$sum_1_uzs = 0;
	$sum_1_qty = 0;
	$sum_2_uzs = 0;
	$sum_2_usd = 0;
	$sum_2_qty = 0;
	$sum_domexp_uzs = 0;
	$sum_domexp_usd = 0;
	$sum_domexp_qty = 0;
	?>

	<table class="tbl-sales tbl-first"  id="fix_table_1">
		<tr class="tr_head">
			<td>Рынок</td>
			<td>Описание</td>
			<td>Всего на <?= $data['year'] ?></td>
			<?php foreach ($data['monthList'] as $month => $monthName) { ?>
				<td><?= $monthName ?></td>
			<? } ?>
		</tr>

		<tr class="tr_item">
			<td rowspan="2"> <a href="<?= Url::toRoute(['report/sales-summary-domestic']) ?>">Внутренный рынок</a> </td>
			<td>Сумма (1000 UZS)</td>
			<td class="td_num total sum_1_uzs">0</td>
			<?php foreach ($data['monthList'] as $month => $monthName) { ?>
				<?php list($yyyymm,$quarter) = explode('|',$month); ?>
				<?php $val = $data['data'][1]['UZS'][$yyyymm] ?? 0;
				$sum_1_uzs += $val ?>
				<td class="td_num"><?= Helpers::numberFormatRemoveZero($val / 1000) ?></td>
			<? } ?>
		</tr>
		<tr class="tr_item">
			<td>Кг</td>
			<td class="td_num total sum_1_qty">0</td>
			<?php foreach ($data['monthList'] as $month => $monthName) { ?>
				<?php list($yyyymm,$quarter) = explode('|',$month); ?>
				<?php $val = $data['data'][1]['qty'][$yyyymm] ?? 0;
				$sum_1_qty += $val ?>
				<td class="td_num"><?= Helpers::numberFormatRemoveZero($val) ?></td>
			<? } ?>
		</tr>

		<tr class="tr_item">
			<td rowspan="3"><a href="<?= Url::toRoute(['report/sales-summary-export']) ?>">Экспорт</a></td>
			<td>Сумма (1000 UZS)</td>
			<td class="td_num total sum_2_uzs">0</td>
			<?php foreach ($data['monthList'] as $month => $monthName) { ?>
				<?php list($yyyymm,$quarter) = explode('|',$month); ?>
				<?php $val = $data['data'][2]['UZS'][$yyyymm] ?? 0;
				$sum_2_uzs += $val ?>
				<td class="td_num"><?= Helpers::numberFormatRemoveZero($val / 1000) ?></td>
			<? } ?>
		</tr>
		<tr class="tr_item">
			<td>Сумма (USD)</td>
			<td class="td_num total sum_2_usd">0</td>
			<?php foreach ($data['monthList'] as $month => $monthName) { ?>
				<?php list($yyyymm,$quarter) = explode('|',$month); ?>
				<?php $val = $data['data'][2]['USD'][$yyyymm] ?? 0;
				$sum_2_usd += $val ?>
				<td class="td_num"><?= Helpers::numberFormatRemoveZero($val) ?></td>
			<? } ?>
		</tr>
		<tr class="tr_item">
			<td>Кг</td>
			<td class="td_num total sum_2_qty">0</td>
			<?php foreach ($data['monthList'] as $month => $monthName) { ?>
				<?php list($yyyymm,$quarter) = explode('|',$month); ?>
				<?php $val = $data['data'][2]['qty'][$yyyymm] ?? 0;
				$sum_2_qty += $val ?>
				<td class="td_num"><?= Helpers::numberFormatRemoveZero($val) ?></td>
			<? } ?>
		</tr>

		<tr class="tr_footer">
			<td rowspan="3">Итого</td>
			<td>Сумма (1000 UZS)</td>
			<td class="td_num total sum_domexp_uzs">0</td>
			<?php foreach ($data['monthList'] as $month => $monthName) { ?>
				<?php list($yyyymm,$quarter) = explode('|',$month); ?>
				<?php $val = $data['data']['domexp']['UZS'][$yyyymm] ?? 0;
				$sum_domexp_uzs += $val ?>
				<td class="td_num"><?= Helpers::numberFormatRemoveZero($val / 1000) ?></td>
			<? } ?>
		</tr>
		<tr class="tr_footer">
			<td>Сумма (USD)</td>
			<td class="td_num total sum_domexp_usd">0</td>
			<?php foreach ($data['monthList'] as $month => $monthName) { ?>
				<?php list($yyyymm,$quarter) = explode('|',$month); ?>
				<?php $val = $data['data']['domexp']['USD'][$yyyymm] ?? 0;
				$sum_domexp_usd += $val ?>
				<td class="td_num"><?= Helpers::numberFormatRemoveZero($val) ?></td>
			<? } ?>
		</tr>
		<tr class="tr_footer">
			<td>Кг</td>
			<td class="td_num total sum_domexp_qty">0</td>
			<?php foreach ($data['monthList'] as $month => $monthName) { ?>
				<?php list($yyyymm,$quarter) = explode('|',$month); ?>
				<?php $val = $data['data']['domexp']['qty'][$yyyymm] ?? 0;
				$sum_domexp_qty += $val ?>
				<td class="td_num"><?= Helpers::numberFormatRemoveZero($val) ?></td>
			<? } ?>
		</tr>
	</table>

	<table class="tbl-sales tbl-second" id="fix_table_2">
		<tr class="tr_head">
			<td>Марка ГП</td>
			<td>Описание</td>
			<td>Всего на <?= $data['year'] ?></td>
			<?php foreach ($data['monthList'] as $month => $monthName) { ?>
				<td><?= $monthName ?></td>
			<? } ?>
		</tr>
		<?php
		$sum_total_uzs = 0;
		$sum_total_usd = 0;
		$sum_total_qty = 0;
		?>
		<?php foreach ($data['types'] as $type) { ?>
			<?
			$sum[$type]['uzs'] = 0;
			$sum[$type]['usd'] = 0;
			$sum[$type]['qty'] = 0;
			?>
			<tr class="tr_item">
				<td rowspan="3"><?= Yii::t('app', $type) ?></td>
				<td>Сумма (1000 UZS)</td>
				<td class="td_num total sum_<?= $type ?>_uzs">0</td>
				<?php foreach ($data['monthList'] as $month => $monthName) { ?>
					<?php list($yyyymm,$quarter) = explode('|',$month); ?>
					<?php $val = $data['dataSecond'][$type]['UZS'][$yyyymm] ?? 0;
					$sum[$type]['uzs'] += $val ?>
					<td class="td_num"><?= Helpers::numberFormatRemoveZero($val / 1000) ?></td>
				<? } ?>
			</tr>
			<tr class="tr_item">
				<td>Сумма (USD)</td>
				<td class="td_num total sum_<?= $type ?>_usd">0</td>
				<?php foreach ($data['monthList'] as $month => $monthName) { ?>
					<?php list($yyyymm,$quarter) = explode('|',$month); ?>
					<?php $val = $data['dataSecond'][$type]['USD'][$yyyymm] ?? 0;
					$sum[$type]['usd'] += $val ?>
					<td class="td_num"><?= Helpers::numberFormatRemoveZero($val) ?></td>
				<? } ?>
			</tr>
			<tr class="tr_item">
				<td>Кг</td>
				<td class="td_num total sum_<?= $type ?>_qty">0</td>
				<?php foreach ($data['monthList'] as $month => $monthName) { ?>
					<?php list($yyyymm,$quarter) = explode('|',$month); ?>
					<?php $val = $data['dataSecond'][$type]['qty'][$yyyymm] ?? 0;
					$sum[$type]['qty'] += $val ?>
					<td class="td_num"><?= Helpers::numberFormatRemoveZero($val) ?></td>
				<? } ?>
			</tr>
		<? } ?>


		<tr class="tr_footer">
			<td rowspan="3">Итого</td>
			<td>Сумма (1000 UZS)</td>
			<td class="td_num total sum_total_uzs">0</td>
			<?php foreach ($data['monthList'] as $month => $monthName) { ?>
				<?php list($yyyymm,$quarter) = explode('|',$month); ?>
				<?php $val = $data['dataSecond']['total']['UZS'][$yyyymm] ?? 0;
				$sum_total_uzs += $val ?>
				<td class="td_num"><?= Helpers::numberFormatRemoveZero($val / 1000) ?></td>
			<? } ?>
		</tr>
		<tr class="tr_footer">
			<td>Сумма (USD)</td>
			<td class="td_num  total sum_total_usd">0</td>
			<?php foreach ($data['monthList'] as $month => $monthName) { ?>
				<?php list($yyyymm,$quarter) = explode('|',$month); ?>
				<?php $val = $data['dataSecond']['total']['USD'][$yyyymm] ?? 0;
				$sum_total_usd += $val ?>
				<td class="td_num"><?= Helpers::numberFormatRemoveZero($val) ?></td>
			<? } ?>
		</tr>
		<tr class="tr_footer">
			<td>Кг</td>
			<td class="td_num  total sum_total_qty">0</td>
			<?php foreach ($data['monthList'] as $month => $monthName) { ?>
				<?php list($yyyymm,$quarter) = explode('|',$month); ?>
				<?php $val = $data['dataSecond']['total']['qty'][$yyyymm] ?? 0;
				$sum_total_qty += $val ?>
				<td class="td_num"><?= Helpers::numberFormatRemoveZero($val) ?></td>
			<? } ?>
		</tr>


	</table>

</div>

<?php

// first table
$sum_1_uzs = Helpers::numberFormatRemoveZero($sum_1_uzs / 1000);
$sum_1_qty = Helpers::numberFormatRemoveZero($sum_1_qty);
$sum_2_uzs = Helpers::numberFormatRemoveZero($sum_2_uzs / 1000);
$sum_2_usd = Helpers::numberFormatRemoveZero($sum_2_usd);
$sum_2_qty = Helpers::numberFormatRemoveZero($sum_2_qty);
$sum_domexp_uzs = Helpers::numberFormatRemoveZero($sum_domexp_uzs / 1000);
$sum_domexp_usd = Helpers::numberFormatRemoveZero($sum_domexp_usd);
$sum_domexp_qty = Helpers::numberFormatRemoveZero($sum_domexp_qty);

//second table
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
		html_xls_export('fix_table_1', '$downloadFileName');
		html_xls_export('fix_table_1', '$downloadFileName');
  	});	

	$('.sum_1_uzs').html('$sum_1_uzs');
	$('.sum_1_qty').html('$sum_1_qty');
	$('.sum_2_uzs').html('$sum_2_uzs');
	$('.sum_2_qty').html('$sum_2_qty');
	$('.sum_2_usd').html('$sum_2_usd');
	$('.sum_domexp_uzs').html('$sum_domexp_uzs');
	$('.sum_domexp_qty').html('$sum_domexp_qty');
	$('.sum_domexp_usd').html('$sum_domexp_usd');

	//console.log(typeof $.parseJSON(sum_types));
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