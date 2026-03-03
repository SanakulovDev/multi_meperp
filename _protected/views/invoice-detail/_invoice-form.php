<?php
use app\enums\CargoType;
use app\enums\ContainerType;
use app\models\ContainerInvoice;
use app\models\Currency;
use app\models\DeliveryTerm;
use app\models\ShipMode;
use app\models\Supplier;
use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ContainerInvoice */
/* @var $form yii\widgets\ActiveForm */
/* @var TYPE_NAME $errorlist */
/* @var TYPE_NAME $modelContainer */
/* @var TYPE_NAME $modelInvoice */
/* @var TYPE_NAME $modelItems */
?>

<div class="invoice-conttainer-form">
  <?php $form = ActiveForm::begin(); ?>
	<div class="row">
	    <div class="col-md-2">
          <?
          $data = Supplier::find()->orderBy(['name' => SORT_ASC])->all();
          $items = ArrayHelper::map($data, 'id', 'name');
          $params = ['prompt' => '. . .', null, 'class' => 'form-control select2', "value" => $model->supplier, 'disabled' => true];
          echo $form->field($model, 'supplier')->dropDownList($items, $params)
                    ->label(Yii::t('app', 'Supplier'));
          ?>
	    </div>
	    <div class="col-md-2">
          <?=$form->field($model, 'ship_mode_id')
                  ->label(Yii::t('app', 'Ship mode'))
                  ->dropDownList(ArrayHelper::map(ShipMode::find()->all(), 'id', 'name'), [
                    "value" => $model->ship_mode_id,
                    'disabled' => true
                  ])
          ?>
	    </div>
	    <div class="col-md-2">
	    	<div class="form-group">
	    		<label class="control-label"><?=Yii::t('app', 'Container type')?></label>
              <?=Html::dropDownList("Container[container_type]", "$modelContainer->container_type", ContainerType::list(), [
                'class' => 'form-control cont-type-val cont-type-required',
                'value' => $model->container_type,
                'disabled' => true
                ])?>
	    	</div>
	    </div>
	    <div class="col-md-2">
            <?
            $data = DeliveryTerm::find()->orderBy(['name' => SORT_ASC])->all();
            $items = ArrayHelper::map($data, 'id', 'name');
            $params = ['prompt' => '. . .', null, 'class' => 'form-control select2', "value" => $model->delivery_term_id, 'disabled' => true];
            echo $form->field($model, 'delivery_term_id')->dropDownList($items, $params);
            ?>
	    </div>
	    <div class="col-md-2">
          <?
          $data = Currency::find()->orderBy(['code' => SORT_ASC])->all();
          $items = ArrayHelper::map($data, 'id', 'code');
          $params = ['prompt' => '. . .', null, 'class' => 'form-control select2', "value" => 1];
          echo $form->field($model, 'currency')->dropDownList($items, $params)
                    ->label(Yii::t('app', 'Currency'));
          ?>
	    </div>
	</div>

	<div class="form-group pull-right">
        <!-- <?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?> -->
        <!-- <?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?> -->
	</div>

  <?php ActiveForm::end(); ?>

</div>

<?php
$icheck_script = <<< JS
$(document).ready(function() {  
  changeContTypeVal();
  $("#containerinvoice-ship_mode_id").change(function() {
      changeContTypeVal();
  });
  $("#btnAddContDetail").click(function() {
      changeContTypeVal();
  });  
  function changeContTypeVal(){
    let shipModeVal = $("#containerinvoice-ship_mode_id").val();
    if (shipModeVal == 2) {
      $(".cont-type-val").attr("disabled",false);      
      $(".cont-type-required").attr("required",true);
      $(".cont-type-required").parent().addClass("required");
    }else {
      $(".cont-type-val").attr("disabled",true);      
      $(".cont-type-required").attr("required",false);
      $(".cont-type-required").parent().removeClass("required");
      $(".cont-type-val").val(0); 
    }
  }
});
JS;
$this->registerJs($icheck_script);
?>
