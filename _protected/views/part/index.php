<?php
use app\models\Part;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PartSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var TYPE_NAME $partTypes */
/* @var TYPE_NAME $contractSource */
/* @var TYPE_NAME $warehouses */
$this->title = Yii::t('app', 'Parts');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('part-update');
$canDelete = Yii::$app->user->can('part-delete');
?>
<div class="part-index">

  <p class="pull-right">
    <?php
    if(Yii::$app->user->can('part-create'))
      echo Html::a(Yii::t('app', 'btn-create'), ['create'],
        [
          'class' => 'btn btn-success btn-sm',
          'style' => 'margin-right: 5px',
          'data-step' => 2, 'data-intro' => Yii::t('intro', 'add-new-record')
        ]);
    if(Yii::$app->user->can('part-xls'))
      echo Html::a(Yii::t('app', 'btn-download'), ['xls', 'PartSearch' => ($_GET['PartSearch'] ?? null)],
        [
          'class' => 'btn btn-info btn-sm',
          'data-step' => 3,
          'data-intro' => Yii::t('intro', 'download-button')
        ]);
    ?>
  </p>

  <?=
  GridView::widget(
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
          'template' => '{update} {delete} ',
          'header' => '<i class="fa fa-fw fa-gears"></i>',
          'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
          'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
          'buttons' => [
            'update' => function($url, $model) use ($canUpdate) {
              if(!$canUpdate) return false;
              return Html::a(
                '<span  class="glyphicon glyphicon-pencil"></span>',
                $url,
                [
                  'title' => Yii::t('app', 'Update')
                ]
              );
            },
            'delete' => function($url, $model) use ($canDelete) {
              if(!$canDelete) return false;
              return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                $url,
                [
                  'data-pjax' => 0,
                  'data-confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                  'data-method' => 'post',
                  'title' => Yii::t('app', 'Delete')
                ]);
            }
          ],
          'visible' => $canDelete || $canUpdate,
        ],
        'part_no',
        [
          'attribute' => 'part_name',
          'value' => 'part_name',
          'contentOptions' => function($model, $column) {
            return ['title' => $model->part_name ?? null, 'class' => 'td-nowrap', 'style' => 'max-width:100px'];
          }
        ],
        'part_color',
        [
          'attribute' => 'unit_id',
          'value' => 'unit.unit_value',
          'filter' => $units
        ],
        [
          'attribute' => 'part_type_id',
          'value' => 'partType.typename',
          'filter' => $partTypes
        ],
        [
          'attribute' => 'contract_source_id',
          'value' => 'contractSource.name',
          'filter' => $contractSource
        ],
        [
          'attribute' => 'warehouse_id',
          'value' => 'warehouse.name',
          'contentOptions' => function($model, $column) {
            return ['title' => $model->warehouse->name ?? null, 'class' => 'td-nowrap', 'style' => 'max-width:100px'];
          },
          'filter' => $warehouses
        ],
        [
          'label' => Yii::t('app', 'Status'),
          'filter' => $searchModel->statusList,
          'attribute' => 'status',
          'contentOptions' => ['style' => 'text-align: center;'],
          'content' => function($model, $column) {
            $sts_value = $model->status;
            switch($sts_value) {
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
            $html = Html::tag('span', Html::encode($sts_name), ['title' => $sts_title, 'class' => 'label label-'.$class]);
            return $sts_value === null ? $column->grid->emptyCell : $html;
          },
        ],
        [
          'attribute' => 'state',
          'filter' => [
            Part::STATE_RAW => Yii::t('app', 'Component'),
            Part::STATE_SEMI => Yii::t('app', 'Semi-finished'),
            Part::STATE_FINISHED => Yii::t('app', 'Product'),
            Part::STATE_CASTLE => Yii::t('app', 'Castle')
          ],
          'content' => function($model, $column) {
            return $model->stateText;
          }
        ],
        [
          'attribute' => 'remark',
          'value' => 'remark',
          'contentOptions' => function($model, $column) {
            return ['title' => $model->remark, 'class' => 'td-nowrap', 'style' => 'max-width:150px'];
          }
        ],
      ],
    ]
  );?>
</div>
