<?php

use kartik\datetime\DateTimePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\UnfamiliarOtchotSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Unfamiliar Otchots');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="unfamiliar-otchot-index">

    <div class="row d-flex align-items-center justify-content-between">
        <div class="col-md-10"></div>
        <div class="col-md-2">
            <?= Html::a(Yii::t('app', 'btn-create'), ['create'], [
                // 'class' => 'btn btn-success',
                'class' => 'btn btn-success btn-sm form-modal mr-lg-5',
                ]) ?>
                <?=Html::button(Yii::t('app', 'btn-delete-all'),
              [
                'class' => 'btn btn-info btn-sm modalButtonDelete mr-lg-5',
                'data-intro' => Yii::t('intro', 'delete-all-records'),
                'data-grid' => 'pjaxGrid',
                'data-status' => 1,
                'data-href' => Url::toRoute(['delete-all'])
              ]
            )?>
        </div>
        
    </div>

    <p>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            
            [
                'class' => 'yii\grid\SerialColumn',
                'header' => '№',
                'headerOptions' => ['style' => 'width: auto;text-align: center;color: #3c8dbc;'],
                'contentOptions' => ['style' => 'width: auto;text-align: center;']
              ],
              [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'header' => '<i class="fa fa-fw fa-gears"></i>',
                'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
                'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
                'buttons' => [
                  'update' => function($url, $model) {
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
                  'delete' => function($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                      false,
                      [
                        'class' => 'modalButtonDelete',
                        'data-href' => $url,
                        'data-grid' => 'pjaxGrid',
                        'title' => Yii::t('app', 'Delete')
                      ]);
                  },
                ],
              ],


              [
                'attribute' => 'part_id',
                'value' => function($model) {
                    if ($part = $model->part) {
                        return $part->part_no . ' - ' . $part->part_name . ' (' . $part->part_color . ')';
                    }
                    return null; // Agar $model->part mavjud bo'lmasa, null qaytariladi
                },
                'format' => 'raw',
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'part_id',
                    'data' => $list, // Bu yerda `$list` ni Select2 uchun foydalanamiz
                    'options' => [
                        'placeholder' => 'Select part...',
                        'class' => 'select2',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]),
            ],
            'quantity',
            'location',
            'status',
            [
                'attribute' => 'expected_arrival_date',
                'value' => function($model){
                    // format dd..mm.YYYY
                    return Yii::$app->formatter->asDate($model->expected_arrival_date,'d.M.Y');
                },
                'filter' => DateTimePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'expected_arrival_date',
                    'pluginOptions' => [
                        'autoclose' => true,
                        'todayHighlight' => true,
                        'format' => 'yyyy-mm-dd', // Sana formati
                        'minView' => 2, // Kun darajasiga qadar tanlash
                        'startView' => 2, // Kalendar oynasini kun darajasida ochish
                    ],
                ]),
            ],
            'remark',
            [
                'attribute' => 'user_id',
                'value' => function($model){
                    if($model->user){
                        return $model->user->fullname;
                    }
                }
            ],
            'created_at',
            'updated_at',

            
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
