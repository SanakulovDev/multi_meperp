<?php

use app\assets\AdminLteAsset;
use app\components\Helpers;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Material BOM Cost');
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

  .tablesorter-blue th, .tablesorter-blue thead td{
    background-color: #e1f4f7;
    border: #dcdcda 1px solid;
    color: #333;
    text-shadow: none;
  }

  .tablesorter-blue tbody tr  td{
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
    vertical-align: middle !important;
  }

  .tablesorter-blue .headerSortUp, .tablesorter-blue .tablesorter-headerSortUp, .tablesorter-blue .tablesorter-headerAsc {
    background-color: #d1e8ff;
  }

  .tablesorter-blue .headerSortDown, .tablesorter-blue .tablesorter-headerSortDown, .tablesorter-blue .tablesorter-headerDesc {
    background-color: #c8def5; 
  }

  .tablesorter-blue th, .tablesorter-blue td {
    border: #dcdcda 1px solid;
  }

  .tablesorter-blue .tablesorter-filter-row td {
    background-color: #e1f4f7;
  }

  .tablesorter tbody tr:hover td,
  .tablesorter tbody tr:hover th {
      background-color: inherit !important;
  }

  .zero{
    color: #cac7c7 !important;
  }
  


  .modal-bom-stock {
    width: 95%;
  }

  .td-nowrap {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

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
    <div id="div_fix_table" class="col-lg-12 ">
      <table id="main-table" class="tablesorter">
        <thead>
          <tr>
            <th rowspan="2" style="width: 200px;text-align: center;font-size: 14px;padding: 8px;" class="filter-select" ><?= Yii::t('app', 'Product number') ?></th>
            <th rowspan="2" style="text-align: center;font-size: 14px;padding: 8px;"><?= Yii::t('app', 'Part name') ?></th>
            <th rowspan="2" style="width: 150px;text-align: center;font-size: 14px;padding: 8px;" class="filter-select"><?= Yii::t('app', 'Product group') ?></th>
            <th colspan="6" style="text-align: center;font-size: 14px;padding: 8px;"><?= Yii::t('app', 'BOM Cost') ?></th>
          </tr>
          <tr>
            <th style="width: 120px;text-align: center;font-size: 14px;padding: 8px;">UZS</th>
            <th style="width: 120px;text-align: center;font-size: 14px;padding: 8px;">USD</th>
            <th style="width: 120px;text-align: center;font-size: 14px;padding: 8px;">EUR</th>
            <th style="width: 120px;text-align: center;font-size: 14px;padding: 8px;">RUB</th>
            <th style="width: 140px;text-align: center;font-size: 14px;padding: 8px;"><?=Yii::t('app', 'Total')?><br>(<?=Yii::t('app', 'in 1000 UZS')?>)</th>
          </tr>
        </thead>
        <tbody>

          <?
          foreach ($data as $item) {
          ?>
            <tr>
              <td>
                <?= Html::a($item['prod']->partinfo, '#', [
                  // 'data-toggle' => 'modal',
                  // 'data-target' => '#modal-bom-cost',
                  'class' => 'link-group',
                  'data-part-id' => $item['prod']->id,
                  'data-url' => Url::toRoute(['report/bom-cost-detail'])
                ]) ?>
              </td>
              <td><?= $item['prod']->part_name ?></td>
              <td><?= $item['prod']->productGroup->title ?? '' ?></td>
              <td style="text-align: right;padding: 8px;" class = "td-uzs <?if($item['uzs'] == 0) echo " zero";?>" ><?=Helpers::numberFormatRemoveZero($item['uzs'],2,'.',' ',true,true)?></td>
              <td style="text-align: right;padding: 8px;" class = "td-usd <?if($item['usd'] == 0) echo " zero";?>" ><?=Helpers::numberFormatRemoveZero($item['usd'],2,'.',' ',true,true)?></td>
              <td style="text-align: right;padding: 8px;" class = "td-eur <?if($item['eur'] == 0) echo " zero";?>" ><?=Helpers::numberFormatRemoveZero($item['eur'],2,'.',' ',true,true)?></td>
              <td style="text-align: right;padding: 8px;" class = "td-rub <?if($item['rub'] == 0) echo " zero";?>" ><?=Helpers::numberFormatRemoveZero($item['rub'],2,'.',' ',true,true)?></td>
              <td style="text-align: right;padding: 8px;" class = "td-tuzs <?if($item['tuzs'] == 0) echo " zero";?>" ><?=($item['tuzs'] != 'N/A') ? Helpers::numberFormatRemoveZero($item['tuzs'],2,'.',' ',true,true) : $item['tuzs']?></td>
          <? } ?>


        </tbody>




      </table>
    </div>
  </div>
<div class="modal-temp">

  <div class="modal fade modal-bom-cost" style="display: none;">
    <div class="modal-dialog modal-lg modal-bom-stock">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close btn-close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">

        <div class="row" style="margin-bottom: 15px;">
          <div class="col-md-12 col-lg-12">
            <div class="pull-right">
              <?= Html::button(Yii::t('app', 'btn-download-delivery-plan'), [
                'class' => 'btn btn-info btn-xs download-xls',
                'filename' => $downloadFileNameDetail,
                ]) ?>
            </div>
          </div>
        </div>

          <table class="table table-bordered table-bom-cost">
            <thead>
              <tr>
                <th rowspan="2" style="width: 200px;"  ><?= Yii::t('app', 'Part number') ?></th>
                <th rowspan="2"><?= Yii::t('app', 'Part name') ?></th>
                <th rowspan="2"><?= Yii::t('app', 'Component') ?><br><?= Yii::t('app', 'Semi-finished') ?></th>
                <th rowspan="2" style="width: 100px;"><?= Yii::t('app', 'Usage qty') ?></th>
                <th rowspan="2" style="width: 80px;"><?= Yii::t('app', 'Unit') ?></th>
                <th colspan="6"><?= Yii::t('app', 'BOM Cost') ?></th>
              </tr>
              <tr>
                <th style="width: 100px;">UZS</th>
                <th style="width: 100px;">USD</th>
                <th style="width: 100px;">EUR</th>
                <th style="width: 100px;">RUB</th>
                <th style="width: 120px;"><?=Yii::t('app', 'Total')?><br>(<?=Yii::t('app', 'in 1000 UZS')?>)</th>
              </tr>
              <tr class="ptotal">
                <th colspan="5" style="text-align: right;"><?=Yii::t('app', 'Total')?></th>
                <th style="width: 100px;text-align: right;" class="puzs">0</th>
                <th style="width: 100px;text-align: right;" class="pusd">0</th>
                <th style="width: 100px;text-align: right;" class="peur">0</th>
                <th style="width: 100px;text-align: right;" class="prub">0</th>
                <th style="width: 100px;text-align: right;" class="ptuzs">0</th>
              </tr>
            </thead>
            <tbody>
              
            </tbody>
          </table>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
  <!-- /.modal-dialog -->
  </div>

</div>



<div class="modals">

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
  
  // show modal
  $('.cont-bom-cost').on('click', '.link-group', function (e) {


    $('#loading').show();

    var part_id = $(this).attr('data-part-id');
    var url   = $(this).attr("data-url");

    var tr = $(this).parent().parent();
    var puzs = tr.find('.td-uzs').html();
    var pusd = tr.find('.td-usd').html();
    var peur = tr.find('.td-eur').html();
    var prub = tr.find('.td-rub').html();
    var ptuzs = tr.find('.td-tuzs').html();

    $.ajax({
            dataType: "json",
            type: "GET",
            url: url + "?part_id=" + part_id,
            success: function(data){

              $('#modal_' + part_id).remove();

              $('.modals').append($('.modal-temp').html());

              $('.modals div.modal:last-child').attr('id','modal_' + part_id);
              var modal = $('#modal_' + part_id);

              modal.find('.modal-title').html(data.part.partinfo + ' ' + data.part.part_name);

              modal.find('.download-xls').attr('table-id','table_' + part_id);

              modal.find('.table-bom-cost').attr('id','table_' + part_id);

              // total larni yozish
              // modal.find('.puzs').html(puzs);
              // modal.find('.pusd').html(pusd);
              // modal.find('.peur').html(peur);
              // modal.find('.prub').html(prub);
              // modal.find('.ptuzs').html(ptuzs);

              var tbody = modal.find('.table-bom-cost').find('tbody');
              tbody.html('');
            
              var td = tds = ''
              $.each(data.items, function(k, v) {

                td = "<tr>";
                if(v.comp.state_code == 0){
                  td += "<td>"+v.comp.partinfo+"</td>";
                }else{
                  td += "<td><a href=\"#\" class=\"link-group\" data-part-id = \""+v.comp.id+"\" data-url=\""+url+"\">"+v.comp.partinfo+"</a></td>";
                }

                var uzsZero = '';
                var usdZero = '';
                var eurZero = '';
                var rubZero = '';
                var tuzsZero = '';
                
                if(v.uzs == 0) uzsZero = 'zero';
                if(v.usd == 0) usdZero = 'zero';
                if(v.eur == 0) eurZero = 'zero';
                if(v.rub == 0) rubZero = 'zero';
                if(v.tuzs == 0) tuzsZero = 'zero';

                td += "<td>"+v.comp.part_name+"</td>";
                td += "<td style=\"text-align: center;\">"+v.comp.state+"</td>";
                td += "<td style=\"text-align: right;\">"+v.comp.usage_qty+"</td>";
                td += "<td style=\"text-align: center;\">"+v.comp.unit+"</td>";
                td += "<td style=\"text-align: right;\" class = \"td-uzs "+ uzsZero +"\" >"+v.uzs+"</td>";
                td += "<td style=\"text-align: right;\" class = \"td-usd "+ usdZero +"\" >"+v.usd+"</td>";
                td += "<td style=\"text-align: right;\" class = \"td-eur "+ eurZero +"\" >"+v.eur+"</td>";
                td += "<td style=\"text-align: right;\" class = \"td-rub "+ rubZero +"\" >"+v.rub+"</td>";
                td += "<td style=\"text-align: right;\" class = \"td-tuzs "+ tuzsZero +"\" >"+v.tuzs+"</td>";
                tds += td;

              });

              //console.log(data);

              // total larni yozish
              if(data.items.length > 1){
                modal.find('.puzs').html(data.total.uzs);
                modal.find('.pusd').html(data.total.usd);
                modal.find('.peur').html(data.total.eur);
                modal.find('.prub').html(data.total.rub);
                modal.find('.ptuzs').html(data.total.tuzs);
              }else{
                modal.find('.ptotal').hide();
              }
              

              tbody.html(tds);
              modal.modal('show');

              $('#loading').hide();
              
            }
          });
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
    widthFixed : true,
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
$this->registerJs($docReadyJs);
?>