<?php
use app\models\Part;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PartPackingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('app', 'Component');
$this->params['breadcrumbs'][] = $this->title;
$returnables = [1 => Yii::t('app', 'Y'), 0 => Yii::t('app', 'N')];
$canUpdate = Yii::$app->user->can('part-packing-update');
$canDelete = Yii::$app->user->can('part-packing-delete');
$canView = Yii::$app->user->can('part-packing-view');
$canPrint = Yii::$app->user->can('part-packing-print');
?>
<div class="part-packing-index">
  <p class="pull-right">
    <?php
    if(Yii::$app->user->can('part-packing-create'))
      echo Html::a(Yii::t('app', 'btn-create'), ['create'],
        [
          'class' => 'btn btn-success btn-sm form-modal',
          'style' => 'margin-right: 5px',
          'data-intro' => Yii::t('intro', 'add-new-record')
        ]);
    if(Yii::$app->user->can('part-packing-xls'))
      echo Html::a(Yii::t('app', 'btn-download'), ['xls', 'PartPackingSearch' => ($_GET['PartPackingSearch'] ?? null)],
        [
          'class' => 'btn btn-info btn-sm',
          'data-intro' => Yii::t('intro', 'download-button')
        ])
    ?>
  </p>
  <?php Pjax::begin(['id' => 'pjaxGrid']); ?>

  <?=GridView::widget(
    [
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
          'template' => '{view} {print} {update} {delete} ',
          'header' => '<i class="fa fa-fw fa-gears"></i>',
          'headerOptions' => ['style' => 'width: 90px;text-align: center;vertical-align:middle;color: #3c8dbc;'],
          'contentOptions' => ['style' => 'width: 90px;text-align: center;vertical-align:middle;'],
          'buttons' => [
            'view' => function($url, $model) use ($canView) {
              if(!$canView) return false;

              return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',
                $url,
                [
                  'title' => Yii::t('app', 'View')
                ]);
            },
            'print' => function($url, $model) use ($canPrint) {
              if(!$canPrint) return false;
              $url = Url::toRoute(['part-packing/print', 'id' => $model->id]);

              return Html::a('<span class="fa fa-print" aria-hidden="true"></span>', $url, [
                  'title' => Yii::t('app', 'Print label')
                ]).'&nbsp;';
            },
            'update' => function($url, $model) use ($canUpdate) {
              if(!$canUpdate) return false;

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
            'delete' => function($url, $model) use ($canDelete) {
              if(!$canDelete) return false;

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
          'visible' => $canDelete || $canUpdate || $canView || $canPrint
        ],
        [
          'attribute' => 'part_id',
          'filter' => Html::activeDropDownList($searchModel, 'part_id', $parts, ['class' => 'form-control select2', 'prompt' => '...']),
          'content' => function($model) {
            return $model->part->partinfo;
          },
          'contentOptions' => function($model, $key, $index, $column) {
            return $model->part->state == Part::STATE_FINISHED ? ['class' => 'td-nowrap text-bold'] : [];
          }
        ],
        [
          'content' => function($model) {
            return $model->part ? $model->part->part_name : null;
          },
        ],
        [
          'attribute' => 'supplier_id',
          'filter' => Html::activeDropDownList($searchModel, 'supplier_id', $suppliers, ['class' => 'form-control select2', 'prompt' => '...']),
          'content' => function($model) {
            return $model->supplier ? $model->supplier->name : null;
          },
        ],
        [
          'attribute' => 'returnable',
          'filter' => $returnables,
          'content' => function($model) use ($returnables) {
            return $returnables[$model->returnable];
          },
        ],
        [
          'attribute' => 'pack_id',
          'filter' => Html::activeDropDownList($searchModel, 'pack_id', $packs, ['class' => 'form-control select2', 'prompt' => '...']),
          'content' => function($model) {
            return $model->pack->code;
          },
        ],
        [
          'attribute' => 'pack_qty',
          'content' => function($model) {
            return $model->pack_qty + 0;
          },
        ],
        [
          'attribute' => 'piece_weight',
          'content' => function($model) {
            return $model->piece_weight + 0;
          },
        ],
        [
          'attribute' => 'netto',
          'content' => function($model) {
            return $model->netto + 0;
          },
        ],
        [
          'attribute' => 'brutto',
          'content' => function($model) {
            return $model->brutto + 0;
          },
        ],
      ],
    ]);?>

  <?php Pjax::end(); ?>

</div>
