<?php

use app\assets\AdminLteAsset;
use app\components\Helpers;
use app\models\Warehouse;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Shipment performance');
$this->params['breadcrumbs'][] = $this->title;

?>

<?= $this->render('../common/_loading'); ?>




<style>
  .table-bom-cost th,
  .table-bom-cost td {
    vertical-align: middle !important;
    border-color: #dcdcda !important;
  }


  .table-bom-cost th {
    background-color: #e1f4f7;
    text-align: center;
  }

  .table-bom-cost .tr-total th {
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

  .tr_doh td {
    font-weight: bold;
    font-size: 18px;
  }

  .doh_sum {
    font-size: 28px !important;
    font-weight: normal !important;
    text-align: center;
  }

  .tr_space td {
    border: 0px !important;
    height: 5px !important;
    padding: 0px !important;
  }

  .table-bom-cost {
    border: 0px !important;
  }

  .table-bom-cost>thead>tr>th,
  .table-bom-cost>thead>tr>td {
    border-bottom-width: 1px !important;
  }

  .td-red {
    color: red;
  }

  .tablesorter-blue th,
  .tablesorter-blue thead td {
    background-color: #e1f4f7;
    border: #dcdcda 1px solid;
    color: #333;
    text-shadow: none;
  }

  .tablesorter-blue tbody tr td {
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
    vertical-align: middle !important;
  }

  .tablesorter-blue .headerSortUp,
  .tablesorter-blue .tablesorter-headerSortUp,
  .tablesorter-blue .tablesorter-headerAsc {
    background-color: #d1e8ff;
  }

  .tablesorter-blue .headerSortDown,
  .tablesorter-blue .tablesorter-headerSortDown,
  .tablesorter-blue .tablesorter-headerDesc {
    background-color: #c8def5;
  }

  .tablesorter-blue th,
  .tablesorter-blue td {
    border: #dcdcda 1px solid;
  }

  .tablesorter-blue .tablesorter-filter-row td {
    background-color: #e1f4f7;
  }

  .tablesorter tbody tr:hover td,
  .tablesorter tbody tr:hover th {
    background-color: inherit !important;
  }

  .zero {
    color: #cac7c7 !important;
  }



  .modal-bom-stock {
    width: 95%;
  }

  .field-factbyhourform-floc {
    margin-right: 20px;
    width: 200px;
  }

  .field-factbyhourform-line {
    margin-right: 20px;
    width: 200px;
  }

  .select2-results__options > :first-child {
    /* height: 25px; */
    padding: 6px 12px;
  }

  .select2-results__options > :first-child:before {
    content: " ... ";
  }

  /* .select2-selection__rendered:nth-of-type(2){
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    width: 150px;
  } */
  
</style>
<div class="cont-bom-cost">
  <div class="row">
    <div class="col-md-2 col-lg-2">
      <?= Html::a(Yii::t('app', 'btn-back'), ['index'], ['class' => 'btn btn-default btn-xs']) ?>
    </div>
    <div class="col-md-10 col-lg-10">
      <div class="pull-right">
        <?= Html::button(Yii::t('app', 'btn-download-delivery-plan'), [
          'class' => 'btn btn-info btn-xs download-xls',
          'table-id' => 'main-table',
          'filename' => $downloadFileName
        ]) ?>
      </div>
    </div>
  </div>
  <br>


  <div class="row">
    <div class="col-lg-12 ">
      <table id="main-table" class="tablesorter">
        <thead>

          <tr>
            <th rowspan="2" style="text-align: center;font-size: 14px;padding: 8px;" class="filter-select">
              <div style="width: 50px;">
                <?= Yii::t('app', 'CW') ?> <?= date("Y") ?>
              </div>
            </th>
            <th rowspan="2" style="text-align: center;font-size: 14px;padding: 8px;" class="filter-select supp">
              <div style="width: 150px;">  
                <?= Yii::t('app', 'Supplier') ?>
              </div>
            </th>
            <th rowspan="2" style="text-align: center;font-size: 14px;padding: 8px;" class="filter-select">
              <div style="width: 120px;">
                <?= Yii::t('app', 'Supplied materials') ?>
              </div>
            </th>
            <th colspan="6" style="text-align: center;font-size: 14px;padding: 8px;">
              <?= Yii::t('app', 'Shipment performance (No of parts)') ?>
            </th>
            <th colspan="7" style="text-align: center;font-size: 14px;padding: 8px;">
              <?= Yii::t('app', 'Shipment performance (Amount)') ?>
            </th>
          </tr>

          <tr>

            <th style="text-align: center;font-size: 14px;padding: 8px;">
              <div style="width: 80px;">
                <?= Yii::t('app', 'Planned <br>< {less_dates_count} DOH', ['less_dates_count' => Yii::$app->params['less_dates_count']]) ?>
              </div>
            </th>
            <th style="text-align: center;font-size: 14px;padding: 8px;">
              <div style="width: 80px;">
                <?= Yii::t('app', 'Shipped') ?>
              </div>
            </th>
            <th style="text-align: center;font-size: 14px;padding: 8px;">
              <div style="width: 80px;">
                <?= Yii::t('app', 'OK') ?>
              </div>
            </th>
            <th style="text-align: center;font-size: 14px;padding: 8px;">
              <div style="width: 80px;">
                <?= Yii::t('app', 'Under shipped') ?>
              </div>
            </th>
            <th style="text-align: center;font-size: 14px;padding: 8px;">
              <div style="width: 80px;">
                <?= Yii::t('app', 'Over shipped') ?>
              </div>
            </th>
            <th style="text-align: center;font-size: 14px;padding: 8px;">
              <div style="width: 80px;">
                <?= Yii::t('app', 'Not shipped') ?>
              </div>
            </th>


            <th style="text-align: center;font-size: 14px;padding: 8px;">
              <div style="width: 80px;">
                <?= Yii::t('app', 'Planned <br>< {less_dates_count} DOH', ['less_dates_count' => Yii::$app->params['less_dates_count']]) ?>
              </div>
            </th>
            <th style="text-align: center;font-size: 14px;padding: 8px;">
              <div style="width: 80px;">
                <?= Yii::t('app', 'Shipped') ?>
              </div>
            </th>
            <th style="text-align: center;font-size: 14px;padding: 8px;">
              <div style="width: 80px;">
                <?= Yii::t('app', 'OK') ?>
              </div>
            </th>
            <th style="text-align: center;font-size: 14px;padding: 8px;">
              <div style="width: 80px;">
                <?= Yii::t('app', 'Under shipped') ?>
              </div>
            </th>
            <th style="text-align: center;font-size: 14px;padding: 8px;">
              <div style="width: 80px;">
                <?= Yii::t('app', 'Over shipped') ?>
              </div>
            </th>
            <th style="text-align: center;font-size: 14px;padding: 8px;">
              <div style="width: 80px;">
                <?= Yii::t('app', 'Not shipped') ?>
              </div>
            </th>
            <th style="text-align: center;font-size: 14px;padding: 8px;">
              <div style="width: 80px;">
                <?= Yii::t('app', 'Amount <br>> {greater_dates_count} DOH', ['greater_dates_count' => Yii::$app->params['greater_dates_count']]) ?>
              </div>
            </th>

          </tr>

        </thead>
        <tbody>

          
          <? foreach ($data as $row) {?>
             
          
          <tr>
            <td>
              <div style="text-align: center;"> 
                <?=$row['cw']?>
              </div>
            </td>
            <td class="supp"><div style="overflow: hidden;text-overflow: ellipsis;white-space: nowrap;width: 150px;" title="<?=$row['supplier_name']?>" >
              <?=$row['supplier_name']?>
            </div></td>
            <td><div style="overflow: hidden;text-overflow: ellipsis;white-space: nowrap;width: 120px;" title="<?=$row['contract_subject']?>" >
              <?=$row['contract_subject']?>
            </div></td>
            <td><div style="text-align: right;" class="<?if($row['all'] == 0) echo "zero"?>">
              <?=Helpers::numberFormatRemoveZero($row['all'],2,'.',' ',true,true)?>
            </div></td>
            <td><div style="text-align: right;" class="<?if($row['shipped'] == 0) echo "zero"?>">
              <?=Helpers::numberFormatRemoveZero($row['shipped'],2,'.',' ',true,true)?>
            </div></td>
            <td><div style="text-align: right;" class="<?if($row['ok'] == 0) echo "zero"?>">
              <?=Helpers::numberFormatRemoveZero($row['ok'],2,'.',' ',true,true)?>  
            </div></td>
            <td><div style="text-align: right;" class="<?if($row['under'] == 0) echo "zero"?>">
              <?=Helpers::numberFormatRemoveZero($row['under'],2,'.',' ',true,true)?>
            </div></td>
            <td><div style="text-align: right;" class="<?if($row['over'] == 0) echo "zero"?>">
              <?=Helpers::numberFormatRemoveZero($row['over'],2,'.',' ',true,true)?>
            </div></td>
            <td><div style="text-align: right;" class="<?if($row['not_shipped'] == 0) echo "zero"?>">
              <?=Helpers::numberFormatRemoveZero($row['not_shipped'],2,'.',' ',true,true)?>
            </div></td>
            
            <td><div style="text-align: right;" class="<?if($row['all_amount'] == 0) echo "zero"?>">
              <?=Helpers::numberFormatRemoveZero($row['all_amount'],2,'.',' ',true,true)?>
            </div></td>
            <td><div style="text-align: right;" class="<?if($row['shipped_amount'] == 0) echo "zero"?>">
              <?=Helpers::numberFormatRemoveZero($row['shipped_amount'],2,'.',' ',true,true)?>
            </div></td>
            <td><div style="text-align: right;" class="<?if($row['ok_amount'] == 0) echo "zero"?>">
              <?=Helpers::numberFormatRemoveZero($row['ok_amount'],2,'.',' ',true,true)?>  
            </div></td>
            <td><div style="text-align: right;" class="<?if($row['under_amount'] == 0) echo "zero"?>">
              <?=Helpers::numberFormatRemoveZero($row['under_amount'],2,'.',' ',true,true)?>
            </div></td>
            <td><div style="text-align: right;" class="<?if($row['over_amount'] == 0) echo "zero"?>">
              <?=Helpers::numberFormatRemoveZero($row['over_amount'],2,'.',' ',true,true)?>
            </div></td>
            <td><div style="text-align: right;" class="<?if($row['not_shipped_amount'] == 0) echo "zero"?>">
              <?=Helpers::numberFormatRemoveZero($row['not_shipped_amount'],2,'.',' ',true,true)?>
            </div></td>

            <td><div style="text-align: right;" class="<?if($row['over_doh_amount'] == 0) echo "zero"?>">
              <?=Helpers::numberFormatRemoveZero($row['over_doh_amount'],2,'.',' ',true,true)?>
            </div></td>
          </tr>
          

          <?}?>



        </tbody>




      </table>
    </div>
  </div>






</div>


<?
$this->registerCssFile("@themes/tablesorter/scroller.css", ['depends' => [AdminLteAsset::className()]]);
$this->registerCssFile("@themes/tablesorter/theme.blue.css", ['depends' => [AdminLteAsset::className()]]);
$this->registerJsFile("@themes/tablesorter/jquery.tablesorter.js", ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile("@themes/tablesorter/jquery.tablesorter.widgets.js", ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile("@themes/tablesorter/widget-scroller.js", ['depends' => [JqueryAsset::className()]]);
?>

<?php
$docReadyJs = <<< JS
$(document).ready(function() {

  // download button
  $('.cont-bom-cost').on('click', '.download-xls', function (e) {
		html_xls_export($(this).attr('table-id'), $(this).attr('filename'));
  });	



  $('.flocOrLine').on('change', function (e) {
    var radioValue = $(this).find('input[name="FactByHourForm[flocOrLine]"]:checked').val();
    if(radioValue == 'floc'){
      $('.field-factbyhourform-line').hide();
      $('.field-factbyhourform-floc').show();
    }else{
      $('.field-factbyhourform-line').show();
      $('.field-factbyhourform-floc').hide();
    }
		console.log(radioValue);
  });
  

  

  // tablesorter

  var table = $('#main-table');

  window_h = $(window).height();
  table_h = window_h - 300;

  function resizeAndRefresh() {
    $('#main-table').trigger("applyWidgets");
    $(window).trigger("resize");  
  }

  // Initialize tablesorter
  // ***********************
  table.tablesorter({
    theme: 'blue',
    //widthFixed : true,
    widgets: ["zebra", "filter", "scroller"],
    widgetOptions : {

      filter_cssFilter   : '',
      filter_childRows   : false,
      filter_hideFilters : false,
      filter_ignoreCase  : true,
      filter_reset : '.reset',
      filter_saveFilters : true,
      filter_searchDelay : 300,
      filter_startsWith  : false,

      stickyHeaders_addResizeEvent : true,
      scroller_upAfterSort: true,
      scroller_jumpToHeader: true,
      scroller_height: table_h,
      scroller_fixedColumns: 0,
      scroller_addFixedOverlay: false,
      scroller_rowHighlight: "hover",
      scroller_barWidth: null
    }


    
     

  });

  resizeAndRefresh();  


    $('select.tablesorter-filter').select2();

    $('.tablesorter-filter').css("height", "34px")
                            .css("border", "1px solid #d2d6de")
                           .css("font-weight", "normal")
                           .css("font-size", "14px");

    $('.select2-container').css("font-weight", "normal")
                           .css("font-size", "14px");

    $('.select2-selection').css("padding-left", "5px")
                          .css("text-align", "left");

 

    
   
  $('#loading').hide();
                           
  
}) 

JS;
$this->registerJs($docReadyJs, yii\web\View::POS_END);
?>