<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\ProductionOrder;
use app\models\Part;
use app\models\Unit;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\ProductionPowerSearch */
/* @var $form yii\widgets\ActiveForm */


$validationUrl = ['validate'];
if(!$model->isNewRecord) {
  $validationUrl['id'] = $model->id;
}
$form = ActiveForm::begin([
  'id' => $model->formName(),
  'enableAjaxValidation' => true,
  'validateOnType' => false,
  'validationUrl' => $validationUrl,
  'options' => ['data-pjax' => true, 'class' => 'modalForm']
]);
$lines = ProductionOrder::getLines();

$parts = Part::find()->where(['status' => Part::STATUS_ACTIVE])->all();
$items = ArrayHelper::map($parts, 'id', 'part_no');
$params = [
  'prompt' => '---',
  'class' => 'form-control select2',
  'data-intro' => Yii::t('intro', 'production-release-part_id')
];

$shifts = ProductionOrder::getShifts();
$units = ArrayHelper::map(Unit::find()->all(), 'id', 'description');
?>
<?php ob_start();?>

.modal-dialog{
    width: 1200px;
}


<?php $this->registerCss(ob_get_clean());?>


<div class="production-power-update">


    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'part_id')->dropDownList($items, $params) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'part_name')->textInput(['maxlength' => true, 'readonly'=>true]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'test_pr')->dropDownList($lines, ['prompt' => '----', 'class'=>'form-control select2']) ?>
        </div>
        <div class="col-md-3">
            <?=$form->field($model, 'target_date')->widget(DateTimePicker::classname(), [
                'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
                'layout' => '{picker}{input}{remove}',
                'removeButton' => ['position' => 'append'],
                'language' => 'ru',
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                    'startView' => 'year',
                    'minView' => 'month',
                    'maxView' => 'month',
                ],
                'options' => [
                    'autocomplete' => 'off',
                    'placeholder' => 'YYYY-MM',
                    'class' => ' form-control'
                ]
            ])->label(Yii::t('app', 'Target date'));
            ?>
        </div>
        
    </div>
    <div class="row" >
        <div class="col-md-2">
            <?= $form->field($model, "line")->dropDownList($lines, ['prompt' => '----', 'class'=>'form-control select2 finder-line']) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, "time")->widget(DateTimePicker::classname(), [
                'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
                'layout' => '{picker}{input}{remove}',
                'removeButton' => ['position' => 'append'],
                'language' => 'ru',
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'hh:ii',
                    'startView' => 'day',
                    'minView' => 'day',
                    'maxView' => 'day',
                ],
                'options' => [
                    'autocomplete' => 'off',
                    'placeholder' => 'HH:MM',
                    'class' => ' form-control'
                ]
            ])->label(Yii::t('app', 'Time'));
            ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, "unitId")->dropDownList($units,['prompt' => '----', 'class'=>'form-control select2 finder-unit']) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, "plan_power")->textInput(["type" => 'number']) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, "max_power")->textInput(["type" => 'number']) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, "special")->textInput(["maxlength" => true]) ?>
        </div>
    </div>
   

    
</div>

<?php ActiveForm::end(); ?>