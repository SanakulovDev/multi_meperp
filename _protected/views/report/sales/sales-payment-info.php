<?php

use app\components\Helpers;
use yii\helpers\Html;
use yii\helpers\Url;
use app\services\ReportService;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Client report');
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
	width: 180px;
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
	<div class="col-md-2 col-lg-2">
		<?php echo Html::dropDownList('name', $year, $years, [
			'prompt' => 'Select an years',
			'class' => 'form-control select2 customer-factory-year',
			'data-url' => Url::to(['report/sales-payment-info']),
		]);?>
	</div>
	<div class="col-md-8 col-lg-8">
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
		<td >Счёт фактура</td>
		<td >Оплаты</td>
		<td >Нехватка</td>
	</tr>
	
	<?php $i = 0; $sumInvAmt = 0; $sumPayAmt = 0;; $sumPayAmt = 0;?>
	<?php foreach ($data as $row) {?>
		<?php
			$i++;
			$customerId = $row->id;
			$classOdd = (($i % 2) == 0) ? 'tr_odd' : '';
            $shortage = 0;
            $contract_amount = ReportService::queryCustomerFactory($customerId, $year);
			$inv_amount = ReportService::salesPaymentInfo($customerId, $year);
			// vd($contract_amount);
			$sumInvAmt += $inv_amount;
			$class= '';

			$sumContractAmt += $contract_amount;
            $shortage = $contract_amount - $inv_amount;
            $sumShortage += $shortage;
		?>

		<tr class="tr_item <?=$classOdd?>">
			<td class="td_first"><?=$i?></td>
			<td><a href="<?=Url::toRoute(['report/sales-payment-status-customer','customer_id' => $customerId]) ?>"><?=$row->name?></a> </td>
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
ob_start();?>
$(document).ready(function() {

	$('#download-xls').on('click', function (e) {
		html_xls_export('fix_table', '$downloadFileName');
  });	

  $('.td_shortage').each(function() {
		var cellVal = parseInt($(this).attr('data-value'));    
		if(cellVal < 0){
			$(this).addClass('text-danger');
		}
		else if(cellVal == 0){

		}
		else {
			$(this).addClass('bg-success');
			$(this).addClass('text-white');
			// $(this).css({"color": "white"});
		}
	});
	$('.customer-factory-year').on('change', function(){
		var year = $(this).val();
		var url = $(this).attr('data-url');
		url = url + '?year=' + year;
		// redirect url
		location.href = url;
	})


}) 
<?php
$this->registerJs(ob_get_clean());
?>