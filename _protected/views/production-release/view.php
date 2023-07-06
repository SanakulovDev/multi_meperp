<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\ProductionRelease */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Production Releases'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="production-release-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'part_id',
                'value' => function($model) {
                    return $model->part->part_no.' '.$model->part->part_name;
                }
            ],
            [
                'attribute' => 'line',
                'format' => 'html',
                'value' => function($model) use($lines) {
                    return $lines[$model->line];
                }
            ],
            'pr_order_number',
            'target_date',
           [
                'attribute' => 'shift',
                'format' => 'html',
                'value' => function($model) use($shifts) {
                    return $shifts[$model->shift];
                }
            ],
            'time',
            'quantity',
        ],
    ]) ?>

</div>
