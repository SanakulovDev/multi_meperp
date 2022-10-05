<?php

use app\components\Helpers;
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Cash requirement for import shipments');
$this->params['breadcrumbs'][] = $this->title;

?>
  <style>
    .table-doh {
      border: 0px !important;
    }
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
    .td-diff{
      background-color: #fbe3e7;
    }
    .mln{
      text-align: right;
      font-size: 12px;
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


        <table id="fix_table" class="table table-bordered table-doh">

          <thead>
          <tr>
            <td colspan="10" class="mln">(<?=Yii::t('app','in million UZS')?>)</td>
          </tr>
          <tr>
            <th style="width: 50px;"><?=Yii::t('app', '#')?></th>
            <th  style="width: 150px;" ><?=Yii::t('app','Country')?></th>
            <th><?=Yii::t('app','Supplier')?></th>
            <th  style="width: 100px;" ><?=Yii::t('app','Payment term')?></th>
            <th  style="width: 120px;" ><?=Yii::t('app','Description')?></th>
            <th  style="width: 100px;" ><?=Yii::t('app',date('F'))?> <?=date('Y')?></th>
            <th  style="width: 100px;" ><?=Yii::t('app',date('F', strtotime('+1 month')))?> <?=date('Y', strtotime('+1 month'))?></th>
            <th  style="width: 100px;" ><?=Yii::t('app',date('F', strtotime('+2 month')))?> <?=date('Y', strtotime('+2 month'))?></th>
            <th  style="width: 100px;" ><?=Yii::t('app',date('F', strtotime('+3 month')))?> <?=date('Y', strtotime('+3 month'))?></th>
            <th  style="width: 100px;" ><?=Yii::t('app','Total')?></th>
          </tr>
          </thead>
          <tbody>
          <?
          $i = 1;
          $date1 = date('Y-m-t');
          $date2 = date('Y-m-t', strtotime('+1 month'));
          $date3 = date('Y-m-t', strtotime('+2 month'));
          $date4 = date('Y-m-t', strtotime('+3 month'));
          $all_pariod_debt_total = 0;
          $all_pariod_paid_total = 0;
          $all_pariod_diff_total = 0;

          $date1_debt_total = 0;
          $date1_paid_total = 0;
          $date1_diff_total = 0;

          $date2_debt_total = 0;
          $date2_paid_total = 0;
          $date2_diff_total = 0;

          $date3_debt_total = 0;
          $date3_paid_total = 0;
          $date3_diff_total = 0;

          $date4_debt_total = 0;
          $date4_paid_total = 0;
          $date4_diff_total = 0;

          ?>
          <? foreach ($suppliers as $sp) {?>
            <?
            $key = $sp->supplier_id.'|'.$sp->payment_term_id;

            $date1_debt_total += $data[$key][$date1]['debt'];
            $date1_paid_total += $data[$key][$date1]['paid'];
            $date1_diff_total += $data[$key][$date1]['diff'];

            $date2_debt_total += $data[$key][$date2]['debt'];
            $date2_paid_total += $data[$key][$date2]['paid'];
            $date2_diff_total += $data[$key][$date2]['diff'];

            $date3_debt_total += $data[$key][$date3]['debt'];
            $date3_paid_total += $data[$key][$date3]['paid'];
            $date3_diff_total += $data[$key][$date3]['diff'];

            $date4_debt_total += $data[$key][$date4]['debt'];
            $date4_paid_total += $data[$key][$date4]['paid'];
            $date4_diff_total += $data[$key][$date4]['diff'];
            ?>
            <tr>
              <td rowspan="3" style="text-align: center;"><?=$i++?></td>
              <td rowspan="3"><?=$sp->supplier->countryCode->name ?? ''?></td>
              <td rowspan="3"><?=$sp->supplier->name?></td>
              <td rowspan="3" style="text-align: center;"><?=$sp->paymentTerm->name?><br><span>(<?=$sp->currency->code?>)</span></td>
              <td class="td-debt"><?=Yii::t('app', 'Required')?></td>
              <td style="text-align: right;"><?=number_format(round($data[$key][$date1]['debt'],0),0,'.',' ')?></td>
              <td style="text-align: right;"><?=number_format(round($data[$key][$date2]['debt'],0),0,'.',' ')?></td>
              <td style="text-align: right;"><?=number_format(round($data[$key][$date3]['debt'],0),0,'.',' ')?></td>
              <td style="text-align: right;"><?=number_format(round($data[$key][$date4]['debt'],0),0,'.',' ')?></td>
              <?
              $all_pariod_debt = $data[$key][$date1]['debt'] + $data[$key][$date2]['debt'] + $data[$key][$date3]['debt'] + $data[$key][$date4]['debt'];
              $all_pariod_debt_total += $all_pariod_debt;
              ?>
              <td style="text-align: right;"><?=number_format(round($all_pariod_debt,0),0,'.',' ')?></td>
            </tr>
            <tr>
              <td><?=Yii::t('app', 'Paid')?></td>
              <td style="text-align: right;"><?=number_format(round($data[$key][$date1]['paid'],0),0,'.',' ')?></td>
              <td style="text-align: right;"><?=number_format(round($data[$key][$date2]['paid'],0),0,'.',' ')?></td>
              <td style="text-align: right;"><?=number_format(round($data[$key][$date3]['paid'],0),0,'.',' ')?></td>
              <td style="text-align: right;"><?=number_format(round($data[$key][$date4]['paid'],0),0,'.',' ')?></td>
              <?
              $all_pariod_paid = $data[$key][$date1]['paid'] + $data[$key][$date2]['paid'] + $data[$key][$date3]['paid'] + $data[$key][$date4]['paid'];
              $all_pariod_paid_total += $all_pariod_paid;
              ?>
              <td style="text-align: right;"><?=number_format(round($all_pariod_paid,0),0,'.',' ')?></td>
            </tr>
            <tr>
              <td class="td-diff" ><?=Yii::t('app', 'For payment')?></td>
              <td class="td-diff"  style="text-align: right;"><?=number_format(round($data[$key][$date1]['diff'],0),0,'.',' ')?></td>
              <td class="td-diff"  style="text-align: right;"><?=number_format(round($data[$key][$date2]['diff'],0),0,'.',' ')?></td>
              <td class="td-diff"  style="text-align: right;"><?=number_format(round($data[$key][$date3]['diff'],0),0,'.',' ')?></td>
              <td class="td-diff"  style="text-align: right;"><?=number_format(round($data[$key][$date4]['diff'],0),0,'.',' ')?></td>
              <?
              $all_pariod_diff = $data[$key][$date1]['diff'] + $data[$key][$date2]['diff'] + $data[$key][$date3]['diff'] + $data[$key][$date4]['diff'];
              $all_pariod_diff_total += $all_pariod_diff;
              ?>
              <td class="td-diff"   style="text-align: right;"><?=number_format(round($all_pariod_diff,0),0,'.',' ')?></td>
            </tr>
          <?}?>



          <tr>
            <td rowspan="3" colspan="4"><b><?=Yii::t('app','Total')?></b></td>
            <td><b><?=Yii::t('app', 'Required')?></b></td>
            <td style="text-align: right;"><b><?=$date1_debt_total?></b></td>
            <td style="text-align: right;"><b><?=$date2_debt_total?></b></td>
            <td style="text-align: right;"><b><?=$date3_debt_total?></b></td>
            <td style="text-align: right;"><b><?=$date4_debt_total?></b></td>
            <td style="text-align: right;"><b><?=$all_pariod_debt_total?></b></td>
          </tr>
          <tr>
            <td><b><?=Yii::t('app', 'Paid')?></b></td>
            <td style="text-align: right;"><b><?=$date1_paid_total?></b></td>
            <td style="text-align: right;"><b><?=$date2_paid_total?></b></td>
            <td style="text-align: right;"><b><?=$date3_paid_total?></b></td>
            <td style="text-align: right;"><b><?=$date4_paid_total?></b></td>
            <td style="text-align: right;"><b><?=$all_pariod_paid_total?></b></td>
          </tr>
          <tr>
            <td class="td-diff" ><b><?=Yii::t('app', 'For payment')?></b></td>
            <td class="td-diff" style="text-align: right;"><b><?=$date1_diff_total?></b></td>
            <td class="td-diff" style="text-align: right;"><b><?=$date2_diff_total?></b></td>
            <td class="td-diff" style="text-align: right;"><b><?=$date3_diff_total?></b></td>
            <td class="td-diff" style="text-align: right;"><b><?=$date4_diff_total?></b></td>
            <td class="td-diff" style="text-align: right;"><b><?=$all_pariod_diff_total?></b></td>
          </tr>




          </tbody>
        </table>
      </div>
    </div>


  </div>

<?php
$docReadyJs = <<< JS

$(document).ready(function() {

	$('#fix_table').tableFixer({
	  'left' : 0,
	  'foot' : true,
	  'head' : true
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