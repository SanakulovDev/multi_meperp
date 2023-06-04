<?php

use app\components\Helpers;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Plan/fact of implementation');
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
.tbl-plan{
	width: 100%;
	border-collapse: collapse;
    height: 80vh;
	overflow: scroll;
}
.tbl-plan tr td{
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

.td_delimeter{
	background-color: #ffffff !important;
	border-top: 0px !important;
	border-bottom: 0px !important;
}
.td_delimeter p{
	width: 20px;
	margin: 0px;
}

.content-index{
	overflow: scroll;
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

.no-backcolor{
	background-color: #ffffff !important;
}

.header_td_p {
	width: 100px;
	padding: 0px;
	margin: 0px;
	vertical-align: middle;
}
thead {
  position: sticky;
  top: 0;
}

.content-index{
	height: 75vh;
}

</style>

<?
    // echo "<pre>";
    // print_r($downloadFileName);
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
	<div class="container-header">
		
	</div>
  <table class="tbl-plan tbl-first" id="fix_table">
	<thead>
		<tr class="tr_head">
			<td rowspan="2">№</td>
			<td rowspan="2">Наименование компании</td>
			<td rowspan="2" style="width: 120px;">Наименование компаундов</td>
			<td rowspan="2" style="width: 100px;">Свет компаундов</td>
			<?php foreach ($data['monthList'] as $month => $monthName) {?>
				<?php list($month, $quarter) = explode('|', $month)?>
				<td colspan="3" class="q_<?=$quarter?>"><?=$monthName?></td>
			<? } ?>

			<td class="td_delimeter"><p></p></td>
			<td colspan="3"><a href="<?=Url::toRoute(['implementation-plan-fact','q' => 1])?>">1- квартал</a> </td>
			<td class="td_delimeter"><p></p></td>
			<td colspan="3"><a href="<?=Url::toRoute(['implementation-plan-fact','q' => 2])?>">2- квартал</a> </td>
			<td class="td_delimeter"><p></p></td>
			<td colspan="3"><a href="<?=Url::toRoute(['implementation-plan-fact','q' => 3])?>">3- квартал</a> </td>
			<td class="td_delimeter"><p></p></td>
			<td colspan="3"><a href="<?=Url::toRoute(['implementation-plan-fact','q' => 4])?>">4- квартал</a> </td>
		</tr>
		<tr class="tr_head">
			<?php foreach ($data['monthList'] as $month => $monthName) {?>
			<?php list($month, $quarter) = explode('|', $month)?>
				<td class="q_<?=$quarter?>"><p class="header_td_p">План</p></td>
				<td class="q_<?=$quarter?>"><p class="header_td_p">Факт</p></td>
				<td class="q_<?=$quarter?>"><p class="header_td_p">Разница</p></td>
			<? } ?>

			<td class="td_delimeter"><p></p></td>	
			<td><p class="header_td_p">План</p> </td>
			<td><p class="header_td_p">Факт</p> </td>
			<td><p class="header_td_p">Разница</p> </td>
			<td class="td_delimeter"><p></p></td>
			<td><p class="header_td_p">План</p> </td>
			<td><p class="header_td_p">Факт</p> </td>
			<td><p class="header_td_p">Разница</p> </td>
			<td class="td_delimeter"><p></p></td>
			<td><p class="header_td_p">План</p> </td>
			<td><p class="header_td_p">Факт</p> </td>
			<td><p class="header_td_p">Разница</p> </td>
			<td class="td_delimeter"><p></p></td>
			<td><p class="header_td_p">План</p> </td>
			<td><p class="header_td_p">Факт</p> </td>
			<td><p class="header_td_p">Разница</p> </td>

		</tr>
	</thead>

	<tbody>
		<?php 
			$i = 0; 
		?>
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
					<?php 
						$sumPlan1 = 0; 
						$sumPlan2 = 0; 
						$sumPlan3 = 0; 
						$sumPlan4 = 0; 
						$sumFact1 = 0; 
						$sumFact2 = 0; 
						$sumFact3 = 0; 
						$sumFact4 = 0; 
						$sumDiff1 = 0; 
						$sumDiff2 = 0; 
						$sumDiff3 = 0; 
						$sumDiff4 = 0; 
					?>
					<?php foreach ($data['monthList'] as $month => $monthName) {?>
						<?php list($month, $quarter) = explode('|', $month)?>
						<?php 

							$valPlan = $data['data'][$customerName][$part][$month]['plan_qty'] ?? 0; 
							$valFact = $data['data'][$customerName][$part][$month]['fact_qty'] ?? 0; 
							$valDiff = $valPlan - $valFact;

							if($quarter == 1){
								$sumPlan1 += $valPlan; 
								$sumFact1 += $valFact; 
								$sumDiff1 += $valDiff; 
							}elseif($quarter == 2){
								$sumPlan2 += $valPlan; 
								$sumFact2 += $valFact; 
								$sumDiff2 += $valDiff; 
								
							}elseif($quarter == 3){
								$sumPlan3 += $valPlan; 
								$sumFact3 += $valFact; 
								$sumDiff3 += $valDiff; 
								
							}elseif($quarter == 4){
								$sumPlan4 += $valPlan; 
								$sumFact4 += $valFact; 
								$sumDiff4 += $valDiff; 
								
							}

						?>
						<td class="td_num q_<?=$quarter?>" data-value="<?=$valPlan?>"><?=Helpers::numberFormatRemoveZero($valPlan)?></td>
						<td class="td_num q_<?=$quarter?>" data-value="<?=$valFact?>" ><?=Helpers::numberFormatRemoveZero($valFact)?></td>
						<td class="td_num q_<?=$quarter?>" data-value="<?=$valDiff?>" ><?=Helpers::numberFormatRemoveZero($valDiff)?></td>
					<? } ?>

					<td class="td_delimeter"><p></p></td>
					<td class="td_num" data-value="<?=$sumPlan1?>"><?=Helpers::numberFormatRemoveZero($sumPlan1)?></td>
					<td class="td_num" data-value="<?=$sumFact1?>" ><?=Helpers::numberFormatRemoveZero($sumFact1)?></td>
					<td class="td_num" data-value="<?=$sumDiff1?>" ><?=Helpers::numberFormatRemoveZero($sumDiff1)?></td>

					<td class="td_delimeter"><p></p></td>
					<td class="td_num" data-value="<?=$sumPlan2?>"><?=Helpers::numberFormatRemoveZero($sumPlan2)?></td>
					<td class="td_num" data-value="<?=$sumFact2?>" ><?=Helpers::numberFormatRemoveZero($sumFact2)?></td>
					<td class="td_num" data-value="<?=$sumDiff2?>" ><?=Helpers::numberFormatRemoveZero($sumDiff2)?></td>

					<td class="td_delimeter"><p></p></td>
					<td class="td_num" data-value="<?=$sumPlan3?>"><?=Helpers::numberFormatRemoveZero($sumPlan3)?></td>
					<td class="td_num" data-value="<?=$sumFact3?>" ><?=Helpers::numberFormatRemoveZero($sumFact3)?></td>
					<td class="td_num" data-value="<?=$sumDiff3?>" ><?=Helpers::numberFormatRemoveZero($sumDiff3)?></td>

					<td class="td_delimeter"><p></p></td>
					<td class="td_num" data-value="<?=$sumPlan4?>"><?=Helpers::numberFormatRemoveZero($sumPlan4)?></td>
					<td class="td_num" data-value="<?=$sumFact4?>" ><?=Helpers::numberFormatRemoveZero($sumFact4)?></td>
					<td class="td_num" data-value="<?=$sumDiff4?>" ><?=Helpers::numberFormatRemoveZero($sumDiff4)?></td>

				</tr>
			<?}?>
		<? } ?>
	</tbody>

		<tr class="tr_footer" >
			<td colspan="4">Итого</td>
			<?php 
				$sumPlan1 = 0; 
				$sumPlan2 = 0; 
				$sumPlan3 = 0; 
				$sumPlan4 = 0; 
				$sumFact1 = 0; 
				$sumFact2 = 0; 
				$sumFact3 = 0; 
				$sumFact4 = 0; 
				$sumDiff1 = 0; 
				$sumDiff2 = 0; 
				$sumDiff3 = 0; 
				$sumDiff4 = 0; 
			?>
			<?php foreach ($data['monthList'] as $month => $monthName) {?>
				<?php list($month, $quarter) = explode('|', $month)?>
				<?php 

					$valPlan = $data['data']['total'][$month]['plan_qty'] ?? 0; 
					$valFact = $data['data']['total'][$month]['fact_qty'] ?? 0; 
					$valDiff = $valPlan - $valFact;

					if($quarter == 1){
						$sumPlan1 += $valPlan; 
						$sumFact1 += $valFact; 
						$sumDiff1 += $valDiff; 
					}elseif($quarter == 2){
						$sumPlan2 += $valPlan; 
						$sumFact2 += $valFact; 
						$sumDiff2 += $valDiff; 
						
					}elseif($quarter == 3){
						$sumPlan3 += $valPlan; 
						$sumFact3 += $valFact; 
						$sumDiff3 += $valDiff; 
						
					}elseif($quarter == 4){
						$sumPlan4 += $valPlan; 
						$sumFact4 += $valFact; 
						$sumDiff4 += $valDiff; 
						
					}

				?>
				<td class="td_num q_<?=$quarter?>" data-value="<?=$valPlan?>"><?=Helpers::numberFormatRemoveZero($valPlan)?></td>
				<td class="td_num q_<?=$quarter?>" data-value="<?=$valFact?>" ><?=Helpers::numberFormatRemoveZero($valFact)?></td>
				<td class="td_num q_<?=$quarter?>" data-value="<?=$valDiff?>" ><?=Helpers::numberFormatRemoveZero($valDiff)?></td>
			<? } ?>

			<td class="td_delimeter"><p></p></td>
			<td class="td_num" data-value="<?=$sumPlan1?>"><?=Helpers::numberFormatRemoveZero($sumPlan1)?></td>
			<td class="td_num" data-value="<?=$sumFact1?>" ><?=Helpers::numberFormatRemoveZero($sumFact1)?></td>
			<td class="td_num" data-value="<?=$sumDiff1?>" ><?=Helpers::numberFormatRemoveZero($sumDiff1)?></td>

			<td class="td_delimeter"><p></p></td>
			<td class="td_num" data-value="<?=$sumPlan2?>"><?=Helpers::numberFormatRemoveZero($sumPlan2)?></td>
			<td class="td_num" data-value="<?=$sumFact2?>" ><?=Helpers::numberFormatRemoveZero($sumFact2)?></td>
			<td class="td_num" data-value="<?=$sumDiff2?>" ><?=Helpers::numberFormatRemoveZero($sumDiff2)?></td>

			<td class="td_delimeter"><p></p></td>
			<td class="td_num" data-value="<?=$sumPlan3?>"><?=Helpers::numberFormatRemoveZero($sumPlan3)?></td>
			<td class="td_num" data-value="<?=$sumFact3?>" ><?=Helpers::numberFormatRemoveZero($sumFact3)?></td>
			<td class="td_num" data-value="<?=$sumDiff3?>" ><?=Helpers::numberFormatRemoveZero($sumDiff3)?></td>

			<td class="td_delimeter"><p></p></td>
			<td class="td_num" data-value="<?=$sumPlan4?>"><?=Helpers::numberFormatRemoveZero($sumPlan4)?></td>
			<td class="td_num" data-value="<?=$sumFact4?>" ><?=Helpers::numberFormatRemoveZero($sumFact4)?></td>
			<td class="td_num" data-value="<?=$sumDiff4?>" ><?=Helpers::numberFormatRemoveZero($sumDiff4)?></td>

		</tr>

		


	
  </table>

</div>

<?php
$docReadyJs = <<< JS
$(document).ready(function() {

	var curr_quarter = '$q';

	$('#download-xls').on('click', function (e) {
		html_xls_export('fix_table', '$downloadFileName');
  	});	

	$('.td_num').each(function() {
		var cellVal = $(this).attr('data-value');    
		if(cellVal == '0'){
			$(this).addClass('td_zero');
		}
		if(cellVal < 0){
			$(this).addClass('td_negative');
		}
	});

	if(curr_quarter == 1){
		$('.q_2').hide();
		$('.q_3').hide();
		$('.q_4').hide();
	}

	if(curr_quarter == 2){
		$('.q_1').hide();
		$('.q_3').hide();
		$('.q_4').hide();
	}

	if(curr_quarter == 3){
		$('.q_1').hide();
		$('.q_2').hide();
		$('.q_4').hide();
	}

	if(curr_quarter == 4){
		$('.q_1').hide();
		$('.q_2').hide();
		$('.q_3').hide();
	}

	

}) 
JS;
$this->registerJs($docReadyJs);
?>