<?php
use app\assets\AdminLteAsset;
use app\components\Helpers;
use app\models\LineStopReason;
use app\models\ProductionMonitor;
use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\PartProductionMonitor */
/* @var $searchModel app\models\PartProductionMonitorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $warehouseLists app\controllers\PartProductionMonitorController */
/* @var $selWhId app\controllers\PartProductionMonitorController */
/* @var $needDate app\controllers\PartProductionMonitorController */
/* @var $needShift app\controllers\PartProductionMonitorController */
/* @var $prodPartLists app\controllers\PartProductionMonitorController */
/* @var $productionMonitorStatus app\controllers\PartProductionMonitorController */
/* @var $productionMonitorLineStopsConfirmed app\controllers\PartProductionMonitorController */
$this->title = Yii::t("app", "Production results");
$this->params["breadcrumbs"][] = $this->title;
$canConfirm = Yii::$app->user->can("part-production-monitor-confirm");
$canUnConfirm = Yii::$app->user->can("part-production-monitor-unconfirm");
$canComplete = Yii::$app->user->can("part-production-monitor-complete");
$canUnComplete = Yii::$app->user->can("part-production-monitor-uncomplete");
?>

<div class="row">
	<div class="col-xs-12">
    <?
    echo "<strong>".Yii::t('app', 'The production line').": </strong>";
    foreach($warehouseLists as $keyWh => $valWh) {
      $url = Url::toRoute(['part-production-monitor/index', 'whId' => $keyWh]);
      $btnClass = ($selWhId == $keyWh) ? "btn-success" : "btn-warning";
      echo Html::a($valWh,
        $url,
        [
          'name' => 'whId',
          'class' => 'btn btn-sm '.$btnClass,
          'style' => 'margin-right: 5px',
          'title' => Yii::t('app', 'Warehouses'),
          'data-intro' => Yii::t('intro', 'Warehouses')
        ]);
    }
    ?>
	</div>
</div>
<br>

<?php Pjax::begin(["id" => "pjaxGrid"]); ?>
<div class='row'>
	<div class='col-xs-12 col-sm-6 col-md-3'>
		<div class='form-group has-float-label'>
			<label class='control-label'><?= Yii::t("app", "Date") ?></label>
      <?= DateTimePicker::widget([
        "id" => "need-date",
        "name" => "needDate",
        "value" => $needDate,
        "type" => DateTimePicker::TYPE_INPUT,
        "layout" => "{input}",
        "language" => Yii::$app->language,
        "pluginOptions" => [
          "autoclose" => true,
          "format" => "yyyy-mm-dd",
          "startView" => "month",
          "minView" => "month",
          "maxView" => "month",
        ],
        "options" => [
          "autocomplete" => "off",
          "placeholder" => Yii::t("app", "Date"),
          "class" => "dt-shift form-control",
        ],
      ]) ?>
		</div>
	</div>
	<div class='col-xs-12 col-sm-6 col-md-3'>
		<div class='form-group'>
      <?
      echo Html::radioList(
        "needShift",
        $needShift,
        [
          '1' => Yii::t('app', 'Shift').' 1',
          '2' => Yii::t('app', 'Shift').' 2'
        ],
        [
          'id' => 'need-shift',
          'class' => 'dt-shift',
          'style' => 'margin-right: 20px;'
        ]
      );
      ?>
		</div>

</div>
	<div class='col-xs-12 col-sm-6 col-md-3'>
    <?
    switch ($productionMonitorStatus) {
    case ProductionMonitor::STATUS_COMPLETED:
      $stsClass = "primary";
      $stsText = "Completed";
        break;
    case ProductionMonitor::STATUS_CONFIRMED:
      $stsClass = "success";
      $stsText = "Confirmed";
        break;
    default:
      $stsClass = "warning";
      $stsText = "Open";
    }

    echo Html::button(Yii::t('app', $stsText), [
      'class' => "btn btn-sm btn-".$stsClass,
	    "disabled" => "disabled"
    ])

    ?>
	</div>
