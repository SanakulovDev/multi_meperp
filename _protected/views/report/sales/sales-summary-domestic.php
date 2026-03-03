<?php

use app\components\Helpers;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Sales report domestic');
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
		white-space: nowrap;
	}

	.td_first {
		text-align: center;
		width: 40px;
	}
	.mini_label{
		text-align: right;
		color: #635d5d;
	}
	.td_zero{
		color: #bdbdbd !important;
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

	<div class="buttons">
		<button class="btn btn-primary btn-amount">Сумма</button>
		<button class="btn btn-warning btn-qty">Объем</button>
	</div>

	<div class="mini_label lbl-amount">тыс сум</div>
	<table class="tbl-sales tbl-first" id="fix_table">
		<tr class="tr_head">
			<td>№</td>
			<td>Наименование клиента</td>
			<?php foreach ($data['monthList'] as $month => $monthName) {?>
				<td><?=$monthName?></td>
			<? } ?>
			<td>Итого</td>
		</tr>
		<?php $i = 0;?>		
		<?php foreach ($data['customers'] as $customer) {?>
			<?
				$i++;
				list($customerName, $customerId) = explode('|',$customer);	
				$classOdd = (($i % 2) == 0) ? 'tr_odd' : '';
			?>	
			<tr class="tr_item <?=$classOdd?>">
				<td class="td_first"><?=$i?></td>
				<td><a href="<?= Url::toRoute(['report/sales-summary-customer','customer_id' => $customerId]) ?>"><?=$customerName?></a></td>
				<?php $sumRow = 0;?>
				<?php foreach ($data['monthList'] as $month => $monthName) {?>
					<?php list($yyyymm,$quarter) = explode('|',$month); ?>
					<?php $val = $data['data'][$customer][$yyyymm]['amount'] ?? 0; $sumRow += $val ?>
					<td class="td_num"><?=Helpers::numberFormatRemoveZero($val / 1000)?></td>
				<? } ?>
				<td class="td_num"><?=Helpers::numberFormatRemoveZero($sumRow / 1000)?></td>
			</tr>
		<?}?>

	</table>

	<div class="mini_label lbl-qty">кг</div>
	<table class="tbl-sales tbl-second" style="display: none;">
		<tr class="tr_head">
			<td>№</td>
			<td>Наименование клиента</td>
			<?php foreach ($data['monthList'] as $month => $monthName) {?>
				<td><?=$monthName?></td>
			<? } ?>
			<td>Итого</td>
		</tr>
		<?php $i = 0;?>		
		<?php foreach ($data['customers'] as $customer) {?>
			<?
				$i++;
				list($customerName, $customerId) = explode('|',$customer);	
				$classOdd = (($i % 2) == 0) ? 'tr_odd' : '';
			?>		
			<tr class="tr_item <?=$classOdd?>">
				<td class="td_first"><?=$i?></td>
				<td><a href="<?= Url::toRoute(['report/sales-summary-customer','customer_id' => $customerId]) ?>"><?=$customerName?></a></td>
				<?php $sumRow = 0;?>
				<?php foreach ($data['monthList'] as $month => $monthName) {?>
					<?php list($yyyymm,$quarter) = explode('|',$month); ?>
					<?php $val = $data['data'][$customer][$yyyymm]['qty'] ?? 0; $sumRow += $val ?>
					<td class="td_num"><?=Helpers::numberFormatRemoveZero($val)?></td>
				<? } ?>
				<td class="td_num"><?=Helpers::numberFormatRemoveZero($sumRow)?></td>
			</tr>
		<?}?>

	</table>

</div>

<?php
$docReadyJs = <<< JS
$(document).ready(function() {

	$('#download-xls').on('click', function (e) {
		html_xls_export('fix_table', '$downloadFileName');
  	});	

	$('.btn-amount').on('click', function (e) {
		$('.btn-amount').removeClass('btn-warning').addClass('btn-primary');
		$('.btn-qty').removeClass('btn-primary').addClass('btn-warning');
		$('.tbl-first').show();
		$('.tbl-second').hide();
		$('.lbl-amount').show();
		$('.lbl-qty').hide();
		$('.tbl-first').attr('id','fix_table');
		$('.tbl-second').attr('id','');

	});

	$('.btn-qty').on('click', function (e) {
		$('.btn-qty').removeClass('btn-warning').addClass('btn-primary');
		$('.btn-amount').removeClass('btn-primary').addClass('btn-warning');
		$('.tbl-first').hide();
		$('.tbl-second').show();
		$('.lbl-amount').hide();
		$('.lbl-qty').show();
		$('.tbl-first').attr('id','');
		$('.tbl-second').attr('id','fix_table');
	});

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