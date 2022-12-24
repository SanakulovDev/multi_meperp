<?php
/**
 * @var $this     yii\web\View
 * @var $model    app\models\PartOrder
 * @var $errMsg   app\controllers\PartOrderController
 */
$this->title = Yii::t('app', 'Update part order: {name}', ['name' => $model->order_no,]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Orders Supplier'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
?>
<div class="part-order-update">
  <?=$this->render('_form', ['model' => $model, 'errMsg' => $errMsg, 'contract_model' => $contract_model, 'update' => true])?>
</div>