</div>
<br>
<? if($selWhId) { ?>
	<div class="row">
		<div class="col-xs-12">
			<div class="form-group pull-right">
        <?php
        if ($productionMonitorStatus === ProductionMonitor::STATUS_ENABLED && $canConfirm) {
          echo Html::button(Yii::t("app", "btn-confirm-1"), [
            "class" => "btn btn-success btn-sm confirmOrReject",
            "url" => "confirm",
            "tip" => 1,
            "style" => "margin-right: 5px",
          ]);
        }
        if ($productionMonitorStatus === ProductionMonitor::STATUS_CONFIRMED) {
          if ($canUnConfirm) {
            echo Html::button(Yii::t("app", "btn-un-confirm"), [
              "class" => "btn btn-warning btn-sm confirmOrReject",
              "url" => "confirm",
              "tip" => 0,
              "style" => "margin-right: 5px",
            ]);
          }
          if ($canComplete && $productionMonitorLineStopsConfirmed) {
            echo Html::button(Yii::t("app", "btn-dayClose"), [
              "class" => "btn btn-success btn-sm confirmOrReject",
              "url" => "complete",
              "tip" => 1,
              "style" => "margin-right: 5px",
            ]);
          }
        }
        if ($productionMonitorStatus === ProductionMonitor::STATUS_COMPLETED) {
          if ($canComplete) {
            echo Html::button(Yii::t("app", "btn-dayOpen"), [
              "class" => "btn btn-warning btn-sm confirmOrReject",
              "url" => "complete",
              "tip" => 0,
              "style" => "margin-right: 5px",
            ]);
          }
        }
        ?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
      <?
      $partList = (count($prodPartLists) > 0) ? true : false;
      if($partList == true) { ?>
				<table id="fix_table" class="table table-bordered table-condensed">
					<thead style="font-size:80%">
					<tr>
						<th rowspan="2" class="txt_center">№</th>
						<th rowspan="2" class="txt_center" style="width:180px;"><?= Yii::t("app", "Part No") ?></th>
						<th rowspan="2" class="txt_center"><?= Yii::t("app", "Part name") ?></th>
						<th rowspan="2" class="txt_center" style="width: 100px;"><?= Yii::t("app", "Cycle time") ?></th>
						<th rowspan="2" class="txt_center" style="width: 100px;"><?= Yii::t("app", "Start time") ?></th>
						<th rowspan="2" class="txt_center" style="width: 100px;"><?= Yii::t("app", "End time") ?></th>
						<th rowspan="2" class="txt_center" style="width: 100px;"><?= Yii::t("app", "Production time (min)") ?></th>
						<th rowspan="2" class="txt_center" style="width: 100px;"><?= Yii::t("app", "Planned line stop (min)") ?></th>
						<th rowspan="2" class="txt_center" style="width: 100px;"><?= Yii::t("app", "Not planned line stop (min)") ?></th>
						<th colspan="5" class="txt_center" style="width: 100px;"><?= Yii::t("app", "Production part qty") ?></th>
					</tr>
					<tr>
						<th class="txt_center" style="width: 100px;"><?= Yii::t("app", "Produced") ?></th>
						<th class="txt_center" style="width: 100px;">OK</th>
						<th class="txt_center" style="width: 100px;">NG</th>
						<th class="txt_center" style="width: 100px;"><?= Yii::t("app", "Repair") ?></th>
						<th class="txt_center" style="width: 100px;"><?= Yii::t("app", "Defect") ?></th>
					</tr>
					</thead>
					<tbody class="fs120">
          <?
          $ii = 0;
          foreach($prodPartLists as $prodPartListKey => $prodPartListVal) {
            $ii++;
            $prodTimeId = "prodTime_".$prodPartListVal['ppm_id'];
            $repairedQtyId = "repairedQty_".$prodPartListVal['ppm_id'];
            $brokenQtyId = "brokenQty_".$prodPartListVal['ppm_id'];
            $NGQty = $prodPartListVal['ppm_repaired_qty'] + $prodPartListVal['ppm_broken_qty'];
            $OKQty = $prodPartListVal['ppm_produced_qty'] - $NGQty;
            $producedQty = $prodPartListVal['ppm_produced_qty'] ? : 0;
            $repairedQty = $prodPartListVal['ppm_repaired_qty'] ? : 0;
            $brokenQty = $prodPartListVal['ppm_broken_qty'] ? : 0;
            $startTime = $prodPartListVal['ppm_start_time'] ? date("Y-m-d H:i", strtotime($prodPartListVal['ppm_start_time'])) : null;
            $endTime = $prodPartListVal['ppm_end_time'] ? date("Y-m-d H:i", strtotime($prodPartListVal['ppm_end_time'])) : null;
            $lsConfirmStatus = ($productionMonitorStatus !== ProductionMonitor::STATUS_COMPLETED) ? 'color:red; font-weight:bold' : '';
            ?>
						<tr id="tr<?= $prodPartListVal["ppm_id"] ?>">
							<td class="midtext"><?= $ii ?></td>
							<td class="midtext"><?= $prodPartListVal["pt_part_color"] ?></td>
							<td class="midtext" style="font-size:80%"><?= $prodPartListVal["pt_part_name"] ?></td>
							<td class="midtext txt_right" style="font-size:150%"><?= $prodPartListVal["pt_cycle_time"] ?></td>

							<td id="trStartTime<?= $prodPartListVal[
         "ppm_id"
       ] ?>" class="midtext txt_center pos-rel" style="width:133px;font-weight:bold;">
                <? if($productionMonitorStatus !== ProductionMonitor::STATUS_COMPLETED) { ?>
									<textarea
										rows="2"
										cols="10"
										name="text"
										id="start_time<?= $prodPartListVal["ppm_id"] ?>"
										fieldName="start_time"
										class="editable-input full-h-w full-h-w-outline txt_center no-arrow-input start-end-time"
										style="font-size:80%;padding: 0 3px 0 3px; word-wrap: break-word; resize: none;"
									><?= $startTime ?></textarea>
                <? } else { ?>
									<b class="text-bold" style="font-size:80%">
                    <?= $startTime ?>
									</b>
                <? } ?>
							</td>

							<td id="trEndTime<?= $prodPartListVal[
         "ppm_id"
       ] ?>" class="midtext txt_center pos-rel" style="width:133px;font-weight:bold;">
                <? if($productionMonitorStatus !== ProductionMonitor::STATUS_COMPLETED) { ?>
									<textarea
										rows="2"
										cols="10"
										name="text"
										id="end_time<?= $prodPartListVal["ppm_id"] ?>"
										fieldName="end_time"
										class="editable-input full-h-w full-h-w-outline txt_center no-arrow-input start-end-time"
										style="font-size:80%;padding: 0 3px 0 3px; word-wrap: break-word; resize: none;"
									><?= $endTime ?></textarea>
                <? } else { ?>
									<b class="text-bold" style="font-size:80%">
                    <?= $endTime ?>
									</b>
                <? } ?>
							</td>

							<td class="midtext txt_right" style="font-size:150%">
								<b>
                  <?= Helpers::numberFormat($prodPartListVal["ppm_actual_production_time"], 5, ".", "", true, true) ?>
								</b>
							</td>

							<td class="midtext txt_right">
								<div>
										<span>
											<?
                      echo Html::a(
                        $prodPartListVal["planed_minutes"],
                        Url::toRoute([
                          "line-stop/?LineStopSearch[part_production_monitor_id]=".
                          $prodPartListVal["ppm_id"].
                          "&LineStopSearch[type]=0",
                        ]),
                        ["style" => "font-size:150%", "target" => "_blank"]
                      ) ?>
										</span>
                  <? if($productionMonitorStatus !== ProductionMonitor::STATUS_COMPLETED) { ?>
										<span style="font-size:130%; margin-left: 10px;">
												<a
													href="#"
													data-toggle='modal'
													data-target='#temp-modal'
													class='link-group add-line-stop'
													data-ppmid='<?= $prodPartListVal["ppm_id"] ?>'
													data-planned="0"
												>
													<?= Yii::t("app", "btn-plus") ?>
												</a>
											</span>
                  <? } ?>
								</div>
							</td>
							<td class="midtext txt_right">
								<div>
										<span>
											<?
                      echo Html::a(
                        $prodPartListVal["not_planed_minutes"],
                        Url::toRoute([
                          "line-stop/?LineStopSearch[part_production_monitor_id]=".
                          $prodPartListVal["ppm_id"].
                          "&LineStopSearch[type]=1",
                        ]),
                        ["style" => "font-size:150%;  $lsConfirmStatus", "target" => "_blank"]
                      ) ?>
										</span>
                  <? if($productionMonitorStatus !== ProductionMonitor::STATUS_COMPLETED) { ?>
										<span style='font-size:130%; margin-left: 10px'>
												<a
													href="#"
													data-toggle='modal'
													data-target='#temp-modal'
													class='link-group add-line-stop'
													data-ppmid='<?= $prodPartListVal["ppm_id"] ?>'
													data-planned="1"
												>
													<?= Yii::t("app", "btn-plus") ?>
												</a>
											</span>
                  <? } ?>
								</div>
							</td>
							<td id="trProdQty<?= $prodPartListVal["ppm_id"] ?>" class="midtext txt_right" style="font-size:150%">
								<div id="prodQty<?= $prodPartListVal["ppm_id"] ?>">
                  <?= Helpers::numberFormat($producedQty, 5, ".", "", true, true) ?></div>
							</td>
							<td id="okQty<?= $prodPartListVal["ppm_id"] ?>" class="midtext txt_right" style="font-size:150%">
                <?= Helpers::numberFormat($OKQty, 5, ".", "", true, true) ?>
							</td>
							<td class="midtext txt_right" style="font-size:150%"><?= Helpers::numberFormat($NGQty, 5, ".", "", true, true) ?></td>
							<td id="trRepairedQty<?= $prodPartListVal["ppm_id"] ?>" class="midtext txt_right pos-rel">
                <? if($productionMonitorStatus === ProductionMonitor::STATUS_ENABLED && $producedQty > 0) { ?>
									<input id="repairedQty<?= $prodPartListVal["ppm_id"] ?>" style="font-size:150%"
										fieldName="repaired_qty"
										class="editable-input full-h-w full-h-w-outline txt_right no-arrow-input"
										type="number"
										value="<?= Helpers::numberFormat($repairedQty, 5, ".", "", true, true) ?>">
                <? } else { ?>
									<b class="text-bold" style="font-size:150%">
										<input id="repairedQty<?= $prodPartListVal["ppm_id"] ?>"
											fieldName="repaired_qty"
											class="full-h-w txt_right no-arrow-input"
											type="number"
											disabled
											value="<?= Helpers::numberFormat($repairedQty, 5, ".", "", true, true) ?>">
									</b>
                <? } ?>
							</td>
							<td id="trBrokenQty<?= $prodPartListVal["ppm_id"] ?>" class="midtext txt_right pos-rel">
                <? if($productionMonitorStatus === ProductionMonitor::STATUS_ENABLED && $producedQty > 0) { ?>
									<input id="brokenQty<?= $prodPartListVal["ppm_id"] ?>" style="font-size:150%"
										fieldName="broken_qty"
										class="editable-input full-h-w full-h-w-outline txt_right no-arrow-input"
										type="number"
										value="<?= Helpers::numberFormat($brokenQty, 5, ".", "", true, true) ?>">
                <? } else { ?>
									<b class="text-bold" style="font-size:150%">
										<input id="brokenQty<?= $prodPartListVal["ppm_id"] ?>"
											fieldName="broken_qty"
											class="editable-input full-h-w txt_right no-arrow-input"
											type="number"
											disabled
											value="<?= Helpers::numberFormat($brokenQty, 5, ".", "", true, true) ?>"
										>
									</b>
                <? } ?>
							</td>
						</tr>
          <? } ?>
					</tr>
					</tbody>
				</table>
      <? } else { ?>
				<div class='alert alert-danger alert-dismissible text-center'>
					<h1><?= Yii::t("app", "Data not found") ?></h1>
				</div>
      <? } ?>
		</div>
	</div>
