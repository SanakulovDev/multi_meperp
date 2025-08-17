<?php

use app\components\Helpers;
use app\models\Currency;
use app\models\CurrencyRate;
use app\models\VehicleCoverageInput;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $data_weeklyProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'UzAuto visibility report');
$this->params['breadcrumbs'][] = $this->title;

$period_weekly_veh = app\components\Helpers::getPeriodWeek2(VehicleCoverageInput::getLastCoverageDate());

$period_daily_veh = [];
foreach (app\components\Helpers::getPeriodFull(VehicleCoverageInput::getLastCoverageDate()) as $pdate) {
  if($pdate > date('Y-m-d', strtotime("+2 months", strtotime(VehicleCoverageInput::getLastCoverageDate())))) break;
  $period_daily_veh[] = $pdate;
}


$rateUSD = CurrencyRate::currentRate(Currency::findOneCurrencyCode('USD')->id);
$rateEUR = CurrencyRate::currentRate(Currency::findOneCurrencyCode('EUR')->id);
$rateRUB = CurrencyRate::currentRate(Currency::findOneCurrencyCode('RUB')->id);


$loading = '<img src="/themes/adminlte/img/loading.gif">';



?>

<?= $this->render('../../common/_loading'); ?>


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

    .cov-filter {
      font-size: 14px !important;
    }
    .select2-selection__rendered{
      font-size: 14px !important;
    }

  </style>


<?
$calc_at = '';

$wv = []; // coverage-vehicle weekly
$dv = []; // coverage-vehicle daily
?>

  <div class="req-index">


    <div class="panel">
      <div class="panel-heading">
        <img style="height:28px;" src="/img/logo.jpg" title="<?php echo Yii::$app->params['comp_name'] ?>" class="pull-left" />
        <h3 class="pull-left" style="margin: 5px 0px -5px 10px;">

          <?= Yii::t('app', 'UzAuto visibility report') ?> <span id="calc_at" class="loading" style="font-size: 14px;color: #a29393;"><?= $loading ?></span>
        </h3>
        <p class="pull-right" style="margin: 0px" id="buttons">
          <?= Html::button(Yii::t('app', 'btn-download'),  ['class' => 'btn btn-info btn-sm btn-summary', 'id' => 'btn-download-summary']) ?>
          <?= Html::button(Yii::t('app', 'btn-download'),  ['class' => 'btn btn-primary btn-sm btn-weekly', 'id' => 'btn-download-vehicle', 'style' => 'display:none;']) ?>
        </p>
        <div style="clear: both;"></div>
      </div>
      <div class="panel-body">


        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs">
            <li class="active">
              <a href="#tab_1" data-toggle="tab" aria-expanded="true" id="tabBtn_1" style="border: 0px">
                <h4 style="margin: 0px 0px 0px 0px;font-size: 30px"><span class="label label-success">SUMMARY</span></h4>
              </a>
            </li>
            <li class="">
              <a href="#tab_2" data-toggle="tab" aria-expanded="false" id="tabBtn_2" style="border: 0px; padding-left: 5px;">
                <h4 style="margin: 0px 0px 0px 0px;font-size: 30px"><span class="label label-default custom-text-normal">COVERAGE</span></h4>
              </a>
            </li>
          </ul>
          <div class="tab-content" style="padding-top: 0px;">

            <div class="tab-pane active" id="tab_1">

						<? require_once '_summary-vehicle-set.php'; ?>

            </div>

            <div class="tab-pane" id="tab_2">

              <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                  <li class="active">
                    <a href="#tab_2_1" data-toggle="tab" aria-expanded="true" id="tabBtn_2_1" style="border: 0px;padding-left: 5px;">
                      <h4 style="margin: 0px 0px 0px 0px;font-size: 24px"><span class="label label-success">WEEKLY</span></h4>
                    </a>
                  </li>
                  <li class="">
                    <a href="#tab_2_2" data-toggle="tab" aria-expanded="false" id="tabBtn_2_2" style="border: 0px;padding-left: 5px;">
                      <h4 style="margin: 0px 0px 0px 0px;font-size: 24px"><span class="label label-default custom-text-normal">DAILY</span></h4>
                    </a>
                  </li>
                </ul>
                <div class="tab-content" style="padding-top: 0px;">

                  <div class="tab-pane" id="tab_2_1">
                    <? require_once '_weekly-coverage-vehicle.php'; ?>
                  </div>

                  <div class="tab-pane" id="tab_2_2">
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

