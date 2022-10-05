<?php

use app\components\Helpers;
use app\models\Currency;
use app\models\CurrencyRate;
use app\models\VehicleCoverageInput;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $data_weeklyProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Coverage');
$this->params['breadcrumbs'][] = $this->title;

$period_weekly = app\components\Helpers::getPeriodWeek6Month();

$period_daily = [];
foreach (app\components\Helpers::getPeriodFull() as $pdate) {
  if($pdate > date('Y-m-t', strtotime('+6 month'))) break;
  $period_daily[] = $pdate;
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
$total_doh_sum_import= 0;
$total_doh_sum_l = 0;
$total_doh_sum_c = 0;
$total_doh_sum_s = 0;
$w = [];
$d = [];
$l = [];
$c = [];
$s = [];
?>

  <div class="req-index">


    <div class="panel">
      <div class="panel-heading">
        <img style="height:28px;" src="/img/mep.png" title="<?php echo Yii::$app->params['comp_name'] ?>" class="pull-left" />
        <h3 class="pull-left" style="margin: 5px 0px -5px 10px;">

          <?= Yii::t('app', 'Coverage') ?> <span id="calc_at" class="loading" style="font-size: 14px;color: #a29393;"><?= $loading ?></span>
        </h3>
        <p class="pull-right" style="margin: 0px" id="buttons">
          <?= Html::a(Yii::t('app', 'btn-download'), ['download-weekly-coverage'], ['class' => 'btn btn-info btn-sm', 'id' => 'btn-download']) ?>
          <?= Html::button(Yii::t('app', 'btn-download'),  ['class' => 'btn btn-primary btn-sm btn-weekly', 'id' => 'btn-download-vehicle', 'style' => 'display:none;']) ?>
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
            <li class="">
              <a href="#tab_2" data-toggle="tab" aria-expanded="false" id="tabBtn_2" style="border: 0px; padding-left: 5px;">
                <h4 style="margin: 0px 0px 0px 0px;font-size: 30px"><span class="label label-default custom-text-normal">LOCAL</span></h4>
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
                    <? require_once '_weekly-coverage.php'; ?>
                  </div>

                  <div class="tab-pane" id="tab_1_2">
                    <? require_once '_daily-coverage.php'; ?>
                  </div>

                </div>
              </div>

            </div>

            <div class="tab-pane" id="tab_2">

              <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                  <li class="active">
                    <a href="#tab_2_1" data-toggle="tab" aria-expanded="true" id="tabBtn_2_1" style="border: 0px;padding-left: 5px;">
                      <h4 style="margin: 0px 0px 0px 0px;font-size: 24px"><span class="label label-default custom-text-normal">LOCAL PARTS</span></h4>
                    </a>
                  </li>
                  <li class="">
                    <a href="#tab_2_2" data-toggle="tab" aria-expanded="false" id="tabBtn_2_2" style="border: 0px;padding-left: 5px;">
                      <h4 style="margin: 0px 0px 0px 0px;font-size: 24px"><span class="label label-default custom-text-normal">CONSIGNMENT</span></h4>
                    </a>
                  </li>
                  <li class="">
                    <a href="#tab_2_3" data-toggle="tab" aria-expanded="false" id="tabBtn_2_3" style="border: 0px;padding-left: 5px;">
                      <h4 style="margin: 0px 0px 0px 0px;font-size: 24px"><span class="label label-default custom-text-normal">SEMI PARTS</span></h4>
                    </a>
                  </li>
                </ul>
                <div class="tab-content" style="padding-top: 0px;">

                  <div class="tab-pane active" id="tab_2_1">
                    <? require_once '_local-coverage.php'; ?>
                  </div>

                  <div class="tab-pane" id="tab_2_2">
                    <? require_once '_cons-coverage.php'; ?>
                  </div>

                  <div class="tab-pane" id="tab_2_3">
                    <? require_once '_semi-coverage.php'; ?>
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

$routeDaily = yii\helpers\Url::toRoute(['download-daily-coverage']);
$routeWeekly = yii\helpers\Url::toRoute(['download-weekly-coverage']);
$routeLocal = yii\helpers\Url::toRoute(['download-local-coverage']);
$routeCons = yii\helpers\Url::toRoute(['download-cons-coverage']);
$routeSemi = yii\helpers\Url::toRoute(['download-semi-coverage']);

$total_doh_sum = Helpers::formatRemoveDecimal($total_doh_sum_import);
$total_doh_sum_l = Helpers::formatRemoveDecimal($total_doh_sum_l);
$total_doh_sum_c = Helpers::formatRemoveDecimal($total_doh_sum_c);
$total_doh_sum_s = Helpers::formatRemoveDecimal($total_doh_sum_s);

$w = json_encode($w);
$d = json_encode($d);
$l = json_encode($l);
$c = json_encode($c);
$s = json_encode($s);


$script1 = <<< JS
    
	// table fixer
	$('#fix_table_w').tableFixer({'left' : 4});
	$('#fix_table_d').tableFixer({'left' : 4});
	$('#fix_table_l').tableFixer({'left' : 4});
	$('#fix_table_c').tableFixer({'left' : 4});
	$('#fix_table_s').tableFixer({'left' : 4});
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
		$('#btn-download').attr('href','$routeWeekly');
	})
	
	$('#tabBtn_1_2').on('click', function () {
		$('#btn-download').attr('href','$routeDaily');
	})

	$('#tabBtn_2_1').on('click', function () {
		$('#btn-download').attr('href','$routeLocal');
	})
	$('#tabBtn_2_2').on('click', function () {
		$('#btn-download').attr('href','$routeCons');
	})
	$('#tabBtn_2_3').on('click', function () {
		$('#btn-download').attr('href','$routeSemi');
	})



	//******

	// show headers
	showRedHeaders($w,'w');
	showRedHeaders($d,'d');
	showRedHeaders($l,'l');
	showRedHeaders($c,'c');
	showRedHeaders($s,'s');

	function showRedHeaders(data, type){
		$.each(data, function( index, value ) {
			$('#'+type+'_'+index).html(value);
		});
	}

	// ******
	
	
	
	// total doh sum
	$('.total_doh_sum_import').html('$total_doh_sum_import');
	$('.total_doh_sum_l').html('$total_doh_sum_l');
	$('.total_doh_sum_c').html('$total_doh_sum_c');
	$('.total_doh_sum_s').html('$total_doh_sum_s');
	// ********

	// katta tablar bosilganda pastdagilarni birinchi tabga qaytarish

	$('#tabBtn_1').on('click', function () {

		$('#tabBtn_1').find('span').removeClass('label-default custom-text-normal').addClass('label-success');
		$('#tabBtn_2').find('span').removeClass('label-success').addClass('label-default custom-text-normal');

		$('#tabBtn_1_2').parent().removeClass('active');
		$('#tab_1_2').removeClass('active');
		
		$('#tabBtn_1_1').parent().addClass('active');
		$('#tab_1_1').addClass('active');

		$('#tabBtn_1_1').find('span').removeClass('label-default custom-text-normal').addClass('label-success');
		$('#tabBtn_1_2').find('span').removeClass('label-success').addClass('label-default custom-text-normal');
		
		$('#btn-download').show();
		$('#btn-download-vehicle').hide();
		$('#btn-download').attr('href','$routeWeekly');
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

		$('#btn-download').show();
		$('#btn-download-vehicle').hide();
		$('#btn-download').attr('href','$routeLocal');
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



	$(document).ready(function() {

		$('.select2').select2();

		$('#loading').hide();

	}) 

  
JS;
$this->registerJs($script1);
?>