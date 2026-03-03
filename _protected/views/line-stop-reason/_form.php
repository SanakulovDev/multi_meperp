<?php
use app\rbac\models\AuthItem;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\LineStopReason */
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
$roles = ArrayHelper::map(AuthItem::find()->where(['type'=>AuthItem::TYPE_ROLE])->all(),'name','name');
?>

<?=$form->field($model, 'name')->textInput(['maxlength' => true])?>
<div class="row">
  <div class="col-sm-6 col-md-6 col-lg-6">
    <?=$form->field($model, 'auth_item_name')->dropDownList($roles, ['class' => 'form-control select2'])?>
  </div>
  <div class="col-sm-6 col-md-6 col-lg-6">
    <?=$form->field($model, 'type')->dropDownList($model->getTypes(), ['class' => 'form-control'])?>
  </div>
</div>

<?=$form->field($model, 'fix_list')->textarea(['class' => 'form-control', 'rows'=>10])?>

<?php ActiveForm::end(); ?>
