<?php
	use yii\helpers\Html;
	use yii\helpers\Url;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\ProductionOrder */
	$this->title = Yii::t('app', 'Shop confirm');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Document'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<style>
	.table-order-600{
		font-size:16px;
	}
</style>

<div class="document-create">

	<div class="document-form">


		<div class="row">
			<?php $form = ActiveForm::begin(); ?>
			<div class="col-lg-3 col-sm-3">
				<div class="form-group">
					<?=Html::submitButton(Yii::t('app', 'btn-ok'), ['class' => 'btn btn-success btn-lg'])?>
				</div>
				<?=$form->field($model, 'serial_number')->textarea(['placeholder' => Yii::t('app', 'Serial number'), 'class' => 'form-control input-lg', 'style' => 'font-size: 18px;height:400px;', 'autofocus' => true])->label(false)?>
			</div>
			<?php ActiveForm::end(); ?>

			<div class="col-lg-9 col-sm-9">
				<div class="box-body table-responsive no-padding">
					<table class="table table-hover table-striped table-order-600">
						<tbody>
						<tr>
							<th><i class="fa fa-fw fa-gears"></i></th>
							<th><?=Yii::t('app', 'Serial number')?></th>
							<th><?=Yii::t('app', 'Document number')?></th>
							<th><?=Yii::t('app', 'Part number')?></th>
							<th class="text-center"><?=Yii::t('app', 'Quantity')?></th>
							<th><?=Yii::t('app', 'Date')?></th>
							<th><?=Yii::t('app', 'User')?></th>
						</tr>

						<? foreach($last_confirmed_docs as $conf){ ?>

							<tr>
								<td>
								<? if (Yii::$app->user->can('document-shop-disconfirm')) { ?>
									<?=Html::a(null, Url::to(['shop-disconfirm', 'id' => $conf->id]), [
                                        'class' => 'glyphicon glyphicon-trash',
                                        'data' => [
                                            'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                            'method' => 'post',
                                        ],
                                    ])
                                    ?>
									<?}?>
								</td>
								<td><?=$conf->serial_number?></td>
								<td><?=$conf->docnum?></td>
								<td><?=$conf->documentDetails[0]->part->partinfo?></td>
								<td class="text-center"><?=number_format($conf->documentDetails[0]->qty, 0, '', '')?></td>
								<td><?=$conf->createdAtFormatted?></td>
								<td><?=$conf->createdBy->fullname?></td>
							</tr>

						<? } ?>
						</tbody>
					</table>
				</div>
			</div>

		</div>


	</div>

</div>
