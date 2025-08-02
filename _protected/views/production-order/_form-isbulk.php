<?php

use app\models\ProductionOrder;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductionOrder */
/* @var $form yii\widgets\ActiveForm */
?>

<style>
    .dsp_none {
        display: none;
    }
</style>
<div class="production-order-form" xmlns="">

  <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-3 col-md-4">
          <?= $form->field($model, 'part_id')->dropDownList($parts, ['class' => 'form-control select2', 'options' => $options, 'prompt' => Yii::t('app', '...'), 'data-url' => Url::toRoute(['part/get-partname'])]); ?>
        </div>
        <div class="col-lg-2 col-md-4">
          <?= $form->field($model, 'quantity_of_copy')->textInput(['maxlength' => 2, 'type' => 'number', 'min' => 1, 'max' => 99]) ?>
        </div>
        <div class="col-lg-3 col-md-4">
          <?= $form->field($model, 'is_label')->radioList(ProductionOrder::stateList(), ['class' => 'form-control', 'value' => '1']); ?>
        </div>
        <div class="col-lg-2 col-md-6" id="div_qty" style="display: none">
          <?= $form->field($model, 'quantity')->textInput(['id' => 'pt_qty', 'type' => 'number', 'step'=>'any']) ?>
        </div>
      <?
      $is_produced = true;
      echo Html::hiddenInput('produced', $is_produced);
      ?>
        <div class="col-lg-3 col-md-6">
            <div class="form-group" style="margin-top: 25px;">
                <label class="control-label"></label>
              <?= Html::submitButton(Yii::t('app', 'btn-save-and-print'), ['class' => 'btn btn-success btn-sm']) ?>
              <?= Html::a(Yii::t('app', 'btn-all-po'), ['index'], ['class' => 'btn btn-default btn-sm']) ?>
            </div>
        </div>
    </div>
  <?php ActiveForm::end(); ?>

</div>


<?php
$icheck_script = <<< JS
$(document).ready(function() {  
    $("#div_qty").css("display", "block");
    $('input[type=radio][name="ProductionOrder[is_label]"]').change(function() {
        if (this.value == 0) {
            $("#div_qty").css("display", "none");
        }
        else {
            $("#div_qty").css("display", "block");           
            $("#pt_qty").prop('type', 'text');
        }
    });
    $('#productionorder-part_id').on('select2:select', function (e) {
    $('#pt_qty').val($(e.params.data.element).data('pack-size'));    
    var elemant = $(this);        
        var part_id = $(this).val();
        var url     = $(this).attr('data-url')       
    });
    
  });
JS;
$this->registerJs($icheck_script);
?>
