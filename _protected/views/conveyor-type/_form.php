<?php

use yii\widgets\ActiveForm;


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
    <div class="col-md-3 col-sm-3 col-lg-3">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>
