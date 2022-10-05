<?php

use app\models\CountryCode;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SupplierSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('app', 'Supplier info');
$this->params['breadcrumbs'][] = $this->title;
$countries = ArrayHelper::map(CountryCode::find()->all(), 'id', 'name');
$canUpdate = Yii::$app->user->can('supplier-update');
$canDelete = Yii::$app->user->can('supplier-delete');
$canCreate =  Yii::$app->user->can('supplier-create');
$canXls = Yii::$app->user->can('supplier-xls');
?>
<div class="supplier-index">
    <p class="pull-right">
		<? if ($canCreate) { ?>
      <?= Html::a(Yii::t('app', 'btn-create'), ['create'],
        [
          'class' => 'btn btn-sm btn-success',
          'data-intro' => Yii::t('intro', 'add-new-record')
        ]) ?>
        <? } ?>
		<? if ($canXls) { ?>
      <?= Html::a(Yii::t('app', 'btn-download'), ['xls', 'SupplierSearch' => ($_GET['SupplierSearch'] ?? null)],
        [
          'class' => 'btn btn-info btn-sm',
          'data-intro' => Yii::t('intro', 'download-button')
        ]) ?>
        <? } ?>
    </p>
  <?php Pjax::begin(); ?>

  <?= GridView::widget(
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
          'template' => '{update} {delete} ',
          'header' => '<i class="fa fa-fw fa-gears"></i>',
          'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
          'contentOptions' => ['style' => 'min-width: 50px;text-align: center;vertical-align:middle;'],
					'buttons' => [
						'update' => function ($url, $model) use($canUpdate) {
							if(!$canUpdate) return false;
							$url = Url::toRoute(['supplier/update', 'id' => $model->id]);
							return Html::a('<span class="glyphicon  glyphicon-pencil" aria-hidden="true"></span>', $url, [
								'title' => Yii::t('app', 'Edit')
							]) . '&nbsp;';
						},
						'delete' => function ($url, $model) use($canDelete) {
							if(!$canDelete) return false;
							$url = Url::toRoute(['supplier/delete', 'id' => $model->id]);
							return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>', $url, [
								'title' => Yii::t('app', 'Delete'),
								'data' => [
									'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
									'method' => 'post',
								],
							]) . '&nbsp;';
						},
					],
					'visible' => $canUpdate || $canDelete
        ],
        [
          'attribute' => 'name',
          'value' => 'name',
          'contentOptions' => function ($model, $column) {
            return ['title' => $model->name, 'class' => 'td-nowrap', 'style' => 'max-width:200px'];
          }
        ],
        'duns',
        'alias',
        [
          'attribute' => 'country_code_id',
          'value' => 'countryCode.name',
          'filter' => $countries,
					'filterInputOptions' => [
						'class' => 'select2',
						'prompt' => Yii::t('app', '...'),
						'id' => null
					],
        ],
        'city',
        'address',
        'postal',
        'transit_time',
//        'country',
//        'country_code',
        'contact_name',
        'contact_position',
        'contact_email:email',
        'contact_phone',
        'contact_cellular',
      ],
    ]); ?>
  <?php Pjax::end(); ?>
</div>
