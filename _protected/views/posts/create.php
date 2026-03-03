<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \app\models\Posts */

$this->title = Yii::t('app','Create Posts');
$this->params['breadcrumbs'][] = ['label' => 'Photos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="photos-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
