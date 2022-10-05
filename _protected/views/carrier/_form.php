<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Carrier */
/* @var $form yii\widgets\ActiveForm */
$validationUrl = ['validate'];
if(!$model->isNewRecord){
  $validationUrl['id'] = $model->id;
}
$form = ActiveForm::begin([
                            'id' => $model->formName(),
                            'enableAjaxValidation' => true,
                            'validateOnType' => false,
                            'validationUrl' => $validationUrl,
                            'options' => ['data-pjax' => true, 'class' => 'modalForm']
                          ]);
?>
<div class="row">
  <div class="col-lg-6 col-sm-6">
    <?=$form->field($model, 'company_name')->textInput(['maxlength' => true])?>
  </div>
  <div class="col-lg-6 col-sm-6">
    <?=$form->field($model, 'duns')->textInput(['maxlength' => true])?>
  </div>
</div>

<div class="row">
  <div class="col-lg-6 col-sm-6">
    <?=$form->field($model, 'country_code_id')
            ->dropDownList(\yii\helpers\ArrayHelper::map(\app\models\CountryCode::find()->all(), 'id', 'name'), ['class' => 'form-control select2'])?>
  </div>
  <div class="col-lg-3 col-sm-3">
    <?=$form->field($model, 'city')->textInput(['maxlength' => true])?>
  </div>
  <div class="col-lg-3 col-sm-3">
    <?=$form->field($model, 'postal')->textInput(['maxlength' => true])?>
  </div>
</div>

<?=$form->field($model, 'address')->textInput(['maxlength' => true])?>
<div class="row">
  <div class="col-lg-6 col-sm-6">
    <?=$form->field($model, 'contact_name')->textInput(['maxlength' => true])?>
  </div>
  <div class="col-lg-6 col-sm-6">
    <?=$form->field($model, 'contact_position')->textInput(['maxlength' => true])?>
  </div>
</div>
<div class="row">
  <div class="col-lg-4 col-sm-4">
    <?=$form->field($model, 'contact_email')->textInput(['maxlength' => true])?>
  </div>
  <div class="col-lg-4 col-sm-4">
    <?=$form->field($model, 'contact_phone')->textInput(['maxlength' => true])?>
  </div>
  <div class="col-lg-4 col-sm-4">
    <?=$form->field($model, 'contact_cellular')->textInput(['maxlength' => true])?>
  </div>
</div>

<?php ActiveForm::end(); ?>

