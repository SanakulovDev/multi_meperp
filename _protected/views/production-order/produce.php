<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\ProductionOrder */
	$this->title = Yii::t('app', 'Produce');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Production order'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<style>
	.table-order-600{
		font-size:20px;
	}
</style>

<div class="production-order-create">

	<div class="production-order-form">

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

		<hr style="
                height: 3px;
                margin: 2px 0px 17px 0px;
                background-color: #eaeaea;
                ">

		<div class="box-body table-responsive no-padding">
			<table class="table table-hover table-striped table-order-600">
				<tbody>
				<tr>
					<th><?=Yii::t('app', 'Serial number')?></th>
					<th><?=Yii::t('app', 'Part number')?></th>
					<th><?=Yii::t('app', 'Quantity')?></th>
					<th><?=Yii::t('app', 'Date')?></th>
					<th><?=Yii::t('app', 'User')?></th>
				</tr>

				<? foreach($last_produced_orders as $order){ ?>

					<tr>
						<td><?=$order->serial_number?></td>
						<td><?=$order->part->partinfo?></td>
						<td><?=$order->quantity?></td>
						<td><?=$order->updatedAtFormatted?></td>
						<td><?=$order->updatedBy->fullname?></td>
					</tr>

				<? } ?>
				</tbody>
			</table>
		</div>


		<?php ActiveForm::end(); ?>

	</div>

</div>
