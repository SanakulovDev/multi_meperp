<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\FormulationBase */

$this->title = Yii::t('app', 'Create Formulation Base');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Formulation Bases'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="formulation-base-create">
    <div class="col-md-12">
        <?= $this->render('_form', [
            'model' => $model,
            'titleList' => $titleList,
            'specificList' => $specificList
        ]) ?>
    </div>
</div>
