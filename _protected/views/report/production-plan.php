<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $data_weeklyProvider yii\data\ActiveDataProvider */
/** @var TYPE_NAME $downloadFileNameJV */
/** @var TYPE_NAME $downloadFileNameOEM */
/** @var TYPE_NAME $btnOem_1_isActive */
/** @var TYPE_NAME $btnOem_2_isActive */
/** @var TYPE_NAME $btnJv_isActive */
/** @var TYPE_NAME $need_month */
/** @var TYPE_NAME $need_monthOEM */
$this->title = Yii::t('app', 'Production plan');
$calc_at = '';
$loading = '<img src="/themes/adminlte/img/loading.gif">';
?>

	<style>
		.nav-tabs-custom > .nav-tabs > li.active{
			border:0px !important;
		}
		.nav-tabs{
			border-bottom:0px !important;
		}
		.nav-tabs-custom > .nav-tabs > li{
			border-top:0px !important;
			margin-bottom:0px !important;
			margin-right:0px !important;
		}
		.custom-text-normal{
			font-weight:normal !important;
		}
	</style>

	<div class="req-index">
    <?php $form = ActiveForm::begin(['id' => 'prodPlanReport', 'class' => 'form-group form-group-sm', 'action' => ['production-plan'], 'method' => 'post',]); ?>
		<div class="panel">
			<div class="panel-heading">
				<div class="row">
					<div class="col-sm-2">
            <?=Html::a(Yii::t('app', 'btn-back'), ['index'], ['class' => 'btn btn-default btn-xs'])?>
					</div>
					<div class="col-sm-8">
						<h2 class="text-center" style="margin: 5px 0px 5px 10px;">
							<strong><?=$this->title?></strong>
							<span id="calc_at" class="loading" style="font-size: 14px;color: #a29393;"><?=$loading?></span>
						</h2>
					</div>
					<div class="col-sm-2"></div>
				</div>
			</div>
			<div class="panel-body" style="padding-top:20px !important;">
        <? require_once '_production-plan-jv.php'; ?>
			</div>

		</div>
    <?php ActiveForm::end(); ?>
	</div>

	</div>


<?
$script1 = <<< JS
$(document).ready(function() {
	$('#calc_at').html('$calc_at');
	changeHeight();
	$(window).resize(function(){
		changeHeight();
	});
	$('#fix_table_jv').tableFixer({'left' : 2});
	function changeHeight(){
    window_h = $(window).height();
    table_h = window_h - 170;
    // console.log(window_h+"-"+table_h);
    $('.div_fix_table').height(table_h+'px');
  }  	
	  
  $('#download_xls_jv').on('click', function (e) {
    html_xls_export('fix_table_jv', '$downloadFileNameJV');
  });  
  
	$(".prodPlanReport").on('ready', '.select2', function (){
  	$('.select2').select2();
  })
});

$('#warehouse_id').select2({allowClear: true})
    
JS;
$this->registerJs($script1, yii\web\View::POS_END);
