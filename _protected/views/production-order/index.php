<?php
use app\models\ProductionOrder;
use kartik\datetime\DateTimePicker;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductionOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/** @var TYPE_NAME $users */
$this->title = Yii::t('app', 'Production order');
$this->params['breadcrumbs'][] = $this->title;
$canCreate = Yii::$app->user->can('production-order-create');
$canUpload = Yii::$app->user->can('production-order-upload');
$canDelete = Yii::$app->user->can('production-order-delete');
$canDownload = Yii::$app->user->can('production-order-xls');
$canPrint = Yii::$app->user->can('production-order-selected-print');
$factFreezeTime = isset(Yii::$app->params['fact_freeze_time']) ? Yii::$app->params['fact_freeze_time'] : 0;
$isBulkList = ProductionOrder::isBulkList();
$stateList = array_merge(ProductionOrder::stateList(), [ProductionOrder::LABEL_ACTUAL => Yii::t('app', 'actual')]);
?>

<div class="production-order-index">
  <div class="row">
    <?php $form = ActiveForm::begin(['action' => ['index'], 'method' => 'get',]); ?>
    <div class="col-md-3">
      <?=$form->field($searchModel, 'filter_from')->widget(DateTimePicker::classname(), [
        'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
        'layout' => '{picker}{input}{remove}',
        'removeButton' => ['position' => 'append'],
        'language' => 'ru',
        'pluginOptions' => [
          'autoclose' => true,
          'format' => 'yyyy-mm-dd hh:ii',
        ],
        'options' => [
          'autocomplete' => 'off',
          'placeholder' => 'С...',
          'class' => ' form-control input-sm'
        ]
      ])->label(false)?>
    </div>
    <div class="col-md-3">
      <?=$form->field($searchModel, 'filter_to')->widget(DateTimePicker::classname(), [
        'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
        'layout' => '{picker}{input}{remove}',
        'removeButton' => ['position' => 'append'],
        'language' => 'ru',
        'pluginOptions' => [
          'autoclose' => true,
          'format' => 'yyyy-mm-dd hh:ii',
        ],
        'options' => [
          'autocomplete' => 'off',
          'placeholder' => 'До...',
          'class' => ' form-control input-sm'
        ]
      ])->label(false)?>
    </div>
    <?=$form->field($searchModel, 'part_id')->hiddenInput()->label(false)?>
    <?=$form->field($searchModel, 'serial_number')->hiddenInput()->label(false)?>
    <?=$form->field($searchModel, 'quantity')->hiddenInput()->label(false)?>
    <?=$form->field($searchModel, 'current_seq')->hiddenInput()->label(false)?>
    <?=$form->field($searchModel, 'is_printed')->hiddenInput()->label(false)?>
    <?=$form->field($searchModel, 'created_by')->hiddenInput()->label(false)?>
    <div class="col-md-3">
      <div class="form-group">
        <?=Html::submitButton(Yii::t('app', 'btn-show'), ['class' => 'btn btn-primary btn-sm'])?>
        <?php
        if($canDownload)
