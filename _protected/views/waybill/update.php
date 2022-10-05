<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Waybill */

$this->title = Yii::t('app', 'Update');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Waybills'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="waybill-update">
    <?= $this->render('_form', compact('model', 'factory_items', 'items')) ?>
</div>
