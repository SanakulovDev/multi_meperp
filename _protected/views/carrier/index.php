<?php
use app\models\CountryCode;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\CarrierSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Carrier info');
$this->params['breadcrumbs'][] = $this->title;
$countries = ArrayHelper::map(CountryCode::find()->all(), 'id', 'name');
$canUpdate = Yii::$app->user->can('carrier-update');
$canDelete = Yii::$app->user->can('carrier-delete');
?>
<div class="carrier-index">

  <p class="pull-right">
    <?php
    if (Yii::$app->user->can('carrier-create'))
      echo Html::a(Yii::t('app', 'btn-create'), ['create'],
                   [
                     'class' => 'btn btn-success btn-sm form-modal',
                     'style' => 'margin-right: 5px',
                     'data-intro' => Yii::t('intro', 'add-new-record')
                   ]);
    if (Yii::$app->user->can('carrier-xls'))
      echo Html::a(Yii::t('app', 'btn-download'), ['xls', 'PackSearch' => ($_GET['PackSearch'] ?? null)],
                   [
                     'class' => 'btn btn-info btn-sm',
                     'data-intro' => Yii::t('intro', 'download-button')
                   ]);
    ?>
  </p>

  <?php Pjax::begin(['id' => 'pjaxGrid']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
        'options' => ['style' => 'overflow:auto;clear:both'],
        'emptyText' => Yii::t('app', 'No results found.'),
        'tableOptions' => [
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
            ['class' => 'yii\grid\SerialColumn'],
            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{update} {delete} ',
              'header' => '<i class="fa fa-fw fa-gears"></i>',
              'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
              'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
              'buttons' => [
                'update' => function ($url, $model) use ($canUpdate) {
                  if (!$canUpdate) return false;
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
                'delete' => function ($url, $model) use ($canDelete) {
                  if (!$canDelete) return false;
                  return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                                 false,
                                 [
                                   'class' => 'modalButtonDelete',
                                   'data-href' => $url,
                                   'data-grid' => 'pjaxGrid',
                                   'title' => Yii::t('app', 'Delete')
                                 ]);
                }
              ],
              'visible' => $canDelete || $canUpdate
            ],
            [
              'attribute' => 'company_name',
              'value' => 'company_name',
              'headerOptions' => ['style' => 'width: 200px;text-align: left;vertical-align:middle;color: #3c8dbc;'],
              'contentOptions' => function($model, $column) {
                return ['title' => $model->company_name, 'class' => 'td-nowrap', 'style' => 'max-width:200px'];
              }
            ],
            'duns',
            [
              'attribute' => 'address',
              'value' => 'address',
              'headerOptions' => ['style' => 'width: 200px;text-align: left;vertical-align:middle;color: #3c8dbc;'],
              'contentOptions' => function($model, $column) {
                return ['title' => $model->address, 'class' => 'td-nowrap', 'style' => 'max-width:200px'];
              }
            ],
            [
              'attribute' => 'country_code_id',
              'headerOptions' => ['style' => 'width: 200px;text-align: left;vertical-align:middle;color: #3c8dbc;'],
              'value' => 'countryCode.name',
              'filter' => $countries,
              'contentOptions' => function($model, $column) {
                return ['title' => $model->countryCode->name, 'class' => 'td-nowrap', 'style' => 'max-width:200px'];
              }
            ],
            'city',
            [
              'attribute' => 'postal',
              'headerOptions' => ['style' => 'width: 100px;text-align: left;vertical-align:middle;color: #3c8dbc;'],
            ],
            [
              'attribute' => 'contact_name',
              'headerOptions' => ['style' => 'width: 150px;text-align: left;vertical-align:middle;color: #3c8dbc;'],
              'contentOptions' => function($model, $column) {
                return ['title' => $model->contact_name, 'class' => 'td-nowrap', 'style' => 'max-width:150px'];
              }
            ],
            [
              'attribute' => 'contact_position',
              'headerOptions' => ['style' => 'width: 150px;text-align: left;vertical-align:middle;color: #3c8dbc;'],
              'contentOptions' => function($model, $column) {
                return ['title' => $model->contact_position, 'class' => 'td-nowrap', 'style' => 'max-width:150px'];
              }
            ],
            'contact_email:email',
            [
              'attribute' => 'contact_phone',
              'headerOptions' => ['style' => 'width: 100px;text-align: left;vertical-align:middle;color: #3c8dbc;'],
            ],
            [
              'attribute' => 'contact_cellular',
              'headerOptions' => ['style' => 'width: 100px;text-align: left;vertical-align:middle;color: #3c8dbc;'],
            ],
        ],
    ]); ?>



    <?php Pjax::end(); ?>

</div>
