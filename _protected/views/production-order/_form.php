<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductionOrder */
/* @var $form yii\widgets\ActiveForm */

/** @var TYPE_NAME $models */
/** @var TYPE_NAME $flocs */
/** @var TYPE_NAME $parts_withptnm */
/** @var TYPE_NAME $prev_shift */

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
                <label><?=Yii::t('app', 'Floc')?></label>
              <?=Html::dropDownList('floc', null, $flocs, ['id' => 'select_floc', 'class' => 'form-control select2', 'prompt' => '*', 'data-url' => Url::toRoute('part/get-parts-by-model-and-side')])?>
            </div>
        </div>
        <div class="col-lg-2 col-md-2">
            <div class="form-group">
                <label><?=Yii::t('app', 'Product model')?></label>
              <?=Html::dropDownList('model', null, $models, ['id' => 'select_model', 'class' => 'form-control select2', 'prompt' => '*', 'data-url' => Url::toRoute('part/get-parts-by-model-and-side')])?>
            </div>
        </div>
        <div class="col-lg-1 col-md-1">
            <div class="form-group">
                <label><?=Yii::t('app', 'Side')?></label>
              <?=Html::dropDownList('side', null, ['LH' => 'LH', 'RH' => 'RH', 'FR' => 'FR', 'RR' => 'RR'], ['id' => 'select_side', 'class' => 'form-control select2', 'prompt' => '*', 'data-url' => Url::toRoute('part/get-parts-by-model-and-side')])?>
            </div>
        </div>
        <div class="col-lg-5 col-md-5">
          <?=$form->field($model, 'part_id')->dropDownList($parts_withptnm, ['class' => 'form-control select2', 'options' => $options, 'prompt' => Yii::t('app', '...'), 'data-url' => Url::toRoute(['part/get-partname'])])?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-2 col-md-2">
          <? //=$form->field($model, 'quantity')->textInput(['maxlength' => 6, 'type' => 'number', 'min' => 1, 'max' => 999999])?>
          <?=$form->field($model, 'quantity')->textInput()?>
        </div>
        <div class="col-lg-2 col-md-2">
          <?=$form->field($model, 'quantity_of_copy')->textInput(['maxlength' => 2, 'type' => 'number', 'min' => 1, 'max' => 99])?>
        </div>
      <? if($prev_shift == 1){ ?>
          <div class="col-lg-2 col-md-2">
              <div style="font-weight: bold"> <?=Yii::t('app', 'Shift')?> </div>
              <div class="grp_kecha btn-group btn-toggle" data-toggle="buttons">
                  <label class="kecha btn btn-primary active">
                      <input name="shift" value="0" type="radio"> <?=Yii::t('app', 'Current')?>
                  </label>
                  <label class="kecha btn btn-default">
                      <input name="shift" value="1" checked="" type="radio"> <?=Yii::t('app', 'Preview')?>
                  </label>
              </div>
          </div>
      <? } ?>
      <?php
      $is_produced = true;
      echo Html::hiddenInput('produced', $is_produced);
      ?>
        <div class="col-lg-4 col-md-4">
            <div class="form-group" style="margin-top: 25px;">
                <label class="control-label"></label>
              <?=Html::submitButton(Yii::t('app', 'btn-save-and-print'), ['class' => 'btn btn-success btn-sm'])?>
              <?=Html::a(Yii::t('app', 'btn-all-po'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
            </div>
        </div>
    </div>


  <?php ActiveForm::end(); ?>

</div>


<?php
$icheck_script = <<< JS
$(document).ready(function() {
    
    $('.grp_kecha').click(function() {
        $(this).find('.kecha').toggleClass('active');        
        if ($(this).find('.btn-primary').length>0) {
            $(this).find('.kecha').toggleClass('btn-primary');
        }
        $(this).find('.kecha').toggleClass('btn-default');
           
    });
    
    $('#productionorder-part_id').on('select2:select', function (e) {
    $('#productionorder-quantity').val($(e.params.data.element).data('pack-size'));    
    var elemant = $(this);        
        var part_id = $(this).val();
        var url     = $(this).attr('data-url')        
        /*
        if(part_id != '')
        {
            $.ajax({
                dataType: 'json',
                type: 'GET',
                url: url + '?id=' + part_id,
                success: function (jsondata) {
                  $('#blockquote').show()
                  $('#partname').html(jsondata.partname)
                },
              })
        }
    */
      
    });
  });
JS;
$this->registerJs($icheck_script);
?>
