<?php
	use app\models\Factory;
	use app\models\Part;
	use kartik\datetime\DateTimePicker;
	use yii\helpers\ArrayHelper;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/** @var TYPE_NAME $factory */
	/** @var TYPE_NAME $part */
	/** @var TYPE_NAME $from */
	/** @var TYPE_NAME $to */
	$this->title = Yii::t('app', 'Finished good invoice report');
	$this->params['breadcrumbs'][] = $this->title;
	$factories = ArrayHelper::map(Factory::find()->all(), 'id', 'name');
	$parts = ArrayHelper::map(Part::find()->all(), 'part_no', 'part_no');
	$total = 0;
	$totalVAT = 0;
	$totalQty = 0;
?>
<div class="row">
	<?php $form = ActiveForm::begin(['action' => ['fg-invoice'], 'method' => 'post',]); ?>
	<div class="col-md-2 col-lg-2">
		<?=Html::dropDownList('factory', $factory, $factories, ['prompt' => Yii::t('app', ' . . . '), 'class' => 'form-control select2']);?>
	</div>
	<div class="col-md-2 col-lg-2">
		<?=DateTimePicker::widget(
			[
				'name' => 'from',
				'value' => $from,
				'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
				'layout' => '{picker}{input}{remove}',
				'removeButton' => ['position' => 'append'],
				'language' => Yii::$app->language,
				'pluginOptions' => [
					'autoclose' => true,
					'format' => 'yyyy-mm-dd',
					'startView' => 'month',
					'minView' => 'month',
					'maxView' => 'month',
				],
				'options' => [
					'autocomplete' => 'off',
					'placeholder' => Yii::t('app', 'need_month'),
					'class' => ' form-control'
				]
			]
		);?>
	</div>
	<div class="col-md-2 col-lg-2">
		<?=DateTimePicker::widget(
			[
				'name' => 'to',
				'value' => $to,
				'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
				'layout' => '{picker}{input}{remove}',
				'removeButton' => ['position' => 'append'],
				'language' => Yii::$app->language,
				'pluginOptions' => [
					'autoclose' => true,
					'format' => 'yyyy-mm-dd',
					'startView' => 'month',
					'minView' => 'month',
					'maxView' => 'month',
				],
				'options' => [
					'autocomplete' => 'off',
					'placeholder' => Yii::t('app', 'need_month'),
					'class' => ' form-control'
				]
			]
		);?>
	</div>

	<div class="col-md-2 col-lg-2">
		<?=Html::dropDownList('part', $part, $parts, ['prompt' => Yii::t('app', ' . . . '), 'class' => 'form-control select2']);?>
	</div>
	<div class="col-md-2 col-lg-2">
		<?=Html::submitButton(Yii::t('app', 'btn-show'), ['class' => 'btn btn-primary'])?>
	</div>

	<div class="col-md-2 col-lg-2">
		<div class="form-group pull-right">
			<?=Html::button(Yii::t('app', 'btn-download-delivery-plan'), ['class' => 'btn btn-info', 'id' => 'download-xls'])?>
		</div>
	</div>
	<?php ActiveForm::end(); ?>


</div>
<br>
<div class="row">
	<div class="col-md-12 col-lg-12">
		<div id="div_fix_table" class="col-md-12  col-lg-12">
			<table id="fix_table" class="table table-striped table-bordered table-condensed table-hover table-sm-padding_2_0">
				<thead>
				<tr>
					<th class="txt_center"><?=Yii::t('app', 'Factory')?></th>
					<th class="txt_center"><?=Yii::t('app', 'FG Invoice no (TTN)')?></th>
					<th class="txt_center"><?=Yii::t('app', 'Waybill date')?></th>
					<th class="txt_center"><?=Yii::t('app', 'Contract')?></th>
					<th class="txt_center"><?=Yii::t('app', 'Part')?></th>
					<th class="txt_center"><?=Yii::t('app', 'Quantity')?></th>
					<th class="txt_center"><?=Yii::t('app', 'Total')?></th>
					<th class="txt_center"><?=Yii::t('app', 'VAT')?></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach($data as $item): $total += $item['price'];
					$totalVAT += $item['VAT'];
					$totalQty += $item['qty']; ?>
					<tr>
						<td class="midtext"><?=$item['name']?></td>
						<td class="midtext"><?=$item['invoice_no']?></td>
						<td class="midtext"><?=$item['invoice_date']?></td>
						<td class="midtext"><?=$item['contract']?></td>
						<td class="midtext"><?=$item['part_no']?></td>
						<td class="midtext text-right"><?=Yii::$app->formatter->asInteger($item['qty'])?></td>
						<td class="midtext text-right"><?=Yii::$app->formatter->asDecimal($item['price'])?></td>
						<td class="midtext text-right"><?=Yii::$app->formatter->asDecimal($item['VAT'])?></td>
					</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="5"></td>
					<td class="midtext f_bold text-right"><?=Yii::$app->formatter->asDecimal($totalQty)?></td>
					<td class="midtext f_bold text-right"><?=Yii::$app->formatter->asDecimal($total)?></td>
					<td class="midtext f_bold text-right"><?=Yii::$app->formatter->asDecimal($totalVAT)?></td>
				</tr>
				</tbody>
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
		$('#div_fix_table').height(table_h+'px');
	}
	
	$('#download-xls').on('click', function (e) {		
		html_xls_export('fix_table', '$downloadFileName');
	});
JS;
	$this->registerJs($script1);
?>

