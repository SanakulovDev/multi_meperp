<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\Defect */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="defect-form">

	<?php $form = ActiveForm::begin(); ?>

  <div class="row">
    <div class="col-sm-6 col-md-6 col-lg-6">
      <?=$form->field($model, 'category')->dropDownList($model->categoryList())?>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-6">
      <?=$form->field($model, 'code')->textInput(['maxlength' => true])?>
    </div>
  </div>


	<?=$form->field($model, 'description')->textInput(['maxlength' => true])?>

	<div class="form-group">
		<?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
		<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
