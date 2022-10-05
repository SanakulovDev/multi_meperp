<?php

use app\assets\AdminLteAsset;
use app\components\Helpers;
use app\models\Warehouse;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Fact by hour');
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


  <?php $form = ActiveForm::begin([
    'layout' => 'inline'
  ]); ?>

  <div class="row">
    <div class="col-lg-12">
      <?= $form->field($modelForm, 'flocOrLine')->radioList(['floc' => Yii::t('app', 'FLOC'), 'line' => Yii::t('app', 'Line')], ['style' => 'margin-right: 20px;', 'class' => 'flocOrLine'])->label(false) ?>

      <?
  if($modelForm->flocOrLine == 'floc'){
    $displayFloc = '';
    $displayLine = 'display:none;';
  }else{
    $displayFloc = 'display:none;';
    $displayLine = '';
  }

  ?>

      <?= $form->field($modelForm, 'floc', ['options' => ['class' => 'form-group', 'style' => $displayFloc]])->dropDownList(ArrayHelper::map(app\models\Warehouse::find()->where(['warehouse_type' => Warehouse::TYPE_SHOP])->all(), 'id', 'name'), [
        'class' => ' form-control select2',
        'prompt' => Yii::t('app', 'All ...'),
        ['style' => 'margin-right: 20px;']
      ]);
      ?>

      <?= $form->field($modelForm, 'line', ['options' => ['class' => 'form-group', 'style' => $displayLine]])->dropDownList(ArrayHelper::map(app\models\ProductLine::find()->all(), 'id', 'linename'), [
        'class' => ' form-control select2',
        'prompt' => Yii::t('app', 'All ...'),
        ['style' => 'margin-right: 20px;']
      ]);
      ?>

      <?= $form->field($modelForm, 'todayOrYesterday')->radioList(['today' => Yii::t('app', 'Today'), 'yesterday' => Yii::t('app', 'Yesterday')], ['style' => 'margin-right: 20px;'])->label(false) ?>

      <?= $form->field($modelForm, 'shift')->radioList(['1' => Yii::t('app', 'Shift') . ' 1', '2' => Yii::t('app', 'Shift') . ' 2'], ['style' => 'margin-right: 20px;'])->label(false) ?>

      <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'btn-show'), ['class' => 'btn btn-success btn-sm']) ?>
      </div>
    </div>












  </div>

  <?php ActiveForm::end(); ?>

  <div class="row">
    <div id="div_fix_table" class="col-lg-12 ">
      <table id="main-table" class="tablesorter">
        <thead>
          <tr>
            <th style="text-align: center;font-size: 14px;padding: 8px;" class="filter-select">
              <div style="width: 150px;">
                <?= Yii::t('app', 'FLOC/Line') ?>
              </div>
            </th>
            <th style="width: 150px;text-align: center;font-size: 14px;padding: 8px;" class="filter-select"><?= Yii::t('app', 'Part number') ?></th>
            <th style="text-align: center;font-size: 14px;padding: 8px;"><?= Yii::t('app', 'Part name') ?></th>
            <?for ($i=1; $i <= 12; $i++) {?>
            <? if($modelForm->shift == 2) {
                $c = $i + 12;
              }else{
                $c = $i;
              }
              ?>
            <th style="width: 90px;text-align: center;font-size: 14px;padding: 8px;">H<?= $c ?></th>
            <?}?>
            <th style="width: 90px;text-align: center;font-size: 14px;padding: 8px;">
              <div style="width: 90px;">
                <?= Yii::t('app', $modelForm->shift . '-shift') ?>
              </div>
            </th>
          </tr>
        </thead>
        <tbody>

          <?
          foreach ($data as $item) {
          ?>
          <tr>
            <td class="td-nowrap" style="font-size: 14px;padding: 8px;">
              <div style="width: 150px;">
              <?= ($modelForm->flocOrLine == 'floc') ? $item['floc'] : $item['line'] ?>
              </div>
              
            </td>
            <td class="td-nowrap" style="width: 150px;font-size: 14px;padding: 8px;">
              <div style="overflow: hidden;text-overflow: ellipsis;white-space: nowrap;width: 180px;" title="<?= $item['partinfo'] ?>">
                <?= $item['partinfo'] ?>
              </div>
            </td>
            <td class="td-nowrap" style="font-size: 14px;padding: 8px;">
              <div style="
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    width: 200px;
" title="<?= $item['partname'] ?>">
                <?= $item['partname'] ?>
              </div>


            </td>
            <?for ($i=1; $i <= 12; $i++) {?>
            <td style="text-align: right;padding: 8px;width: 90px;" class="<?if($item['F'.$i] == 0) echo " zero";?>" ><?= number_format($item['F' . $i], 0, '.', ' ') ?></td>
            <?}?>
            <td style="text-align: right;padding: 8px;width: 90px;" class="<?if($item['total'] == 0) echo " zero";?>" ><?= number_format($item['total'], 0, '.', ' ') ?></td>
          </tr>
          <? } ?>


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
$this->registerJs($docReadyJs);
?>