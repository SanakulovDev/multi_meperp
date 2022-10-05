<?php
	use yii\helpers\Html;

	/* @var $this yii\web\View */
	/* @var $searchModel app\models\ReqSearch */
	/* @var $dataProvider yii\data\ActiveDataProvider */
	$this->title = Yii::t('app', 'In transit materials report');
	$this->params['breadcrumbs'][] = $this->title;
	$period = app\components\Helpers::getPeriod60Days();
  $loading = '<img src="/themes/adminlte/img/loading.gif">';
?>

<?= $this->render('../common/_loading'); ?>
	<div class="req-index">


		<div class="panel">
			<div class="panel-heading">
                <img style="height:28px;" src="/img/mep.png" title="<?php echo Yii::$app->params['comp_name'] ?>" class="pull-left"/>
				<h3 class="pull-left" style="margin: 5px 0px -5px 10px;">
					<?=Yii::t('app', 'In transit materials report')?>
          <span id="calc_at" style="font-size: 14px;color: #a29393;"><?=$loading?></span>
				</h3>
				<p class="pull-right" style="margin: 0px">
					<?=Html::a(Yii::t('app', 'btn-download'), ['download-in-transit'], ['class' => 'btn btn-info btn-sm'])?>
				</p>
				<div style="clear: both;"></div>
			</div>
			<div class="panel-body" id="div_fix_table">
				<table class="table table-req" id="fix_table">
					<thead>
					<tr class="tr_head">
						<th style="width: 30px;" class="text-center">№</th>
						<th style="width: 100px;" class="text-center"><?=Yii::t('app', 'Part')?></th>
						<th style="width: 100px;" class="text-center"><?=Yii::t('app', 'Part color')?></th>
						<th><?=mb_strtoupper(Yii::t('app', 'Part name'))?></th>
						<th style="width: 100px;" class="text-center"><?=Yii::t('app', 'Type')?></th>
						<th style="width: 100px;" class="text-center"><?=Yii::t('app', 'Model')?></th>
						<th style="width: 100px;" class="text-center"><?=Yii::t('app', 'Unit')?></th>
						<? foreach($period as $col => $per){ ?>
							<th style="width: 90px;" class="text-center"><?=date('d.m', strtotime($per['plandate']))?></th>
						<? } ?>
					</tr>
					</thead>
					<tbody>
					<? $i = 0; ?>
					<? foreach($data as $row){ ?>
						<tr <?=($i++%2 == 0) ? 'class="tr_odd"' : ''?>>
							<td class="text-center"><?=$i?></td>
							<td class="text-center"><?=$row['part']->part_no?></td>
							<td class="text-center" title="<?=$row['part']->remark?>"><?=$row['part']->part_color?></td>
							<td style="max-width: 150px;" class="td-nowrap"><?=mb_strtoupper($row['part']->part_name)?></td>
							<td><?=$row['part']->contractSource->name;?></td>
							<td><?=$row['part']->productModel->modelname ?? ''?></td>
							<td><?=$row['part']->unit->unit_value;?></td>
							<? foreach($period as $col => $per){ ?>
								<td class="text-right <?=($row[$per['plandate']] != 0) ? 'qty-green' : 'qty-zero'?>">
									<? //= Helpers::formatRemoveDecimal($row[$per['plandate']])?>
									<?=$row[$per['plandate']]?>
								</td>
							<? } ?>

						</tr>

					<? } ?>
					</tbody>
				</table>
			</div>
		</div>

	</div>


<?
  $calc_at = date('Y-m-d H:i');
	$script1 = <<< JS
	
	$('#fix_table').tableFixer({'left' : 4});
	changeHeight();
	$(window).resize(function(){
		changeHeight();
	});
	function changeHeight(){
		window_h = $(window).height();
		table_h = window_h - 100;
		// console.log(window_h+"-"+table_h);		
		$('#div_fix_table').height(table_h+'px');
	}
        
	$('#calc_at').html('($calc_at)'); 
	
	$('#loading').hide();

	
JS;
	$this->registerJs($script1);
?>