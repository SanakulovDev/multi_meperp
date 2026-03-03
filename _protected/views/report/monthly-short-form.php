<?php 

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="monthly-short-form">
  <?php $form = Activeform::begin();?>
    <div class="row">
      <div class="col-md-4">
        <?= $form->field($model, 'arrived_qty')->textInput(['type'=>'number'])?>
      </div>
      
      <div class="col-md-4">
        <?= $form->field($model, 'arrived_at')->textInput(['type'=>'date'])?>
      </div>

    </div>

  <?php ActiveForm::end();?>
</div>