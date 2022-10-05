<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\LocationType */

$this->title = Yii::t('app', 'Update Location-type', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Location Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="location-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('create', [
        'model' => $model,
    ]) ?>

</div>
