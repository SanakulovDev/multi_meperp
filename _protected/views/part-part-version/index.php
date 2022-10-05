<?php
use app\components\Helpers;
use app\models\Part;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PartPartVersionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/** @var TYPE_NAME $parentParts */
/** @var TYPE_NAME $parts */
$this->title = Yii::t('app', 'BOM versions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="part-part-version-index">

  <?php Pjax::begin(); ?>
  <?=
  GridView::widget(
    [
      'dataProvider' => $dataProvider,
      'filterModel' => $searchModel,
      'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
      'rowOptions' => ['style' => 'white-space:nowrap; vertical-align:middle;'],
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
          'headerOptions' => ['style' => 'width:100px;text-align: center;color: #3c8dbc;'],
          'contentOptions' => ['style' => 'width:100px;text-align: center;']
        ],
        [
          'attribute' => 'version',
          'headerOptions' => ['style' => 'width: 100px;text-align: left;vertical-align:middle;'],
          'contentOptions' => ['class' => 'td-nowrap text-right'],
          'content' => function($model) {
            return $model->version;
          },
        ],
        [
          'attribute' => 'action',
          'headerOptions' => ['class' => ' text-center', 'style' => 'vertical-align:middle;'],
          'contentOptions' => function($model) {
            switch($model->action) {
              case "+":
                $class = 'plus_cnt';
                $sts_title = Yii::t('app', 'Added');
                break;
              case "-":
                $class = 'minus_cnt';
                $sts_title = Yii::t('app', 'Removed');
                break;
            }
            return ['class' => ' text-center '.$class, 'title' => $sts_title];
          },
          'content' => function($model) {
            return Html::encode($model->action);
          },
        ],
        [
          'attribute' => 'part_id',
          'headerOptions' => ['style' => 'width: 100px;text-align: left;vertical-align:middle;'],
          'filter' => Html::activeDropDownList($searchModel, 'part_id', $parentParts, ['class' => 'form-control select2', 'prompt' => '...']),
          'contentOptions' => ['class' => 'td-nowrap'],
          'content' => function($model) {
            switch($model->part->state) {
              case Part::STATE_FINISHED:
                $color_by_state = 'text-success';
                break;
              case Part::STATE_SEMI:
                $color_by_state = 'text-primary';
                break;
            }
            return "<span class='text-bold ".$color_by_state."'>".$model->part->partinfo."</span>";
          },
        ],
        [
          'attribute' => 'sub_part_id',
          'headerOptions' => ['style' => 'width: 100px;text-align: left;vertical-align:middle;'],
          'filter' => Html::activeDropDownList($searchModel, 'sub_part_id', $parts, ['class' => 'form-control select2', 'prompt' => '...']),
          'contentOptions' => ['class' => 'td-nowrap'],
          'content' => function($model) {
            switch($model->part->state) {
              case Part::STATE_FINISHED:
                $color_by_state = 'text-success';
                break;
              case Part::STATE_SEMI:
                $color_by_state = 'text-primary';
                break;
            }
            return "<span class='text-bold ".$color_by_state."'>".$model->subPart->partinfo."</span>";
          },
        ],
        [
          'attribute' => 'usage_qty',
          'headerOptions' => ['style' => 'width: 100px;text-align: left;vertical-align:middle;'],
          'contentOptions' => ['class' => 'td-nowrap text-right'],
          'content' => function($model) {
            return Helpers::numberFormatRemoveZero($model->usage_qty);
          },
        ],
        [
          'attribute' => 'warehouse_id',
          'value' => 'part.warehouse.name',
          'contentOptions' => function($model, $column) {
            return ['title' => $model->part->warehouse->description ?? null, 'class' => 'td-nowrap', 'style' => 'max-width:100px'];
          },
          'filter' => $warehouses
        ],
        [
          'attribute' => 'status',
          'label' => Yii::t('app', 'Status'),
          'filter' => $searchModel->statusList,
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
        'remark',
        [
          'attribute' => 'created_at',
          'value' => function($data) {
            $create_val = ($data->created_at) ? date('d.m.Y (H:i:s)', $data->created_at) : '-';
            return $create_val;
          },
          'contentOptions' => function($model, $key, $index, $column) {
            return [
              'style' => 'text-align: center; vertical-align:middle;',
              'title' => (!empty($model->createdBy)) ? $model->createdBy->username : '-'
            ];
          }
        ],
        [
          'attribute' => 'updated_at',
          'value' => function($data) {
            $update_val = ($data->updated_at) ? date('d.m.Y (H:i:s)', $data->updated_at) : '-';
            return $update_val;
          },
          'contentOptions' => function($model, $key, $index, $column) {
            return [
              'style' => 'text-align: center; vertical-align:middle;',
              'title' => (!empty($model->updatedBy)) ? $model->updatedBy->username : '-'
            ];
          }
        ],
        [
          'attribute' => 'deleted_at',
          'value' => function($data) {
            $update_val = ($data->deleted_at) ? date('d.m.Y (H:i:s)', $data->deleted_at) : '-';
            return $update_val;
          },
          'contentOptions' => function($model, $key, $index, $column) {
            return [
              'style' => 'text-align: center; vertical-align:middle;',
              'title' => (!empty($model->deletedBy)) ? $model->deletedBy->username : '-'
            ];
          }
        ],
      ],
    ]);?>

  <?php Pjax::end(); ?>

</div>
