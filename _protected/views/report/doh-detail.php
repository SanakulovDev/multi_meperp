<?
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Detailed materials in pipeline') . ' (' . $title . ')';
$this->params['breadcrumbs'][] = $this->title;

?>
<style>
	.table-doh th,
	.table-doh td {
		text-align: center;
		vertical-align: middle !important;
	}

	.table-doh th {
		background-color: #e8ebf3;
	}

	.table-doh .tr_total th {
		background-color: #bfc2c5;
	}

	.tr_total {
		font-weight: bold;
	}
	.td-nowrap{
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
}
</style>
<div class="row">
	<div class="col-md-2 col-lg-2">
		<?= Html::a(Yii::t('app', 'btn-back'), ['report/doh'], ['class' => 'btn btn-default btn-xs']) ?>
	</div>
	<div class="col-md-10 col-lg-10">
		<div class="pull-right">
			<?= Html::button(Yii::t('app', 'btn-download-delivery-plan'), ['class' => 'btn btn-info btn-xs', 'id' => 'download-xls']) ?>
		</div>
	</div>
</div>
<br>
<div class="req-index">
	<div class="row">
		<div id="div_fix_table" class="col-lg-12">


			<table id="fix_table" class="table table-bordered table-doh">
				<thead>

					<tr>
						<th style="width: 50px;"><?= Yii::t('app', '#') ?></th>
						<th ><?= Yii::t('app', 'Country') ?></th>
						<th ><?= Yii::t('app', 'Supplier') ?></th>
						<th style="width: 120px;"><?= Yii::t('app', 'Part number') ?></th>
						<th ><?= Yii::t('app', 'Part name') ?></th>
						<th style="width: 100px;"><?= Yii::t('app', 'Contract source') ?></th>
						<th style="width: 100px;"><?= Yii::t('app', 'Model') ?></th>
						<th style="width: 100px;"><?= Yii::t('app', 'Unit') ?></th>
						<th style="width: 100px;"><?= Yii::t('app', 'Price') ?></th>
						<th style="width: 100px;"><?= Yii::t('app', 'Total stock') ?></th>
						<th style="width: 100px;"><?= Yii::t('app', 'DOH') ?></th>
						<th style="width: 100px;"><?= Yii::t('app', 'Average usage') ?></th>

						<?if($type == 'l60' or $type == 'g120'){?>

							<?if($type != 'l60'){?>
								<th style="width: 100px;"><?= Yii::t('app', 'Average qty for {cnt} days', ['cnt' => Yii::$app->params['greater_dates_count']])?></th>
							<?}?>
							
							<th style="width: 100px;"><?= ($type == 'l60') ? Yii::t('app', 'Required quantity') : Yii::t('app', 'Temporarily frozen amount')?></th>
							<th style="width: 100px;"><?= Yii::t('app', 'Amount ($)') ?></th>

						<?}else{?>

							<th style="width: 100px;"><?= Yii::t('app', 'Required quantity for {cnt}th day', ['cnt' => Yii::$app->params['less_dates_count']]) ?></th>
							<th style="width: 100px;"><?= Yii::t('app', 'Amount ($) {cnt}', ['cnt' => Yii::$app->params['less_dates_count']]) ?></th>	

							<th style="width: 100px;"><?= Yii::t('app', 'Average qty for {cnt} days', ['cnt' => Yii::$app->params['greater_dates_count']])?></th>
							<th style="width: 100px;"><?= Yii::t('app', 'Temporarily frozen amount for {cnt} days', ['cnt' => Yii::$app->params['greater_dates_count']]) ?></th>
							<th style="width: 100px;"><?= Yii::t('app', 'Amount ($) {cnt}', ['cnt' => Yii::$app->params['greater_dates_count']]) ?></th>	

						<?}?>

					</tr>
				</thead>
				<tbody>
					<? $i = 0; ?>
					<?
					foreach ($result as $res) {
						$coverage = $res['coverage'];
						$part = $coverage->part;
					?>
						<tr>
							<td><?= ++$i ?></td>
							<td style="text-align: left; max-width: 200px;" class="td-nowrap" title="<?=  htmlspecialchars($res['country'])  ?>"><?= $res['country'] ?></td>
							<td style="text-align: left; max-width: 200px;" class="td-nowrap" title="<?=  htmlspecialchars($res['supplier']) ?>"><?= $res['supplier'] ?></td>
							<td style="text-align: center;"><?= $part->part_no ?></td>
							<td style="text-align: left; max-width: 200px;"  class="td-nowrap" title="<?= htmlspecialchars($part->part_name) ?>"><?= $part->part_name ?></td>
							<td style="text-align: center;"><?= $part->contractSource->name ?></td>
							<td style="text-align: center;"><?= $part->productModel->description ?? ''?></td>
							<td style="text-align: center;"><?= $part->unit->unit_value ?></td>
							<td style="text-align: right;"><?= round($res['price'],2)?></td>
							<td style="text-align: right;"><?= round($coverage->totalstock) ?></td>
							<td style="text-align: right;"><?= round($coverage->doh) ?></td>
							<td style="text-align: right;"><?= round($part->averageUsage) ?></td>

							<?if($type == 'l60' or $type == 'g120'){?>

								<?if($type != 'l60'){?>
									<td style="text-align: right;"><?= round($res['daysQty']) ?></td>
								<?}?>
								<td style="text-align: right;"><?= round($res['needQty']) ?></td>
								<td style="text-align: right;"><?= round($res['amount']) ?></td>
							
								<?}else{
											$needQty60 = 0;
											$amount60 = 0;
											$daysQty120 = 0;
											$needQty120 = 0;
											$amount120 = 0;

											if ($res['lg'] == 'l60') {
													$needQty60 = $res['needQty'];
													$amount60 = $res['amount'];
											} else {
													$daysQty120 = $res['daysQty'];
													$needQty120 = $res['needQty'];
													$amount120 = $res['amount'];
											}
									

									?>

									<td style="text-align: right;"><?= round($needQty60) ?></td>
									<td style="text-align: right;"><?= round($amount60) ?></td>
									
									<td style="text-align: right;"><?= round($daysQty120) ?></td>
									<td style="text-align: right;"><?= round($needQty120) ?></td>
									<td style="text-align: right;"><?= round($amount120) ?></td>

								<?}?>

						</tr>
					<? } ?>

				<tbody>
				</tbody>
			</table>
		</div>
	</div>


</div>

<?php
$docReadyJs = <<< JS
$(document).ready(function() {

	$('#fix_table').tableFixer({
	  'left' : 8,
	  'foot' : true
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