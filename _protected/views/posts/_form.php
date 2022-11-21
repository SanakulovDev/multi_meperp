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
        <?= $form->field($model, 'date')->widget(DateTimePicker::classname(), [
          'pluginOptions' => [
            'language' => Yii::$app->language,
            'autoclose' => true,
            'format' => 'yyyy-mm-dd',
            'minView' => 'month',
            'maxView' => 'month',
          ],
          'options' => [
            'autocomplete' => 'off'
          ]
        ]) ?>
        <?= $form->field($model, 'material')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'comment')->textarea(['maxlength' => true]) ?>
      </div>
      <div class="col-md-4">

        <?= $form->field($model, 'weight')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'is_where')->textInput() ?>

      </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
