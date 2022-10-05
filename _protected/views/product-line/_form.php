<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductLine */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-line-form">

  <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4 col-sm-4 col-lg-4">
          <?=$form->field($model, 'linename')->textInput(['maxlength' => true, 'autofocus' => true])?>
        </div>
        <div class="col-md-5 col-sm-5 col-lg-5">
          <?=$form->field($model, 'description')->textInput()?>
        </div>
        <div class="col-md-3 col-sm-3 col-lg-3">
          <?=$form->field($model, 'is_zone')->dropDownList($model->statusList())?>
        </div>
    </div>





    <div class="form-group">
      <?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
      <?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
    </div>

  <?php ActiveForm::end(); ?>

</div>
