<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Materials in pipeline');
$this->params['breadcrumbs'][] = $this->title;

// echo '<pre>';
// print_r($total);
// echo '</pre>';
// echo '<pre>';
// print_r($data);
// echo '</pre>';



?>
<style>
	.table-doh th,
	.table-doh td {
		text-align: center;
		vertical-align: middle !important;
	}
	.table-doh th{
		background-color: #e8ebf3;
	}
	.table-doh .tr_total th{
		background-color: #bfc2c5;
	}
	.tr_total{
		font-weight: bold;
	}
</style>
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
<div class="req-index">
	<div class="row">
		<div id="div_fix_table" class="col-lg-10 col-lg-offset-1">


			<table id="fix_table" class="table table-hover table-bordered table-doh">
				<thead>
					<tr>
					 	<th rowspan="2" style="width: 70px;"><?=Yii::t('app', '#')?></th>
						<th rowspan="2" ><?=Yii::t('app','Country')?></th>
						<th rowspan="2" ><?=Yii::t('app','Supplier')?></th>
						<th colspan="2"><?=Html::a(Yii::t('app','DOH < {cnt} days',['cnt' => Yii::$app->params['less_dates_count']]),Url::toRoute(['report/doh-detail','type' => 'l60']), ['target' => '_blank'])?></th>
						<th colspan="2"><?=Html::a(Yii::t('app','DOH > {cnt} days',['cnt' => Yii::$app->params['greater_dates_count']]),Url::toRoute(['report/doh-detail','type' => 'g120']), ['target' => '_blank'])?></th>
					</tr>
					<tr>
						<th style="width: 120px;"><?=Yii::t('app','Number of items')?></th>
						<th style="width: 120px;"><?=Yii::t('app','Amount ($)')?></th>
						<th style="width: 120px;"><?=Yii::t('app','Number of items')?></th>
						<th style="width: 120px;"><?=Yii::t('app','Amount ($)')?></th>
					</tr>
					<tr class="tr_total">
						<th></th>
						<th><?=$total['countries']?></th>
						<th><?=$total['suppliers']?></th>
						<th><?=$total['less60']?></th>
						<th><?=($total['less60Amount']!=0) ? number_format(round($total['less60Amount']),0,'.',' ') : 0?></th>
						<th><?=$total['greater120']?></th>
						<th><?=($total['greater120Amount']!=0) ? number_format(round($total['greater120Amount']),0,'.',' ') : 0?></th>
					</tr>
					</thead>
					<tbody>
					<? $i = 0; ?>
					<?foreach ($data as $row) {?>
						<?
							if($row['unknown'] == '1') continue;
						?>
						<tr>
							<td><?=++$i?></td>
							<td style="text-align: left;"><?=Html::a($row['country'],Url::toRoute(['report/doh-detail','type' => 'country','id' => $row['country_id']]), ['target' => '_blank'])?></td>
							<td style="text-align: left;"><?=Html::a($row['supplier'],Url::toRoute(['report/doh-detail','type' => 'supplier','id' => $row['supplier_id']]), ['target' => '_blank'])?></td>
							<td class = "<?if($row['less60']>0)echo" danger";elseif($row['less60']==0)echo " success";?>"><?=$row['less60']?></td>
							<td><?=($row['less60Amount']!=0) ? number_format(round($row['less60Amount']),0,'.',' ') : 0?></td>
							<td class = "<?if($row['greater120']>0)echo" danger";elseif($row['greater120']==0)echo " success";?>"><?=$row['greater120']?></td>
							<td><?=($row['greater120Amount']!=0) ? number_format(round($row['greater120Amount']),0,'.',' ') : 0?></td>
						</tr>
					<?}?>
					<?foreach ($data as $row) {?>
						<?
							if($row['unknown'] != '1') continue;
						?>
						<tr>
							<td><?=++$i?></td>
							<td style="text-align: left;"><?=Html::a($row['country'],Url::toRoute(['report/doh-detail','type' => 'country','id' =>  $row['country_id']]), ['target' => '_blank'])?></td>
							<td style="text-align: left;"><?=Html::a($row['supplier'],Url::toRoute(['report/doh-detail','type' => 'supplier','id' => $row['supplier_id']]), ['target' => '_blank'])?></td>
							<td class = "<?if($row['less60']>0)echo" danger";elseif($row['less60']==0)echo " success";?>"><?=$row['less60']?></td>
							<td><?=($row['less60Amount']!=0) ? number_format(round($row['less60Amount'],1),1,'.',' ') : 0?></td>
							<td class = "<?if($row['greater120']>0)echo" danger";elseif($row['greater120']==0)echo " success";?>"><?=$row['greater120']?></td>
							<td><?=($row['greater120Amount']!=0) ? number_format(round($row['greater120Amount'],1),1,'.',' ') : 0?></td>
						</tr>
					<?}?>

					
					
				
				</tbody>
			</table>
		</div>
	</div>


</div>

<?php
$docReadyJs = <<< JS
$(document).ready(function() {

	$('#fix_table').tableFixer({
	  'left' : 0,
	  'foot' : true,
	  'head' : true
	});
	
	changeHeight();
  
	$(window).resize(function(){
		changeHeight();
  });
  
	function changeHeight(){
    window_h = $(window).height();
    table_h = window_h - 220;
    // console.log(window_h+"-"+table_h);
    $('#div_fix_table').height(table_h+'px');
  }


	$('#download-xls').on('click', function (e) {
		html_xls_export('fix_table', '$downloadFileName');
	});	
	
}) 
JS;
$this->registerJs($docReadyJs);
?>