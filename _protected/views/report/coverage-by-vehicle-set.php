<?php

use app\components\Helpers;
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'UzAuto visibility report');
$this->params['breadcrumbs'][] = $this->title;


?>
<style>
	.table-doh th,
	.table-doh td {
		vertical-align: middle !important;
		border-color: #dcdcda !important;
	}


	.table-doh th{
		background-color: #e1f4f7;
		text-align: center;
	}
	.table-doh .tr_total th{
		background-color: #bfc2c5;
	}
	.tr_total{
		font-weight: bold;
	}
	.td-red{
		background-color: #fbe3e7;
	}
	.mln{
		text-align: right;
		font-size: 12px;
	}

  .tr_doh td{
    font-weight: bold;
    font-size: 18px;
  }
  .doh_sum{
    font-size: 28px !important;
    font-weight: normal !important;
    text-align: center;
  }
  .tr_space td{
    border: 0px !important;
    height: 5px !important;
    padding: 0px !important;
  }
  .table-doh{
    border: 0px !important; 
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

			<div class="mln">(<?=Yii::t('app','in vehicle sets')?>)</div>
			<table id="fix_table" class="table table-bordered table-doh">
				<tbody>
					<tr>
            <th><?=Yii::t('app','Description')?></th>
            <?foreach($models as $model){?>
              <th  style="width: 100px;" ><?=$model->description?></th>
            <?}?>
						<th  style="width: 100px;" ><?=Yii::t('app','Total')?></th>
						<th  style="width: 120px;" ><?=Yii::t('app','Covered until')?></th>
          </tr>
          
					<tr>
            <td><?=Yii::t('app','On hand stock')?></td>
            <? $totalStock = 0; $totalByModel = [];?>
            <?foreach($models as $model){?>
              <td class="text-right"><?= number_format($data['stock'][$model->id],0,'',' ') ?? 0?></td>
              <?
								$totalStock += $data['stock'][$model->id];
								$totalByModel[$model->id] = $data['stock'][$model->id] ?? 0;
              ?>
            <?}?>
						<td class="text-right" ><?=number_format($totalStock,0,'',' ')?></td>
						<td class="text-center" ><?=$dates['DS']?></td>
					</tr>
					<tr>
            <td><?=Yii::t('app','In transit stock')?></td>
            <? $totalIntransit = 0; ?>
            <?foreach($models as $model){?>
							<td class="text-right"><?= number_format($data['intransit'][$model->id],0,'',' ') ?? 0?></td>
              <?
                $totalIntransit += $data['intransit'][$model->id];
								$totalByModel[$model->id] += $data['intransit'][$model->id] ?? 0;
              ?>
            <?}?>
						<td class="text-right" ><?=number_format($totalIntransit,0,'',' ')?></td>
						<td class="text-center" ><?=$dates['D']?></td>
					</tr>
					<tr>
            <td><?=Yii::t('app','Paid not shipped order')?></td>
            <? $totalOrders = 0; ?>
            <?foreach($models as $model){?>
							<td class="text-right"><?= number_format($data['orders'][$model->id],0,'',' ') ?? 0?></td>
              <?
                $totalOrders += $data['orders'][$model->id];
								$totalByModel[$model->id] += $data['orders'][$model->id] ?? 0;
              ?>
            <?}?>
						<td class="text-right" ><?=number_format($totalOrders,0,'',' ')?></td>
						<td class="text-center" ><?=$dates['DO']?></td>
          </tr>
					</tr>
					<tr class="tr_total">
            <td class="td-red"><?=Yii::t('app','Total pipeline stock')?></td>
            <?foreach($models as $model){?>
							<td class="text-right td-red"><?= number_format($totalByModel[$model->id],0,'',' ') ?? 0?></td>
            <?}?>
						<td class="text-right td-red" ><?=number_format($totalStock + $totalIntransit + $totalOrders,0,'',' ')?></td>
						<td class="text-center td-red" ></td>
          </tr>
          <tr class="tr_space">
            <td></td>
          </tr>
          <tr class="tr_doh">
            <td colspan="2"><?=Yii::t('app','Cash required to cover {cnt} DOH (in USD)',['cnt' => Yii::$app->params['less_dates_count']])?></td>
            <td colspan="2" class="doh_sum td-red"><?= number_format($dohData['less60Amount'],0,'',' ') ?? 0?></td>
          </tr>
          <tr class="tr_doh">
            <td colspan="2"><?=Yii::t('app','Excessive cash over {cnt} DOH (in USD)',['cnt' => Yii::$app->params['greater_dates_count']])?></td>
            <td colspan="2" class="doh_sum td-red"><?= number_format($dohData['greater120Amount'],0,'',' ') ?? 0?></td>
          </tr>
					
				
						
					
					
				
				</tbody>
			</table>
		</div>
	</div>


</div>

<?php
$docReadyJs = <<< JS
$(document).ready(function() {
	$('#download-xls').on('click', function (e) {
		html_xls_export('fix_table', '$downloadFileName');
	});	
}) 
JS;
$this->registerJs($docReadyJs);
?>