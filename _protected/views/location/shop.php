<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Location */

$this->title = Yii::t('app', 'Create Location');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Locations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-shop">
    
    <?= $this->render('_form', [
        'model' => $model,
        'location_type_id' => 2
    ]) ?>

</div>