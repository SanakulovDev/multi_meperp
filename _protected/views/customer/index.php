<?php

use app\models\CountryCode;
use app\models\CustomerType;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Customer info');
$this->params['breadcrumbs'][] = $this->title;
$customerTypes = ArrayHelper::map(CustomerType::find()->all(), 'id', 'name');
$countries = ArrayHelper::map(CountryCode::find()->all(), 'id', 'name');
$canUpdate = Yii::$app->user->can('customer-update');
$canDelete = Yii::$app->user->can('customer-delete');
$canView = Yii::$app->user->can('customer-view');
?>
<div class="customer-index">


  <p class="pull-right">
    <? if (Yii::$app->user->can('customer-create')) { ?>
      <?= Html::a(
        Yii::t('app', 'btn-create'),
        ['create'],
        [
          'class' => 'btn btn-success btn-sm',
          'data-intro' => Yii::t('intro', 'add-new-record')
        ]
      ) ?>
    <? } ?>
    <? if (Yii::$app->user->can('customer-xls')) { ?>
      <?= Html::a(
        Yii::t('app', 'btn-download'),
        ['xls', 'ContactSearch' => ($_GET['ContactSearch'] ?? null)],
        [
          'class' => 'btn btn-info btn-sm',
          'data-intro' => Yii::t('intro', 'download-button')
        ]
      ) ?>
    <? } ?>
  </p>

  <?php Pjax::begin(['id' => 'pjaxGrid']); ?>
  <?= GridView::widget(
    [
      'dataProvider' => $dataProvider,
      'filterModel' => $searchModel,
      'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
      'options' => ['style' => 'overflow:auto;clear:both'],
      'emptyText' => Yii::t('app', 'No results found.'),
      'tableOptions' => [
        'class' => 'sm_filter table table-striped table-bordered table-condensed table-sm-padding_2_0',
        'data-step' => 4,
        'data-intro' => Yii::t('intro', 'data-table')
      ],
      'filterRowOptions' => ['data-step' => 5, 'data-intro' => Yii::t('intro', 'filter')],
      'pager' => [
        'class' => '\yii\widgets\LinkPager',
        'options' => [
          'class' => 'pagination',
          'data-step' => 6,
          'data-intro' => Yii::t('intro', 'pagination')
        ],
      ],
      'columns' => [
        [
          'class' => 'yii\grid\SerialColumn',
          'header' => '№',
          'headerOptions' => ['style' => 'width: auto;text-align: center;color: #3c8dbc;'],
          'contentOptions' => ['style' => 'width: auto;text-align: center;']
        ],
        [
          'class' => 'yii\grid\ActionColumn',
          'template' => '{view}{update}{delete}',
          'header' => '<i class="fa fa-fw fa-gears"></i>',
          'headerOptions' => ['style' => 'min-width:80px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
          'contentOptions' => ['style' => 'min-width:80px;text-align:center;vertical-align:middle;'],
          'buttons' => [
            'view' => function ($url, $model) use ($canView) {
              if (!$canView) return false;
              $url = Url::toRoute(['customer/view', 'id' => $model->id]);
              return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>', $url, [
                'title' => Yii::t('app', 'View')
              ]) . '&nbsp;';
            },
            'update' => function ($url, $model) use ($canUpdate) {
              if (!$canUpdate) return false;
              $url = Url::toRoute(['customer/update', 'id' => $model->id]);
              return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url, [
                'title' => Yii::t('app', 'Edit')
              ]) . '&nbsp;';
            },
            'delete' => function ($url, $model) use ($canDelete) {
              if (!$canDelete) return false;
              $url = Url::toRoute(['customer/delete', 'id' => $model->id]);
              return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>', $url, [
                'title' => Yii::t('app', 'Delete'),
                'data' => [
                  'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                  'method' => 'post',
                ],
              ]) . '&nbsp;';
            },
          ],
          'visible' => $canUpdate || $canDelete || $canView
        ],
        [
          'attribute' => 'name',
          'value' => 'name',
          'contentOptions' => function ($model, $column) {
            return ['title' => $model->name ?? null, 'class' => 'td-nowrap', 'style' => 'max-width:200px'];
          }
        ],
        'duns',
        'alias',
        [
          'attribute' => 'country_code_id',
          'value' => 'countryCode.name',
          'filter' => $countries,
          'filterInputOptions' => [
            'class' => 'select2',
            'prompt' => Yii::t('app', '...'),
            'id' => null
          ],
        ],
        'city',
        [
          'attribute' => 'address',
          'value' => 'address',
          'contentOptions' => function ($model, $column) {
            return ['title' => $model->address ?? null, 'class' => 'td-nowrap', 'style' => 'max-width:200px'];
          }
        ],
        'postal',
        //        'country',
        //        'country_code',
        'contact_name',
        'contact_position',
        'contact_email:email',
        'contact_phone',
        'contact_cellular',
        [
          'attribute' => 'customer_type_id',
          'value' => 'customerType.name',
          'filter' => $customerTypes
        ],
        [
          'label' => Yii::t('app', 'Status'),
          'filter' => $searchModel->statusList,
          'attribute' => 'status',
          'contentOptions' => ['style' => 'text-align: center;'],
          'content' => function ($model, $column) {
            $sts_value = $model->status;
            switch ($sts_value) {
              case 1:
                $class = 'success';
                $sts_name = "✔";
                $sts_title = Yii::t('app', 'Active');
                break;
              case 0:
                $class = 'danger';
                $sts_name = "✖";
                $sts_title = Yii::t('app', 'Inactive');
                break;
            }
            $html = Html::tag('span', Html::encode($sts_name), ['class' => 'label label-' . $class]);
            return $sts_value === null ? $column->grid->emptyCell : $html;
          },
        ],
        //        [
        //          'attribute' => 'created_by',
        //          'value' => function ($data) {
        //            $created_val = (!empty($data->createdBy)) ? $data->createdBy->username : '-';
        //            return $created_val;
        //          },
        //          'contentOptions' => function ($model, $key, $index, $column) {
        //            $content_with_title = (!empty($model->createdBy)) ? ['style' => 'text-align: center;', 'title' => date('d.m.Y (H:i:s)', $model->created_at)] : ['style' => 'text-align: center;'];
        //            return $content_with_title;
        //          }
        //        ],
        //        [
        //          'attribute' => 'updated_by',
        //          'value' => function ($data) {
        //            $updated_val = (!empty($data->updatedBy)) ? $data->updatedBy->username : '-';
        //            return $updated_val;
        //          },
        //          'contentOptions' => function ($model, $key, $index, $column) {
        //            $content_with_title = (!empty($model->updatedBy)) ? ['style' => 'text-align: center;', 'title' => date('d.m.Y (H:i:s)', $model->updated_at)] : ['style' => 'text-align: center;'];
        //            return $content_with_title;
        //          }
        //        ]
      ],
    ]
  ); ?>
  <?php Pjax::end(); ?>
</div>