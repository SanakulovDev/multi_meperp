<?php

use app\components\Helpers;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Implementation plan');
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
.tbl-plan{
	width: 100%;
	border-collapse: collapse;
}
.tbl-plan, .tbl-plan tr, .tbl-plan tr td{
	border: 1px solid #cccccc;
} 
.tbl-plan tr td{
	padding: 2px 3px;
	white-space: nowrap;
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
	width: 90px;
}

.tbl-second{
	margin-top: 30px;
}

.td_first{
	text-align: center;
	width: 30px;
}
.td_zero{
	color: #bdbdbd !important;
}

.tr_odd{
	background-color: #eceeef;
}

.no-backcolor{
	background-color: #ffffff !important;
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
		<td>№</td>
		<td>Наименование компании</td>
		<td style="width: 120px;">Наименование компаундов</td>
		<td style="width: 100px;">Свет компаундов</td>
		<?php foreach ($data['monthList'] as $month => $monthName) {?>
				<td><?=$monthName?></td>
		<? } ?>
		<td>Итого</td>
	</tr>
	<?php $i = 0; $sumRow = 0; $workCustomer = ''; $cntRows = 0;?>
		<?php foreach ($data['data'] as $customerName => $arrCustomer) { ?>
			<?php if ($customerName == 'total') continue; ?>
			<?php $j = 0; ?>
			<?php foreach ($arrCustomer as $part => $arrMonth) {?>
				<?php $j++; ?>
				<?php
					$i++;
					list($partNo,$partName,$partColor) = explode('?', $part);
					$classOdd = (($i % 2) == 0) ? 'tr_odd' : '';
					
					
				?>
				<tr class="tr_item <?=$classOdd?>" >
					<td class="td_first"><?=$i?></td>
					<?php if($j == 1) {?>
							<td rowspan="<?=count($arrCustomer)?>" class="no-backcolor"><?= $customerName ?></td>
						<?}?>
					<td><?=$partName?></td>
					<td><?=$partColor?></td>
					<?php foreach ($data['monthList'] as $month => $monthName) {?>
						<?php list($yyyymm,$quarter) = explode('|',$month); ?>
						<?php $val = $data['data'][$customerName][$part][$yyyymm]['qty'] ?? 0; $sumRow += $val ?>
							<td class="td_num"><?=Helpers::numberFormatRemoveZero($val)?></td>
						<? } ?>
					<td class="td_num"><?=Helpers::numberFormatRemoveZero($sumRow)?></td>
				</tr>
			<?}?>
		<? } ?>


	

	<tr class="tr_footer">
		<td colspan="4">Итого</td>
		<?php $sumRow = 0; ?>
		<?php foreach ($data['monthList'] as $month => $monthName) {?>
			<?php list($yyyymm,$quarter) = explode('|',$month); ?>
			<?php $val = $data['data']['total'][$yyyymm]['qty'] ?? 0; $sumRow += $val ?>
				<td class="td_num"><?=Helpers::numberFormatRemoveZero($val)?></td>
			<? } ?>
		<td class="td_num"><?=Helpers::numberFormatRemoveZero($sumRow)?></td>
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
		var cellText = $(this).html();    
		if(cellText == '0'){
			$(this).addClass('td_zero');
		}
	});
}) 
JS;
$this->registerJs($docReadyJs);
?>