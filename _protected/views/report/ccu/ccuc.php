<?php

use app\components\Helpers;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Container cube utilization') . ' (' . $data['container'] . ')';
$this->params['breadcrumbs'][] = $this->title;

?>
<style>
  .table-ccum th,
  .table-ccum td {
    vertical-align: middle !important;
    border-color: #dcdcda !important;
  }


  .table-ccum th {
    background-color: #e1f4f7;
    text-align: center;
  }

  .table-ccum .tr-total th {
    background-color: #bfc2c5;
  }

  .tr-total {
    font-weight: bold;
    background-color: #fbe3e7;
  }

  .mln {
    text-align: right;
    font-size: 12px;
  }

  .tr_ccum td {
    font-weight: bold;
    font-size: 18px;
  }

  .ccum_sum {
    font-size: 28px !important;
    font-weight: normal !important;
    text-align: center;
  }

  .tr_space td {
    border: 0px !important;
    height: 5px !important;
    padding: 0px !important;
  }

  .table-ccum {
    border: 0px !important;
    font-size: 12px;
  }

  .table-ccum>thead>tr>th,
  .table-ccum>thead>tr>td {
    border-bottom-width: 1px !important;
  }

  .td-red {
    color: red;
  }

  .tr-data td {
    text-align: center;
  }
</style>
<div class="row">
  <div class="col-md-2 col-lg-2">
    <?= Html::a(Yii::t('app', 'btn-back-to-reports'), ['index'], ['class' => 'btn btn-default btn-xs']) ?>
  </div>
  <div class="col-md-10 col-lg-10">
    <div class="pull-right">
      <?= Html::button(Yii::t('app', 'btn-download-delivery-plan'), ['class' => 'btn btn-info btn-xs', 'id' => 'download-xls']) ?>
    </div>
  </div>
</div>
<br>

