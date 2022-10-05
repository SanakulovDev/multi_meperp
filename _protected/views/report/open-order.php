<?php
use app\assets\AdminLteAsset;
use app\components\Helpers;
use yii\helpers\Html;
use yii\web\JqueryAsset;

$title = Yii::t('app', 'Order status');
$this->title = $title."&emsp;".Html::button(Yii::t('app', 'btn-filter-short'), ['class' => 'btn btn-primary btn-xs', 'id' => 'table-filter']);
$this->title .= "&emsp;".Html::button(Yii::t('app', 'btn-download-delivery-plan'), ['class' => 'btn btn-info btn-xs', 'id' => 'download-xls']);
$this->params['breadcrumbs'][] = $title;
?>
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

<div class="row">
  <div class="col-md-12 col-lg-12">
    <div id="div_fix_table" class="col-md-12  col-lg-12" style="padding:0 5px 0 5px;">
      <? if ($data != null) { ?>
        <table id="fix_table" class="table-sm-padding_2_0">
          <thead>
          <tr style='font-size:90%;'>
            <!--            <th class="txt_center">№</th>-->
            <th class="txt_center"><?=Yii::t('app', 'Supplier')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Order no')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Order date')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Need date')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Last invoice date')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Order type')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Part No')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Part name')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Part color')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Unit')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Order qty')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Price')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Amount')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Currency')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Invoice qty')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Balance')?></th>
          </tr>
          </thead>
          <tbody>
          <?php
          $no = 1;
          foreach ($data as $data_list) {
            $balance = $data_list['order_qty'] - $data_list['inv_qty_sum'];
            $supplier_txt = (strlen($data_list['order_no']) > 0) ? $data_list['supplier_name']." (<span style='font-size:70%'>".$data_list['supplier_duns']."</span>)" : "";
            ?>
            <tr style='font-size:90%;'>
              <!--              <td class="midtext mar txt_right"> --><? //=$no++;?><!-- </td>-->
              <td class="midtext mar td-nowrap" style="max-width:90px;" title="<?=$supplier_txt;?>"> <?=$supplier_txt;?></td>
              <td class="midtext mar td-nowrap" style="max-width:80px;" title="<?=$data_list['order_no']?>"> <?=$data_list['order_no']?> </td>
              <td class="midtext mar txt_center"> <?=$data_list['order_dt']?> </td>
              <td class="midtext mar txt_center"> <?=$data_list['mr_dt']?> </td>
              <td class="midtext mar txt_center"> <?=$data_list['last_ship_dt']?> </td>
              <td class="midtext mar"> <?=$data_list['order_type']?> </td>
              <td class="midtext mar"> <?=$data_list['part_no']?></td>
              <td class="midtext mar td-nowrap" style="max-width:90px;" title="<?=$data_list['part_name']?>"> <?=$data_list['part_name']?> </td>
              <td class="midtext mar td-nowrap" style="max-width:80px;" title="<?=$data_list['part_color']?>"> <?=$data_list['part_color']?></td>
              <td class="midtext mar td-nowrap text-center" style="width:50px;" title="<?=$data_list['unit_value']?>"><?=$data_list['unit_value']?> </td>
              <td class="midtext mar txt_right">  <?=Helpers::numberFormatRemoveZero($data_list['order_qty'], 2, '.', " ", true)?> </td>
              <td class="midtext mar txt_right">  <?=Helpers::numberFormatRemoveZero($data_list['cont_price'], 2, '.', " ", true)?> </td>
              <td class="midtext mar txt_right">  <?=Helpers::numberFormatRemoveZero(($data_list['cont_price']*$data_list['order_qty']), 2, '.', " ", true)?> </td>
              <td class="midtext mar text-center"><?=$data_list['currency_code']?> </td>
              <td class="midtext mar txt_right">  <?=Helpers::numberFormatRemoveZero($data_list['inv_qty_sum'], 2, '.', " ", true)?> </td>
              <td class="midtext mar txt_right f_bold <? if ($balance < 0) {
                echo 'text-danger';
              } else {
                echo 'text-success';
              } ?>"> <?=Helpers::numberFormatRemoveZero($balance, 2, '.', " ", true)?> </td>
            </tr>
          <? } ?>
          </tbody>
        </table>
      <? } ?>
    </div>
  </div>
</div>

<?
$this->registerCssFile("@themes/tablesorter/scroller.css", ['depends' => [AdminLteAsset::className()]]);
$this->registerCssFile("@themes/tablesorter/theme.ice.css", ['depends' => [AdminLteAsset::className()]]);
$this->registerJsFile("@themes/tablesorter/jquery.tablesorter.js", ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile("@themes/tablesorter/jquery.tablesorter.widgets.js", ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile("@themes/tablesorter/widget-scroller.js", ['depends' => [JqueryAsset::className()]]);
?>
<?php
$script1 = <<< JS
  // $('#fix_table').tableFixer({'left' : 2});
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
    html_xls_export('fix_table', '$downloadFileName');
  });
  
  $('.tablesorter-filter-row').toggle();

  $('#table-filter').click(function(){
    $('.tablesorter-filter-row').toggle();    
    resizeAndRefresh();
  });
  
});
JS;
$this->registerJs($script1, yii\web\View::POS_END);
?>
