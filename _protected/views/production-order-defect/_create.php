<?php
	use app\models\Defect;
	use app\models\ProductionOrderDefect;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\ProductionOrder */
	$this->title = Yii::t('app', 'Defect entry');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Quality control'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
	$defects = Defect::find()->all();
	$histories = (new \yii\db\Query())// ProductionOrderDefect::find()->alias('t')
      ->select(['po.serial_number','max(t.created_at) as created_at','u.fullname'])
      ->from('production_order_defect as t')
      ->innerJoin('production_order po', 't.production_order_id=po.id')
      ->innerJoin('user u', 't.created_by=u.id')
      ->groupBy('po.serial_number,u.fullname')
      ->orderBy('created_at', SORT_DESC)
      ->limit(20)->all();
?>
<style>
	.icheck .checkbox label{
		padding-left:0px !important;
	}
</style>

<div class="document-create">
	<div class="document-form">
		<?php $form = ActiveForm::begin(); ?>

		<div class="row">
			<div class="col-lg-10 col-sm-10">
				<?=$form->field($model, 'serial_number')->textInput(['placeholder' => Yii::t('app', 'Serial number'), 'class' => 'form-control input-lg', 'style' => 'font-size: 25px;', 'autofocus' => true])->label(false)?>
			</div>
			<div class="col-lg-2 col-sm-2">
				<div class="form-group">
					<?=Html::submitButton(Yii::t('app', 'btn-ok'), ['class' => 'btn btn-success btn-lg'])?>
				</div>
			</div>
		</div>
		<div class="row">

			<div class="col-sm-7 col-md-7 col-lg-7">
				<?php foreach($defects as $defect): ?>
					<!--				  <label class="container col-sm-6">-->
					<!--					  <input type="checkbox" name="ProductionOrderDefect[defect_id][--><? //=$defect->id?><!--]" id="productionorderdefect-defect_id--><? //=$defect->id?><!--"/>-->
					<!--					  <span>--><? //=$defect->code?><!--</span>-->
					<!--						<span> <input type="number" class="" value="1"> </span>-->
					<!--				  </label>-->

					<div class="container input-group">
							 <span class="input-group-addon">
								 <input type="checkbox" name="ProductionOrderDefect[defect_id][<?=$defect->id?>] id=" productionorderdefect-defect_id<?=$defect->id?>" /> <?=$defect->code?>
							 </span>
						<input type="number" min="1" max="50" name="ProductionOrderDefect[qty][<?=$defect->id?>] class=" form-control" value="1" style="width:70px;" />
					</div>
				<?php endforeach; ?>
			</div>

			<div class="col-sm-5 col-md-5 col-lg-5">
				<div class="box-body table-responsive no-padding">
					<table class="table table-hover table-striped">
						<thead>
						<tr>
							<th><?=Yii::t('app', 'Serial number')?></th>
							<th><?=Yii::t('app', 'Date')?></th>
							<th><?=Yii::t('app', 'User')?></th>
						</tr>
						</thead>
						<tbody>
						<?php foreach($histories as $item): ?>
							<tr>
								<td><?=$item['serial_number']//$item->productionOrder->serial_number?></td>
								<td><?=date('d.m.Y H:i', $item['created_at'])?></td>
								<td><?=$item['fullname']//$item->createdBy->username?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table
				</div>
			</div>

		</div>
		<?php ActiveForm::end(); ?>
	</div>

</div>

<?
	$script = <<< JS
    $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });
  });
JS;
	$this->registerJs($script, yii\web\View::POS_END);
?>
