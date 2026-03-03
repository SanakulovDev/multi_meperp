<?php
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\LineStopSearch */
/* @var $form yii\widgets\ActiveForm */

$types = (new \app\models\LineStopReason())->getTypes();
$shiftList = Yii::$app->params['shifts'] ?? [1=>1,2=>2];
$shifts = [];
foreach ($shiftList as $k=>$v) {
  $shifts[$k] = Yii::t('app', "$k-shift");
}

?>

<div class="line-stop-search">

    <?php $form = ActiveForm::begin([
      "action" => ["index"],
      "method" => "get",
    ]); ?>

    <div class="col-sm-3">
      <?= $form
        ->field($model, "type")
        ->dropDownList($types, ["class" => "form-control"])
        ->label(false) ?>

    </div>
    <?=$form->field($model, 'part_production_monitor_id')->hiddenInput()->label(false); ?>
    <div class="col-sm-3">
      <?=$form->field($model, 'production_date')->widget(DateTimePicker::classname(), [
        'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
        'layout' => '{picker}{input}{remove}',
        'removeButton' => ['position' => 'append'],
        'language' => 'ru',
        'pluginOptions' => [
          'autoclose' => true,
          'format' => 'yyyy-mm-dd',
          'startView' => 'month',
          'minView' => 'month',
          'maxView' => 'month',
        ],
        'options' => [
          'autocomplete' => 'off',
          'class' => 'form-control'
        ]
      ])->label(false)
      ?>
    </div>

    <div class="col-sm-2">
      <?=$form->field($model, 'shift')->dropDownList($shifts, ['prompt' => '...'])->label(false) ?>
    </div>
    <?= Html::submitButton(Yii::t("app", "btn-show"), ["class" => "btn btn-primary btn-sm"]) ?>
    <?php ActiveForm::end(); ?>

</div>
