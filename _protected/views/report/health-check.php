<?php
use yii\helpers\Html;

$title = Yii::t('app', 'Savolnoma');
$this->title = $title."&emsp;".Html::button(Yii::t('app', 'btn-download-delivery-plan'), ['class' => 'btn btn-info btn-xs', 'id' => 'download-xls']);
$this->params['breadcrumbs'][] = $title;
?>
<style>
  .bg-red{ background-color: red !important;}
  .bg-yellow{ background-color: yellow !important;}
  .bg-green{ background-color: green !important;}
  .sts-txt{color: black !important; font-weight:bold; }
</style>
  <br>
  <div class="row">
    <div class="col-md-12 col-lg-12">
      <div id="div_fix_table" class="col-md-12  col-lg-12">
        <? if ($data != null) { ?>
<!--          <table id="fix_table" class="table-td-nowrap table table-striped table-bordered table-condensed table-sm-padding_2_0">-->
          <table id="fix_table" class="table-td-nowrap table table-striped table-bordered table-condensed">
            <thead>
            <tr style='font-size:90%;'>
              <th>№</th>
              <th class="txt_center"><?=Yii::t('app', 'Question list')?></th>
              <th class="txt_center"><?=Yii::t('app', 'Status')?></th>
              <th class="txt_center"><?=Yii::t('app', 'Refreshed at')?></th>
            </tr>
            </thead>
            <tbody>
            <? $q = 1;
            foreach ($data as $data_list) {
              switch ($data_list['status']) {
                case 'G':
                  $sts = 'bg-green';
                  break;
                case 'Y':
                  $sts = 'bg-yellow';
                  break;
                case 'R':
                  $sts = 'bg-red';
                  break;
                default:
                  $sts = 'bg-white';
              } ?>
              <tr>
                <td class="midtext mar txt_right"> <?=$q++?> </td>
                <td class="midtext mar"> <?=$data_list['savol_nomi']?> </td>
                <td class="midtext mar txt_center sts-txt <?=$sts?>" title="<?=$data_list['sabab']?>"> <?=$data_list['status']?> </td>
                <td class="midtext mar"> <?=$data_list['updated_at']?> </td>
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
	$('#fix_table').tableFixer({'left': 1});
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