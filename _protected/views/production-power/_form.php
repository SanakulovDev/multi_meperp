<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\ProductionOrder;
use app\models\Part;
use app\models\Unit;
use kartik\datetime\DateTimePicker;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $this yii\web\View */
/* @var $model app\models\ProductionRelease */
/* @var $form yii\widgets\ActiveForm */
$validationUrl = ['validate'];
if(!$model->isNewRecord) {
  $validationUrl['id'] = $model->id;
}
$form = ActiveForm::begin([
  'id' => 'dynamic-form',
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
$units = ArrayHelper::map(Unit::find()->all(), 'id', 'description');
?>


<!-- css stylelar -->

<?php ob_start();?>

    .modal-dialog{
        width: 1200px;
    }


<?php $this->registerCss(ob_get_clean());?>
<div class="production-power-form">

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($modelMain, 'part_id')->dropDownList($items1, $params) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($modelMain, 'part_name')->dropDownList($items2, $params) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($modelMain, 'test_pr')->dropDownList($lines, ['prompt' => '----', 'class'=>'form-control select2']) ?>
        </div>
        <div class="col-md-3">
            <?=$form->field($modelMain, 'target_date')->widget(DateTimePicker::classname(), [
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
   

<?php DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
        'widgetBody' => '.container-items', // required: css class selector
        'widgetItem' => '.item', // required: css class
        'limit' => 15, // the maximum times, an element can be cloned (default 999)
        'min' => 1, // 0 or 1 (default 1)
        'insertButton' => '.add-item', // css class
        'deleteButton' => '.remove-item', // css class
        'model' => $models[0],
        'formId' => 'dynamic-form',
        'formFields' => [
            'line',
            'shift',
            'unitId',
            'plan_power',
            'max_power',
            'special'
        ],
    ]); ?>


    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="javascript::void(0)" class="pull-right add-item btn btn-success btn-xs"><?=Yii::t('app', 'btn-create')?></i></a>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body container-items" style="overflow-y: scroll; max-height: 350px;">
        <?php foreach ($models as $index => $item): ?>
            <div class="item panel panel-default"><!-- widgetBody -->
                <?php
                    // necessary for update action.
                    if (!$item->isNewRecord) {
                        echo Html::activeHiddenInput($item, "[{$index}]id");
                    }
                ?>
                <div class="row" style="display: flex; align-items:center; justify-content:between;">
                    <div class="col-md-2">
                        <?= $form->field($item, "[{$index}]line")->dropDownList($lines, ['prompt' => '----', 'class'=>'form-control select2 finder-line']) ?>
                    </div>
                    <div class="col-md-3">
                        <?=$form->field($item, "[{$index}]time")->dropDownList($selectTimes, $params)?>
                    </div>
                    <div class="col-md-1">
                        <?= $form->field($item, "[{$index}]unitId")->dropDownList($units,['prompt' => '----', 'class'=>'form-control select2 finder-unit']) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($item, "[{$index}]plan_power")->textInput(["type" => 'number']) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($item, "[{$index}]max_power")->textInput(["type" => 'number']) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($item, "[{$index}]special")->textInput(["type" => 'number']) ?>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="pull-right remove-item btn btn-danger btn-xs"><i class="fa fa-minus"></i></button>
                    </div>
                </div>

            </div>
        <?php endforeach; ?>
        </div>
    </div>
<?php DynamicFormWidget::end(); ?>
    
    



    
</div>
<?php ActiveForm::end(); ?>
<?php $partsUrl = Url::to(['production-release/generate-order-number'], true);
  ob_start();?>
    $("#modalError").find(".help-block").css({"text-align":"center", "font-size": "25px"});
    $(".dynamicform_wrapper").on("afterInsert", function(e, item) {
        $(item).find('.finder-part_id').select2();
        $(item).find('.finder-unit').select2();
        $(item).find('.finder-time').datetimepicker({
            format: 'hh:ii',
            autoclose: true,
            todayBtn: true,
            startView: 'day',
            minView: 'day',
            maxView: 'day',
            // Boshqa sozlovlar va parametrlar shu yerdan kiritilishi mumkin
        });
    });

    $(".dynamicform_wrapper").on("beforeDelete", function(e, item) {
        if (! confirm("Are you sure you want to delete this item?")) {
            return false;
        }
        return true;
    });

    $(".dynamicform_wrapper").on("afterDelete", function(e) {
        console.log("Deleted item!");
    });

    $(".dynamicform_wrapper").on("limitReached", function(e, item) {
        alert("Limit reached");
    });         




    $('#productionpowerdynamic-part_id').on('change', function() {
        let id = $(this).val();
        $('#productionpowerdynamic-part_name').val(id);
    })
    $('#productionpowerdynamic-part_name').on('change', function() {
        let id = $(this).val();
        $('#productionpowerdynamic-part_id').val(id);
    })
    function load() {
        var url = "<?= $partsUrl?>";
        let date = $('#productionpowerdynamic-target_date').val();
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