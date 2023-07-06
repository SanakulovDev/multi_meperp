<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ProductionRelease */

$this->title = Yii::t('app', 'Create Production Release');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Production Releases'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="production-release-create">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
