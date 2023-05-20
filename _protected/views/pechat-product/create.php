<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PechatProduct */

$this->title = Yii::t('app', 'Create Pechat Product');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pechat Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pechat-product-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'items' => $items,
        'colorList' => $colorList
    ]) ?>

</div>
