<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UnfamiliarOtchot */

$this->title = Yii::t('app', 'Create Unfamiliar Otchot');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Unfamiliar Otchots'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="unfamiliar-otchot-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
