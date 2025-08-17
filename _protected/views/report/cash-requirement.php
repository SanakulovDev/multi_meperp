<?php

use app\components\Helpers;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Cash requirement for import shipments');
$this->params['breadcrumbs'][] = $this->title;
$period_weekly = app\components\Helpers::getPeriodWeek6Month();
$period_daily = [];
foreach (app\components\Helpers::getPeriodFull() as $pdate) {
	if ($pdate > date('Y-m-t', strtotime('+6 month'))) break;
	$period_daily[] = $pdate;
}

$loading = '<img src="/themes/adminlte/img/loading.gif">';


?>

<?= $this->render('../common/_loading'); ?>


<div class="req-index">


	<div class="panel">
		<div class="panel-heading">
			<img style="height:28px;" src="/img/logo.jpg" title="<?php echo Yii::$app->params['comp_name'] ?>" class="pull-left" />
			<h3 class="pull-left" style="margin: 5px 0px -5px 10px;">
				<?= Yii::t('app', 'Cash requirement for import shipments') ?>
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
							<h4 style="margin: 5px 0px -5px 0px;"><b>WEEKLY</b></h4>
						</a></li>
					<li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false" id="tabBtn_2">
							<h4 style="margin: 5px 0px -5px 0px;"><b>DAILY</b></h4>
						</a></li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="tab_1">
						<div colspan="10" class="mln text-right" style="font-size: 12px;" >(<?=Yii::t('app','in million UZS')?>)</div>
						<table class="table table-req" id="fix_table_w">
							<thead>
								<tr class="tr_head">
									<th style="width: 30px;" class="text-center">№</th>
									<th><?= Yii::t('app', 'Country') ?></th>
									<th><?= Yii::t('app', 'Supplier') ?></th>
									<th style="width: 100px;" class="text-center"><?= Yii::t('app', 'Payment term') ?></th>
									<th style="width: 100px;" class="text-center"><?= Yii::t('app', 'Currency') ?></th>
									<? foreach($period_weekly as $col => $per){ ?>
									<th style="width: 90px;" class="text-center"><?= ((strlen(trim($per['plandate'])) > 7)) ? date("d.m", strtotime($per['from'])) . '<br>-<br>' . date("d.m", strtotime($per['to'])) : date("m.Y", strtotime($per['plandate'])) ?></th>
									<? } ?>
								</tr>
							</thead>
							<tbody>
								<? $i = 0; ?>
								<? foreach($data as $row){
								$i++; ?>
								<tr <?= ($i % 2 == 0) ? 'class="tr_odd"' : '' ?>>
									<td class="text-center"><?= $i ?></td>
									<td style="max-width: 150px;" class="text-left td-nowrap"><?= $row['country'] ?></td>
									<td style="max-width: 120px;" class="text-left td-nowrap"><?= $row['supplier'] ?></td>
									<td class="text-center"><?= $row['paymentTerm'] ?></td>
									<td class="text-center"><?= $row['currency'] ?></td>

									<? foreach($period_weekly as $col => $per){ ?>
									<td class="text-right  <?= ($row['weekly'][$per['plandate']] == 0) ? 'qty-zero' : 'qty-bold' ?>">
										<?= Helpers::formatRemoveDecimal($row['weekly'][$per['plandate']]) ?>
									</td>
									<? } ?>

								</tr>
								<? } ?>
							</tbody>
						</table>
					</div>
					<!-- /.tab-pane -->
					<div class="tab-pane" id="tab_2">
						<div colspan="10" class="mln text-right" style="font-size: 12px;" >(<?=Yii::t('app','in million UZS')?>)</div>
						<table class="table table-req" id="fix_table_d">
							<thead>
								<tr class="tr_head">
									<th style="width: 30px;" class="text-center">№</th>
									<th><?= Yii::t('app', 'Country') ?></th>
									<th><?= Yii::t('app', 'Supplier') ?></th>
									<th style="width: 100px;" class="text-center"><?= Yii::t('app', 'Payment term') ?></th>
									<th style="width: 100px;" class="text-center"><?= Yii::t('app', 'Currency') ?></th>
									<? foreach($period_daily as $col => $pdate){ ?>
									<th style="width: 60px;" class="text-center"><?= date("d.m", strtotime($pdate)) ?></th>
									<? } ?>
								</tr>
							</thead>
							<tbody>
								<? $i = 0; ?>
								<? foreach($data as $row){
								$i++; ?>
								<tr <?= ($i % 2 == 0) ? 'class="tr_odd"' : '' ?>>
									<td class="text-center"><?= $i ?></td>
									<td style="max-width: 150px;" class="text-left td-nowrap"><?= $row['country'] ?></td>
									<td style="max-width: 120px;" class="text-left td-nowrap"><?= $row['supplier'] ?></td>
									<td class="text-center"><?= $row['paymentTerm'] ?></td>
									<td class="text-center"><?= $row['currency'] ?></td>

									<? foreach($period_daily as $col => $pdate){ ?>
									<td class="text-right  <?= ($row['daily'][$pdate] == 0) ? 'qty-zero' : 'qty-bold' ?>">
										<?= Helpers::formatRemoveDecimal($row['daily'][$pdate]) ?>
									</td>
									<? } ?>

								</tr>
								<? } ?>
							</tbody>
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
	$script1 = <<< JS
	
	$('#fix_table_w').tableFixer({'left' : 4});
	changeHeightW();
	$(window).resize(function(){
		changeHeightW();
	});
	function changeHeightW(){
		window_h = $(window).height();
		table_h = window_h - 200;
		// console.log(window_h+"-"+table_h);		
		$('#tab_1').height(table_h+'px');
	}
        
        
        $('#fix_table_d').tableFixer({'left' : 4});
	changeHeightD();
	$(window).resize(function(){
		changeHeightD();
	});
	function changeHeightD(){
		window_h = $(window).height();
		table_h = window_h - 200;
		// console.log(window_h+"-"+table_h);		
		$('#tab_2').height(table_h+'px');
	}
        
        
        
        $('#tabBtn_1').on('click', function () {
            $('#btnDownload').attr('href','$routeWeekly')
        })
        
        $('#tabBtn_2').on('click', function () {
            $('#btnDownload').attr('href','$routeDaily')
				})
				
				$('#loading').hide();

	
JS;
	$this->registerJs($script1);
?>