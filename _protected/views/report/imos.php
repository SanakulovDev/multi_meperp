<?php

use app\components\Helpers;
use app\models\PartOrder;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Import materials order statust');
$this->params['breadcrumbs'][] = $this->title;

$loading = '<img src="/themes/adminlte/img/loading.gif">';


?>

<?= $this->render('../common/_loading'); ?>


<div class="req-index">


	<div class="panel">
		<div class="panel-heading">
			<img style="height:28px;" src="/img/logo.jpg" title="<?php echo Yii::$app->params['comp_name'] ?>" class="pull-left" />
			<h3 class="pull-left" style="margin: 5px 0px -5px 10px;">
				<?= Yii::t('app', 'Import materials order status') ?>
			</h3>
			<p class="pull-right" style="margin: 0px">
				<?//= Html::a(Yii::t('app', 'btn-download'), ['download-weekly-requirement'], ['class' => 'btn btn-info btn-sm', 'id' => 'btnDownload']) ?>
			</p>
			<div style="clear: both;"></div>
		</div>
		<div class="panel-body">


			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true" id="tabBtn_1">
							<h4 style="margin: 5px 0px -5px 0px;"><b>QUANTITY</b></h4>
						</a></li>
					<li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false" id="tabBtn_2">
							<h4 style="margin: 5px 0px -5px 0px;"><b>AMOUNT</b></h4>
						</a></li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="tab_1">
						<table class="table table-req" id="fix_table_w">
							<thead>
								<tr class="tr_head_imos">
									<th colspan="5"></th>
									<th colspan="10"></th>
									<? foreach($data['months'] as $key => $month){ ?>
										<? $colspan = ($key === 0) ? 5 : 5?>
										<th colspan="<?=$colspan?>" class="text-left" style="height: 30px;"><?= Yii::t('app', date('F',strtotime($month)));?></th>
									<? } ?>
								</tr>
								<tr class="tr_head_imos">
									<th colspan="5" rowspan="2"></th>
									<th colspan="9" rowspan="2"></th>
									<th rowspan="2" class="text-right"><?= Yii::t('app', 'Total balance') ?></th>
									<? foreach($data['months'] as $key => $month){ ?>
										<?if($key !== 0){?>
											<th rowspan="2" style="width: 90px;" class="text-center"><?= Yii::t('app', 'Stock') ?></th>
										<?}?>
										<?if($key === 0){?>
											<th rowspan="2" style="width: 90px;" class="text-center"><?= Yii::t('app', 'Open orders') ?></th>
										<?}?>
										<th rowspan="2" style="width: 90px;" class="text-center"><?= Yii::t('app', 'Plan') ?></th>
										<th colspan="3" class="text-center"><?= Yii::t('app', 'Order') ?></th>
									<? } ?>
								</tr>
								<tr class="tr_head_imos">
									<? foreach($data['months'] as $key => $month){ ?>
										<th style="width: 90px;" class="text-center"><?= Yii::t('app', 'All') ?></th>
										<th style="width: 90px;" class="text-center"><?= Yii::t('app', 'Shipped') ?></th>
										<th style="width: 90px;" class="text-center"><?= Yii::t('app', 'Balance') ?></th>
									<? } ?>
								</tr>
								<tr class="tr_head_imos">
									<th style="width: 30px;" class="text-center">№</th>
									<th style="width: 100px;" class="text-center"><?= Yii::t('app', 'Part') ?></th>
									<th style="width: 100px;" class="text-center"><?= Yii::t('app', 'Part color') ?></th>
									<th><?= mb_strtoupper(Yii::t('app', 'Part name')) ?></th>
									<th style="width: 100px;" class="text-center"><?= Yii::t('app', 'Contract source') ?></th>
									<th style="width: 100px;" class="text-center"><?= Yii::t('app', 'Model') ?></th>
									<th style="width: 100px;" class="text-center"><?= Yii::t('app', 'Type') ?></th>
									<th style="width: 100px;" class="text-center"><?= Yii::t('app', 'Unit') ?></th>
									<th style="width: 100px;" class="text-center"><?= Yii::t('app', 'Supplier') ?></th>
									<th style="width: 100px;" class="text-center"><?= Yii::t('app', 'Country code') ?></th>
									<th style="width: 100px;" class="text-center"><?= Yii::t('app', 'Leed time (Day)') ?></th>
									<th style="width: 100px;" class="text-center"><?= Yii::t('app', 'Currency') ?></th>
									<th style="width: 100px;" class="text-center"><?= Yii::t('app', 'Price') ?></th>
									<th style="width: 100px;" class="text-center"><?= Yii::t('app', 'MOQ') ?></th>
									<th><p style="width: 60px;" class="text-center"><?= Yii::t('app', 'Qty') ?></p></th>
									<? foreach($data['months'] as $key => $month){ ?>
										<?if($key !== 0){?>
											<th><p style="width: 60px;" class="text-center"><?= Yii::t('app', 'Qty') ?></p></th>
										<?}?>
										<?if($key === 0){?>
											<th><p style="width: 60px;" class="text-center"><?= Yii::t('app', 'Qty') ?></p></th>
										<?}?>
										<th><p style="width: 60px;" class="text-center"><?= Yii::t('app', 'Qty') ?></p></th>
										<th><p style="width: 60px;" class="text-center"><?= Yii::t('app', 'Qty') ?></p></th>
										<th><p style="width: 60px;" class="text-center"><?= Yii::t('app', 'Qty') ?></p></th>
										<th><p style="width: 60px;" class="text-center"><?= Yii::t('app', 'Qty') ?></p></th>
									<? } ?>
								</tr>
							</thead>
							<tbody>
							<? $i = 0; ?>
							<? foreach($data['data'] as $row){
								$i++; ?>
								<tr <?=($i%2 == 0) ? 'class="tr_odd"' : ''?>>
									<td class="text-center"><?=$i?></td>
									<td class="text-left"><?=$row['part_no']?></td>
									<td class="text-left"><?=$row['part_color']?></td>
									<td style="max-width: 150px;" class="td-nowrap"><?=mb_strtoupper($row['part_name'])?></td>
									<td class="text-center"><?=$row['contract_source']?></td>
									<td class="text-center"><?= $row['model'] ?></td>
									<td class="text-center"><?= $row['part_type'] ?></td>
									<td class="text-center"><?= $row['uom'] ?></td>
									<td style="max-width: 150px;" class="td-nowrap"><?=mb_strtoupper($row['supplier'])?></td>
									<td class="text-center"><?= $row['country'] ?></td>
									<td class="text-center"><?= $row['lead_time'] ?></td>
									<td class="text-center"><?= $row['currency'] ?></td>
									<td class="text-right"><?=Helpers::numberFormatRemoveZero($row['price'],2,'.','')?></td>
									<td class="text-right  <?=($row['moq'] == 0) ? 'qty-zero' : 'qty-bold'?>">
										<?=Helpers::numberFormatRemoveZero($row['moq'],2,'.','')?>
									</td>
									<td class="text-right  <?=($row['cbal'] == 0) ? 'qty-zero' : 'qty-bold'?>">
										<?=Helpers::numberFormatRemoveZero($row['cbal'],2,'.','')?>
									</td>

									<? foreach($data['months'] as $key => $month){ ?>
										<?if($key !== 0){?>
											<td class="text-right  <?=($row['months'][$key]['stock'] == 0) ? 'qty-zero' : 'qty-bold'?>">
												<?=Helpers::numberFormatRemoveZero($row['months'][$key]['stock'],2,'.','')?>
											</td>
										<?}?>
										<?if($key === 0){?>
											<td class="text-right  <?=($row['open_orders'] == 0) ? 'qty-zero' : 'qty-bold'?>">
												<?=Helpers::numberFormatRemoveZero($row['open_orders'],2,'.','')?>
											</td>
										<?}?>
										<td class="text-right  <?=($row['months'][$key]['req_qty'] == 0) ? 'qty-zero' : 'qty-bold'?>">
											<?=Helpers::numberFormatRemoveZero($row['months'][$key]['req_qty'],2,'.','')?>
										</td>
										<td class="text-right  <?=($row['months'][$key]['order_qty'] == 0) ? 'qty-zero' : 'qty-bold'?>">
											<?=Helpers::numberFormatRemoveZero($row['months'][$key]['order_qty'],2,'.','')?>
										</td>
										<td class="text-right  <?=($row['months'][$key]['inv_qty'] == 0) ? 'qty-zero' : 'qty-bold'?>">
											<?=Helpers::numberFormatRemoveZero($row['months'][$key]['inv_qty'],2,'.','')?>
										</td>
										<td class="text-right  <?=($row['months'][$key]['open_order'] == 0) ? 'qty-zero' : 'qty-bold'?>">
											<?=Helpers::numberFormatRemoveZero($row['months'][$key]['open_order'],2,'.','')?>
										</td>
									<? } ?>

								</tr>
							<? } ?>
							</tbody>
						
						</table>
					</div>
					<!-- /.tab-pane -->
					<div class="tab-pane" id="tab_2">
						<table class="table table-req" id="fix_table_d">
											
						soon ...
											
						</table>
					</div>

				</div>
				<!-- /.tab-content -->
			</div>


		</div>
	</div>

</div>


<?
	$routeDaily = yii\helpers\Url::toRoute(['download-daily-requirement']);
	$routeWeekly = yii\helpers\Url::toRoute(['download-weekly-requirement']);
	
	$script = <<< JS
	
	$('#fix_table_w').tableFixer({'left' : 5});
	$('#fix_table_d').tableFixer({'left' : 5});
	
	changeHeightW();
	changeHeightD();
	
	$(window).resize(function(){
		changeHeightW();
	});

	$(window).resize(function(){
		changeHeightD();
	});
        
        
	$('#tabBtn_1').on('click', function () {
			$('#btnDownload').attr('href','$routeWeekly')
	})
	
	$('#tabBtn_2').on('click', function () {
			$('#btnDownload').attr('href','$routeDaily')
	})
	
	$('#loading').hide();

	
	
	function changeHeightW(){
		window_h = $(window).height();
		table_h = window_h - 200;
		$('#tab_1').height(table_h+'px');
	}
	
	function changeHeightD(){
		window_h = $(window).height();
		table_h = window_h - 200;
		$('#tab_2').height(table_h+'px');
	}

	
JS;

	$this->registerJs($script);

?>