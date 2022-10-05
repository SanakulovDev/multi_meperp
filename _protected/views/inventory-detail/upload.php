<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\ContractDetail */
	$this->title = Yii::t('app', 'Upload details');
	$this->params['breadcrumbs'][] = ['label' => $model->api->invinfo, 'url' => ['inventory/update', 'id' => $model->api_id]];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contract-detail-create">

	<?php $form = ActiveForm::begin(); ?>
	<?=$form->field($model, 'api_id')->hiddenInput()->label(false)?>

	<div class="row">
		<div class="col-lg-6">

			<div class="form-group">
				<label class="control-label" for="apidetail-api_id"><?=$model->getAttributeLabel('api_id')?></label>
				<input type="text" class="form-control" value="<?=$model->api->invinfo?>" disabled="1">
				<div class="help-block"></div>
			</div>

			<?=$form->field($model, 'file')->fileInput()?>

			<div class="form-group">
				<?=Html::a(Yii::t('app', 'btn-cancel'), ['inventory/update', 'id' => $model->api_id], ['class' => 'btn btn-default btn-sm'])?>
				<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
			</div>


		</div>
	</div>

	<?php ActiveForm::end(); ?>

</div>
