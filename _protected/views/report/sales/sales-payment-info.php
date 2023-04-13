<?php

use app\components\Helpers;
use yii\helpers\Html;
use yii\helpers\Url;
use app\services\ReportService;
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
		<td >№</td>
		<td >Наименование компании</td>
		<td >Контракт</td>
		<td >Кредит</td>
		<td >Нехватка</td>
	</tr>
	
	<?php $i = 0; $sumInvAmt = 0; $sumPayAmt = 0;; $sumPayAmt = 0;?>
	<?php foreach ($data['data'] as $row) {?>
		<?php
			$i++;
			list($customerName, $customerId) = explode('|', $row['customer']);
			$classOdd = (($i % 2) == 0) ? 'tr_odd' : '';
            $shortage = 0;
            $contract_amount = ReportService::queryCustomerFactory($customerId);
			$inv_amount = ReportService::salesPaymentInfo($customerId);
			// vd($contract_amount);
			$sumInvAmt += $inv_amount;
			$class= '';

			$sumContractAmt += $contract_amount;
            $shortage = $contract_amount - $inv_amount;
            $sumShortage += $shortage;
		?>

		<tr class="tr_item <?=$classOdd?>">
			<td class="td_first"><?=$i?></td>
			<td><a href="<?=Url::toRoute(['report/sales-payment-status-customer','customer_id' => $customerId]) ?>"><?=$customerName?></a> </td>
			<td class="td_num" data-value="<?=$contract_amount?>"><?=Helpers::numberFormatRemoveZero($contract_amount)?></td>
			<td class="td_num" data-value="<?=$inv_amount?>"><?=Helpers::numberFormatRemoveZero($inv_amount)?></td>
			<td class="td_num td_shortage" data-value="<?=$shortage*(-1)?>"><?=Helpers::numberFormatRemoveZero($shortage*(-1))?></td>
		</tr>

	<?php } ?>

	<tr class="tr_footer">
		<td colspan="2">Итого</td>
		<td class="td_num"><?=Helpers::numberFormatRemoveZero($sumContractAmt)?></td>
		<td class="td_num"><?=Helpers::numberFormatRemoveZero($sumInvAmt)?></td>
		<td class="td_num"><?=Helpers::numberFormatRemoveZero($sumShortage)?></td>
	</tr>
  </table>

</div>

<?php
$docReadyJs = <<< JS
$(document).ready(function() {

	$('#download-xls').on('click', function (e) {
		html_xls_export('fix_table', '$downloadFileName');
  });	

  $('.td_shortage').each(function() {
		var cellVal = parseInt($(this).attr('data-value'));    
		if(cellVal <= 0){
			$(this).addClass('td_negative');
		}
		else {
			$(this).css({"background-color": "green"});
			$(this).css({"color": "white"});
		}
	});


}) 
JS;
$this->registerJs($docReadyJs);
?>