<?php
use app\models\LineStopReason;
use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\LineStop */
/* @var $form yii\widgets\ActiveForm */
$validationUrl = ['validate'];
if(!$model->isNewRecord){
  $validationUrl['id'] = $model->id;
}


Pjax::begin(['id' => 'formPjax']);
$form = ActiveForm::begin([
                            'id' => 'line-stop-form',
                            'enableAjaxValidation' => true,
                            'validateOnType' => false,
                            'validationUrl' => $validationUrl,
                            'options' => ['data-pjax' => true, 'class' => 'modalForm']
                          ]);
$reasons = ArrayHelper::map(LineStopReason::find()->all(), 'id', 'name');
?>
<?=$form->field($model, 'part_production_monitor_id')->hiddenInput()->label(false) ?>
<?=$form->field($model, 'status')->hiddenInput()->label(false)?>
<?=$form->field($model, 'line_stop_reason_id')->dropDownList($reasons, ['class' => 'form-control select2'])?>

<div class="row">
  <div class="col-sm-6 col-md-6 col-lg-6">
    <?=$form->field($model, 'start_time')->widget(DateTimePicker::classname(), [
      'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
      'layout' => '{picker}{input}{remove}',
      'removeButton' => ['position' => 'append'],
      'language' => 'ru',
      'pluginOptions' => [
        'autoclose' => true,
        'format' => 'yyyy-mm-dd HH:ii'
      ],
      'options' => [
        'autocomplete' => 'off',
        'class' => 'form-control input-sm'
      ]
    ]);
    ?>
  </div>
  <div class="col-sm-6 col-md-6 col-lg-6">
    <?=$form->field($model, 'end_time')->widget(DateTimePicker::classname(), [
      'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
      'layout' => '{picker}{input}{remove}',
      'removeButton' => ['position' => 'append'],
      'language' => 'ru',
      'pluginOptions' => [
        'autoclose' => true,
        'format' => 'yyyy-mm-dd HH:ii'
      ],
      'options' => [
        'autocomplete' => 'off',
        'class' => 'form-control input-sm'
      ]
    ]);
    ?>
  </div>
</div>
<?= $form->field($model, 'bypass')->textInput() ?>
<?= $form->field($model, 'remark')->textarea(['maxlength' => true]) ?>

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

<?php Pjax::end(); ?>
