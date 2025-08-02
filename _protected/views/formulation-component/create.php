<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\FormulationComponent */

$this->title = Yii::t('app', 'Create Formulation Component');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Formulation Components'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="formulation-component-create">


<div class="col-md-12 well bs-component">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>

</div>
