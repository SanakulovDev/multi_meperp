<?php

use app\components\Helpers;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $data_weeklyProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Coverage');
$this->params['breadcrumbs'][] = $this->title;
$period_weekly = app\components\Helpers::getPeriodWeek2();

$period_daily = [];
foreach (app\components\Helpers::getPeriodFull() as $pdate) {
	if($pdate > date('Y-m-d', strtotime('+2 month'))) break;
	$period_daily[] = $pdate;
}

$loading = '<img src="/themes/adminlte/img/loading.gif">';



?>
<style>
	.nav-tabs-custom>.nav-tabs>li.active {
		border: 0px !important;
	}
	
	.nav-tabs {
		border-bottom: 0px !important;	
	}
	.nav-tabs-custom>.nav-tabs>li {
		border-top: 0px !important;	
		margin-bottom: 0px !important;	
		margin-right: 0px !important;	
	}
	.custom-text-normal{
		font-weight: normal!important;	
	}

</style>


<?
$calc_at = '';
$total_doh_sum_import= 0;
$w = [];
$d = [];
?>

<div class="req-index">


	<div class="panel">
		<div class="panel-heading">
			<img style="height:28px;" src="/img/logo.jpg" title="<?php echo Yii::$app->params['comp_name'] ?>" class="pull-left" />
			<h3 class="pull-left" style="margin: 5px 0px -5px 10px;">

				<?= Yii::t('app', 'Coverage') ?> <span id="calc_at" class="loading" style="font-size: 14px;color: #a29393;"><?= $loading ?></span>
			</h3>
			<p class="pull-right" style="margin: 0px">
				<?= Html::a(Yii::t('app', 'btn-download'), ['download-weekly-coverage-vehicle'], ['class' => 'btn btn-info btn-sm', 'id' => 'btnDownload']) ?>
			</p>
			<div style="clear: both;"></div>
		</div>
		<div class="panel-body">


			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#tab_1" data-toggle="tab" aria-expanded="true" id="tabBtn_1" style="border: 0px">
							<h4 style="margin: 0px 0px 0px 0px;font-size: 30px"><span class="label label-success">IMPORT</span></h4>
						</a>
					</li>
				</ul>
				<div class="tab-content" style="padding-top: 0px;">

					<div class="tab-pane active" id="tab_1">

						<div class="nav-tabs-custom">
							<ul class="nav nav-tabs">
								<li class="active">
									<a href="#tab_1_1" data-toggle="tab" aria-expanded="true" id="tabBtn_1_1" style="border: 0px;padding-left: 5px;">
										<h4 style="margin: 0px 0px 0px 0px;font-size: 24px"><span class="label label-success">WEEKLY</span></h4>
									</a>
								</li>
								<li class="">
									<a href="#tab_1_2" data-toggle="tab" aria-expanded="false" id="tabBtn_1_2" style="border: 0px;padding-left: 5px;">
										<h4 style="margin: 0px 0px 0px 0px;font-size: 24px"><span class="label label-default custom-text-normal">DAILY</span></h4>
									</a>
								</li>
							</ul>
							<div class="tab-content" style="padding-top: 0px;">

								<div class="tab-pane active" id="tab_1_1">
									<? require_once '_weekly-coverage-vehicle.php'; ?>
								</div>

								<div class="tab-pane" id="tab_1_2">
									<? require_once '_daily-coverage-vehicle.php'; ?>
								</div>

							</div>
						</div>

					</div>

					

				</div>
			</div>

		</div>
	</div>

</div>


<?

$routeDaily = yii\helpers\Url::toRoute(['download-daily-coverage-vehicle']);
$routeWeekly = yii\helpers\Url::toRoute(['download-weekly-coverage-vehicle']);

$total_doh_sum = Helpers::formatRemoveDecimal($total_doh_sum_import);

$w = json_encode($w);
$d = json_encode($d);

