<?php
use app\models\Part;
use app\models\Warehouse;
use app\models\ProductionOrder;
use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
/* @var $this yii\web\View */
/* @var $model app\models\ProductionPlan */
/* @var $form yii\widgets\ActiveForm */
$validationUrl = ['validate'];
if(!$model->isNewRecord) {
  $validationUrl['id'] = $model->id;
}
$form = ActiveForm::begin([
  'id' => 'dynamic-form',
  'options' => ['class' => 'modalForm']
]);
$lines = ProductionOrder::getLines();
$smena_list = [
    1=> '1 - Смена',
    2=> '2 - Смена',
]
?>
<!-- Dynamicform for js -->
<?php ob_start();?>
jQuery(".dynamicform_wrapper").on("beforeInsert", function(e, item) {
    $(item).find('.select2').select2();
    jQuery(".dynamicform_wrapper .panel-title-address").each(function(index) {
    });
});
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    $(item).find('.select2').select2();
    jQuery(".dynamicform_wrapper .panel-title-address").each(function(index) {
        jQuery(this).html((index + 1))
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-address").each(function(index) {
        jQuery(this).html((index + 1))
    });
});
<?php $this->registerJs(ob_get_clean());?>

<?php ob_start();?>
.form-group{
    margin: 0px!important
}
<?php $this->registerCss(ob_get_clean());?>
<?php 
  $cond_pt = (!Yii::$app->user->can('admin')) ? ['and', ['in', 'warehouse_id', Yii::$app->user->identity->warehouseIds]] : '';
  $parts = Part::find()->with(['warehouse' => function($q) {
  $q->andWhere(['warehouse_type' => [Warehouse::TYPE_PHYSICAL, Warehouse::TYPE_SHOP]]);
  }
  ]);
  $parts = $parts->where($cond_pt)->andWhere(['status' => Part::STATUS_ACTIVE])->all();
  $items = ArrayHelper::map($parts, 'id', 'part_no');
  $params = ['prompt' => '. . .', 'class' => 'form-control select2 plan-part_id'];
  $cond_wh = (!Yii::$app->user->can('admin')) ? ['warehouse.id' => Yii::$app->user->identity->warehouseIds] : '';
?>
<div class="row">
    <!-- <div class="col-md-2">
        <button type="button" class="pull-right add-item btn btn-success btn-xs"><i class="fa fa-plus fa-2x"></i></button>
    </div> -->
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
            'production_date',
            'shift',
            'target_qty',
            'line',
            'part_id', 
            'warehouse_id',
        ],
    ]); ?>
<div class="panel panel-default">
    <div class="panel-heading">
        <a href="javascript::void(0)" class="pull-right add-item btn btn-success btn-xs"><?=Yii::t('app', 'btn-create')?></i></a>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body container-items" style="overflow-y: scroll; max-height: 350px;">
            <?php foreach ($models as $index => $model): ?>
                <div class="item panel panel-default"><!-- widgetBody -->
                    <?php
                        // necessary for update action.
                        if (!$model->isNewRecord) {
                            echo Html::activeHiddenInput($model, "[{$index}]id");
                        }
                    ?>
                        <div class="row" style="display: flex;align-items: center;justify-content: center;">
                        
                        <div class="col-md-4">
                            <label class="form-group has-float-label">
                                <?=$form->field($model, "[{$index}]part_id")->dropDownList($items, $params)?>
                                <span><?=Yii::t('app', 'Part No')?></span>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label class="form-group has-float-label">
                                <?=$form->field($model, "[{$index}]warehouse_id")->dropDownList([],
                                    ['prompt' => '. . .', 'class' => 'form-control select2 plan-warehouse_id']
                                  );?>
                                <span><?=Yii::t('app', 'Location')?></span>
                            </label>
                        </div>
                            <div class="col-md-4">
                                <label class="form-group has-float-label">
                                    <?=$form->field($model, "[{$index}]target_qty")->textInput()?>
                                    <span><?=Yii::t('app', 'Target qty')?></span>
                                </label>
                            </div>
                            
                            <div class="col-md-1">
                                <button type="button" class="pull-right remove-item btn btn-danger btn-xs"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                </div>                            
            <?php endforeach;?>
    </div>
</div>
<?php    DynamicFormWidget::end();?>
<?php ActiveForm::end(); ?>

<?
$urlOrder = Url::to(['production-plan/wh-list-by-part'], true);
ob_start();?>

$(function(){
    $('body').on('change', '.plan-part_id', function(){
        var part_id = $(this).val();
        var warehouse_id = $(this).closest('.item').find('.plan-warehouse_id').attr('id');
        $('#'+warehouse_id).empty();
        $.ajax({
            url: '<?=$urlOrder?>',
            type: 'GET',
            data: {id: part_id},
            success: function(data){
              $.each(data, function(index, value){
                $('#'+warehouse_id).append('<option selected value="'+value.id+'">'+value.text+'</option>');
                
              });
            }
        });
    });
});
<?php
$this->registerJs(ob_get_clean());
?>