<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ConveyorType */

$this->title = Yii::t('app', 'Create Conveyor-type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Conveyor Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="conveyor-type-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
