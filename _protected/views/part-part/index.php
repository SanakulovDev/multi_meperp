<?php
use app\components\Helpers;
use app\models\Part;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductPartsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/** @var TYPE_NAME $warehouses */
$this->title = Yii::t('app', 'BOM');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = false; // Yii::$app->user->can('part-part-update');
$canDelete = false; //Yii::$app->user->can('part-part-delete');
?>

  <div class="product-parts-index">
    <p class="pull-right">
      <?php
//      if(Yii::$app->user->can('part-part-create'))
//        echo Html::a(
//          Yii::t('app', 'btn-create'),
//          ['create'],
//          [
//            'class' => 'btn btn-success btn-sm form-modal',
//            'style' => 'margin-right: 5px',
//            'data-intro' => Yii::t('intro', 'add-new-record')
//          ]
//        );
      if(Yii::$app->user->can('bom-log-index'))
        echo Html::a(
          Yii::t('app', 'btn-history'),
          ['bom-log/index'],
          [
            'class' => 'btn btn-warning btn-sm',
            'style' => 'margin-right: 5px',
            'data-intro' => Yii::t('intro', 'history')
          ]
        );
      if(Yii::$app->user->can('part-part-xls'))
        echo Html::a(
          Yii::t('app', 'btn-download'),
          ['xls', 'PartPartSearch' => ($_GET['PartPartSearch'] ?? null)],
          [
            'class' => 'btn btn-info btn-sm m-r searchPjax',
            'style' => 'margin-right: 5px',
            'data-intro' => Yii::t('intro', 'download-button')
          ]
        );
      if(Yii::$app->user->can('part-part-part-raw-excel'))
        echo Html::a(
          Yii::t('app', 'btn-download-part-raw'),
          ['part-raw-excel', 'PartPartSearch' => (null)],
          [
            'class' => 'btn btn-primary btn-sm m-r',
            'data-intro' => Yii::t('intro', 'download-button')
          ]
        ); ?>
    </p>

    <?php Pjax::begin(['id' => 'pjaxGrid']); ?>
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

                return Html::a(
                  '<span class="glyphicon glyphicon-trash"></span>',
                  false,
                  [
                    'class' => 'modalButtonDelete',
                    'data-href' => $url,
                    'data-grid' => 'pjaxGrid',
                    'title' => Yii::t('app', 'Delete')
                  ]
                );
              }
            ],
            'visible' => $canDelete || $canUpdate
          ],
          [
            'attribute' => 'part_id',
            'headerOptions' => ['style' => 'width: 100px;text-align: left;vertical-align:middle;'],
            'filter' => Html::activeDropDownList($searchModel, 'part_id', $notRawParts, ['class' => 'form-control select2', 'prompt' => '...']),
            'contentOptions' => ['class' => 'td-nowrap'],
            'content' => function($model) {
              $hasChild = false;
              switch($model->part->state) {
                case Part::STATE_FINISHED:
                  $color_by_state = 'text-success';
                  $hasChild = true;
                  break;
                case Part::STATE_SEMI:
                  $color_by_state = 'text-primary';
                  $hasChild = true;
                  break;
              }
              if($hasChild) {
                return Html::a($model->part->partinfo, null, ['class' => 'part_link text-bold '.$color_by_state, 'data-part_id' => $model->part_id, 'data-url' => Url::toRoute('part-part/detailed-bom')]);
              } else {
                return $model->part->partinfo;
              }
            },
          ],
          [
            'attribute' => 'part_nm',
            'headerOptions' => ['style' => 'width: 200px;text-align: left;vertical-align:middle;color: #3c8dbc;'],
            'contentOptions' => function($model, $column) {
              return ['title' => $model->part->part_name ?? null, 'class' => 'td-nowrap', 'style' => 'max-width:200px'];
            },
            'content' => function($model) {
              return $model->part->part_name;
            }
          ],
          [
            'attribute' => 'sub_part_id',
            'headerOptions' => ['style' => 'width: 100px;text-align: left;vertical-align:middle;'],
            'filter' => Html::activeDropDownList($searchModel, 'sub_part_id', $parts, ['class' => 'form-control select2', 'prompt' => '...']),
            'contentOptions' => ['class' => 'td-nowrap'],
            'content' => function($model) {
              $hasChild = false;
              switch($model->subPart->state) {
                case Part::STATE_FINISHED:
                  $color_by_state = 'text-success';
                  $hasChild = true;
                  break;
                case Part::STATE_SEMI:
                  $color_by_state = 'text-primary';
                  $hasChild = true;
                  break;
              }
              if($hasChild) {
                return Html::a($model->subPart->partinfo, null, ['class' => 'part_link text-bold '.$color_by_state, 'data-part_id' => $model->sub_part_id, 'data-url' => Url::toRoute('part-part/detailed-bom')]);
              } else {
                return $model->subPart->partinfo;
              }
            },
          ],
          [
            'attribute' => 'sub_part_nm',
            'headerOptions' => ['style' => 'width: 200px;text-align: left;vertical-align:middle;color: #3c8dbc;'],
            'contentOptions' => function($model) {
              return ['title' => $model->subPart->part_name ?? null, 'class' => 'td-nowrap', 'style' => 'max-width:200px'];
            },
            //            'contentOptions' => function($model, $column){
            //							return ['title' => $model->part->part_name ?? null, 'class' => 'td-nowrap', 'style' => 'max-width:100px'];
            //						},
            'content' => function($model) {
              return $model->subPart->part_name;
            },
          ],
          [
            'attribute' => 'usage_qty',
            'headerOptions' => ['style' => 'width: 80px;text-align: right;vertical-align:middle;'],
            'contentOptions' => ['style' => 'width: 80px;text-align: right;vertical-align:middle;'],
            'content' => function($model) {
              return Helpers::numberFormatRemoveZero($model->usage_qty, 10);
            },
          ],
          [
            'attribute' => 'unit_value',
            'headerOptions' => ['style' => 'width: 80px;text-align: center;vertical-align:middle;color: #3c8dbc;'],
            'contentOptions' => ['style' => 'width: 80px;text-align: center;vertical-align:middle;'],
            'content' => function($model) {
              return $model->subPart->unit->unit_value;
            },
            //'filter' => yii\helpers\ArrayHelper::map(app\models\Unit::find()->all(), 'id', 'unit_value')
            'filter' => false
          ],

          //					[
          //						'attribute' => 'warehouse_id',
          //						'headerOptions' => ['style' => 'width: 100px;text-align: left;vertical-align:middle;'],
          //						'contentOptions' => ['style' => 'width: 100px;text-align: left;vertical-align:middle;'],
          //						'filter' => Html::activeDropDownList($searchModel, 'warehouse_id', $warehouses, ['class' => 'form-control select2', 'prompt' => '...']),
          //						'content' => function($model){
          //							return '<span title="'.$model->warehouse->description.'">'.$model->warehouse->name.'</span>';
          //
          //						},
          //					],
          [
            'attribute' => 'warehouse_id',
            'value' => 'warehouse.name',
            'contentOptions' => function($model, $column) {
              return ['title' => $model->warehouse->description ?? null, 'class' => 'td-nowrap', 'style' => 'max-width:100px'];
            },
            'filter' => $warehouses
          ],
          [
            'attribute' => 'remark',
            'value' => 'remark',
            'contentOptions' => function($model, $column) {
              return ['title' => $model->remark ?? null, 'class' => 'td-nowrap', 'style' => 'max-width:200px'];
            }
          ],
          [
            'filter' => $searchModel->statusList,
            'attribute' => 'status',
            'headerOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;'],
            'contentOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;'],
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
        ],
      ]
    );?>
    <?php Pjax::end(); ?>
  </div>

<? require_once '_modal-bom-collapse.php'; ?>
