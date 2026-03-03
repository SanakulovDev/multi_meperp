<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ProductionPower */

$this->title = Yii::t('app', 'Create Production Power');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Production Powers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="production-power-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
