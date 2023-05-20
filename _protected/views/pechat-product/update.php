<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PechatProduct */

$this->title = Yii::t('app', 'Update Pechat Product: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pechat Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="pechat-product-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'items' => $items,
        'colorList' => $colorList
    ]) ?>

</div>
