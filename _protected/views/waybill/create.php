<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Waybill */
/* @var $factory_items app\Controllers\WaybillController */

$this->title = Yii::t('app', 'Create Waybill(FG Invoice)');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Waybills'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="waybill-create">
    <?= $this->render('_form', compact('model', 'factory_items')) ?>
</div>
