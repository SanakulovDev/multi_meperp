<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FormulationSpecificationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Formulation Specifications');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="formulation-specification-index">

    <p class="pull-right">
        <?= Html::a(Yii::t('app', 'btn-create'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'formulation_id',
            'item',
            'min',
            'max',
            //'result',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
