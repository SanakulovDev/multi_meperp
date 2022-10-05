<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\FormulationBase */

$this->title = Yii::t('app', 'Update Formulation Base: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Formulation Bases'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="formulation-base-update">    

    <?= $this->render('_form', [
        'model' => $model,
        'titleList' => $titleList,
        'specificList' => $specificList
    ]) ?>

</div>
