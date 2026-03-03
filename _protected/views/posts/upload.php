<?php
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \app\models\Posts */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="photos-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
      <div class="col-md-4">

        <?= $form->field($model, 'image')->fileInput(['required' => 'required', 'accept' => 'image/png, image/jpeg']) ?>
      </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