<? } else { ?>
	<br>
	<div class="row">
		<div class='col-xs-12'>
			<div class='alert alert-danger alert-dismissible text-center'>
				<h1><?= Yii::t("app", "Select line") ?></h1>
			</div>
		</div>
	</div>
<? } ?>

<?php Pjax::end(); ?>

<?
$appLang = Yii::$app->language;
$fromTitle = Yii::t('app', 'From');
$toTitle = Yii::t('app', 'To');
$params = ['prompt' => '. . .', 'id' => 'line_stop_reason_id', 'class' => 'form-control input-sm select2'];
$lsStartTime = date("Y-m-d H:i");
$lsEndTime = date("Y-m-d H:i", strtotime($lsStartTime));
$notPlanedReasons = ArrayHelper::map(LineStopReason::find()->where(['type' => LineStopReason::TYPE_NOTPLANNED])->all(), 'id', 'name');
$notPlanedReasonList = Html::dropDownList('line_stop_reason_id', null, $notPlanedReasons, $params);
$planedReasons = ArrayHelper::map(LineStopReason::find()->where(['type' => LineStopReason::TYPE_PLANNED])->all(), 'id', 'name');
$planedReasonList = Html::dropDownList('line_stop_reason_id', null, $planedReasons, $params);
?>

<div class="modal fade" id="temp-modal" style="display: none;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span></button>
				<h4 class="modal-title"><?= Yii::t("app", "Create") ?></h4>
			</div>
			<div class="modal-body">
				<div class='row'>
					<div class='col-xs-12'>
						<input type="hidden" id="ppm_id">
						<input type="hidden" id="planned">
						<input type="hidden" id="reason_id">
					</div>
				</div>
				<div class='row'>
					<div class='col-xs-12'>
						<div class='form-group'>
							<label class='control-label'><?= Yii::t("app", "Reason") ?></label>
							<div id="not-planned-list" style="display: none;">
                <?= $notPlanedReasonList ?>
							</div>
							<div id="planned-list" style="display: none;">
                <?= $planedReasonList ?>
							</div>
						</div>
					</div>
					<div class='col-xs-12'>
						<div class='row'>
							<div class='col-xs-12 col-sm-6'>
								<div class='form-group'>
									<label class='control-label'><?= Yii::t("app", "From") ?></label>
                  <?= DateTimePicker::widget([
                    "id" => "start_time",
                    "name" => "start_time",
                    "value" => $lsStartTime,
                    "type" => DateTimePicker::TYPE_COMPONENT_PREPEND,
                    "layout" => "{picker}{input}{remove}",
                    "removeButton" => ["position" => "append"],
                    "language" => Yii::$app->language,
                    "pluginOptions" => [
                      "autoclose" => true,
                      "format" => "yyyy-mm-dd HH:ii",
                    ],
                    "options" => [
                      "autocomplete" => "off",
                      "placeholder" => Yii::t("app", "From"),
                      "class" => " form-control",
                    ],
                  ]) ?>

								</div>
							</div>
							<div class='col-xs-12 col-sm-6'>
								<div class='form-group'>
									<label class='control-label'><?= Yii::t("app", "To") ?></label>
                  <?= DateTimePicker::widget([
                    "id" => "end_time",
                    "name" => "end_time",
                    "value" => $lsEndTime,
                    "type" => DateTimePicker::TYPE_COMPONENT_PREPEND,
                    "layout" => "{picker}{input}{remove}",
                    "removeButton" => ["position" => "append"],
                    "language" => Yii::$app->language,
                    "pluginOptions" => [
                      "autoclose" => true,
                      "format" => "yyyy-mm-dd HH:ii",
                    ],
                    "options" => [
                      "autocomplete" => "off",
                      "placeholder" => Yii::t("app", "to"),
                      "class" => " form-control",
                    ],
                  ]) ?>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="form-group" id="bypassContainer" style="display: none">
							<label class="control-label"><?= Yii::t("app", "By pass") ?></label>
							<input type="number" id="bypass" class="form-control">
						</div>
					</div>
					<div class='col-xs-12'>
						<div class='form-group'>
							<label class='control-label'><?= Yii::t("app", "Remark") ?></label>
              <?= Html::textarea("remark", null, ["id" => "remark", "class" => "form-control", "rows" => 5]) ?>
						</div>
					</div>
				</div>
				<div class='row'>
					<div class='col-xs-12'>
						<div class="form-group pull-right">
              <?= Html::button(Yii::t("app", "btn-cancel"), [
                "id" => "btn-cancel",
                "class" => "btn btn-default btn-sm",
              ]) ?>
              <?= Html::button(Yii::t("app", "btn-save"), [
                "id" => "btn-save",
                "class" => "btn btn-success btn-sm",
              ]) ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

