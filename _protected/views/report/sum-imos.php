<?php

use app\components\Helpers;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Import materials order statust (SUM)');
$this->params['breadcrumbs'][] = $this->title;

$loading = '<img src="/themes/adminlte/img/loading.gif">';
?>

<?= $this->render('../common/_loading'); ?>


<div class="req-index">


	<div class="panel">
		<div class="panel-heading">
			<img style="height:28px;" src="/img/logo.jpg" title="<?php echo Yii::$app->params['comp_name'] ?>" class="pull-left" />
			<h3 class="pull-left" style="margin: 5px 0px -5px 10px;">
				<?= Yii::t('app', 'Import materials order status (SUM)') ?>
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
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="tab_1">
						<table class="table table-req" id="fix_table_w">
							<thead>
								<tr class="tr_head_imos">
									<th style="width: 30px;" class="text-center">№</th>
									<th style="width: 100px;" class="text-center"><?= Yii::t('app', 'Supplier') ?></th>
									<th style="width: 100px;" class="text-center"><?= Yii::t('app', 'Contract subject') ?></th>
									<th style="width: 100px;" class="text-center"><?= Yii::t('app', 'Country code') ?></th>
									<th style="width: 100px;" class="text-center"><?= Yii::t('app', 'DOH WH, Bank stock') ?></th>
									<th style="width: 100px;" class="text-center"><?= Yii::t('app', 'Transit time') ?></th>
									<th style="width: 100px;" class="text-center"><?= Yii::t('app', 'Total DOH days') ?></th>
									<th style="width: 100px;" class="text-center"><?= Yii::t('app', 'Total Days on Hand with MOQ') ?></th>
									<th style="width: 100px;" class="text-center"><?= Yii::t('app', 'Total Days on Hand without MOQ') ?></th>
									<th style="width: 100px;" class="text-center"><?= Yii::t('app', 'DIFF. Amount') ?></th>
								</tr>
							</thead>
							<tbody>
							<? $i = 0; ?>
							<? foreach($data['data'] as $row){
								$i++; ?>
								<tr <?=($i%2 == 0) ? 'class="tr_odd"' : ''?>>
									<td class="text-center"><?=$i?></td>
									<td class="text-left"><?=$row['supplier']?></td>
									<td class="text-left"><?=$row['contractSubject']?></td>
									<td class="text-left"><?= $row['country'] ?></td>
									<td class="text-right" style="white-space: nowrap;"><?=Helpers::numberFormatRemoveZero($row['bankStock'],0,'.','')?></td>
									<td class="text-right" style="white-space: nowrap;"><?=Helpers::numberFormatRemoveZero($row['transitTime'],0,'.','')?></td>
									<td class="text-right" style="white-space: nowrap;"><?=Helpers::numberFormatRemoveZero($row['totalDohDays'],0,'.','')?></td>
									<td class="text-right" style="white-space: nowrap;"><?=Helpers::numberFormatRemoveZero($row['totalDohAmountWithMoq'],0,'.','')?></td>
									<td class="text-right" style="white-space: nowrap;"><?=Helpers::numberFormatRemoveZero($row['totalDohAmountWithoutMoq'],0,'.','')?></td>
									<td class="text-right" style="white-space: nowrap;"><?=Helpers::numberFormatRemoveZero($row['totalDohDiff'],0,'.','')?></td>
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