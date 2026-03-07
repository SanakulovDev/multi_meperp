<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\StockInfoWrapperSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Stock Info');
$this->params['breadcrumbs'][] = $this->title;
$shifts = [
  1 => 'Cмена - 1',
  2 => 'Cмена - 2'
];
?>
<div class="stock-info-wrapper-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'btn-plus').Yii::t('app', 'Add'), ['create'], ['class' => 'btn btn-success', 'target'=>'_blank']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
              'class' => 'yii\grid\SerialColumn',
              'header' => '#',
              'headerOptions' => ['style' => 'width: 20px;text-align: center;color: #3c8dbc;'],
              'contentOptions' => ['style' => 'width: 20px;text-align: center;']
          ],
            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{view}{delete}',
              'header' => '<i class="fa fa-fw fa-gears"></i>',
              'headerOptions' => ['style' => 'max-width:60px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
              'contentOptions' => ['style' => 'max-width:60px;text-align:center;vertical-align:middle;'],
              
            ],
            'code',
            [
              'attribute' =>'part_id',
              'headerOptions' => ['style' => 'max-width: 200px;text-align: left;vertical-align:middle;'],
              'filter' => Html::activeDropDownList($searchModel, 'part_id', $notRawParts, ['class' => 'form-control select2', 'prompt' => '...']),
              'contentOptions' => ['class' => 'td-nowrap'],	
              'content' => function ($model) {
                  return $model->part->partinfo;
              },
            ],
            [
              'attribute' => 'qty',
              'label' => Yii::t('app', 'Quantity') . ' (kg)',
            ],
            [
              'label' => Yii::t('app', 'Quantity') . ' (m)',
              'headerOptions' => ['style' => 'text-align: right;vertical-align:middle;'],
              'contentOptions' => ['style' => 'text-align: right;vertical-align:middle;'],
              'content' => function ($model) {
                  $meter = $model->part->kgToMeter($model->qty);
                  return $meter !== null ? number_format($meter, 4, '.', ' ') : '-';
              },
            ],
            [
              'attribute' =>'warehouse_id',
              'filter' => $warehouses,
              'headerOptions' => ['style' => 'max-width: 200px;text-align: left;vertical-align:middle;'],
              'value' => function($model){
                return $model->warehouse?$model->warehouse->name:'---';
              }
            ],
            [
              'attribute' => 'shift',
              'filter' => $shifts,
              'value' => function($model) use ($shifts){
                return $shifts[$model->shift];
              }
            ],
            'date',
            [
              'filter' => $searchModel->statusList(),
              'attribute' => 'status',
              'headerOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;'],
              'contentOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;'],
              'content' => function ($model, $column) {
                $sts_value = $model->status;
                switch ($sts_value) {
                case 1:
                  $class = 'success';
                  $sts_name = '✔';
                  $sts_title = Yii::t('app', 'Active');
                  break;
                case 0:
                  $class = 'danger';
                  $sts_name = '✖';
                  $sts_title = Yii::t('app', 'Inactive');
                  break;
                }
                $html = Html::tag('span', Html::encode($sts_name), ['title' => $sts_title, 'class' => 'label label-' . $class]);
                return $sts_value === null ? $column->grid->emptyCell : $html;
              },
            ],
            //'updated_at',

            
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
