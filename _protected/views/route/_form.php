<?php

use app\enums\ShipMode;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>

<div class="ship-mode-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'ship_mode')
		->dropDownList($shipModes, [
			'prompt' => Yii::t('app', 'Select...'),
			'data-url' => Url::toRoute('point/get-points-by-ship-mode')
		]) ?>

	<?= $form->field($model, 'from_point_id')
		->dropDownList($points, ['prompt' => Yii::t('app', 'Select...')]) ?>

	<?= $form->field($model, 'to_point_id')
		->dropDownList($points, ['prompt' => Yii::t('app', 'Select...')]) ?>


	<?= $form->field($model, 'description')
		->textInput(['maxlength' => true]) ?>

	<div class="form-group pull-right">
		<?= Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm']) ?>
		<?= Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>