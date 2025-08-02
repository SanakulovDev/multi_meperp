<?php

use app\components\Helpers;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Payment status for customers (Debit/credit)');
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
.tbl-plan{
	width: 70%;
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
.tr_footer td{
	font-weight: bold;
	height: 30px;
	background-color: #b6e8ff;
}

.td_num{
	text-align: right;
	width: 120px;
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

</style>
<?
    // echo "<pre>";
    // print_r($data);
    // echo "</pre>";
    // die;
?>
<div class="row">
	<div class="col-md-2 col-lg-2">
		<?=Html::a(Yii::t('app', 'btn-back'), ['index'], ['class' => 'btn btn-default btn-xs'])?>
	</div>
	<div class="col-md-10 col-lg-10">
		<div class="pull-right">
			<?=Html::button(Yii::t('app', 'btn-download-delivery-plan'), ['class' => 'btn btn-info btn-xs', 'id' => 'download-xls'])?>
		</div>
	</div>
</div>
	<br>
<div class="content-index">

  <table class="tbl-plan tbl-first" id="fix_table">
	<tr class="tr_head">
		<td rowspan="2">№</td>
		<td rowspan="2">Наименование компании</td>
		<td colspan="2">Дебит</td>
		<td colspan="2">Кредит</td>
		<!-- <td rowspan="2">Итого</td> -->
	</tr>
	<tr class="tr_head">
		<td>Сумма</td>
		<td>Дата</td>
		<td>Сумма</td>
		<td>Дата</td>
	</tr>
	<?php $i = 0; $sumInvAmt = 0; $sumPayAmt = 0;; $sumPayAmt = 0;?>
	<?php foreach ($data['data'] as $row) {?>
		<?php
			$i++;
			list($customerName, $customerId) = explode('|', $row['customer']);
			$classOdd = (($i % 2) == 0) ? 'tr_odd' : '';
			$sumInvAmt += $row['inv_amt'];
			$sumPayAmt += $row['pay_amt'];
		?>

		<tr class="tr_item <?=$classOdd?>">
			<td class="td_first"><?=$i?></td>
			<td><a href="<?=Url::toRoute(['report/sales-payment-status-customer','customer_id' => $customerId]) ?>"><?=$customerName?></a> </td>
			<td class="td_num" data-value="<?=$row['inv_amt']?>"><?=Helpers::numberFormatRemoveZero($row['inv_amt'])?></td>
			<td class="text-center"><?=$row['inv_date']?></td>
			<td class="td_num" data-value="<?=$row['pay_amt']?>"><?=Helpers::numberFormatRemoveZero($row['pay_amt'])?></td>
			<td class="text-center"><?=$row['pay_date']?></td>
			<!-- <td class="td_num">???</td> -->
		</tr>

	<?php } ?>

	<tr class="tr_footer">
		<td colspan="2">Итого</td>
		<td class="td_num"><?=Helpers::numberFormatRemoveZero($sumInvAmt)?></td>
		<td class="td_num"></td>
		<td class="td_num"><?=Helpers::numberFormatRemoveZero($sumPayAmt)?></td>
		<td class="td_num"></td>
		<!-- <td class="td_num">???</td> -->
	</tr>
  </table>

</div>

<?php
$docReadyJs = <<< JS
$(document).ready(function() {

	$('#download-xls').on('click', function (e) {
		html_xls_export('fix_table', '$downloadFileName');
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