<?php
	use app\components\Helpers;
	use kartik\datetime\DateTimePicker;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $searchModel app\models\ReqSearch */
	/* @var $dataProvider yii\data\ActiveDataProvider */
	$this->title = Yii::t('app', 'Monthly material import report');
	$this->params['breadcrumbs'][] = $this->title;
	$period = app\components\Helpers::getPeriod60Days();
?>
	<div class="req-index">


		<div class="panel">
			<div class="panel-heading">
                <img style="height:28px;" src="/img/logo.jpg" title="<?php echo Yii::$app->params['comp_name'] ?>" class="pull-left"/>
				<h3 class="pull-left" style="margin: 5px 0px -5px 10px;">
					<?=Yii::t('app', 'Monthly material import report')?>
				</h3>
				<p class="pull-right" style="margin: 0px">
					<?=Html::a(Yii::t('app', 'btn-download'), ['download-import', 'need_month' => $need_month], ['class' => 'btn btn-info btn-sm'])?>
				</p>
				<div style="clear: both;"></div>
			</div>
			<div class="panel-body">
				<p>
					<?php $form = ActiveForm::begin(['action' => '', 'method' => 'post',]); ?>
				<div class="row">
					<div class="col-md-2 col-lg-2">
						<?=
							DateTimePicker::widget(
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
										'placeholder' => Yii::t('app', 'need_month'),
										'class' => ' form-control input-sm'
									]
								]
							);
						?>
					</div>
					<div class="col-md-2 col-lg-2">
						<?=Html::submitButton(Yii::t('app', 'btn-show'), ['class' => 'btn btn-primary btn-sm'])?>
					</div>
				</div>
				<?php ActiveForm::end(); ?>
				</p>

				<div id="div_fix_table">
					<table class="table table-req" id="fix_table">
						<thead>
						<tr class="tr_head">
							<th style="width: 30px;" class="text-center">№</th>
							<th style="width: 100px;" class="text-left"><?=Yii::t('app', 'Part')?></th>
							<th style="width: 100px;" class="text-left"><?=Yii::t('app', 'Part color')?></th>
							<th><?=mb_strtoupper(Yii::t('app', 'Part name'))?></th>
							<th style="width: 200px;" class="text-left"><?=Yii::t('app', 'Supplier')?></th>
							<th style="width: 100px;" class="text-center"><?=Yii::t('app', 'Country')?></th>
							<th style="width: 100px;" class="text-center"><?=Yii::t('app', 'CNFEA Code')?></th>
							<th style="width: 100px;" class="text-right"><?=Yii::t('app', 'Qty')?></th>
							<th style="width: 100px;" class="text-right"><?=Yii::t('app', 'Price')?></th>
							<th style="width: 100px;" class="text-center"><?=Yii::t('app', 'UOM')?></th>
							<th style="width: 100px;" class="text-right"><?=Yii::t('app', 'Amount')?></th>
							<th style="width: 100px;" class="text-center"><?=Yii::t('app', 'Currency')?></th>

						</tr>
						</thead>
						<tbody>
						<? $i = 0; ?>
						<? foreach($data as $row){ ?>
							<tr <?=($i++%2 == 0) ? 'class="tr_odd"' : ''?>>
								<td class="text-center"><?=$i?></td>
								<td class="text-left"><?=$row['part_no']?></td>
								<td class="text-left"><?=$row['part_color']?></td>
								<td style="max-width: 150px;" class="td-nowrap"><?=mb_strtoupper($row['part_name'])?></td>
								<td class="text-left"><?=$row['supplier']?></td>
								<td class="text-center"><?=$row['country']?></td>
								<td class="text-center"><?=$row['tnved']?></td>
								<td class="text-right"><?=Helpers::formatRemoveDecimal($row['qty'])?></td>
								<td class="text-right"><?=Helpers::formatRemoveDecimal($row['price'])?></td>
								<td class="text-center"><?=$row['uom']?></td>
								<td class="text-right"><?=Helpers::formatRemoveDecimal($row['amount'])?></td>
								<td class="text-center"><?=$row['currency']?></td>


							</tr>

						<? } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

	</div>


<?
	$script1 = <<< JS
	
	$('#fix_table').tableFixer();
	
        changeHeight();
        
	$(window).resize(function(){
		changeHeight();
	});
        
	function changeHeight(){
		window_h = $(window).height();
		table_h = window_h - 100;
		console.log(window_h+"-"+table_h);		
		$('#div_fix_table').height(table_h+'px');
	}
        
  

	
JS;
	$this->registerJs($script1);
?>