<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\PechatProduct */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pechat Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="pechat-product-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Print'), ['pechat-product/print-form', 'id' => $model->id], ['class' => 'btn btn-success form-modal']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute'=>'part_id',
                'value'=>function($data){
                    $name = '';
                    $name .= $data->part->part_name;
                    $name .= $data->part->part_no;
                    return $name;    
                },
            ],
            [
                'attribute'=>'color_id',
                'value'=>function($data){
                    return $data->partColor?$data->partColor->name:'';    
                },
            ],
            
            'number_lot',
            [
                'attribute'=>'line',
                'value'=>function($data){
                    return $data->line;    
                },
            ],
            'date',
            'weight_netto',
            'weight_brutto',
            'comment',
        ],
    ]) ?>

</div>

