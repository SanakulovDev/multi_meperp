<?php
use app\components\Helpers;
use app\models\Part;
use app\models\Unit;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\StockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/** @var TYPE_NAME $total */
/** @var TYPE_NAME $user_warehouses */
$this->title = Yii::t('app', 'Stocks Info');
$this->params['breadcrumbs'][] = $this->title;
$canXls = Yii::$app->user->can('stock-xls-info');
$partModel = new Part();
?>
<div class="stock-index">
  <p class="pull-right">
    <? if($canXls) { ?>
      <?=Html::a(Yii::t('app', 'btn-download'), ['xls-info', 'StockInfoSearch' => ($_GET['StockInfoSearch'] ?? null)], ['class' => 'btn btn-info btn-sm searchPjax', 'data-intro' => Yii::t('intro', 'stock-info-download')])?>
    <? } ?>
  </p>
  <?php Pjax::begin(); ?>
  <h4 class="pull-left" data-step="3" data-intro="<?=Yii::t('intro', 'stock-total')?>">
    <?=Yii::t('app', 'Total')?>: <b> <?=number_format($total, 2, '.', ' ')?> </b>
  </h4>

  <?=
  GridView::widget(
    [
      'dataProvider' => $dataProvider,
      'filterModel' => $searchModel,
      'emptyText' => Yii::t('app', 'No results found.'),
      'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
      'options' => ['style' => 'overflow-x:scroll;clear:both'],
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
          'attribute' => 'Заказчик',
          'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
          'value' => function($model) {
            return Part::findOne(['id' => $model->part_id])->remark;
          }
        ],
        [
          'attribute' => 'warehouse_id',
          'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
          'contentOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
          //'filter' => $user_warehouses,
          'filter' => Html::activeDropDownList($searchModel, 'warehouse_id', $user_warehouses, ['class' => 'form-control select2', 'prompt' => '...']),
          'content' => function($model) {
            return '<span title="'.$model->warehouse->description.'">'.$model->warehouse->name.'</span>';
          }
        ],
        [
          'attribute' => 'part_id',
          'filter' => Html::activeDropDownList($searchModel, 'part_id', $parts, ['class' => 'form-control select2', 'prompt' => '...']),
          'headerOptions' => ['style' => 'width:200px;text-align:left;vertical-align:middle;'],
          'contentOptions' => ['style' => 'width:200px;text-align:left;vertical-align:middle;'],
          'content' => function($model) {
            return $model->part->partinfo;
          }
        ],
        [
          'attribute' => 'part_name',
          'value' => 'part.part_name',
        ],
        [
          'attribute' => 'part_color',
          'value' => 'part.part_color',
          'headerOptions' => ['style' => 'width: 150px;text-align: left;vertical-align:middle;'],
          'contentOptions' => ['style' => 'width: 150px;text-align: left;vertical-align:middle;'],
        ],
        [
          'attribute' => 'part_state',
          'headerOptions' => ['style' => 'width: 80px;text-align: center;vertical-align:middle;'],
          'contentOptions' => ['style' => 'width: 80px;text-align: center;vertical-align:middle;'],
          'filter' => $partModel->stateList,
          'content' => function($model, $column) {
            return $model->part->stateText;
          }
        ],
        [
          'attribute' => 'unit',
          'headerOptions' => ['style' => 'width: 80px;text-align: center;vertical-align:middle;'],
          'contentOptions' => ['style' => 'width: 80px;text-align: center;vertical-align:middle;'],
          'value' => 'part.unit.unit_value',
          'filter' => yii\helpers\ArrayHelper::map(Unit::find()->all(), 'id', 'unit_value'),
        ],
        [
          'attribute' => 'qty',
          'headerOptions' => ['style' => 'width: 80px;text-align: right; vertical-align:middle;'],
          'filter' => ['1' => 'Все', '0' => 'без 0'],
          'content' => function($model) {
            return Helpers::numberFormatRemoveZero($model->qty, 10, '.', "", true, true);
          },
          'contentOptions' => function($model, $key, $index, $column) {
            $add_style = '';
            if($model->qty < 0) {
              $add_style = 'color: #b13036;background-color:#FFC8CE;font-weight:bold;';
            }

            return [
              'style' => 'width:80px; text-align: right;vertical-align:middle;'.$add_style
            ];
          },
          //                        'contentOptions' => function($model, $key, $index, $column){
          //                                $style = 'width: 150px;text-align: right; vertical-align:middle;';
          //                                if($model->qty < 0) {
          //                                    $arrOptions['style'] = $style . 'color:red;';
          //                                }
          //                                return $arrOptions;
          //                            }
        ],
        [
          'label' => Yii::t('app', 'Status'),
          'filter' => ['1' => Yii::t('app', 'Active'), '0' => Yii::t('app', 'Inactive')],
          'attribute' => 'part_status',
          'contentOptions' => ['style' => 'text-align: center;width: 80px;'],
          'content' => function($model, $column) {
            $sts_value = $model->part->status;
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
            $html = Html::tag('span', Html::encode($sts_name), ['class' => 'label label-'.$class]);

            return $sts_value === null ? $column->grid->emptyCell : $html;
          },
        ],
      ],
    ]
  );
  ?>
  <?php Pjax::end(); ?>
</div>

