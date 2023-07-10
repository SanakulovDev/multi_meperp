<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\ProductionOrder;
use app\models\Part;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\ProductionRelease */
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
?>

<div class="production-release-form">

    <div class="row">
        <div class="col-md-3">
             <?= $form->field($model, 'part_id')->dropDownList($items, $params) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'part_name')->textInput(['maxlength' => true, 'readonly'=>true]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'line')->dropDownList($lines, $params) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'pr_order_number')->textInput(['maxlength' => true]) ?>

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
        <div class="col-md-3">
            <?= $form->field($model, 'shift')->dropDownList($shifts, $params) ?>
        </div>
        <div class="col-md-3">
            <?=$form->field($model, 'time')->widget(DateTimePicker::classname(), [
                'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
                'layout' => '{picker}{input}{remove}',
                'removeButton' => ['position' => 'append'],
                'language' => 'ru',
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'hh:ii',  
                    'startView' => 'day',
                    // 'minView' => 'hour',
                    // 'maxView' => 'hour',
                ],
                'options' => [
                    'autocomplete' => 'off',
                    'placeholder' => 'HH:MM',
                    'class' => ' form-control'
                ]
            ]);?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'quantity')->textInput() ?>
        </div>
    </div>
   

   


    
</div>

<?php ActiveForm::end(); ?>
<?php $partsUrl = Url::to(['part/get-partname'], true);
  ob_start();?>
    $('#productionrelease-part_id').on('change', function() {
        let id = $(this).val();
        load(id);
    })
    function load(partId) {
      var url = "<?= $partsUrl?>";
      $.ajax({
        //dataType: "json",
        type: "GET",
        url: url,
        data: {
          id: partId,
        },
        success: function(response){
            $('#productionrelease-part_name').val(response.partname);        
        },
        error: function(response){
          console.log(response);
        }
      });
    }
<?php $this->registerJs(ob_get_clean(), \yii\web\View::POS_READY )?>