<?php
use app\models\ProductionPlan;
use app\models\ProductionOrder;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductionPlanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/** @var TYPE_NAME $warehouses */
$this->title = Yii::t('app', 'Production plans Weekly');
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
    <div class="col-md-8">
      <h4 class="pull-left text-center" data-step="3" data-intro="<?=Yii::t('intro', 'stock-total')?>">
        <?=Yii::t('app', 'Total')?>: <b> <?=number_format($total, 2, '.', ' ')?> </b>
      </h4>
    </div>
			<div class="col-md-4">
				<div class="form-group pull-right">
          <?= Html::a('Обеспеченность', ['/report/weekly-requirement-short'],
          [
            'class'=>'btn btn-primary btn-sm mr-lg-5'
          ])?>
          <?php if($canCreate): ?>
            <?=Html::a(Yii::t('app', 'btn-create'),
              ['create'], [
                'class' => 'btn btn-success btn-sm form-modal mr-lg-5',
                'data-intro' => Yii::t('intro', 'add-new-record')
              ]
            )?>
           <?=Html::button(Yii::t('app', 'btn-delete-all'),
              [
                'class' => 'btn btn-info btn-sm modalButtonDelete mr-lg-5',
                'data-intro' => Yii::t('intro', 'delete-all-records'),
                'data-grid' => 'pjaxGrid',
                'data-status' => 1,
                'data-href' => Url::toRoute(['delete-all'])
              ]
            )?>
          <?php endif; ?>
          
				</div>
			</div>
		</div>
    <?php
    Modal::begin([
        'header' => '<h2>'.Yii::t('app', 'BOM').'</h2>',
        'id' =>'specification-part',
        'class'=>'modal modal-lg'
    ]);


    Modal::end();
    ?>

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
            ],
            'visible' => $canUpdate || $canDelete
          ],
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
            'format' =>'raw',
            'value' => function($model){
              $id = $model->id;
              return '<span class="monthly-part" data-monthly-id="'.$id.'">'.$model->part->part_name.'</span>';
            }
            // 'filter' => $parts,
            // 'filterInputOptions' => [
            //   'class' => 'select2',
            //   'prompt' => Yii::t('app', 'All...'),
            //   'id' => null
            // ],
          ],
          'target_qty',
          [
            'attribute' => 'line',
            'value' => function($model) {
              return $model->line ? (Yii::t('app', 'Line').'-'.$model->line) : '';
            },
            'headerOptions' => [
              'style' => 'color:#3c8dbc',
            ],
            'filter' => ProductionOrder::getLines(),
          ],
          'remark'
        ],
      ]
    );?>

    <?php Pjax::end(); ?>

	</div>

 <?php ob_start();?>
 $(function(){
  $('.monthly-part').css('cursor','pointer');
  $('.monthly-part').css('font-weight','bold');
  $('.monthly-part').on('click', function(){
    let id = $(this).data('monthly-id');
    let url = 'specification';
    let data = {
      id: id
    };
    ajaxRequest(url, data, 'POST', function(data){
      $('#specification-part').modal('show');
      console.log(data);
      $('#specification-part').find('.modal-body').html(data);
    
    })
  })

  function ajaxRequest(url, data, method = 'POST', callback) {
    $.ajax({
      url: url,
      data: data,
      type: method,
      success: function(data){
        callback(data);
      },
      error: function(){
        alert('Something is wrong!');
      }
    })
  }
 })
 <?php $this->registerJs(ob_get_clean());?>
