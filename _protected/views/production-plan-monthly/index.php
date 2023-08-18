<?php
use app\models\ProductionPlan;
use app\models\ProductionOrder;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductionPlanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/** @var TYPE_NAME $warehouses */
$this->title = Yii::t('app', 'Production plans');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('production-plan-update');
$canDelete = Yii::$app->user->can('production-plan-delete');
$canCreate = Yii::$app->user->can('production-plan-create');
$canComment = Yii::$app->user->can('production-plan-comment');
$canUploadToday = Yii::$app->user->can('production-plan-upload-today');
$canUpload = Yii::$app->user->can('production-plan-upload');
?>
	<div class="production-plan-index">
		<div class="row" data-intro="<?=Yii::t('intro', 'Active_form')?>">
			<div class="col-md-12">
				<div class="form-group pull-right">
          <?php if($canCreate): ?>
            <?=Html::a(Yii::t('app', 'btn-create'),
              ['create'], [
                'class' => 'btn btn-success btn-sm form-modal mr-lg-5',
                'data-intro' => Yii::t('intro', 'add-new-record')
              ]
            )?>
          <?php endif; ?>
          <?php if($canUpload): ?>
            <?=Html::a(Yii::t('app', 'btn-upload-txt'), ['upload'], ['class' => 'btn btn-warning btn-sm'])?>
          <?php endif; ?>
          <?php if($canUploadToday): ?>
            <?=Html::a(Yii::t('app', 'btn-upload-today'), ['upload-today'], ['class' => 'btn btn-warning btn-sm'])?>
          <?php endif; ?>
				</div>
			</div>
		</div>

    <?php Pjax::begin(['id' => 'pjaxGrid']); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

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
          [
            'class' => 'yii\grid\SerialColumn',
            'header' => '№',
            'headerOptions' => ['style' => 'width: auto;text-align: center;color: #3c8dbc;'],
            'contentOptions' => ['style' => 'width: auto;text-align: center;']
          ],
          [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update} {delete} {comment}',
            'header' => '<i class="fa fa-fw fa-gears"></i>',
            'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
            'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
            'buttons' => [
              'update' => function($url, $model) use ($canUpdate) {
                if(!$canUpdate) return false;
                if(ProductionPlan::allowEdit($model->shift, $model->production_date) == 0) return false;
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
                if(ProductionPlan::allowEdit($model->shift, $model->production_date) == 0) return false;
                return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                  false,
                  [
                    'class' => 'modalButtonDelete',
                    'data-href' => $url,
                    'data-grid' => 'pjaxGrid',
                    'title' => Yii::t('app', 'Delete')
                  ]);
              },
              'comment' => function($url, $model) use ($canComment) {
                if(!$canComment)
                  return false;
                $url = Url::toRoute(['/production-plan/comment', 'id' => $model->id]);
                return Html::a(
                  '<span  class="glyphicon glyphicon-comment"></span>',
                  false,
                  [
                    'class' => 'modalButtonUpdate',
                    'value' => $url,
                    'title' => Yii::t('app', 'Comment')
                  ]
                );
              },
            ],
            'visible' => $canUpdate || $canDelete || $canComment
          ],
          'production_date',
          'shift',
          [
            'attribute' => 'warehouse_id',
            'value' => 'warehouse.name',
            'filter' => $warehouses,
            'filterInputOptions' => [
              'class' => 'select2',
              'prompt' => Yii::t('app', 'All...'),
              'id' => null
            ],
          ],
          [
            'attribute' => 'part_id',
            'value' => 'part.part_no',
            'filter' => $parts,
            'filterInputOptions' => [
              'class' => 'select2',
              'prompt' => Yii::t('app', 'All...'),
              'id' => null
            ],
          ],
          [
            'attribute' => 'Марка',
            'value' => 'part.part_name',
            // 'filter' => $parts,
            // 'filterInputOptions' => [
            //   'class' => 'select2',
            //   'prompt' => Yii::t('app', 'All...'),
            //   'id' => null
            // ],
          ],
          'target_qty',
          [
            'attribute' => 'comment',
            'value' => 'planComment.comment',
            'headerOptions' => [
              'style' => 'color:#3c8dbc',
            ],
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
        ],
      ]
    );?>

    <?php Pjax::end(); ?>

	</div>

<? //
//$script1 = <<< JS
//$(document).ready(function(){
//	$('.btn').on('click', function(){
//		setTimeout(()=> {
//			$.pjax.reload({container: ".main-footer", async:false});
//		},1000)
//	});
//
//	$('.pagination').on('click', function(){
//		setTimeout(()=> {
//			$.pjax.reload({container: ".content-header", async:false});
//		})
//	});
//})
//JS;
//$this->registerJs($script1);
