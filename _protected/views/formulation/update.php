<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Formulation */

$this->title = Yii::t('app', 'Update Formulation: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Formulations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="formulation-update">

    <h1><?= Html::encode("")// Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
            'errorlist' => $errorlist ?? null,
            'model' => $model, 'list'=>$list,
            'titleList' => $titleList,
            'specificList' => $specificList
    ]) ?>

</div>
