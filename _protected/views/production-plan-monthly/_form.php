<?php
use app\models\Part;
use app\models\Warehouse;
use app\models\ProductionOrder;
use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductionPlan */
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
?>
<div class="row">
	<div class="col-lg-3">
    <?
    $cond_pt = (!Yii::$app->user->can('admin')) ? ['and', ['in', 'warehouse_id', Yii::$app->user->identity->warehouseIds]] : '';
    $parts = Part::find()->with(['warehouse' => function($q) {
      $q->andWhere(['warehouse_type' => [Warehouse::TYPE_PHYSICAL, Warehouse::TYPE_SHOP]]);
    }
    ]);
    $parts = $parts->where($cond_pt)
                   ->andWhere(['status' => Part::STATUS_ACTIVE])
                   ->all();
    $items = ArrayHelper::map($parts, 'id', 'part_no');
    $params = ['prompt' => '. . .', 'class' => 'form-control select2'];
    ?>
		<label class="form-group has-float-label">
      <?=$form->field($model, 'part_id')->dropDownList($items, $params)?>
			<span><?=Yii::t('app', 'Part No')?></span>
		</label>
	</div>
	<div class="col-lg-3">
    <? $cond_wh = (!Yii::$app->user->can('admin')) ? ['warehouse.id' => Yii::$app->user->identity->warehouseIds] : ''; ?>
		<label class="form-group has-float-label">
      <?=$form->field($model, 'warehouse_id')
              ->dropDownList(ArrayHelper::map(
                Warehouse::find()
                         ->where($cond_wh)
                         ->all(), 'id', 'name'),
                ['prompt' => '. . .', 'class' => 'form-control select2']
              );?>
			<span><?=Yii::t('app', 'Location')?></span>
		</label>
	</div>
	
	<div class="col-lg-3">
		<label class="form-group has-float-label">
      <?=$form->field($model, 'target_qty')->textInput()?>
			<span><?=Yii::t('app', 'Target qty')?></span>
		</label>
	</div>
  <div class="col-md-3">
      <label class="form-group has-float-label">
          <?=$form->field($model, "remark")->textInput()?>
          <span><?=Yii::t('app', 'Remark')?></span>
      </label>
  </div>
  
</div>

<?php ActiveForm::end(); ?>

<?
$urlOrder = Url::to(['production-plan/wh-list-by-part'], true);
$script1 = ob_start();?>
$(document).ready(function() {	
	var part_id = $('#productionplan-part_id').children("option:selected"). val();
	if(part_id>0){
		let url = '$urlOrder' + '?id=' + part_id;
	$.get(url, function(data, status){
	  // clear
	  $('#productionplan-warehouse_id').find('option').remove();
	  // append
	  $.each(data, function () {
	    $('#productionplan-warehouse_id').append(new Option(this.text, this.id));
	  });	  
	  var data = {
      "id": $("#productionplan-warehouse_id option:first").val(),
      "text": $("#productionplan-warehouse_id option:first").text()
    };    
    $('#productionplan-warehouse_id').trigger({
        type: 'select2:select',
        params: {
            data: data
        }
    });
	 });
	}
	
	$(document).on("select2:select", "#productionplan-part_id", function(e) {
	 let data = e.params.data;
	 let url = '$urlOrder'+'?id='+data.id;
	 $.get(url, function(data, status){
	  // clear
	  $('#productionplan-warehouse_id').find('option').remove();
	  // append
	  $.each(data, function () {
	    $('#productionplan-warehouse_id').append(new Option(this.text, this.id));
	  });	  
	  var data = {
      "id": $("#productionplan-warehouse_id option:first").val(),
      "text": $("#productionplan-warehouse_id option:first").text()
    };    
    $('#productionplan-warehouse_id').trigger({
        type: 'select2:select',
        params: {
            data: data
        }
    });
	 });
	});
	
});
<?php
$this->registerJs(ob_get_clean());
?>
