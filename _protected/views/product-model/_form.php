<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductModel */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-model-form">

  <?php $form = ActiveForm::begin(); ?>

  <div class="row">
      <div class="col-xs-6">
        <?=$form->field($model, 'modelname')->textInput(['maxlength' => true, 'autofocus' => true])?>
      </div>
      <div class="col-xs-6">
        <?=$form->field($model, 'is_vehicle')->textInput(['maxlength' => true])?>
      </div>
  </div>

  <div class="row">
    <div class="col-xs-12">
      <?=$form->field($model, 'description')->textInput(['maxlength' => true])?>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12">
      <div class="form-group pull-right">
        <?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
        <?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
      </div>
    </div>
  </div>



  <?php ActiveForm::end(); ?>

</div>
