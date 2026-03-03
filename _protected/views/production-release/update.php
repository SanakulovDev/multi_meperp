<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ProductionRelease */

$this->title = Yii::t('app', 'Update Production Release: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Production Releases'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="production-release-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
