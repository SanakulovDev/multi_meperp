<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\ProductionOrder;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductionOrder */
/* @var $form yii\widgets\ActiveForm */

/** @var TYPE_NAME $models */
/** @var TYPE_NAME $flocs */
/** @var TYPE_NAME $parts_withptnm */
/** @var TYPE_NAME $prev_shift */
$lines = ProductionOrder::getLines();
$shifts = [
  1 => 'Cмена - 1',
  2 => 'Cмена - 2'
];
?>

<div class="production-order-form" xmlns="">

  <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <blockquote style="display: none" id="blockquote">
                <p id="partname"></p>
            </blockquote>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-2 col-md-2">
            <div class="form-group">
              <?=$form->field($model, 'warehouse_id')->dropDownList($flocs, ['class' => 'form-control select2', 'id'=>'select_floc', 'options' => $options, 'prompt' => Yii::t('app', '...'), 'data-url' => Url::toRoute(['part/get-parts-by-model-and-side'])])?>
            </div>
        </div>
        
        <div class="col-lg-2 col-md-2">
            <div class="form-group">
              <?=$form->field($model, 'shift')->dropDownList($shifts, ['class' => 'form-control', 'options' => $options, 'prompt' => Yii::t('app', '...')])?>
            </div>
        </div>

        <div class="col-lg-2 col-md-5">
          <?=$form->field($model, 'part_id')->dropDownList($parts_withptnm, ['class' => 'form-control select2', 'options' => $options, 'prompt' => Yii::t('app', '...'), 'data-url' => Url::toRoute(['part/get-partname'])])?>
        </div>
    <!-- </div>

    <div class="row"> -->
        <div class="col-lg-2 col-md-2">
          <? //=$form->field($model, 'quantity')->textInput(['maxlength' => 6, 'type' => 'number', 'min' => 1, 'max' => 999999])?>
          <?=$form->field($model, 'qty')->textInput(['id' => 'qty-input'])?>
        </div>
        <div class="col-lg-2 col-md-2">
          <?=$form->field($model, 'qty_meter')->textInput(['id' => 'qty-meter-input', 'readonly' => true])?>
        </div>
        <div class="col-lg-2 col-md-2">
            <!-- line column drodownList activeform -->
          <?=$form->field($model, 'line')->dropDownList($lines, ['class' => 'form-control select2','prompt' => Yii::t('app', '...')])?>
        </div>
      
        <div class="col-lg-2 col-md-2">
            <div class="form-group" style="margin-top: 25px;">
                <label class="control-label"></label>
              <?=Html::submitButton(Yii::t('app', 'btn-save-and-print'), ['class' => 'btn btn-success btn-sm'])?>
            </div>
        </div>
    </div>


  <?php ActiveForm::end(); ?>

</div>


<?php ob_start();?>
$(document).ready(function() {
    $('#select_floc').on('change', function(){
      let key = $(this).val();
      if(key == 11){
        $('.select_stock_info_wrapper').removeClass('hidden');
      }
      else{
        $('.select_stock_info_wrapper').addClass('hidden');
        $('.select_stock_info_wrapper').val('');
      }
    })
    $('.grp_kecha').click(function() {
        $(this).find('.kecha').toggleClass('active');        
        if ($(this).find('.btn-primary').length>0) {
            $(this).find('.kecha').toggleClass('btn-primary');
        }
        $(this).find('.kecha').toggleClass('btn-default');
           
    });
    
    var partCoefficients = {};
    function calcMeter() {
      var partId = $('#stockinfowrapper-part_id').val();
      var qty = parseFloat($('#qty-input').val()) || 0;
      var coeff = partCoefficients[partId];
      if (coeff && coeff > 0 && qty > 0) {
        var meter = qty / (coeff / 1000);
        $('#qty-meter-input').val(meter.toFixed(4));
      } else {
        $('#qty-meter-input').val('');
      }
    }

    $('#qty-input').on('input change', function() {
      calcMeter();
    });

    $('#stockinfowrapper-part_id').on('select2:select', function (e) {
    $('#stockinfowrapper-qty').val($(e.params.data.element).data('pack-size'));
    $('#qty-input').val($(e.params.data.element).data('pack-size'));
    calcMeter();
    });


    $('#select_floc').change(function (e){
        var floc     = $(this).val()
        var model_id = 1; 
        var url      = $(this).attr('data-url')
        console.log(url)
        $.ajax({
                dataType: 'json',
                type: 'GET',
                url: url + '?floc=' + floc + '&model_id=' + model_id,
                success: function (data){
                  partCoefficients = {};
                  $('#stockinfowrapper-part_id').each(function (i, obj){
                    var el = $(this)
                    el.html('')
                    el.append($('<option>', {
                      value: '',
                      text: 'Выберите...',
                    }))
                    $.each(data, function (k, part){
                      el.append($('<option>', {
                        value: part.id,
                        text: part.info,
                      }))
                      if (part.coefficient) {
                        partCoefficients[part.id] = parseFloat(part.coefficient);
                      }
                    })
                  })
                },
              })
      })
  });
<?php $this->registerJs(ob_get_clean());
?>
