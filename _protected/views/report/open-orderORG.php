<?php
use app\components\Helpers;
use yii\helpers\Html;

$title = Yii::t('app', 'Order status');
$this->title = $title."&emsp;".Html::button(Yii::t('app', 'btn-download-delivery-plan'), ['class' => 'btn btn-info btn-xs', 'id' => 'download-xls']);
$this->params['breadcrumbs'][] = $title;
?>
  <br>
  <div class="row">
    <div class="col-md-12 col-lg-12">
      <div id="div_fix_table" class="col-md-12  col-lg-12">
        <? if ($data != null) { ?>
          <table id="fix_table" class="table-td-nowrap table table-striped table-bordered table-condensed table-sm-padding_2_0">
            <thead>
            <tr style='font-size:80%;'>
              <th class="txt_center"><?=Yii::t('app', 'Supplier')?></th>
              <th class="txt_center"><?=Yii::t('app', 'Order no')?></th>
              <th class="txt_center"><?=Yii::t('app', 'Order date')?></th>
              <th class="txt_center"><?=Yii::t('app', 'Need date')?></th>
              <th class="txt_center"><?=Yii::t('app', 'Last invoice date')?></th>
              <th class="txt_center"><?=Yii::t('app', 'Order type')?></th>
              <th class="txt_center"><?=Yii::t('app', 'Part No')?></th>
              <th class="txt_center"><?=Yii::t('app', 'Part color')?></th>
              <th class="txt_center"><?=Yii::t('app', 'Part name')?></th>
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
            foreach ($data as $data_list) {
              $balance = $data_list['order_qty'] - $data_list['inv_qty_sum'];
              $supplier_txt = (strlen($data_list['order_no']) > 0) ? $data_list['supplier_name']." (<span style='font-size:70%'>".$data_list['supplier_duns']."</span>)" : "";
              ?>
              <tr>
                <td class="midtext mar"> <?=$supplier_txt;?> </td>
                <td class="midtext mar"> <?=$data_list['order_no']?> </td>
                <td class="midtext mar txt_center"> <?=$data_list['order_dt']?> </td>
                <td class="midtext mar txt_center"> <?=$data_list['mr_dt']?> </td>
                <td class="midtext mar txt_center"> <?=$data_list['last_ship_dt']?> </td>
                <td class="midtext mar"> <?=$data_list['order_type']?> </td>
                <td class="midtext mar"> <?=$data_list['part_no']?></td>
                <td class="midtext mar"> <?=$data_list['part_color']?></td>
                <td class="midtext mar td-nowrap" style="max-width:150px;" title="<?=$data_list['part_name']?>"> <?=$data_list['part_name']?> </td>
                <td class="midtext mar">            <?=$data_list['unit_value']?> </td>
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
              <?
            }
            ?>
            </tbody>
          </table>
        <? } ?>
      </div>
    </div>
  </div>
<?php
$script1 = <<< JS
	$('#fix_table').tableFixer({'left' : 2});
	changeHeight();
	$(window).resize(function(){
		changeHeight();
	});
	function changeHeight(){
		window_h = $(window).height();
		table_h = window_h - 200;
		$('#div_fix_table').height(table_h+'px');
	}

	$('#download-xls').on('click', function (e) {
		html_xls_export('fix_table', '$downloadFileName');
	});

JS;
$this->registerJs($script1);
?>