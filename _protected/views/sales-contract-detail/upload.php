<?php
	use yii\helpers\ArrayHelper;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\ContractDetail */
	$this->title = Yii::t('app', 'Upload details');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'FG contract'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contract-detail-create">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">
		<div class="col-lg-6">

			<?=
				$form->field($model, 'sales_contract_id')->dropDownList(ArrayHelper::map(app\models\SalesContract::find()->all(), 'id', 'contractInfo'), [
					'class' => ' form-control select2',
					'prompt' => Yii::t('app', 'Select')
				]);
			?>

			<?=$form->field($model, 'file')->fileInput()?>

			<div class="form-group">
				<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
			</div>


		</div>
	</div>

	<?php ActiveForm::end(); ?>

</div>