<?
$this->registerCssFile("@themes/css/jquery-confirm.min.css", ['depends' => [AdminLteAsset::className()]]);
$this->registerJsFile("@themes/js/jquery-confirm.min.js", ['depends' => [JqueryAsset::className()]]);
$this->registerCssFile("@themes/css/notify.css", ['depends' => [AdminLteAsset::className()]]);
$this->registerJsFile("@themes/js/notify.js", ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile("@themes/js/jquery-dateformat.min.js", ['depends' => [JqueryAsset::className()]]);
$errTitle = Yii::t('app', 'Error !');
$startEndTimeError = Yii::t('app', 'Enter the start and finished time correctly');
$currUrl = Yii::$app->request->url;
//echo "<pre>"; print_r($currUrl);echo "</pre>";
$okSms = Yii::t('app', 'Successfully.');
$currentWhId = Yii::$app->getRequest()->getQueryParam('whId');
$add_item = <<< JS
 $(document).ready(function() {

	 $(document).on('click', '#btn-cancel', function(e){
	  $('#temp-modal').modal("hide");
	 });

	 $(document).on('change', '.dt-shift', function(e){
	  var selDate = $("#need-date").val();
	  var selShift = $('input[name="needShift"]:checked').val();
	  $.pjax({
        container  : '#pjaxGrid',
        url        : "$currUrl" + "&needDate=" + selDate + "&needShift=" + selShift,
    })
	 });

	 $(document).on('click', '.add-line-stop', function(e){
	  var ppm_id = $(this).data('ppmid');
	  var planned = $(this).data('planned')
	  $("#ppm_id").val(ppm_id);
	  $("#planned").val(planned);
	  if(planned == 1){
	    $("#not-planned-list").show()
	    $("#bypassContainer").show()
	    $("#planned-list").hide()
	  }else{
      $("#bypassContainer").hide()
	    $("#not-planned-list").hide()
	    $("#planned-list").show()
	  }
	});

	 $(document).on('change', '#line_stop_reason_id', function(e){
	  $("#reason_id").val($(this).val())
	 });

	 $(document).on('click', '#btn-save', function(e){
	   e.preventDefault();
	   $("#temp-modal .form-group .help-block").remove();
		 var ppm_id = $("#ppm_id").val();
		 var planned = $("#planned").val();
		 var line_stop_reason_id = $("#reason_id").val();
		 var start_time = $("#start_time").val();
		 var end_time = $("#end_time").val();
		 var remark = $("#remark").val();
		 var bypass = planned == 0 ? null : ($("#bypass").val() || null);
		 var url = "/line-stop/create?id="+ppm_id+"&planned="+planned;
		 $.ajax({
					url: url,
					type: "post",
					data: {
					  LineStop:{
					  'part_production_monitor_id':ppm_id,
					  'line_stop_reason_id':line_stop_reason_id,
					  'start_time':start_time,
					  'end_time':end_time,
					  'remark':remark,
					  'bypass':bypass,
					  'ajax':'line-stop-form'
					  }
					},
	        beforeSend: function (){
	           WaitDialog = $.dialog({
	                title: false,
	                cancelButton: true,
	                confirmButton: false,
	                backgroundDismiss: true,
	                backgroundDismissAnimation: 'glow',
	                closeIcon: false,
	                columnClass: 'col-xs-6 col-xs-offset-3 col-sm-4 col-sm-offset-4 col-md-2 col-md-offset-5',
	                content: '<img src="/img/loading.gif" style="width:100%;height:100%"/>',
	           });
	        },
	        complete: function (){ WaitDialog.close() },
					success: function(response) {
					  console.log(response)
						if(response.status == 1){
						  $('#notify-modal').notify().show("$okSms", {
						    type: 'success',         // success, info, warning, danger
						    icon: '<span>✔</span>',  // ✔, 🛈, ⚠, ⮾
						    title: '<i class="fa fa-smile-o"></i> <i class="fa fa-smile-o"></i> <i class="fa fa-smile-o"></i>',
						    sticky: false
						  })
						  $('#temp-modal').modal("hide");
						  $.pjax.reload({container:'#pjaxGrid', async: false});
						  // return false;
	          }else{
						  var errMessage = "";
						  $.each( response.errors, function( key, value ) {
						    fieldErrMessage = ''
						    if($.isArray(value)){
						      fieldErrMessage = value[0]
						    }else{
						      fieldErrMessage = value
						    }
						    $("#"+key).closest('.form-group').append('<div class="help-block text-danger">'+fieldErrMessage+'</div>');
						  });

	          }
					},
					error: function(xhr) {
						$.alert({
	             keyboardEnabled: true,
	             draggable: true,
	             columnClass: 'col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1',
	             icon: 'fa fa-warning',
	             title: "<span class='text-bold'>$errTitle</span>",
	             content: "<div class='text-danger'>" + xhr.statusText + '<br>' + xhr.responseText + "</div>",
	          });
						console.log(xhr)
					}
				});

	});

	 $(document).on('click', '.confirmOrReject', function(e){
	  var acceptOrReject = $(this).attr('tip');
	  var url = $(this).attr('url');
	  var selDate = $("#need-date").val();
	  var selShift = $('input[name="needShift"]:checked').val();
	  $.ajax({
					url: url,
					type: "post",
					data: {
					  whId: "$selWhId",
					  needDate:  selDate,
					  needShift:  selShift,
					  allow: acceptOrReject,
					},
	        beforeSend: function (){
	           WaitDialog = $.dialog({
	                title: false,
	                cancelButton: true,
	                confirmButton: false,
	                backgroundDismiss: true,
	                backgroundDismissAnimation: 'glow',
	                closeIcon: false,
	                columnClass: 'col-xs-6 col-xs-offset-3 col-sm-4 col-sm-offset-4 col-md-2 col-md-offset-5',
	                content: '<img src="/img/loading.gif" style="width:100%;height:100%"/>',
	           });
	        },
	        complete: function (){ WaitDialog.close() },
					success: function(response) {
						if(response.sts=='OK'){
						  $('#notify-modal').notify().show("$okSms", {
						    type: 'success',         // success, info, warning, danger
						    icon: '<span>✔</span>',  // ✔, 🛈, ⚠, ⮾
						    title: '<i class="fa fa-smile-o"></i> <i class="fa fa-smile-o"></i> <i class="fa fa-smile-o"></i>',
						    sticky: false
						  })
						  $.pjax.reload({container:'#pjaxGrid'});
	          }else{
	            $.alert({
	               keyboardEnabled: true,
	               draggable: true,
	               columnClass: 'col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1',
	               icon: 'fa fa-warning',
	               title: "<span class='text-bold'>$errTitle</span>",
	               content: "<div class='text-danger'>" + response.sms + "</div>",
	            });
	          }
					},
					error: function(xhr) {
						$.alert({
	             keyboardEnabled: true,
	             draggable: true,
	             columnClass: 'col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1',
	             icon: 'fa fa-warning',
	             title: "<span class='text-bold'>$errTitle</span>",
	             content: "<div class='text-danger'>" + xhr.statusText + '<br>' + xhr.responseText + "</div>",
	          });
						console.log(xhr)
					}
				});
	});


	 $(document).on('focus', '.start-end-time', function(e){
	   var thisVal = $(this).val().trim();
	   var currTime = $.format.date(new Date(), "yyyy-MM-dd HH:mm");
	   if(thisVal.length==0){
	     $(this).val(currTime)
	   }
	 });

	 $(document).on('keyup', '.editable-input', function(e){
	  var trId = $(this).closest('tr').attr('id');
	  var rowId = trId.replace('tr', '')

	  $("#trProdQty"+rowId).removeClass('minus_cnt')
	  $("#trRepairedQty"+rowId).removeClass('minus_cnt')
	  $("#trBrokenQty"+rowId).removeClass('minus_cnt')
		$("#trStartTime"+rowId).removeClass('minus_cnt')
		$("#trEndTime"+rowId).removeClass('minus_cnt')

	  var prodQty = parseFloat($("#"+trId).find("#prodQty"+rowId).text());
	  var repairedQty = parseFloat($("#"+trId).find("#repairedQty"+rowId).val());
	  var brokenQty = parseFloat($("#"+trId).find("#brokenQty"+rowId).val());

	  console.log("prodQty:", prodQty)
	  console.log("repairedQty:", repairedQty)
	  console.log("brokenQty:", brokenQty)

	  var diffQty = prodQty - repairedQty + brokenQty;

		var hasAccess = false;
	  var fieldName = $(this).attr('fieldName');
	  var thisVal = $(this).val().trim().replace("\\n", "");

    if( $('#start_time'+rowId).val().trim()==null){
      hasAccess = true
    }else{
			var timeStart = (new Date($('#start_time'+rowId).val().trim().replace("\\n", "")).getTime()) / 60000;
			var timeEnd = (new Date($('#end_time'+rowId).val().trim().replace("\\n", "")).getTime()) / 60000;

			var timeDiff = 0;
			if( $('#end_time'+rowId).val().trim().length > 0 ){
				timeDiff = (timeEnd - timeStart);
			}
			console.log("START:", $('#start_time'+rowId).val().trim().replace("\\n", "") )
			console.log("END:", $('#end_time'+rowId).val().trim().length  )
			console.log("DIFF:", timeDiff)
			console.log("diffQty:", diffQty)

			if(timeDiff > 0 || $('#end_time'+rowId).val().trim().length == 0){
			  hasAccess = true;
			}else{
				$("#trStartTime"+rowId).addClass('minus_cnt')
				$("#trEndTime"+rowId).addClass('minus_cnt')
			}
    }

    if(diffQty < 0){
      hasAccess = false
      $("#trProdQty"+rowId).addClass('minus_cnt')
			$("#trRepairedQty"+rowId).addClass('minus_cnt')
			$("#trBrokenQty"+rowId).addClass('minus_cnt')
    }

	  if(hasAccess == true && diffQty >= 0){
	    if(e.keyCode === 13){
				$.ajax({
					url: "edit",
					type: "post",
					data: {
					  fieldName: fieldName,
					  fieldValue: thisVal,
					  id: rowId
					},
	        beforeSend: function (){
	           WaitDialog = $.dialog({
	                title: false,
	                cancelButton: true,
	                confirmButton: false,
	                backgroundDismiss: true,
	                backgroundDismissAnimation: 'glow',
	                closeIcon: false,
	                columnClass: 'col-xs-6 col-xs-offset-3 col-sm-4 col-sm-offset-4 col-md-2 col-md-offset-5',
	                content: '<img src="/img/loading.gif" style="width:100%;height:100%"/>',
	           });
	        },
	        complete: function (){ WaitDialog.close() },
					success: function(response) {
						if(response.sts=='OK'){
						  $('#notify-modal').notify().show("$okSms", {
						    type: 'success',         // success, info, warning, danger
						    icon: '<span>✔</span>',  // ✔, 🛈, ⚠, ⮾
						    title: '<i class="fa fa-smile-o"></i> <i class="fa fa-smile-o"></i> <i class="fa fa-smile-o"></i>',
						    sticky: false
						  })
						  // window.location = window.location.href;
						  // alert("2: " + window.location.href)
						  $.pjax.reload({container:'#pjaxGrid'});
	          }else{
	            $.alert({
	               keyboardEnabled: true,
	               draggable: true,
	               columnClass: 'col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1',
	               icon: 'fa fa-warning',
	               title: "<span class='text-bold'>$errTitle</span>",
	               content: "<div class='text-danger'>" + response.sms + "</div>",
	            });
	          }
					},
					error: function(xhr) {
						$.alert({
	             keyboardEnabled: true,
	             draggable: true,
	             columnClass: 'col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1',
	             icon: 'fa fa-warning',
	             title: "<span class='text-bold'>$errTitle</span>",
	             content: "<div class='text-danger'>" + xhr.statusText + '<br>' + xhr.responseText + "</div>",
	          });
						console.log(xhr)
					}
				});
	    }
	  }

	});

 });
JS;
$this->registerJs($add_item, yii\web\View::POS_END);
?>

<style>
    .fs120{
        font-size:120%;
        padding:0px 4px !important;
    }
</style>
