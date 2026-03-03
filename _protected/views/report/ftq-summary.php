<?php
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/** @var $data app\controllers\ReportController */
/** @var $lineList app\controllers\ReportController */
/** @var $filterLine app\controllers\ReportController */
/** @var $filterMonth app\controllers\ReportController */
/** @var $filterShift app\controllers\ReportController */
/** @var $downloadFileName app\controllers\ReportController */
$this->title = Yii::t('app', 'FTQ by product line');
$this->params['breadcrumbs'][] = $this->title;
$monthEnd = date("Y-m-t", strtotime($filterMonth));
$monthEndDT = date('t', strtotime($monthEnd));
?>
<style>
	#gridContent{
		height:500px;
		margin-top:-20px;
	}
	.gray-title{
		background-color:#ececec !important;
		color:#515151 !important;
	}
	.id-column-cls .fancy-grid-cell-inner{
		margin-left:14px;
		font-size:14px;
	}
	.fancy-theme-gray .fancy-panel{
		border-right-width:1px;
		border-bottom:1px solid #ddd;
	}
	.fancy-theme-gray .fancy-panel-body{
		border:1px solid #ddd !important;
	}
	.fancy-theme-gray.fancy-panel .fancy-panel-header{
		border:thin solid #ddd !important;
	}
	.fancy-theme-gray .fancy-grid-header-cell{
		background:#ececec; !important;
		border-color:#ddd;
		color:#515151;
		font-size:11px;
		text-align:center;
	}
	.fancy-theme-gray .fancy-grid-header{
		background-color:#F3F3F3;
		text-align:center;
	}
	.fancy-theme-gray .fancy-grid-cell-even{
		background-color:#f5f7f7;
		background-color:#F9F9F9;
	}
	.fancy-theme-gray .fancy-scroll-bottom-inner{
		background:#DAE1E8;
	}
	.fancy-theme-gray .fancy-grid-cell-inner{
		color:#888;
	}
	.fancy-theme-gray .fancy-grid-cell{
		border-bottom-color:#ccc;
	}
	.column-cls-gray-model-name .fancy-grid-cell{
		background:#626262 !important;
		border-right-color:#626262;
	}
	.column-cls-gray-model-name .fancy-grid-cell-inner{
		color:#FFF;
		font:12px/20px Arial;
		margin-top:6px;
	}
	.fancy-theme-gray .fancy-grid-column .fancy-grid-cell-over{
		background:#e5e5e7;
	}

</style>

<div class="req-index">
	<div class="panel">
		<div class="panel-heading">
			<img style="height:28px;" src="/img/logo.jpg" title="<?php echo Yii::$app->params['comp_name'] ?>" class="pull-left"/>
			<div class="row">
        <?php $form = ActiveForm::begin(['action' => ['ftq-summary'], 'method' => 'post',]); ?>
				<div class="col-sm-3 col-md-2">
          <?=DateTimePicker::widget(
            [
              'name' => 'filterMonth',
              'value' => $filterMonth,
              'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
              'layout' => '{picker}{input}{remove}',
              'removeButton' => ['position' => 'append'],
              'language' => 'ru',
              'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm',
                'startView' => 'year',
                'minView' => 'year',
                'maxView' => 'year',
              ],
              'options' => [
                'autocomplete' => 'off',
                'placeholder' => Yii::t('app', 'filterMonth'),
                'class' => ' form-control'
              ]
            ]
          );?>
				</div>
				<div class="col-sm-3 col-md-2">
          <?=Html::dropDownList('filterLine', $filterLine, $lineList,
            ['prompt' => Yii::t('app', 'All lines'), 'class' => 'form-control select2']
          )?>
				</div>
				<div class="col-sm-3 col-md-2">
          <?=Html::dropDownList('filterShift', $filterShift,
            ['1' => 1, '2' => 2],
            ['prompt' => Yii::t('app', 'Shift'), 'class' => 'form-control select2']
          )?>
				</div>
				<div class="col-sm-3 col-md-2">
					<div class="form-group">
            <?=Html::submitButton(Yii::t('app', 'btn-show'), ['class' => 'btn btn-primary btn-sm'])?>
            <?=Html::button(Yii::t('app', 'btn-download'), ['class' => 'btn btn-info btn-sm', 'id' => 'download-xls'])?>
					</div>
				</div>
        <?php ActiveForm::end(); ?>
			</div>
		</div>
		<div class="panel-body">
			<div class="div_fix_table col-md-12">
				<div class="text-bold text-center" style="font-size:150%"><?=Yii::t('app', $this->title)?> </div>
				<div class="clearfix"></div>
				<table id="fix_table" class="table table-striped table-bordered table-condensed table-sm-padding_2_0">
					<thead>
					<tr>
						<th><?=Yii::t('app', 'Calendar Days')?></th>
            <? for($i = 1; $i <= $monthEndDT; $i++) { ?>
							<th class="text-center"><?=sprintf("%02d", $i)?></th>
            <? } ?>
					</tr>
					</thead>
					<tbody>
          <? foreach($data as $dataKey => $dataList) { ?>
						<tr>
              <? foreach($dataList as $key => $val) {
                $txtVal = null;
                if($key == 'title') {
                  $txtAlign = 'text-left';
                  $txtVal = Yii::t('app', $val);
                } else {
                  $txtAlign = 'text-right';
                  $txtVal = $val;
                }
                ?>
								<td class="midtext td-nowrap <?=$txtAlign?>">
                  <?=$txtVal;?>
								</td>
              <? } ?>
						</tr>
            <? if($dataKey == 2) { ?>
							<tr>
								<th><?=Yii::t('app', 'allDefDetailHeader');?></th>
								<th colspan="<?=$monthEndDT?>" class="midtext text-center"><?=Yii::t('app', 'allDefDetailHeaderByDT');?></th>
							</tr>
            <? } ?>
          <? } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<? $this->registerJsFile("@themes/js/xlsx.full.min.js", ['depends' => [JqueryAsset::className()]]); ?>
<?
$script1 = <<< JS
$(document).ready(function(){
	$('#fix_table').tableFixer({'left' : 1});
    changeHeight();
    $(window).resize(function(){
    changeHeight();
  });
  function changeHeight(){
    window_h = $(window).height();
    table_h = window_h - 130;
    $('.div_fix_table').height(table_h+'px');
  }	
	$('#download-xls').on('click', function (e) {
    var elt = document.getElementById('fix_table');
    var wb = XLSX.utils.table_to_book(elt, {sheet:"FTQ"});        
    return XLSX.writeFile(wb, '$downloadFileName.xlsx'	);
  });	
});
JS;
$this->registerJs($script1);
?>

