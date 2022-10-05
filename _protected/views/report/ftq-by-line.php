<?php
	use app\models\ProductLine;
	use kartik\datetime\DateTimePicker;
	use yii\helpers\ArrayHelper;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $searchModel app\models\DeliveryPlanSearch */
	/* @var $dataProvider yii\data\ActiveDataProvider */
	/* @var $need_month app\controllers\DeliveryPlanController */
	/** @var TYPE_NAME $data */
	/** @var TYPE_NAME $filter_from */
	/** @var TYPE_NAME $filter_to */
	$this->title = Yii::t('app', 'FTQ by product line');
	$this->params['breadcrumbs'][] = $this->title;
	//	$month_end = date("Y-m-t", strtotime($need_month));
	//	$month_end_date = date('t', strtotime($month_end));
?>
<div class="row">
	<?php $form = ActiveForm::begin(['action' => ['ftq-by-line'], 'method' => 'post',]); ?>

	<div class="col-md-3 col-lg-3">
		<?=DateTimePicker::widget([
			                          'name' => 'filter_from',
			                          'value' => $filter_from,
			                          'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
			                          'layout' => '{picker}{input}',
			                          'removeButton' => ['position' => 'append'],
			                          'language' => Yii::$app->language,
			                          'pluginOptions' => [
				                          'autoclose' => true,
				                          'format' => 'yyyy-mm-dd HH:ii',
				                          'startView' => 'month',
				                          'minView' => 'hour',
				                          'todayBtn' => 'linked',
				                          //'maxView' => 'month',
			                          ],
			                          'options' => [
				                          'autocomplete' => 'off',
				                          'placeholder' => 'С...',
				                          'class' => 'form-control'
			                          ]
		                          ])
		?>
	</div>

	<div class="col-md-3 col-lg-3">

		<?=DateTimePicker::widget([
			                          'name' => 'filter_to',
			                          'value' => $filter_to,
			                          'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
			                          'layout' => '{picker}{input}',
			                          'removeButton' => ['position' => 'append'],
			                          'language' => Yii::$app->language,
			                          'pluginOptions' => [
				                          'autoclose' => true,
				                          'format' => 'yyyy-mm-dd HH:ii',
				                          'startView' => 'month',
				                          'minView' => 'hour',
				                          'todayBtn' => 'linked',
				                          //'maxView' => 'month',
			                          ],
			                          'options' => [
				                          'autocomplete' => 'off',
				                          'placeholder' => 'С...',
				                          'class' => 'form-control'
			                          ]
		                          ])
		?>
	</div>
	<div class="col-md-2 col-lg-2">
		<?=Html::dropDownList('product_line', $product_line,
		                      ArrayHelper::map(ProductLine::find()->all(), 'id', 'linename'), ['prompt' => Yii::t('app', ' . . . '), 'class' => 'form-control select2'])?>
	</div>
	<div class="col-md-3 col-lg-3">
		<div class="form-group pull-right">
			<?=Html::submitButton(Yii::t('app', 'btn-show'), ['class' => 'btn btn-primary'])?>

			<?=Html::button(Yii::t('app', 'btn-download-delivery-plan'), ['class' => 'btn btn-info', 'id' => 'download-xls'])?>
		</div>
	</div>

	<?php ActiveForm::end(); ?>
</div>
<br>
<div class="row">
	<div class="col-md-12 col-lg-12">
		<div id="div_fix_table" class="col-md-12  col-lg-12">
			<table id="fix_table" class="table table-striped table-bordered">
				<thead>
				<tr>
					<th class="txt_center"><?=Yii::t('app', 'Part')?></th>
					<th class="txt_center"><?=Yii::t('app', 'The production lines')?></th>
					<th class="txt_center"><?=Yii::t('app', 'The models of product')?></th>
					<th class="txt_center"><?=Yii::t('app', 'Produced')?></th>
					<th class="txt_center"><?=Yii::t('app', 'Defects')?></th>
				</tr>
				</thead>
				<tbody>
				<?
					$totalProcuded = 0;
					$totalDefects = 0;
					foreach($data as $item){
						$totalProcuded += $item['produced'];
						$totalDefects += $item['defects']; ?>
						<tr>
							<td class="midtext"><?=$item['part_no']?></td>
							<td class="midtext"><?=$item['linename']?></td>
							<td class="midtext"><?=$item['modelname']?></td>
							<td class="midtext"><?=$item['produced']?></td>
							<td class="midtext"><?=$item['defects']?></td>
						</tr>
					<? } ?>
				</tbody>
				<?php
					$ftq = 0;
					if($totalProcuded > 0){
						$ftq = (1 - ($totalDefects/$totalProcuded))*100;
					}
				?>
				<tfoot>
				<tr>
					<th colspan="3" class="midtext"><?=$ftq > 0 ? ('FTQ: '.Yii::$app->formatter->asDecimal($ftq, 4)) : ''?></th>
					<th class="midtext"><?=$totalProcuded?></th>
					<th class="midtext"><?=$totalDefects?></th>
				</tr>
				</tfoot>
			</table>
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
		table_h = window_h - 270;
		// console.log(window_h+"-"+table_h);
		$('#div_fix_table').height(table_h+'px');
	}
	
	$('#download-xls').on('click', function (e) {		
		html_xls_export('fix_table', '$downloadFileName');
	});
JS;
	$this->registerJs($script1);
?>

