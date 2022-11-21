<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \app\models\Posts */

$this->title = Yii::t('app','Update Posts').': ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Posts', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="photos-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
