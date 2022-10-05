<?php

use app\enums\ShipMode;
use yii\helpers\Html;
use yii\widgets\ActiveForm;


?>

<div class="ship-mode-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'ship_mode')
		->dropDownList($shipModes, ['prompt' => Yii::t('app', 'Select...')]) ?>

	<?= $form->field($model, 'name')
		->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'description')
		->textInput(['maxlength' => true]) ?>

	<div class="form-group pull-right">
		<?= Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm']) ?>
		<?= Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>