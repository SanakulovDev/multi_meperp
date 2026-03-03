<?php

use app\models\CountryCode;
use app\models\CustomerType;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$customerTypes = ArrayHelper::map(CustomerType::find()->all(), 'id', 'name');
/* @var $this yii\web\View */
/* @var $model app\models\Customer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-form">

  <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-lg-6 col-sm-6">
          <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3 col-sm-3">
          <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3 col-sm-3">
          <?= $form->field($model, 'status')->dropDownList($model->statusList) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4 col-sm-4">
          <?= $form->field($model, 'tin')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-4 col-sm-4">
          <?= $form->field($model, 'vat')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-4 col-sm-4">
          <?= $form->field($model, 'duns')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4 col-sm-4">
          <?= $form->field($model, 'customer_type_id')->dropDownList($customerTypes) ?>
        </div>
        <div class="col-lg-4 col-sm-4">
          <?= $form->field($model, 'country_code_id')
            ->dropDownList(ArrayHelper::map(CountryCode::find()->all(), 'id', 'name'), ['class' => 'form-control select2']) ?>
        </div>
        <div class="col-lg-4 col-sm-4">
          <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 col-sm-8">
          <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-4 col-sm-4">
          <?= $form->field($model, 'postal')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 col-sm-6"><?= $form->field($model, 'contact_name')
            ->textInput(['maxlength' => true]) ?></div>
        <div class="col-lg-6 col-sm-6"><?= $form->field($model, 'contact_position')
            ->textInput(['maxlength' => true]) ?></div>
    </div>
    <div class="row">
        <div class="col-lg-4 col-sm-4"><?= $form->field($model, 'contact_email')
            ->textInput(['maxlength' => true]) ?></div>
        <div class="col-lg-4 col-sm-4"><?= $form->field($model, 'contact_phone')
            ->textInput(['maxlength' => true]) ?></div>
        <div class="col-lg-4 col-sm-4"><?= $form->field($model, 'contact_cellular')
            ->textInput(['maxlength' => true]) ?></div>
    </div>


    <div class="form-group">
      <?= Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm']) ?>
      <?= Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm']) ?>
    </div>

  <?php ActiveForm::end(); ?>

  <?php if ($model->isNewRecord == false): ?>
      <div class="">
          <table class="table table-bordered table-condensed">
              <tr>
                  <th><?= Yii::t('app', 'Created by') ?></th>
                  <th><?= Yii::t('app', 'Created at') ?></th>
                  <th><?= Yii::t('app', 'Updated by') ?></th>
                  <th><?= Yii::t('app', 'Updated at') ?></th>
              </tr>
              <tr>
                  <td><?= $model->createdBy->fullname ?></td>
                  <td><?= $model->createdAtFormatted ?></td>
                  <td><?= $model->updatedBy->fullname ?></td>
                  <td><?= $model->updatedAtFormatted ?></td>
              </tr>
          </table>
      </div>
  <?php endif; ?>

</div>
