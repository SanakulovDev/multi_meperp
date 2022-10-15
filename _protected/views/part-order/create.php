<?php
/**
 * @var $this     yii\web\View
 * @var $model    app\models\PartOrder
 * @var $errMsg   app\controllers\PartOrderController
 */
$this->title = Yii::t('app', 'Create part order');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Orders Supplier'), 'url' => ['index']];
?>
<div class="part-order-create">
  <?=$this->render('_form', [
    'model' => $model,
    'contract_model' => $contract_model,
    'errMsg' => $errMsg
  ])?>
</div>
