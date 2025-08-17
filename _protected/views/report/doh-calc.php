<?php

use app\assets\AdminLteAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Days on Hand calculation');
$this->params['breadcrumbs'][] = $this->title;

?>
<style>
	.table-doh th,
	.table-doh td {
		vertical-align: middle !important;
		border-color: #dcdcda !important;
	}


	.table-doh th {
		background-color: #e1f4f7;
		text-align: center;
	}

	.table-doh .tr-total th {
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

	.table-doh {
		border: 0px !important;
	}

	.table-doh>thead>tr>th,
	.table-doh>thead>tr>td {
		border-bottom-width: 1px !important;
	}

	.td-red {
		color: red;
	}
</style>
<div class="row">
	<div class="col-md-2 col-lg-2">
		<?= Html::a(Yii::t('app', 'btn-back'), ['index'], ['class' => 'btn btn-default btn-xs']) ?>
	</div>
	<div class="col-md-10 col-lg-10">
		<div class="pull-right">
			<?= Html::a(Yii::t('app', 'btn-download-delivery-plan'), Url::toRoute(['report/doh-calc', 'type' => 'download']) ,['class' => 'btn btn-info btn-xs']) ?>
		</div>
	</div>
</div>
<br>
<div class="req-index">
	<div class="row">
		<div id="div_fix_table" class="col-lg-12">
			<table id="fix_table" class="table table-bordered table-doh">

				<thead>
					<tr>
						<th rowspan="2"><?= Yii::t('app', 'Part number') ?></th>
						<th rowspan="2"><?= Yii::t('app', 'Part name') ?></th>
						<th rowspan="2"><?= Yii::t('app', 'Supplier') ?></th>
						<th rowspan="2"><?= Yii::t('app', 'Country') ?></th>
						<th rowspan="2"><?= Yii::t('app', 'Unit') ?></th>
						<th rowspan="2"><?= Yii::t('app', 'Part price') ?></th>
						<th rowspan="2"><?= Yii::t('app', 'Average daily usage') ?></th>
						<th rowspan="2"><?= Yii::t('app', 'DOH WH, Bank stock') ?></th>
						<th rowspan="2"><?= Yii::t('app', 'Transit time') ?></th>
						<th colspan="2"><?= Yii::t('app', 'MOQ') ?></th>
						<th colspan="3"><?= Yii::t('app', 'Total Days on Hand with MOQ') ?></th>
						<th colspan="3"><?= Yii::t('app', 'Total Days On Hand without MOQ') ?></th>
						<th rowspan="2"><?= Yii::t('app', 'DIFF. Amount') ?></th>
					</tr>
					<tr>
						<th><?= Yii::t('app', 'Pieces') ?></th>
						<th><?= Yii::t('app', 'Days') ?></th>
						<th><?= Yii::t('app', 'Days') ?></th>
						<th><?= Yii::t('app', 'Qty') ?></th>
						<th><?= Yii::t('app', 'Amount') ?></th>
						<th><?= Yii::t('app', 'Days') ?></th>
						<th><?= Yii::t('app', 'Qty') ?></th>
						<th><?= Yii::t('app', 'Amount') ?></th>
					</tr>
				</thead>
				<tbody>

					<?foreach ($data as $row) {?>
					<tr>
						<td style="text-align: left;"><?= $row['part_no'] ?></td>
						<td style="text-align: left;" class="td-nowrap"><?= $row['part_name'] ?></td>
						<td style="text-align: left;width: 100px;" class="td-nowrap"><?= $row['supplier'] ?></td>
						<td style="text-align: center;"><?= $row['country'] ?></td>
						<td style="text-align: center;"><?= $row['uom'] ?></td>
						<td style="text-align: right;white-space: nowrap;"><?= $row['price'] ?></td>
						<td style="text-align: right;white-space: nowrap;"><?= $row['average_usage'] ?></td>
						<td style="text-align: right;white-space: nowrap;"><?= $row['bank'] ?></td>
						<td style="text-align: right;white-space: nowrap;"><?= $row['transit_time'] ?></td>
						<td style="text-align: right;white-space: nowrap;"><?= $row['moq_pieces'] ?></td>
						<td style="text-align: right;white-space: nowrap;"><?= $row['moq_days'] ?></td>
						<td style="text-align: right;white-space: nowrap;"><?= $row['total_doh_days_with_moq'] ?></td>
						<td style="text-align: right;white-space: nowrap;"><?= $row['total_doh_qty_with_moq'] ?></td>
						<td style="text-align: right;white-space: nowrap;"><?= $row['total_doh_amount_with_moq'] ?></td>
						<td style="text-align: right;white-space: nowrap;"><?= $row['total_doh_days_without_moq'] ?></td>
						<td style="text-align: right;white-space: nowrap;"><?= $row['total_doh_qty_without_moq'] ?></td>
						<td style="text-align: right;white-space: nowrap;"><?= $row['total_doh_amount_without_moq'] ?></td>
						<td style="text-align: right;white-space: nowrap;"><?= $row['diff_amounts'] ?></td>
					</tr>
					<?}?>

				</tbody>




			</table>
		</div>
	</div>


</div>

<?php
$this->registerCssFile("@themes/tablesorter/scroller.css", ['depends' => [AdminLteAsset::class]]);
$this->registerCssFile("@themes/tablesorter/theme.ice.css", ['depends' => [AdminLteAsset::class]]);
$this->registerJsFile("@themes/tablesorter/jquery.tablesorter.js", ['depends' => [JqueryAsset::class]]);
$this->registerJsFile("@themes/tablesorter/jquery.tablesorter.widgets.js", ['depends' => [JqueryAsset::class]]);
$this->registerJsFile("@themes/tablesorter/widget-scroller.js", ['depends' => [JqueryAsset::class]]);
?>

<?php
$script1 = <<< JS
		
$(document).ready(function() {

	window_h = $(window).height();
  table_h = window_h - 250;
  
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

		$(window).trigger("resize");  
  
		//$('.tablesorter-filter-row').toggle();
  
});
JS;
$this->registerJs($script1, yii\web\View::POS_END);
?>