$wv = json_encode($wv);
$dv = json_encode($dv);

// Coverage vehicle downloaded file names

$downloadFileNameSummary = rtrim(Helpers::downloadFileName('summary-vehicle-daily', '1'), '.1');
$downloadFileNameCVD = rtrim(Helpers::downloadFileName('coverage-vehicle-daily', '1'), '.1');
$downloadFileNameCVW = rtrim(Helpers::downloadFileName('coverage-vehicle-weekly', '1'), '.1');


$script1 = <<< JS

	$('#fix_table_wv').tableFixer({'left' : 8});
	$('#fix_table_dv').tableFixer({'left' : 8});
	// ***

	// change height
	changeHeightAll();

	$(window).resize(function(){
		changeHeightAll();
	});

	function changeHeightAll(){

		changeHeight('#tab_2_1');
		changeHeight('#tab_2_2');
	}

	function changeHeight(tab_id){
		window_h = $(window).height();
		table_h = window_h - 200;	
		$(tab_id).height(table_h+'px');
	}

	//*****
        
        
	

	
        
	$('#calc_at').html('($calc_at)');
	//******

	// show headers

	showRedHeaders($wv,'wv');
	showRedHeaders($dv,'dv');

	function showRedHeaders(data, type){
		$.each(data, function( index, value ) {
			$('#'+type+'_'+index).html(value);
		});
	}

	// ******
	
	


	// katta tablar bosilganda pastdagilarni birinchi tabga qaytarish

	$('#tabBtn_1').on('click', function () {

		$('#tabBtn_1').find('span').removeClass('label-default custom-text-normal').addClass('label-success');
		$('#tabBtn_2').find('span').removeClass('label-success').addClass('label-default custom-text-normal');
		
		$('#btn-download-summary').show();
		$('#btn-download-vehicle').hide();
	})

	$('#tabBtn_2').on('click', function () {

		$('#tabBtn_2').find('span').removeClass('label-default custom-text-normal').addClass('label-success');
		$('#tabBtn_1').find('span').removeClass('label-success').addClass('label-default custom-text-normal');

		$('#tabBtn_2_2').parent().removeClass('active');
		$('#tab_2_2').removeClass('active');

		$('#tabBtn_2_1').parent().addClass('active');
		$('#tab_2_1').addClass('active');

		$('#tabBtn_2_1').find('span').removeClass('label-default custom-text-normal').addClass('label-success');
		$('#tabBtn_2_2').find('span').removeClass('label-success').addClass('label-default custom-text-normal');

		
		$('#btn-download-summary').hide();
		$('#btn-download-vehicle').show();
		$('#btn-download-vehicle').removeClass('btn-daily').addClass('btn-weekly');

	})


	$('#tabBtn_2_1').on('click', function () {
		$('#tabBtn_2_1').find('span').removeClass('label-default custom-text-normal').addClass('label-success');
		$('#tabBtn_2_2').find('span').removeClass('label-success').addClass('label-default custom-text-normal');

		$('#btn-download-vehicle').removeClass('btn-daily').addClass('btn-weekly');
	})

	$('#tabBtn_2_2').on('click', function () {
		$('#tabBtn_2_2').find('span').removeClass('label-default custom-text-normal').addClass('label-success');
		$('#tabBtn_2_1').find('span').removeClass('label-success').addClass('label-default custom-text-normal');

		$('#btn-download-vehicle').removeClass('btn-weekly').addClass('btn-daily');
	})

	

	$(document).ready(function() {
		//*****
		$('#buttons').on('click', '.btn-summary', function () {
				html_xls_export('table_summary', '$downloadFileNameSummary');
		});	

		$('#buttons').on('click', '.btn-daily', function () {
				console.log('daily');
				html_xls_export('fix_table_dv', '$downloadFileNameCVD');
		});	

		$('#buttons').on('click', '.btn-weekly', function () {
				console.log('weekly');
				html_xls_export('fix_table_wv', '$downloadFileNameCVW');
		});	

		$('.select2').select2();

		$('#loading').hide();

	}) 

  
JS;
$this->registerJs($script1);
?>