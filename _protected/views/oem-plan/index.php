<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OemPlanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'OEM');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('oem-plan-update');
$canDelete = Yii::$app->user->can('oem-plan-delete');
$canUpload = Yii::$app->user->can('oem-plan-upload');

?>
<div class="oem-plan-index">

    <p class="pull-right">
		<?php if($canUpload): ?>
            <?=Html::a(Yii::t('app', 'btn-upload-txt'), ['upload'], ['class' => 'btn btn-warning btn-sm'])?>
		<?php endif; ?>
    <?php
	  if (Yii::$app->user->can('oem-plan-create')) {
	  	echo Html::a(
			Yii::t('app', 'btn-create'),
			['create'],
			[
				'class' => 'btn btn-success btn-sm form-modal',
				'style' => 'margin-right: 5px',
				'data-intro' => Yii::t('intro', 'add-new-record')
			]
		);
	  }
	  if (Yii::$app->user->can('oem-plan-xls')) {
	  	echo Html::a(
			Yii::t('app', 'btn-download'),
			['xls', 'OemPlanSearch' => ($_GET['OemPlanSearch'] ?? null)],
			[
				'class' => 'btn btn-info btn-sm',
				'id'=>'btn_download',
				'data-intro' => Yii::t('intro', 'download-button')
			]
		);
	  }
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
    			'headerOptions' => ['style' => 'width:80px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
    			'contentOptions' => ['style' => 'width:80px;text-align:center;vertical-align:middle;'],
    			'buttons' => [
    				'update' => function ($url, $model) use ($canUpdate) {
    					if (!$canUpdate) {
    						return false;
    					}
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
    					if (!$canDelete) {
    						return false;
    					}
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
    			'attribute' => 'model_id',
    			'filter' => Html::activeDropDownList($searchModel, 'model_id', $models, ['class' => 'form-control select2', 'prompt' => '...']),
    			'content' => function ($model) {
    				return $model->model->description;
    			}
    		],
    		[
					'attribute' => 'target_date',
					'filter' => Html::activeInput('date', $searchModel,'target_date', ['class' => 'form-control']),
    			'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
    			'contentOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
    		],
    		[
    			'attribute' => 'quantity',
    			'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
    			'contentOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
    		],
    	],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php
$js = <<< JS
$(document).ready(function() {
  $(document).on('pjax:success', function() {
		$('#btn_download').attr('href','/oem-plan/xls'+window.location.search);
	});
});
JS;

$this->registerJs($js, yii\web\View::POS_END);
?>

