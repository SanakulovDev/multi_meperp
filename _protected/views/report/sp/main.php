<?php

use app\assets\AdminLteAsset;
use yii\helpers\Html;
use yii\web\JqueryAsset;

$this->title = Yii::t('app', 'Shipment performance');
$this->params['breadcrumbs'][] = $this->title;
// echo '<pre>';
// print_r($data);
// echo '</pre>';
// die;

?>

<? require(Yii::getAlias('@views') . '/common/_loading.php'); ?>


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

  .red {
    font-weight: bold;
    color: #d42519 !important;
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

<?
  if(isset($_GET['sp_id'])){
    if(!isset($_GET['supplier_id'])){
      $viewfile = '_supplier';
      $backRoute = ['report/sp'];
    }else{
      $viewfile = '_part';
      $backRoute = ['report/sp','sp_id' => $_GET['sp_id']];
    }
  }else{
    $viewfile = '_cw';
    $backRoute = ['report/index'];
  }
?>

<div class="cont-bom-cost">
  <div class="row">
    <div class="col-md-2 col-lg-2">
      <?= Html::a(Yii::t('app', 'btn-back'), $backRoute, ['class' => 'btn btn-default btn-xs']) ?>
    </div>
    <div class="col-md-10 col-lg-10">
      <div class="pull-right">

        <button type="button" id="table-filter" class="btn btn-primary btn-xs">
          <i class="glyphicon glyphicon-filter"></i> <span class="show-filter"><?=Yii::t('app', 'Show filter');?></span><span class="hide-filter" style="display: none;"><?=Yii::t('app', 'Hide filter');?></span>
        </button>

        <?= Html::button(Yii::t('app', 'btn-download-delivery-plan'), [
          'class' => 'btn btn-info btn-xs download-xls',
          'table-id' => 'main-table',
          'filename' => $downloadFileName
        ]) ?>
      </div>
    </div>
  </div>
  
  <div class="row">
    <div class="col-lg-12 ">

    <?= 
      $this->render($viewfile . '.php',[
        'data' => $data
      ])
    ?>


    

      
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
  $('.tablesorter-filter-row').toggle();
  
  $('#table-filter').click(function(){
    $('.tablesorter-filter-row').toggle();    
    $('.hide-filter').toggle();
    $('.show-filter').toggle();
    resizeAndRefresh();
  });
  resizeAndRefresh();  
    $('select.tablesorter-filter').select2();
    $('.tablesorter-filter').css("height", "25px")
                            .css("border", "1px solid #d2d6de")
                           .css("font-weight", "normal")
                           .css("font-size", "12px");
    $('.select2-container').css("font-weight", "normal")
                           .css("font-size", "12px");
    $('.select2-selection').css("padding-left", "5px")
                          .css("text-align", "left");
    $('.tablesorter-filter-row').find('td').css("padding", "2px");                      
 
    
   
  $('#loading').hide();
                           
  
}) 
JS;
$this->registerJs($docReadyJs);
?>