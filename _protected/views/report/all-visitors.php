<?php
	use kartik\datetime\DateTimePicker;
	use yii\grid\GridView;
	use yii\helpers\Html;
	use yii\helpers\Url;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $searchModel app\models\VisitorSearch */
	/* @var $dataProvider yii\data\ActiveDataProvider */
	$this->title = Yii::t('app', 'All visitors');
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="visitor-index">


	<div class="downtime-search">

		<?php $form = ActiveForm::begin([
			                                'action' => ['all-visitors'],
			                                'method' => 'get',
		                                ]); ?>

		<div class="row">

			<div class="col-md-3">

				<?=$form->field($searchModel, 'filter_from')->widget(DateTimePicker::classname(), [
					'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
					'layout' => '{picker}{input}{remove}',
					'removeButton' => ['position' => 'append'],
					'language' => 'ru',
					'pluginOptions' => [
						'autoclose' => true,
						'format' => 'yyyy-mm-dd hh:ii'
					],
					'options' => [
						'autocomplete' => 'off'
					]
				])?>
			</div>
			<div class="col-md-3">
				<?=$form->field($searchModel, 'filter_to')->widget(DateTimePicker::classname(), [
					'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
					'layout' => '{picker}{input}{remove}',
					'removeButton' => ['position' => 'append'],
					'language' => 'ru',
					'pluginOptions' => [
						'autoclose' => true,
						'format' => 'yyyy-mm-dd hh:ii'
					],
					'options' => [
						'autocomplete' => 'off'
					]
				])?>
			</div>
			<?=$form->field($searchModel, 'user_id')->hiddenInput()->label(false)?>
			<?=$form->field($searchModel, 'user_ip')->hiddenInput()->label(false)?>
			<?=$form->field($searchModel, 'page')->hiddenInput()->label(false)?>
			<div class="col-md-3">
				<div class="form-group" style="padding-top: 10px;">

					<?=Html::submitButton(Yii::t('app', 'btn-show'), ['class' => 'btn btn-primary'])?>

				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group pull-right" style="padding-top: 10px;">
					<?=Html::a(Yii::t('app', 'btn-download'), ['xls-visitor', 'VisitorSearch' => $_GET['VisitorSearch'] ?? null], ['class' => 'btn btn-info '])?>
				</div>
			</div>
		</div>


		<?php ActiveForm::end(); ?>

	</div>


	<?=GridView::widget([
		                    'dataProvider' => $dataProvider,
		                    'filterModel' => $searchModel,
		                    'emptyText' => Yii::t('app', 'No results found.'),
		                    'options' => ['style' => 'overflow-x:scroll;'],
		                    'tableOptions' => ['style' => 'table-layout: fixed;', 'class' => 'table table-striped table-bordered'],
		                    'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
		                    'columns' => [
			                    [
				                    'class' => 'yii\grid\SerialColumn',
				                    'headerOptions' => ['style' => 'width: 30px;text-align: center;vertical-align:middle;color: #3c8dbc;'],
				                    'contentOptions' => ['style' => 'width: 30px;text-align: center;vertical-align:middle;'],
			                    ],
			                    [
				                    'class' => 'yii\grid\ActionColumn',
				                    'header' => Yii::t('app', 'Action'),
				                    'headerOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;color: #3c8dbc;'],
				                    'contentOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;'],
				                    'template' => '{view}',
				                    'buttons' => [
					                    'view' => function($url, $model){
						                    $url = Url::toRoute(['report/view-visitor', 'id' => $model->id]);
						                    return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>', $url, [
							                    'title' => Yii::t('yii', 'View')
						                    ]);
					                    },
				                    ],
			                    ],
			                    [
				                    'attribute' => 'user_id',
				                    'content' => function($model){
					                    return $model->user->fullname;
				                    },
			                    ],
			                    [
				                    'attribute' => 'page',
				                    'content' => function($model){
					                    return $model->pageroute;
				                    },
			                    ],
			                    'user_ip',
//			                    'user_browser',
//			                    'user_browser_version',
//			                    'user_platform',
//			                    'user_device_type',
			                    [
				                    'attribute' => 'visited_at',
				                    'filter' => false
			                    ],
		                    ],
	                    ]);?>
</div>
