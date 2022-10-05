<?php

use app\components\Helpers;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Container cube utilization') . ' ('.Yii::t('app', date("F",strtotime($month))).' '.date("Y",strtotime($month)).')';
$this->params['breadcrumbs'][] = $this->title;

// echo '<pre>';
// print_r($data);
// echo '</pre>';

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
  }

  .table-ccum>thead>tr>th,
  .table-ccum>thead>tr>td {
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
    <?= Html::a(Yii::t('app', 'btn-back-to-reports'), ['index'], ['class' => 'btn btn-default btn-xs']) ?>
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
      <?= Html::a('<i class="glyphicon glyphicon-backward"></i> ' . Yii::t('app', 'Back to Pivot'), ['report/ccu']) ?>
      <table id="fix_table" class="table table-bordered table-ccum">

        <thead>
          <tr>
            <th rowspan="2" style="width: 200px;"><?= Yii::t('app', 'Carrier') ?></th>
            <th rowspan="2" style="width: 200px;"><?= Yii::t('app', 'CONT-TRUCK- AWB No') ?></th>
            <th rowspan="2"style="width: 100px;"><?= Yii::t('app', 'Type') ?></th>
            <th rowspan="2"style="width: 100px;"><?= Yii::t('app', 'Ship mode') ?></th>
            <th rowspan="2"style="width: 100px;"><?= Yii::t('app', 'Shipment term') ?></th>
            <th rowspan="2"style="width: 100px;"><?= Yii::t('app', 'Transportation cost (in USD)') ?></th>
            <th colspan="2"><?= Yii::t('app', 'Cube utilizatioin') ?></th>
            <th rowspan="2"style="width: 100px;"><?= Yii::t('app', 'Transportation lost CBM<br>(90%-actual)*cost') ?></th>
          </tr>
          <tr>
            <th style="width: 100px;"><?= Yii::t('app', 'CBM %') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'Weight %') ?></th>
          </tr>
        </thead>

        <tbody>
        <?
          foreach ($data as $row) {
        ?>
          <tr class="tr-data">
            <td style="text-align: left;"><?=$row['carrier']?></td>
            <td style="text-align: left;"><a href="<?=Url::toRoute(['report/ccuc', 'detail_id' => $row['fr_inv_det_id']])?>"><?=$row['container']?></a></td>
            <td style="text-align: center;"><?=$row['container_type']?></td>
            <td style="text-align: center;"><?=$row['ship_mode']?></td>
            <td style="text-align: center;"><?=$row['del_term']?></td>
            <td><?= Helpers::numberFormatRemoveZero($row['trans_cost'])?></td>
            <td><?=Helpers::numberFormatRemoveZero($row['cu_cbm'],0)?></td>
            <td><?=Helpers::numberFormatRemoveZero($row['cu_weight'],0)?></td>
            <td><?=Helpers::numberFormatRemoveZero($row['trans_lost'],0)?></td>
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