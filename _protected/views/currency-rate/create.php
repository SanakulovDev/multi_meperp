<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CurrencyRate */

$this->title = Yii::t('app', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Currency rate'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="currency-rate-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
