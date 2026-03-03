<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CurrencyRate */

$this->title = Yii::t('app', 'Update');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Currency rate'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="currency-rate-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