//          echo Html::a(
//            Yii::t('app', 'btn-download'),
//            ['xls', 'ProductionOrderSearch' => ($_GET['ProductionOrderSearch'] ?? null)],
//            ['class' => 'btn btn-info btn-sm']
//          );
          echo Html::a(
            Yii::t('app', 'btn-download'),
            ['xlsx', 'ProductionOrderSearch' => ($_GET['ProductionOrderSearch'] ?? null)],
            ['class' => 'btn btn-info btn-sm']
          );
        if($canPrint)
          echo Html::a(Yii::t('app', 'btn-print'),
            ['selected-print', 'ProductionOrderSearch' => ($_GET['ProductionOrderSearch'] ?? null)],
            ['class' => 'btn btn-warning btn-sm', 'style' => 'margin-left: 5px']
          );
        ?>
      </div>
    </div>
    <div class="col-md-3">
      <p class="pull-right">
        <?php
        if($canUpload)
          echo Html::a(Yii::t('app', 'btn-upload-txt'), ['upload'],
            ['class' => 'btn btn-warning btn-sm', 'style' => 'margin-left: 5px', 'data-intro' => Yii::t('intro', 'add-upload')]
          );
        if($canCreate)
          echo Html::a(Yii::t('app', 'btn-create'), ['create'],
            ['class' => 'btn btn-success btn-sm', 'style' => 'margin-left: 5px']
          );
        ?>
      </p>
    </div>
    <?php ActiveForm::end(); ?>
  </div>

  <?=GridView::widget(
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
          'headerOptions' => ['style' => 'width: 50px;text-align: center;color: #3c8dbc;'],
          'contentOptions' => ['style' => 'width: 50px;text-align: center;']
        ],
        [
          'class' => 'yii\grid\ActionColumn',
          'header' => '<i class="fa fa-fw fa-gears"></i>',
          'headerOptions' => ['style' => 'width: 70px;text-align: center;vertical-align:middle;color: #3c8dbc;'],
          'contentOptions' => ['style' => 'width: 70px;text-align: center;vertical-align:middle;'],
          'template' => '{print}{delete}',
          'buttons' => [
            'print' => function($url, $model) use ($canPrint) {
              if(!$canPrint || $model->is_label == ProductionOrder::LABEL_ACTUAL)
                return false;
              $url = Url::toRoute(['production-order/selected-print', 'ProductionOrderSearch[id]' => $model->id]);
              return Html::a('<span class="fa fa-print" aria-hidden="true"></span>', $url, [
                  'title' => Yii::t('app', 'Print')
                ]).'&nbsp;';
            },
            'delete' => function($url, $model) use ($canDelete, $factFreezeTime) {
              if(!$canDelete || (!empty($model->document_id))) {
                return false;
              }
              if(
                $factFreezeTime > 0 &&
                (time() > ($model->created_at + ($factFreezeTime*60)))
                //                  && (!in_array(Yii::$app->user->identity->rolename, ['superadmin'])) // adminga ham deb qolishlari mumkin
              ) {
                return false;
              }
              $url = Url::toRoute(['production-order/delete', 'id' => $model->id, 'searchUrl' => ($_GET['ProductionOrderSearch'] ?? null)]);
              return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>', $url, [
                  'title' => Yii::t('app', 'Delete'),
                  'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                  ],
                ]).'&nbsp;';
            },
          ],
          'visible' => $canDelete || $canPrint
        ],
        [
          'attribute' => 'part_id',
          'headerOptions' => ['style' => 'vertical-align:middle;'],
          'filter' => Html::activeDropDownList($searchModel, 'part_id', $parts, ['class' => 'form-control select2', 'prompt' => '...']),
          'content' => function($model) {
            return $model->part->partinfo;
          },
          'contentOptions' => function($model, $key, $index, $column) {
            return [
              'style' => 'width:350px;vertical-align:middle;',
              'title' => $model->part->partinfo ? $model->part->part_name : '-'
            ];
          }
        ],
        [
          'attribute' => 'serial_number',
          'headerOptions' => ['style' => 'width: 220px;vertical-align:middle;'],
          'contentOptions' => ['style' => 'width: 220px;vertical-align:middle;'],
        ],
        [
          'attribute' => 'quantity',
          'headerOptions' => ['style' => 'width: 100px;vertical-align:middle;text-align: right;'],
          'contentOptions' => ['style' => 'width: 100px;vertical-align:middle;text-align: right;'],
          'content' => function($model) {
            return app\components\Helpers::formatRemoveDecimal($model->quantity, 6);
          }
        ],
        [
          'attribute' => 'current_seq',
          'headerOptions' => ['style' => 'width: 100px;vertical-align:middle;text-align: center;'],
          'contentOptions' => ['style' => 'width: 100px;vertical-align:middle;text-align: center;'],
        ],
        [
          'attribute' => 'is_label',
          'filter' => $stateList,
          'headerOptions' => ['style' => 'width: 100px;vertical-align:middle;text-align: center;'],
          'contentOptions' => ['style' => 'width: 100px;vertical-align:middle;text-align: center;'],
          'content' => function($model) use ($stateList) {
            return $stateList[$model->is_label];
          }
        ],        
        [
          'attribute' => 'created_by',
          'headerOptions' => ['style' => 'width: 100px;vertical-align:middle;text-align: center;'],
          'contentOptions' => ['style' => 'width: 100px;vertical-align:middle;text-align: center;'],
          'value' => function($data) {
            return (!empty($data->createdBy)) ? $data->createdBy->username : '-';
            //            return $create_val;
          }
        ],
        [
          'attribute' => 'created_at',
          'headerOptions' => ['style' => 'width: 200px;vertical-align:middle;text-align: center;'],
          'contentOptions' => ['style' => 'width: 200px;vertical-align:middle;text-align: center;'],
          'value' => function($data) {
            return (!empty($data->created_at)) ? date('d.m.Y (H:i:s)', $data->created_at) : '-';
          },
        ],
        [
          'attribute' => 'line',
          'value' => function($model) {
            return $model->line ? (Yii::t('app', 'Line').'-'.$model->line) : '';
          },
          'headerOptions' => [
            'style' => 'color:#3c8dbc',
          ],
          'filter' => ProductionOrder::getLines(),
        ]
        //        [
        //          'attribute' => 'updated_at',
        //          'value' => function($data) {
        //            $update_val = (!empty($data->updatedBy)) ? $data->updatedBy->username : '-';
        //
        //            return $update_val;
        //          },
        //          'contentOptions' => function($model, $key, $index, $column) {
        //            return [
        //              'style' => 'width:200px;vertical-align:middle;',
        //              'title' => ($model->updated_at) ? date('d.m.Y (H:i:s)', $model->updated_at) : '-'
        //            ];
        //          }
        //        ],
      ],
    ]);?>
</div>
