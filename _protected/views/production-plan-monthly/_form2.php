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
  'enableAjaxValidation' => true,
  'validateOnType' => false,
  'validationUrl' => $validationUrl,
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
    jQuery(".dynamicform_wrapper .panel-title-address").each(function(index) {
    });
});
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    $(item).find('.datetimepicker').datetimepicker({
      format: 'yyyy-mm',
      autoclose: true,
      todayBtn: true,
      startView: 'year',
      minView: 'year',
      maxView: 'year',
      // Boshqa sozlovlar va parametrlar shu yerdan kiritilishi mumkin
    });
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
<div class="row">
    <div class="col-lg-4">
        <?
        $cond_pt = (!Yii::$app->user->can('admin')) ? ['and', ['in', 'warehouse_id', Yii::$app->user->identity->warehouseIds]] : '';
        $parts = Part::find()->with(['warehouse' => function($q) {
        $q->andWhere(['warehouse_type' => [Warehouse::TYPE_PHYSICAL, Warehouse::TYPE_SHOP]]);
        }
        ]);
        $parts = $parts->where($cond_pt)->andWhere(['status' => Part::STATUS_ACTIVE])->all();
        $items = ArrayHelper::map($parts, 'id', 'part_no');
        $params = ['prompt' => '. . .', 'class' => 'form-control select2'];
        ?>
        <label class="form-group has-float-label">
            <?=$form->field($modelMain, 'part_id')->dropDownList($items, $params)?>
            <span><?=Yii::t('app', 'Part No')?></span>
        </label>
    </div>
    <div class="col-lg-4">
        <? $cond_wh = (!Yii::$app->user->can('admin')) ? ['warehouse.id' => Yii::$app->user->identity->warehouseIds] : ''; ?>
        <label class="form-group has-float-label">
            <?=$form->field($modelMain, 'warehouse_id')->dropDownList(ArrayHelper::map(Warehouse::find()->where($cond_wh)->all(), 'id', 'name'),
                ['prompt' => '. . .', 'class' => 'form-control select2']
              );?>
            <span><?=Yii::t('app', 'Location')?></span>
        </label>
    </div>
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
                                    <?=$form->field($model, "[{$index}]production_date")->widget(DateTimePicker::classname(), [
                                            'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
                                            'layout' => '{picker}{input}{remove}',
                                            'removeButton' => ['position' => 'append'],
                                            'language' => 'ru',
                                            'pluginOptions' => [
                                                'autoclose' => true,
                                                'format' => 'yyyy-mm',
                                                'startView' => 'year',
                                                'minView' => 'year',
                                                'maxView' => 'year',
                                            ],
                                            'options' => [
                                                'autocomplete' => 'off',
                                                'placeholder' => 'YYYY-MM',
                                                'class' => ' form-control datetimepicker'
                                            ]
                                        ]);?>
                                    <span><?=Yii::t('app', 'Production date')?></span>
                                </label>
                            </div>
                            <div class="col-md-2">
                                <label class="form-group has-float-label">
                                    <?=$form->field($model, "[{$index}]shift")->dropDownList($smena_list, ['prompt'=>'---'])?>
                                    <span><?=Yii::t('app', 'Shift')?></span>
                                </label>
                            </div>
                            <div class="col-md-2">
                                <label class="form-group has-float-label">
                                    <?=$form->field($model, "[{$index}]target_qty")->textInput()?>
                                    <span><?=Yii::t('app', 'Target qty')?></span>
                                </label>
                            </div>
                            <div class="col-md-3">
                                <label class="form-group has-float-label">
                                    <?= $form->field($model, "[{$index}]line")->dropDownList($lines, ['prompt' => '. . .', 'class' => 'form-control select2'])->label(false) ?>
                                    <span><?= Yii::t('app', 'Line')?></span>
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
$script1 = <<< JS
$(document).ready(function() {	
	var part_id = $('#productionplanshort-part_id').children("option:selected"). val();
	if(part_id>0){
		let url = '$urlOrder' + '?id=' + part_id;
	$.get(url, function(data, status){
	  // clear
	  $('#productionplanshort-warehouse_id').find('option').remove();
	  // append
	  $.each(data, function () {
	    $('#productionplanshort-warehouse_id').append(new Option(this.text, this.id));
	  });	  
	  var data = {
      "id": $("#productionplanhort-warehouse_id option:first").val(),
      "text": $("#productionplanshort-warehouse_id option:first").text()
    };    
    $('#productionplanshort-warehouse_id').trigger({
        type: 'select2:select',
        params: {
            data: data
        }
    });
	 });
	}
	
	$(document).on("select2:select", "#productionplanshort-part_id", function(e) {
	 let data = e.params.data;
	 let url = '$urlOrder'+'?id='+data.id;
	 $.get(url, function(data, status){
	  // clear
	  $('#productionplanshort-warehouse_id').find('option').remove();
	  // append
	  $.each(data, function () {
	    $('#productionplanshort-warehouse_id').append(new Option(this.text, this.id));
	  });	  
	  var data = {
      "id": $("#productionplanshort-warehouse_id option:first").val(),
      "text": $("#productionplanshort-warehouse_id option:first").text()
    };    
    $('#productionplanshort-warehouse_id').trigger({
        type: 'select2:select',
        params: {
            data: data
        }
    });
	 });
	});
	
});
JS;
$this->registerJs($script1);
?>