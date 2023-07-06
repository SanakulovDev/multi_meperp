<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\url;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductionReleaseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Production Order Release');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="production-release-index">

    <h1 style="display: inline-block; margin:0; padding: 0;"><?= Html::encode($this->title) ?></h1>
    <div class="pull-right">

        <p style="margin:0; padding: 0;">
            <?= Html::a(Yii::t('app', 'btn-create'), ['create'], [
                'class' => 'btn btn-success btn-sm form-modal mr-lg-5',
                'data-intro' => Yii::t('intro', 'add-new-record')
              ]) ?>
        </p>
    </div>

    <?php Pjax::begin(['id' => 'pjaxGrid']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}{delete} ',
                'header' => '<i class="fa fa-fw fa-gears"></i>',
                'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
                'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
                'buttons' => [
                    'update' => function($url, $model)  {
                        
                        $url = Url::toRoute(['production-release/update', 'id' => $model->id]);
                        return Html::a(
                            '<span  class="glyphicon glyphicon-pencil"></span>',
                            false,
                            [
                                'class' => 'modalButtonUpdate',
                                'value' => $url,
                                'title' => Yii::t('app', 'Update')
                            ]
                        );
                    },
                ],
            ],

            [
                'attribute'=> 'part_id',
                'value' => function($model) {
                    return $model->part->part_no.' '.$model->part->part_name;
                },
                'filter' => $parts,
                'filterInputOptions' => [
                    'class' => 'form-control select2',
                    'prompt' => '---',
                    'data-intro' => Yii::t('intro', 'production-release-part_id')
                ],
            ],
            [
                'attribute'=> 'line',
                'value' => function($model) {
                    return $model->line;
                },
                'filter' =>$lines,
                'filterInputOptions' => [
                    'class' => 'form-control select2',
                    'prompt' => '---',
                    'data-intro' => Yii::t('intro', 'production-release-line')
                ],    
            ],
            'pr_order_number',
            'target_date',
            [
                    'attribute'=> 'shift',
                    'value' => function($model) {
                        return $model->shift;
                    },
                    'filter' => Html::activeDropDownList($searchModel, 'shift', $shifts, ['prompt'=>'']),
            ],
            'time',
            'quantity',

        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
