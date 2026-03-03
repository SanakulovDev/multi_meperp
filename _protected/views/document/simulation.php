<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\Document */
	$this->title = Yii::t('app', 'Simulation');
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-create">
	<?php $form = ActiveForm::begin(); ?>
	<div class="form-group field-document-comment has-success">
		<label class="control-label" for="document-comment">Products and counts</label>
		<?=Html::textarea('issue_data', $_POST['issue_data'], ['class' => 'form-control', 'rows' => 10]);?>
		<div class="help-block"></div>
	</div>


	<div class="form-group pull-right">
		<?=Html::submitButton(Yii::t('app', 'GO !'), ['class' => 'btn btn-success btn-lg', 'name' => 'submitSml'])?>
	</div>

	<?php ActiveForm::end(); ?>


</div>
