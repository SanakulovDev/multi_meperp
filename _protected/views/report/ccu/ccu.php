<?php

use app\components\Helpers;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Container cube utilization');
$this->params['breadcrumbs'][] = $this->title;


?>
<style>
  .table-ccu th,
  .table-ccu td {
    vertical-align: middle !important;
    border-color: #dcdcda !important;
  }


  .table-ccu th {
    background-color: #e1f4f7;
    text-align: center;
  }

  .table-ccu .tr-total th {
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

  .tr_ccu td {
    font-weight: bold;
    font-size: 18px;
  }

  .ccu_sum {
    font-size: 28px !important;
    font-weight: normal !important;
    text-align: center;
  }

  .tr_space td {
    border: 0px !important;
    height: 5px !important;
    padding: 0px !important;
  }

  .table-ccu {
    border: 0px !important;
  }

  .table-ccu>thead>tr>th,
  .table-ccu>thead>tr>td {
    border-bottom-width: 1px !important;
  }

  .td-red {
    color: red;
  }
  .tr-data td{
    text-align: right;
  }
</style>
<div class="row">
  <div class="col-md-2 col-lg-2">
    <?= Html::a(Yii::t('app', 'btn-back'), ['index'], ['class' => 'btn btn-default btn-xs']) ?>
  </div>
  <div class="col-md-10 col-lg-10">
    <div class="pull-right">
      <?= Html::button(Yii::t('app', 'btn-download-delivery-plan'), ['class' => 'btn btn-info btn-xs', 'id' => 'download-xls']) ?>
    </div>
  </div>
</div>
<br>
<div class="req-index">
  <div class="row">
    <div id="div_fix_table" class="col-lg-10 col-lg-offset-1">
      <table id="fix_table" class="table table-bordered table-ccu">

        <thead>
          <tr>
            <th rowspan="2" style="width: 200px;"><?= Yii::t('app', 'Month') ?></th>
            <th colspan="5"><?= Yii::t('app', 'Transportation cost (in USD)') ?></th>
            <th colspan="2"><?= Yii::t('app', 'Cube utilization') ?></th>
            <th colspan="2"><?= Yii::t('app', 'Transportation lost CBM<br>(90%-actual)*cost') ?></th>
          </tr>
          <tr>
            <th style="width: 100px;"><?= Yii::t('app', 'Total') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'Container') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'Truck') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'Air') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'Local') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'CBM %') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'Weight %') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'Monthly') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'YTD') ?></th>
          </tr>
        </thead>

        <tbody>
          <?foreach($data as $row){?>
            <tr class="tr-data">
              <td style="text-align: left;"><a href="<?=Url::toRoute(['report/ccum', 'month' => $row['month']])?>"><?=Yii::t('app',date('F', strtotime($row['month'])))?>, <?=Yii::t('app',date('Y', strtotime($row['month'])))?></a></td>
              <td><?=Helpers::numberFormatRemoveZero($row['data']['total_cost'],0)?></td>
              <td><?=Helpers::numberFormatRemoveZero($row['data']['container_cost'],0)?></td>
              <td><?=Helpers::numberFormatRemoveZero($row['data']['truck_cost'],0)?></td>
              <td><?=Helpers::numberFormatRemoveZero($row['data']['air_cost'],0)?></td>
              <td><?=Helpers::numberFormatRemoveZero($row['data']['local_cost'],0)?></td>
              <td><?=Helpers::numberFormatRemoveZero($row['data']['cu_cbm_avg'],0)?></td>
              <td><?=Helpers::numberFormatRemoveZero($row['data']['cu_weight_avg'],0)?></td>
              <td><?=Helpers::numberFormatRemoveZero($row['data']['trans_lost_monthly_avg'],0)?></td>
              <td>soon...</td>
            </tr>
          <?}?>
          



        </tbody>




      </table>
    </div>
  </div>


</div>



<?php
$docReadyJs = <<< JS
$(document).ready(function() {

	$('#download-xls').on('click', function (e) {
		html_xls_export('fix_table', '$downloadFileName');
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