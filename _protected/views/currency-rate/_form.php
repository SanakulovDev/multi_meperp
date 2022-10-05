<?php
  use yii\helpers\Html;
  use yii\widgets\ActiveForm;
  use yii\helpers\ArrayHelper;
  use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
  /* @var $model app\models\CurrencyRate */
  /* @var $form yii\widgets\ActiveForm */
?>

<div class="currency-rate-form">

  <div class="row">
    <div class="col-lg-6">
      <?php $form = ActiveForm::begin();?>

      <?=$form->field($model, 'rate_date')->widget(DateTimePicker::classname(), [
				'pluginOptions' => [
					'language' => 'ru',
					'autoclose' => true,
					'format' => 'yyyy-mm-dd',
					'minView' => 'month',
					'maxView' => 'month',
				],
				'options' => [
					'autocomplete' => 'off'
				]
			])?>

      <?=
        $form->field($model, 'currency_id')->dropDownList(ArrayHelper::map(app\models\Currency::find()->all(), 'id', 'name'), [
          'class' => ' form-control select2',
          'prompt' => Yii::t('app', 'Select')
        ]);
      ?>

      <?=$form->field($model, 'uzs_value')->textInput(['maxlength' => true])?>

      <div class="form-group">
        <?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
        <?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
      </div>

      <?php ActiveForm::end();?>
    </div>
  </div>

</div>
