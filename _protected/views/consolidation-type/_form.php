<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\ContractSubject */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="consolidation-type-form">

	<?php $form = ActiveForm::begin(); ?>

	<?=$form->field($model, 'name')
	        ->textInput([
		                    'maxlength' => true,
		                    'data-step' => 2,
		                    'data-intro' => Yii::t('intro', 'consolidation-name')
	                    ])?>

	<?=$form->field($model, 'description')
	        ->textInput([
		                    'maxlength' => true,
		                    'data-step' => 3,
		                    'data-intro' => Yii::t('intro', 'consolidation-description')
	                    ])?>

	<div class="form-group">
		<?=Html::a(Yii::t('app', 'btn-cancel'), ['index'],
		           [
			           'class' => 'btn btn-default btn-sm',
			           'data-step' => 4,
			           'data-intro' => Yii::t('intro', 'cancel-button')
		           ])
		?>
		<?=Html::submitButton(Yii::t('app', 'btn-save'),
		                      [
			                      'class' => 'btn btn-success btn-sm',
			                      'data-step' => 5,
			                      'data-intro' => Yii::t('intro', 'save-button')
		                      ])?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
