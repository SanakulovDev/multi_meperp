<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\LocationType */

$this->title = Yii::t('app', 'Create-type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Location Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-type-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
