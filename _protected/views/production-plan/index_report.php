<?php
	use app\models\Warehouse;
	use kartik\datetime\DateTimePicker;
	use yii\helpers\ArrayHelper;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $model app\models\ProductionPlan */
	/* @var $this yii\web\View */
	/* @var $searchModel app\models\ProductionPlanSearch */
	/* @var $dataProvider yii\data\ActiveDataProvider */
	/* @var $need_month app\controllers\ProductionPlanController */
	/* @var $shift app\controllers\ProductionPlanController */
	/* @var $warehouse_id app\controllers\ProductionPlanController */
	$this->title = Yii::t('app', 'Production plans');
	$this->params['breadcrumbs'][] = $this->title;
	$month_end = date("Y-m-t", strtotime($need_month));
	$month_end_date = date('t', strtotime($month_end));
?>
<div class="row">
	<div class="col-md-12 col-lg-12">
		<div class="form-group pull-right">
			<?=Html::a(Yii::t('app', 'btn-upload-txt'), ['upload'], ['class' => 'btn btn-success btn-sm'])?>
			<?=Html::button(Yii::t('app', 'btn-download-txt'), ['class' => 'btn btn-info btn-sm', 'id' => 'download-xls'])?>
			<? //=Html::a(Yii::t('app', 'btn-download-template'), ['/public/ProductionProducts.xlsx'], ['class' => 'btn btn-warning btn-sm'])?>
			<!--				--><? //=Html::a(Yii::t('app', 'btn-download-template'), ['download-template'], ['class' => 'btn btn-warning btn-sm'])?>
		</div>
	</div>
</div>
<div class="row">
	<?php $form = ActiveForm::begin(['action' => ['index'], 'method' => 'post',]); ?>
	<div class="col-md-2 col-lg-2">
		<? // echo '<label class="control-label">'.Yii::t('app', 'need_month').'</label>';
			echo DateTimePicker::widget(
				[
					'name' => 'need_month',
					'value' => $need_month,
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
						'placeholder' => 'Период...',
						'class' => ' form-control input-sm'
					]
				]
			);
		?>
	</div>
	<?
		//		echo "<pre>1:"; print_r($shift);echo "</pre>";
		//		echo "<pre>1:"; print_r($warehouse_id);echo "</pre>";
	?>
	<div class="col-md-2 col-lg-2">
		<?
			$params = ['prompt' => Yii::t('app', 'Select shift'), 'value' => $shift];
			echo $form->field($model, 'shift')->dropDownList($model->smena, $params)->label(false);
		?>
	</div>
	<div class="col-md-2 col-lg-2">
		<?
			$warehouse = Warehouse::find()->all();
			$items = ArrayHelper::map($warehouse, 'id', 'name');
			$params = ['prompt' => Yii::t('app', 'Select warehouse'), 'value' => $warehouse_id];
			echo $form->field($model, 'warehouse_id')->dropDownList($items, $params)->label(false);
		?>
	</div>
	<div class="col-md-2 col-lg-2">
		<?=Html::submitButton(Yii::t('app', 'btn-show'), ['class' => 'btn btn-primary btn-sm'])?>
	</div>
	<?php ActiveForm::end(); ?>
</div>

<div id="div_fix_table" class="col-md-12  col-lg-12">
	<table id="fix_table" class="table table-striped table-bordered">
		<thead>
		<tr>
			<th><?=Yii::t('app', 'Product<br>no')?></th>
			<?
				for($i = 1; $i <= $month_end_date; $i++){
					?>
					<th> <?=sprintf("%02d", $i)?> </th>
				<? } ?>
		</tr>
		</thead>
		<tbody>
		<?
			$qq = 1;
			if(!empty($DB_part_list)){
				//echo "<pre>1:"; print_r($DB_part_list);echo "</pre>";
				foreach($DB_part_list as $part_list){
					?>
					<tr>
						<td class="midtext"><?=$part_list->part_no?></td>
						<?
							if(count($part_list->productionPlans) == 0){
								for($i = 0; $i < $month_end_date; $i++){
									?>
									<td class="midtext">-</td>
									<?
								}
							}else{
								foreach($part_list->productionPlans as $prod_plan){
									?>
									<td class="midtext"> <?=$prod_plan->target_qty?> </td>
									<?
								}
							} ?>
					</tr>
				<? }
			} ?>
		</tbody>
	</table>
</div>
<?
	$script1 = <<< JS
	
	$('#fix_table').tableFixer({'left' : 1});
	changeHeight();
	$(window).resize(function(){
		changeHeight();
	});
	function changeHeight(){
		window_h = $(window).height();
		table_h = window_h - 270;
		// console.log(window_h+"-"+table_h);		
		$('#div_fix_table').height(table_h+'px');
	}


	$('#download-xls').on('click', function (e) {
		var xlsarea = $('#div_fix_table').html();
		var data = '<html xmlns:x="urn:schemas-microsoft-com:office:excel"><head></head><body>'+xlsarea+'</body></html>';
		window.open('data:application/vnd.ms-excel,' + encodeURIComponent(data));
		e.preventDefault();
	});
	
JS;
	$this->registerJs($script1);
?>
