<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\FormulationSpecification */

$this->title = Yii::t('app', 'Create Formulation Specification');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Formulation Specifications'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="formulation-specification-create">

    <div class="col-md-12 well bs-component">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
