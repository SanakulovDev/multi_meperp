<?php
/* @var $this yii\web\View */
/* @var $model app\models\VehicleCoverageInput */
/** @var TYPE_NAME $productModel */
/** @var TYPE_NAME $curStock */
/** @var TYPE_NAME $paidNotShippedOrder */
/** @var TYPE_NAME $descriptionList */
/** @var TYPE_NAME $intransitETA */
$this->title = Yii::t('app', 'Refresh')." ".Yii::t('app', 'Vehicle set coverage input');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Vehicle set coverage input'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vehicle-coverage-input-create">
  <?=$this->render('_form', [
    'model' => $model,
    'descriptionList' => $descriptionList,
    'productModel' => $productModel,
    'curStock' => $curStock,
    'uamStock' => $uamStock,
    'paidNotShippedOrder' => $paidNotShippedOrder,
    'intransitETA' => $intransitETA,
  ])?>

</div>