$script1 = <<< JS
    
	// table fixer
	$('#fix_table_w').tableFixer({'left' : 4});
	$('#fix_table_d').tableFixer({'left' : 4});
	// ***

	// change height
	changeHeightAll();

	$(window).resize(function(){
		changeHeightAll();
	});

	function changeHeightAll(){
		changeHeight('#tab_1_1');
		changeHeight('#tab_1_2');
		changeHeight('#tab_2_1');
		changeHeight('#tab_2_2');
		changeHeight('#tab_2_3');
	}

	function changeHeight(tab_id){
		window_h = $(window).height();
		table_h = window_h - 200;	
		$(tab_id).height(table_h+'px');
	}

	//*****
        
        
	

	
        
	$('#calc_at').html('($calc_at)');

	// download button link change
	$('#tabBtn_1_1').on('click', function () {
		$('#btnDownload').attr('href','$routeWeekly');
	})
	
	$('#tabBtn_1_2').on('click', function () {
		$('#btnDownload').attr('href','$routeDaily');
	})


	})
	//******

	// show headers
	showRedHeaders($w,'w');
	showRedHeaders($d,'d');

	function showRedHeaders(data, type){
		$.each(data, function( index, value ) {
			$('#'+type+'_'+index).html(value);
		});
	}

	// ******
	
	
	
	// total doh sum
	$('.total_doh_sum_import').html('$total_doh_sum_import');
	// ********

	// katta tablr vosilganda pastdagilarni birinchi tabga qaytarish

	$('#tabBtn_1').on('click', function () {

		$('#tabBtn_1').find('span').removeClass('label-default custom-text-normal').addClass('label-success');
		$('#tabBtn_2').find('span').removeClass('label-success').addClass('label-default custom-text-normal');

		$('#tabBtn_1_2').parent().removeClass('active');
		$('#tab_1_2').removeClass('active');
		
		$('#tabBtn_1_1').parent().addClass('active');
		$('#tab_1_1').addClass('active');

		$('#tabBtn_1_1').find('span').removeClass('label-default custom-text-normal').addClass('label-success');
		$('#tabBtn_1_2').find('span').removeClass('label-success').addClass('label-default custom-text-normal');

		$('#btnDownload').attr('href','$routeWeekly');
	})

	$('#tabBtn_2').on('click', function () {

		$('#tabBtn_2').find('span').removeClass('label-default custom-text-normal').addClass('label-success');
		$('#tabBtn_1').find('span').removeClass('label-success').addClass('label-default custom-text-normal');

		$('#tabBtn_2_2').parent().removeClass('active');
		$('#tab_2_2').removeClass('active');

		$('#tabBtn_2_3').parent().removeClass('active');
		$('#tab_2_3').removeClass('active');

		$('#tabBtn_2_1').parent().addClass('active');
		$('#tab_2_1').addClass('active');

		$('#tabBtn_2_1').find('span').removeClass('label-default custom-text-normal').addClass('label-success');
		$('#tabBtn_2_2').find('span').removeClass('label-success').addClass('label-default custom-text-normal');
		$('#tabBtn_2_3').find('span').removeClass('label-success').addClass('label-default custom-text-normal');

	})


	$('#tabBtn_1_1').on('click', function () {
		$('#tabBtn_1_1').find('span').removeClass('label-default custom-text-normal').addClass('label-success');
		$('#tabBtn_1_2').find('span').removeClass('label-success').addClass('label-default custom-text-normal');
	})

	$('#tabBtn_1_2').on('click', function () {
		$('#tabBtn_1_2').find('span').removeClass('label-default custom-text-normal').addClass('label-success');
		$('#tabBtn_1_1').find('span').removeClass('label-success').addClass('label-default custom-text-normal');
	})


	$('#tabBtn_2_1').on('click', function () {
		$('#tabBtn_2_1').find('span').removeClass('label-default custom-text-normal').addClass('label-success');
		$('#tabBtn_2_2').find('span').removeClass('label-success').addClass('label-default custom-text-normal');
		$('#tabBtn_2_3').find('span').removeClass('label-success').addClass('label-default custom-text-normal');
	})

	$('#tabBtn_2_2').on('click', function () {
		$('#tabBtn_2_2').find('span').removeClass('label-default custom-text-normal').addClass('label-success');
		$('#tabBtn_2_1').find('span').removeClass('label-success').addClass('label-default custom-text-normal');
		$('#tabBtn_2_3').find('span').removeClass('label-success').addClass('label-default custom-text-normal');
	})

	$('#tabBtn_2_3').on('click', function () {
		$('#tabBtn_2_3').find('span').removeClass('label-default custom-text-normal').addClass('label-success');
		$('#tabBtn_2_1').find('span').removeClass('label-success').addClass('label-default custom-text-normal');
		$('#tabBtn_2_2').find('span').removeClass('label-success').addClass('label-default custom-text-normal');
	})

	//*****
    
JS;
$this->registerJs($script1);
?>