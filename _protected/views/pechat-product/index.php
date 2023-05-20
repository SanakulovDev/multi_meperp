<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PechatProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'BIRKA');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pechat-product-index">


    <p class="text-right">
        <?= Html::a(Yii::t('app', 'Добавиты'), ['create'], ['class' => 'btn btn-success']) ?>
        <!-- restore Search buttons -->
        <?= Html::a(Yii::t('app', 'Reset Search'), ['index'], ['class' => 'btn btn-default']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'part_id',
                'value'=>function($data){
                    $name = '';
                    $name .= $data->part->part_name.':';
                    $name .= $data->part->part_no;
                    return $name; 
                },
                'filter' => Html::activeDropDownList($searchModel, 'part_id', $items, ['class'=>'form-control select2','prompt' => '----']),

            ],
            [
                'attribute'=>'color_id',
                'value'=>function($data){
                    return $data->partColor?$data->partColor->name:'';    
                },
                'filter' => Html::activeDropDownList($searchModel, 'color_id', $colorList, ['class'=>'form-control select2','prompt' => '----']),
            ],
            'number_lot',
            'line',
            'date',
            'weight_netto',
            'weight_brutto',
            'comment',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {print}',
                'buttons' => [
                    'print' => function ($url, $model) {
                        $url = '/pechat-product/print-form?id='.$model->id;
                        return Html::a('<span class="glyphicon glyphicon-print"></span>', $url, [
                                    'title' => Yii::t('app', 'print'),
                                    'class'=>'btn btn-success form-modal',
                                    'target'=>'_blank',
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>


</div>
