<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\ContainerInvoice */
	/* @var $form yii\widgets\ActiveForm */
	$validationUrl = ['validate'];
	if(!$model->isNewRecord){
		$validationUrl['id'] = $model->id;
	}
	$form = ActiveForm::begin(
		[
			'id' => $model->formName(),
			'enableAjaxValidation' => true,
			'validateOnType' => false,
			'validationUrl' => $validationUrl,
			'options' => ['data-pjax' => true, 'class' => 'modalForm']
		]);
?>

<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6">
		<?=Html::input('text', 'cont_no', $model->container->container_no, ['class' => 'modalForm']);
		?>
	</div>
</div>

<?php ActiveForm::end(); ?>
