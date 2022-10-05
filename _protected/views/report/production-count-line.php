<?php
	use app\models\ProductLine;
	use app\models\Warehouse;
	use yii\helpers\ArrayHelper;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $searchModel app\models\DeliveryPlanSearch */
	/* @var $dataProvider yii\data\ActiveDataProvider */
	/* @var $need_month app\controllers\DeliveryPlanController */
	/** @var TYPE_NAME $performance_list */
	/** @var TYPE_NAME $warehouse_id */
	/** @var TYPE_NAME $product_line_id */
	/** @var TYPE_NAME $part_id */
	$this->title = Yii::t('app', 'Production result by product line');
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<?php $form = ActiveForm::begin(['action' => ['production-count-line'], 'method' => 'post',]); ?>
	<div class="col-md-3 col-lg-2">
		<?
			$model->warehouse_id = $warehouse_id;
			$warehouses_list = ArrayHelper::map(Warehouse::find()->where('warehouse_type=1')->all(), 'id', 'name');
			$warehouses_list = [0 => Yii::t('app', 'All')] + $warehouses_list;
			$params = ['prompt' => Yii::t('app', 'Select warehouse'), 'class' => 'form-control select2'];
			echo $form->field($model, 'warehouse_id')
			          ->dropDownList($warehouses_list, $params)
			          ->label(false);
		?>
	</div>
	<div class="col-md-3 col-lg-2">
		<?
			$product_line = $product_line_id;
			$product_lines_list = ArrayHelper::map(ProductLine::find()->all(), 'id', 'linename');
			$product_lines_list = [0 => Yii::t('app', 'All')] + $product_lines_list;
			$params = ['prompt' => Yii::t('app', 'Select product_line'), 'class' => 'form-control select2'];
			echo Html::dropDownList('product_line_id', $product_line_id, $product_lines_list, $params);
		?>
	</div>
	<div class="col-md-3 col-lg-2">
		<?=Html::submitButton(Yii::t('app', 'btn-show'), ['class' => 'btn btn-primary'])?>
	</div>
	<?php ActiveForm::end(); ?>

	<div class="col-md-3 col-lg-6">
		<div class="form-group pull-right">
			<?=Html::button(Yii::t('app', 'btn-download-delivery-plan'), ['class' => 'btn btn-info', 'id' => 'download-xls'])?>
		</div>
	</div>
</div>
<br>
<div class="row">
	<div class="col-md-12 col-lg-12">
		<div id="div_fix_table" class="col-md-12  col-lg-12">
			<table id="fix_table" class="table table-striped table-bordered table-condensed table-sm-padding_2_0">
				<thead>
				<tr>
					<th rowspan="2" class="txt_center"><?=Yii::t('app', 'Line')?></th>
					<th rowspan="2" class="txt_center"><?=Yii::t('app', 'Product name')?></th>
					<th colspan="4" class="txt_center"><?=Yii::t('app', 'Today')." (".date('j F, Y l').")"?></th>
					<th colspan="4" class="txt_center"><?=Yii::t('app', 'Month to date')." (".date('F Y').")"?></th>
				</tr>
				<tr>
					<? for($r = 1; $r <= 2; $r++){ ?>
						<th><?=Yii::t('app', 'plan')?></th>
						<th><?=Yii::t('app', 'on target')?></th>
						<th><?=Yii::t('app', 'Short')?></th>
						<th><?=Yii::t('app', 'Over')?></th>
					<? } ?>
				</tr>
				</thead>
				<tbody>
				<?
					$qq = 1;
					foreach($performance_list as $prod_list){ ?>
						<tr>
							<td class="midtext"><?=$prod_list['warehouse_nm']?></td>
							<td class="midtext"><?=$prod_list['line_nm']?></td>

							<td class="midtext txt_center"> <?=$prod_list['all_d_plan']?> </td>
							<td class="midtext txt_center"> <?=$prod_list['equal_d_balance']?> </td>
							<td class="midtext txt_center f_bold"> <?=$prod_list['short_d_balance']?> </td>
							<td class="midtext txt_center f_bold"> <?=$prod_list['over_d_balance']?> </td>

							<td class="midtext txt_center"> <?=$prod_list['all_m_plan']?> </td>
							<td class="midtext txt_center"> <?=$prod_list['equal_m_balance']?> </td>
							<td class="midtext txt_center f_bold "> <?=$prod_list['short_m_balance']?> </td>
							<td class="midtext txt_center f_bold "> <?=$prod_list['over_m_balance']?> </td>


						</tr>
					<? } ?>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?
	$script1 = <<< JS
	$('#fix_table').tableFixer({'left' : 2});
	changeHeight();
	$(window).resize(function(){
		changeHeight();
	});
	function changeHeight(){
		window_h = $(window).height();
		table_h = window_h - 270;
		$('#div_fix_table').height(table_h+'px');
	}
	
	$('#download-xls').on('click', function (e) {		
		html_xls_export('fix_table', '$downloadFileName');
	});
JS;
	$this->registerJs($script1);
?>

