<?php
	use yii\helpers\ArrayHelper;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\ContractDetail */
	$this->title = Yii::t('app', 'Upload part');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Part'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contract-detail-create">
  
  <p class="pull-right">
		<?=Html::a(Yii::t('app', 'btn-download-template'), '/public/part_template.xlsx', ['class' => 'btn btn-info btn-sm', 'data-intro' => Yii::t('intro', 'btn-download-template')])?>
	</p>

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">
		<div class="col-lg-6">

			<?=$form->field($model, 'file')->fileInput()?>

			<div class="form-group">
				<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
			</div>


		</div>
	</div>

	<?php ActiveForm::end(); ?>

</div>
