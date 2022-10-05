<?php
use app\assets\AdminLteAsset;
use app\components\Helpers;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\JqueryAsset;

$title = Yii::t('app', 'PFEP Active Parts');
$this->title = $title."&emsp;".Html::button(Yii::t('app', 'btn-filter-short'), ['class' => 'btn btn-primary btn-xs', 'id' => 'table-filter']);
$this->title .= "&emsp;".Html::button(Yii::t('app', 'btn-download-delivery-plan'), ['class' => 'btn btn-info btn-xs', 'id' => 'download-xls']);
$this->params['breadcrumbs'][] = $title;
?>

<?= $this->render('../common/_loading'); ?>

<style>
  /* OPTIONAL CSS! */
  #fix_table tbody td{
    /* force "Notes" column to not wrap, so we get a horizontal scrolling demo! */
    white-space:nowrap !important;
    /* Add min column width, or "Index" column filter gets too narrow to use */
    min-width:10px;
  }
  .tablesorter-filter-row{
    font-size:70%;
  }
</style>

<?php $form = ActiveForm::begin([ 'layout' => 'inline' ]); ?>

<div class="row">
  <div class="col-lg-12">
    <?= $form->field($model, 'type')->dropDownList($filters)->label(false) ?>
    <div class="form-group">
      <?= Html::submitButton(Yii::t('app', 'btn-show'), ['class' => 'btn btn-success btn-sm']) ?>
    </div>
  </div>

</div>

<?php ActiveForm::end(); ?>

<div class="row">
  <div class="col-md-12 col-lg-12">
    <div id="div_fix_table" class="col-md-12  col-lg-12" style="padding:0 5px 0 5px;">
      <? if ($data != null) { ?>
        <table id="fix_table" class="table-sm-padding_2_0">
          <thead>
          <tr style="font-size:90%;">
            <th class="txt_center" rowspan="2"><?=Yii::t('app', 'Product model')?></th>
            <th class="txt_center" rowspan="2"><?=Yii::t('app', 'Part No')?></th>
            <th class="txt_center" rowspan="2"><?=Yii::t('app', 'Part name')?></th>
            <th class="txt_center" rowspan="2"><?=Yii::t('app', 'Part color')?></th>
            <th class="txt_center" rowspan="2"><?=Yii::t('app', 'CNFEA Code')?></th>
            <th class="txt_center" rowspan="2"><?=Yii::t('app', 'Supplier')?></th>
            <th class="txt_center" rowspan="2"><?=Yii::t('app', 'Standard pack')?></th>
            <th class="txt_center" rowspan="2"><?=Yii::t('app', 'Part weight (kg)')?></th>
            <th class="txt_center" colspan="7">1</th>
            <th class="txt_center" colspan="7">2</th>
            <th class="txt_center" rowspan="2"><?=Yii::t('app', 'Quantity')?></th>
            <th class="txt_center" rowspan="2"><?=Yii::t('app', 'Warehouse')?></th>
            <th class="txt_center" rowspan="2"><?=Yii::t('app', 'MRP')?></th>
            <th class="txt_center" rowspan="2"><?=Yii::t('app', 'Dloc')?></th>
            <th class="txt_center" rowspan="2"><?=Yii::t('app', 'Minimum')?></th>
            <th class="txt_center" rowspan="2"><?=Yii::t('app', 'Maximum')?></th>
            <th class="txt_center" rowspan="2"><?=Yii::t('app', 'Stack')?></th>
          </tr>
          <tr style="font-size:90%;">
            <th class="txt_center"><?=Yii::t('app', 'Packaging code')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Packaging material')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Thickness (mm)')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Weight (kg)')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Length (cm)')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Width (cm)')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Height (cm)')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Packaging code')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Packaging material')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Thickness (mm)')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Weight (kg)')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Length (cm)')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Width (cm)')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Height (cm)')?></th>
          </tr>
          </thead>
          <tbody>
          <?php
          $no = 1;
          foreach ($data as $item) {
          ?>
            <tr style="font-size:90%;">
              <td class="midtext mar td-nowrap" style="max-width:90px;" title="<?=$item['modelName']?>"> <?=$item['modelName']?> </td>
              <td class="midtext mar"> <?=$item['part_no']?></td>
              <td class="midtext mar"> <?=$item['part_color']?></td>
              <td class="midtext mar td-nowrap" style="max-width:90px;" title="<?=$item['part_name']?>"> <?=$item['part_name']?> </td>
              <td class="midtext mar td-nowrap" style="max-width:90px;" title="<?=$item['cnfea']?>"> <?=$item['cnfea']?> </td>
              <td class="midtext mar td-nowrap" style="max-width:90px;" title="<?=$item['supplier']?>"> <?=$item['supplier']?> </td>
              <td class="midtext mar txt_center">
                <?=Helpers::numberFormatRemoveZero($item['pack_qty'],2,'.',' ',true,true)?>
              </td>
              <td class="midtext mar txt_center">
                <?=Helpers::numberFormatRemoveZero($item['piece_weight'],2,'.',' ',true,false)?>
              </td>
              <td class="midtext mar td-nowrap" style="max-width:80px;" title="<?=$item['code']?>"> <?=$item['code']?> </td>
              <td class="midtext mar td-nowrap" style="max-width:80px;" title="<?=$item['construction']?>"> <?=$item['construction']?> </td>
              <td class="midtext mar txt_center"> <?=$item['thickness']?> </td>
              <td class="midtext mar txt_center"> <?=$item['weight']?> </td>
              <td class="midtext mar"> <?=$item['length']?> </td>
              <td class="midtext mar"> <?=$item['width']?> </td>
              <td class="midtext mar"> <?=$item['height']?> </td>

              <td class="midtext mar td-nowrap" style="max-width:80px;" title="<?=$item['code1']?>"> <?=$item['code1']?> </td>
              <td class="midtext mar td-nowrap" style="max-width:80px;" title="<?=$item['construction1']?>"> <?=$item['construction1']?> </td>
              <td class="midtext mar txt_center"> <?=$item['thickness1']?> </td>
              <td class="midtext mar txt_center"> <?=$item['weight1']?> </td>
              <td class="midtext mar"> <?=$item['length1']?> </td>
              <td class="midtext mar"> <?=$item['width1']?> </td>
              <td class="midtext mar"> <?=$item['height1']?> </td>
              <td class="midtext mar">
                <?=Helpers::numberFormatRemoveZero($item['pack_level_quantity'],2,'.',' ',true,true)?>
              </td>
              <td class="midtext mar td-nowrap" style="max-width:90px;" title="<?=$item['warehouse']?>"> <?=$item['warehouse']?> </td>
              <td class="midtext mar td-nowrap" style="max-width:90px;" title="<?=$item['mpr']?>"> <?=$item['mpr']?> </td>
              <td class="midtext mar td-nowrap" style="max-width:90px;" title="<?=$item['dloc']?>"> <?=$item['dloc']?> </td>
              <td class="midtext mar td-nowrap" style="max-width:90px;" title="<?=$item['minimum']?>"> <?=$item['minimum']?> </td>
              <td class="midtext mar td-nowrap" style="max-width:90px;" title="<?=$item['maximum']?>"> <?=$item['maximum']?> </td>
              <td class="midtext mar td-nowrap" style="max-width:90px;" title="<?=$item['stack']?>"> <?=$item['stack']?> </td>
            </tr>
          <? } ?>
          </tbody>
        </table>
      <? } ?>
    </div>
  </div>
