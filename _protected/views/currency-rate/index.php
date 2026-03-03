<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use app\models\Currency;
use app\models\CurrencyRate;
use app\components\Helpers;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CurrencyRateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Currency rate');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('currency-rate-update');
$canDelete = Yii::$app->user->can('currency-rate-delete');

$currentcyUSD = Currency::findOneCurrencyCode('USD');
$currentcyEUR = Currency::findOneCurrencyCode('EUR');
$currentcyRUB = Currency::findOneCurrencyCode('RUB');
?>
<div class="currency-rate-index">

  <p class="pull-left">
    <div class="col-lg-3 col-xs-4">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3><?= $currentcyUSD ? Helpers::formatRemoveDecimal(CurrencyRate::currentRate($currentcyUSD->id)) : '' ?></h3>
          <h4>1 USD</h4>
        </div>
        <div class="icon">
          <i class="fa fa-dollar"></i>
        </div>

      </div>
    </div>
    <div class="col-lg-3 col-xs-4">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3><?= $currentcyEUR ? Helpers::formatRemoveDecimal(CurrencyRate::currentRate($currentcyEUR->id)) : '' ?></h3>

          <h4>1 EUR</h4>
        </div>
        <div class="icon">
          <i class="fa fa-euro"></i>
        </div>

      </div>
    </div>
    <div class="col-lg-3 col-xs-4">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3><?= $currentcyRUB ? Helpers::formatRemoveDecimal(CurrencyRate::currentRate($currentcyRUB->id)) : '' ?></h3>

          <h4>1 RUB</h4>
        </div>
        <div class="icon">
          <i class="fa fa-ruble"></i>
        </div>

      </div>
    </div>
  </p>
  <p class="pull-right">
    <? if (Yii::$app->user->can('currency-rate-create')) { ?>
      <?=
        Html::a(Yii::t('app', 'btn-create'), ['create'], [
          'class' => 'btn btn-success btn-sm',
          'data-intro' => Yii::t('intro', 'add-new-record')
        ])
      ?>
    <? } ?>
  </p>

  <?php Pjax::begin(); ?>

  <?=
    GridView::widget([
      'dataProvider' => $dataProvider,
      'filterModel' => $searchModel,
      'emptyText' => Yii::t('app', 'No results found.'),
      'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
      'options' => ['style' => 'overflow-x:scroll;clear:both'],
      'tableOptions' => [
        'style' => 'table-layout: fixed;',
        'class' => 'sm_filter table table-striped table-bordered table-condensed table-sm-padding_2_0',
        'data-intro' => Yii::t('intro', 'data-table')
      ],
      'filterRowOptions' => ['data-intro' => Yii::t('intro', 'filter')],
      'pager' => [
        'class' => '\yii\widgets\LinkPager',
        'options' => [
          'class' => 'pagination',
          'data-intro' => Yii::t('intro', 'pagination')
        ],
      ],
      'columns' => [
        [
          'class' => 'yii\grid\SerialColumn',
          'headerOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;'],
          'contentOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;'],
        ],
        [
          'class' => 'yii\grid\ActionColumn',
          'template' => '{update} {delete} ',
          'header' => '<i class="fa fa-fw fa-gears"></i>',
          'headerOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
          'contentOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;'],
          'buttons' => [
            'update' => function ($url, $model) use ($canUpdate) {
              if (!$canUpdate) return false;
              $url = Url::toRoute(['currency-rate/update', 'id' => $model->id]);
              return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url, [
                'title' => Yii::t('app', 'Edit')
              ]) . '&nbsp;';
            },
            'delete' => function ($url, $model) use ($canDelete) {
              if (!$canDelete) return false;
              $url = Url::toRoute(['currency-rate/delete', 'id' => $model->id]);
              return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>', $url, [
                'title' => Yii::t('app', 'Delete'),
                'data' => [
                  'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                  'method' => 'post',
                ],
              ]) . '&nbsp;';
            },
          ],
          'visible' => $canUpdate || $canDelete
        ],
        'rate_date',
        [
          'attribute' => 'currency_id',
          'headerOptions' => ['style' => 'width:100px;text-align: center;vertical-align:middle;'],
          'contentOptions' => ['style' => 'width:100px;text-align: center;vertical-align:middle;'],
          'content' => function ($model) {
            return $model->currency->code;
          },
          'filter' => ArrayHelper::map(Currency::find()
            ->all(), 'id', 'code')
        ],
        [
          'attribute' => 'uzs_value',
          'headerOptions' => ['style' => 'width:120px;text-align: right;vertical-align:middle;'],
          'contentOptions' => ['style' => 'width:120px;text-align: right;vertical-align:middle;'],
          'content' => function ($model) {
            return \app\components\Helpers::formatRemoveDecimal($model->uzs_value);
          }
        ],
        [
          'attribute' => 'created_at',
          'headerOptions' => ['style' => 'width:200px;text-align: center;vertical-align:middle;'],
          'value' => function ($data) {
            return date('d.m.Y (H:i:s)', $data->created_at);
          },
          'contentOptions' => function ($model, $key, $index, $column) {
            return [
              'style' => 'width:auto;text-align:center;vertical-align:middle;',
              'title' => (!empty($model->createdBy)) ? $model->createdBy->username : '-'
            ];
          }
        ],
        [
          'attribute' => 'updated_at',
          'headerOptions' => ['style' => 'width:200px;text-align: center;vertical-align:middle;'],
          'value' => function ($data) {
            return (!empty($data->updated_at)) ? date('d.m.Y (H:i:s)', $data->updated_at) : '-';
          },
          'contentOptions' => function ($model, $key, $index, $column) {
            return [
              'style' => 'width:auto;text-align:center;vertical-align:middle;',
              'title' => (!empty($model->updatedBy)) ? $model->updatedBy->username : '-'
            ];
          }
        ],
        [
          'attribute' => 'type',
          'headerOptions' => ['style' => 'width:120px;text-align: center;vertical-align:middle;color:#3c8dbc;'],
          'contentOptions' => ['style' => 'width:120px;text-align: center;vertical-align:middle;'],
          'content' => function ($model) {
            if ($model->type == 'Auto') {
              $color = 'info';
            } else {
              $color = 'success';
            }
            return '<b class="text-' . $color . '">' . $model->type . '</b>';
          }
        ],
      ],
    ]);
  ?>

  <?php Pjax::end(); ?>

</div>