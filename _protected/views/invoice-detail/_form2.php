<?php
use app\models\Contract;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\InvoiceDetail */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="row">
  <div class="col-lg-6">
    <? $contracts = Contract::find()->select(['id', 'contract_no', 'contract_date'])
                            ->where('status>1 and supplier_id='.$model->contInv->invoice->supplier->id)
                            ->orderBy(['contract_no' => SORT_DESC, 'contract_date' => SORT_DESC])
                            ->all();
    $items = ArrayHelper::map($contracts, 'id', 'contract_no');
    $params = ['prompt' => '. . .', null, 'class' => 'form-control select2'];
    echo $form->field($model, 'contract_id')->dropDownList($items, $params);
    ?>
  </div>
  <div class="col-lg-6">
    <?=$form->field($model, 'part_order_id')->dropdownlist([],['prompt' => '. . .', null, 'class' => 'form-control select2']);?>
  </div>
</div>
<div class="row">
  <div class="col-lg-6">
    <?=$form->field($model, 'part_id')->dropDownList([],['prompt' => '. . .', null, 'class' => 'form-control select2']);?>
  </div>
  <div class="col-lg-3">
    <?=$form->field($model, 'qty')->textInput(['type' => 'number', 'step' => '0.00001', 'maxlength' => true])?>
  </div>
  <div class="col-lg-3">
    <?=$form->field($model, 'price')->textInput(['type' => 'number', 'step' => '0.00001', 'maxlength' => true])?>
  </div>
</div>

<div class="row ">
  <div class="col-lg-12">
    <?=$form->field($model, 'remarks')->textInput(['maxlength' => true])?>
  </div>
</div>

</div>
