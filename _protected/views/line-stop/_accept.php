<?php
use app\models\LineStopReason;
use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\LineStop */
/* @var $form yii\widgets\ActiveForm */
$validationUrl = ["validate"];

Pjax::begin(["id" => "formPjax"]);
$form = ActiveForm::begin([
  "id" => "line-stop-form",
  "enableAjaxValidation" => true,
  "validateOnType" => false,
  "validationUrl" => $validationUrl,
  "options" => ["data-pjax" => true, "class" => "modalForm"],
]);
?>
<?= $form->field($model, "fix_list")->dropDownList($reasons, ["class" => "form-control select2", 'multiple'=>'multiple']) ?>
<?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>
