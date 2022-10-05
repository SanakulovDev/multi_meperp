<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PackLevel */
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
$packs = \yii\helpers\ArrayHelper::map(\app\models\Pack::find()->all(),'id','code');
$parts = \yii\helpers\ArrayHelper::map(\app\models\Part::find()->all(),'id','partinfo');
?>

<div class="row">
  <div class="col-sm-6 col-md-6 col-lg-6">
    <?=$form->field($model, 'part_id')->dropDownList($parts, ['class' => 'form-control select2'])?>
  </div>
  <div class="col-sm-6 col-md-6 col-lg-6">
    <?=$form->field($model, 'pack_id')->dropDownList($packs, ['class' => 'form-control select2'])?>
  </div>
</div>
<div class="row">
    <div class="col-sm-4 col-md-4 col-lg-4">
        <?=$form->field($model, 'in_pack_id')->dropDownList($packs, ['class' => 'form-control select2'])?>
    </div>
    <div class="col-sm-4 col-md-4 col-lg-4">
        <?=$form->field($model, 'quantity')->textInput(['maxlength' => true, 'type' => 'number'])?>
    </div>
    <div class="col-sm-4 col-md-4 col-lg-4">
        <?=$form->field($model, 'level')->textInput(['maxlength' => true, 'type' => 'number'])?>
    </div>
</div>

<?php if(!$model->isNewRecord): ?>
    <div class="row">
        <div class="col-sm-6 col-md-6 col-lg-6">
			<span class="form-control">
				<?=$model->getAttributeLabel('created_by').' '.$model->createdBy->fullname.' '.$model->createdAtFormatted?>
			</span>
        </div>
        <div class="col-sm-6 col-md-6 col-lg-6">
			<span class="form-control">
				<?=$model->getAttributeLabel('updated_by').' '.$model->updatedBy->fullname.' '.$model->updatedAtFormatted?>
			</span>
        </div>
    </div>
<?php endif ?>
<?php ActiveForm::end(); ?>
