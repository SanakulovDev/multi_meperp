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
$items1 = ArrayHelper::map($parts, 'id', 'part_no');
$items2 = ArrayHelper::map($parts, 'id', 'part_name');
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
             <?= $form->field($model, 'part_id')->dropDownList($items1, $params) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'part_name')->dropDownList($items2, $params) ?>
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
<?php $partsUrl = Url::to(['production-release/generate-order-number'], true);
  ob_start();?>
    $('#productionrelease-part_id').on('change', function() {
        let id = $(this).val();
        $('#productionrelease-part_name').val(id);
        load();
    })
    $('#productionrelease-part_name').on('change', function() {
        let id = $(this).val();
        $('#productionrelease-part_id').val(id);
        load();
    })
    load();
    $('#productionrelease-target_date').on('change', function() {
        load();
    })
    $('#productionrelease-line').on('change', function() {
        load();
    });
    function load() {
        var url = "<?= $partsUrl?>";
        let date = $('#productionrelease-target_date').val();
        let line = $('#productionrelease-line').val();
        $.ajax({
            //dataType: "json",
            type: "POST",
            url: url,
            data: {
                date: date,
                line: line
            },
            success: function(response){
                console.log(response);
                $('#productionrelease-pr_order_number').val(response);        
            },
            error: function(response){
            console.log(response);
            }
        });
    }
<?php $this->registerJs(ob_get_clean(), \yii\web\View::POS_READY )?>