</div>

<?php
$this->registerJsFile("@themes/js/xlsx.full.min.js", ['depends' => [JqueryAsset::className()]]);
$this->registerCssFile("@themes/tablesorter/scroller.css", ['depends' => [AdminLteAsset::className()]]);
$this->registerCssFile("@themes/tablesorter/theme.ice.css", ['depends' => [AdminLteAsset::className()]]);
$this->registerJsFile("@themes/tablesorter/jquery.tablesorter.js", ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile("@themes/tablesorter/jquery.tablesorter.widgets.js", ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile("@themes/tablesorter/widget-scroller.js", ['depends' => [JqueryAsset::className()]]);
?>

<?php
$script1 = <<< JS
  window_h = $(window).height();
  table_h = window_h - 210;
		
$(function(){
  function resizeAndRefresh() {
    $("#fix_table").trigger("applyWidgets");
    $(window).trigger("resize");  
  }
  
  $("#fix_table").tablesorter({
      theme: "ice",
      showProcessing: true,
      headerTemplate: "{content} {icon}",
      // headers: { 0: { sorter: false, filter: false} },
      widgets: ["zebra", "filter", "scroller"],
      widgetOptions: {
        stickyHeaders_addResizeEvent : true,
        // scroll tbody to top after sorting
        scroller_upAfterSort: true,
        // pop table header into view while scrolling up the page
        scroller_jumpToHeader: true,
        scroller_height: table_h,
        // set number of columns to fix
        scroller_fixedColumns: 0,
        // add a fixed column overlay for styling
        scroller_addFixedOverlay: false,
        // add hover highlighting to the fixed column (disable if it causes slowing)
        scroller_rowHighlight: "hover",
        // bar width is now calculated; set a value to override
        scroller_barWidth: null
      }
    });

  resizeAndRefresh();  
  
  $('#download-xls').on('click', function (e) {
    var elt = document.getElementById('fix_table');
    var wb = XLSX.utils.table_to_book(elt, {sheet:"PFEP Active Parts"});        
    return XLSX.writeFile(wb, '$downloadFileName.xlsx'	);
  });
  
  $('.tablesorter-filter-row').toggle();

  $('#table-filter').click(function(){
    $('.tablesorter-filter-row').toggle();    
    resizeAndRefresh();
  });
  
  $('#loading').hide();
  
});
JS;
$this->registerJs($script1, yii\web\View::POS_END);
?>