<div class="req-index" id="page-content">

  <div class="row">

    <div class="col-lg-6">
      <?= Html::a('<i class="glyphicon glyphicon-backward"></i> ' . Yii::t('app', 'Back to Month'), ['report/ccum', 'month' => $data['month']]) ?>
      <table class="table table-bordered table-ccum">

        <thead>
          <tr>
            <th style="width: 200px;"><?= Yii::t('app', 'Carrier') ?></th>
            <th style="width: 200px;"><?= Yii::t('app', 'CONT-TRUCK- AWB No') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'Supplier') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'Type') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'Ship mode') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'Shipment term') ?></th>
          </tr>
        </thead>

        <tbody>
          <tr class="tr-data">
            <td><?= $data['carrier'] ?></td>
            <td><?= $data['container'] ?></td>
            <td><?= $data['suppliers'] ?></td>
            <td><?= $data['type'] ?></td>
            <td><?= $data['ship_mode'] ?></td>
            <td><?= $data['del_term'] ?></td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>

  <div class="row">

    <div class="col-lg-6">
      <table class="table table-bordered table-ccum">

        <thead>
          <tr>
            <th colspan="4"><?= Yii::t('app', 'Cube utilizatioin') ?></th>
            <th rowspan="2" style="width: 200px;"><?= Yii::t('app', 'Transportation lost CBM<br>(90%-actual)*cost') ?></th>
          </tr>
          <tr>
            <th style="width: 100px;"><?= Yii::t('app', 'CBM %') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'Weight %') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'CBM m3') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'Weight kg') ?></th>
          </tr>
        </thead>

        <tbody>
          <tr class="tr-data">
            <td><?= Helpers::numberFormatRemoveZero($data['cu_cbm'], 0) ?></td>
            <td><?= Helpers::numberFormatRemoveZero($data['cu_weight'], 0) ?></td>
            <td><?= Helpers::numberFormatRemoveZero($data['cbm'], 0) ?></td>
            <td><?= Helpers::numberFormatRemoveZero($data['weight'], 0) ?></td>
            <td><?= Helpers::numberFormatRemoveZero($data['trans_lost'], 0) ?></td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>

  <div class="row">

    <div class="col-lg-7">
      <table class="table table-bordered table-ccum">

        <thead>
          <tr>
            <th colspan="3"><?= Yii::t('app', 'OUTBOUND') ?> (<?= $data['invoice_no'] ?>)</th>
            <th colspan="3"><?= Yii::t('app', 'INBOUND') ?> (<?= $data['in_invoice_no'] ?>)</th>
          </tr>
          <tr>
            <th style="width: 150px;"><?= Yii::t('app', 'Route') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'Currency unit') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'Transportation cost') ?></th>
            <th style="width: 150px;"><?= Yii::t('app', 'Route') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'Currency unit') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'Transportation cost') ?></th>
          </tr>
        </thead>

        <tbody>
          <tr class="tr-data">
            <td><?= $data['out_route'] ?></td>
            <td><?= $data['out_currency'] ?></td>
            <td><?= Helpers::numberFormatRemoveZero($data['out_trans_cost'], 0) ?></td>
            <td><?= $data['in_route'] ?></td>
            <td><?= $data['in_currency'] ?></td>
            <td><?= Helpers::numberFormatRemoveZero($data['in_trans_cost'], 0) ?></td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>


  <div class="row">

    <div class="col-lg-7">
      <table class="table table-bordered table-ccum">

        <thead>
          <tr>
            <th style="width: 200px;"><?= Yii::t('app', 'Cargo type') ?></th>
            <th style="width: 200px;"><?= Yii::t('app', 'Point of departure') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'Shipping date') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'Destination station') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'Date of arrival at the station') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'Number of days on the way to the station') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'Date of arrival at the warehouse') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'Total number of transit days') ?></th>
          </tr>
        </thead>

        <tbody>
          <tr class="tr-data">
            <td><?= $data['cargo_type'] ?></td>
            <td><?= $data['point_of_dep'] ?></td>
            <td><?= $data['shipping_date'] ?></td>
            <td><?= $data['dest_station'] ?></td>
            <td><?= $data['station_date'] ?></td>
            <td><?= $data['dif_station_shipping'] ?></td>
            <td><?= $data['arrive_date'] ?></td>
            <td><?= $data['dif_arrive_shipping'] ?></td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>

  <div class="row">

    <div class="col-lg-7">
      <div class="row">

        <div class="col-lg-6">
          <div style="text-align: center;"><?= Yii::t('app', 'OUTBOUND Transportation Cost Details') ?></div style="text-align: center;">
          <table class="table table-bordered table-ccum">

            <tr>
              <th colspan="2" style="width: 200px;"><?= Yii::t('app', 'CONT-TRUCK- AWB No') ?></th>
              <th style="width: 200px;"><?= Yii::t('app', 'Ship mode') ?></th>
            </tr>

            <tr class="tr-data">
              <td colspan="2"><b><?= $data['container'] ?></b></td>
              <td><b><?= $data['ship_mode'] ?></b></td>
            </tr>

            <tr>
              <th style="width: 200px;"><?= Yii::t('app', 'Payment type') ?></th>
              <th style="width: 200px;"><?= Yii::t('app', 'Amount') ?></th>
              <th style="width: 200px;"><?= Yii::t('app', 'Comment') ?></th>
            </tr>

            <?foreach($data['out_costs'] as $cost){?>

            <tr class="tr-data">
              <td style="text-align: left;"><?= $cost['name'] ?></td>
              <td style="text-align: right;"><?= Helpers::numberFormatRemoveZero($cost['value'], 0) ?></td>
              <td></td>
            </tr>

            <?}?>



            <tr class="tr-data">
              <td style="text-align: left;">Итого</td>
              <td style="text-align: right;"><b><?= Helpers::numberFormatRemoveZero($data['out_costs_total'], 0) ?></b></td>
              <td></td>
            </tr>



          </table>
        </div>
        <div class="col-lg-6">
          <div style="text-align: center;"><?= Yii::t('app', 'INBOUND Transportation Cost Details') ?></div style="text-align: center;">
          <table class="table table-bordered table-ccum">

            <tr>
              <th colspan="2" style="width: 200px;"><?= Yii::t('app', 'CONT-TRUCK- AWB No') ?></th>
              <th style="width: 200px;"><?= Yii::t('app', 'Ship mode') ?></th>
            </tr>

            <tr class="tr-data">
              <td colspan="2"><b><?= $data['container'] ?></b></td>
              <td><b><?= $data['ship_mode'] ?></b></td>
            </tr>

            <tr>
              <th style="width: 200px;"><?= Yii::t('app', 'Payment type') ?></th>
              <th style="width: 200px;"><?= Yii::t('app', 'Amount') ?></th>
              <th style="width: 200px;"><?= Yii::t('app', 'Comment') ?></th>
            </tr>

            <?foreach($data['in_costs'] as $cost){?>

            <tr class="tr-data">
              <td style="text-align: left;"><?= $cost['name'] ?></td>
              <td style="text-align: right;"><?= Helpers::numberFormatRemoveZero($cost['value'], 0) ?></td>
              <td></td>
            </tr>

            <?}?>



            <tr class="tr-data">
              <td style="text-align: left;">Итого</td>
              <td style="text-align: right;"><b><?= Helpers::numberFormatRemoveZero($data['in_costs_total'], 0) ?></b></td>
              <td></td>
            </tr>



          </table>
        </div>


      </div>
    </div>

  </div>


</div>



<?php
$docReadyJs = <<< JS
$(document).ready(function() {

	$('#download-xls').on('click', function (e) {
		html_xls_export('page-content', '$downloadFileName');
  });	

  $('.link-group').on('click', function (e) {
    var objGroup = $.parseJSON($(this).attr('data-row'));
    $('.modal-title').html(objGroup.group_name);
    $('.td-total-uzs').html(objGroup.UZS);
    $('.td-total-usd').html(objGroup.USD);
    $('.td-total-eur').html(objGroup.EUR);
    $('.td-total-rub').html(objGroup.RUB);
    $('.td-total-tuzs').html(objGroup.TUZS);
    
    $('.modal-tbody').html('');
    
    var td = tds = ''
    $.each(objGroup.items, function(k, v) {
      td = "<tr>";
      td += "<td>"+v.wh_name+"</td>";
      td += "<td style=\"text-align: right;\">"+v.UZS+"</td>";
      td += "<td style=\"text-align: right;\">"+v.USD+"</td>";
      td += "<td style=\"text-align: right;\">"+v.EUR+"</td>";
      td += "<td style=\"text-align: right;\">"+v.RUB+"</td>";
      td += "<td style=\"text-align: right;\">"+v.TUZS+"</td>";
      tds += td;

		});
    $('.modal-tbody').html(tds);
            
            
    

		console.log(objGroup);
  });	
  
  
}) 
JS;
$this->registerJs($docReadyJs);
?>