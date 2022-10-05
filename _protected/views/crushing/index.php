<?php

use kartik\datetime\DateTimePicker;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CrushingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var TYPE_NAME $users */

$this->title = Yii::t('app', 'Shredding');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('crushing-update');
$canDelete = Yii::$app->user->can('crushing-delete');
?>
<div class="crushing-index">
	<div class="row" data-intro="<?= Yii::t('intro', 'Active_form') ?>">
		<?php $form = ActiveForm::begin(['action' => ['index'], 'method' => 'get',]); ?>
		<div class="col-md-3">
			<?= $form->field($searchModel, 'filter_from')->widget(DateTimePicker::classname(), [
				'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
				'layout' => '{picker}{input}{remove}',
				'removeButton' => ['position' => 'append'],
				'language' => 'ru',
				'pluginOptions' => [
					'autoclose' => true,
					'format' => 'yyyy-mm-dd',
					'startView' => 'month',
					'minView' => 'month',
					'maxView' => 'month',
				],
				'options' => [
					'autocomplete' => 'off',
					'placeholder' => 'С...',
					'class' => ' form-control input-sm'
				]
			])
				->label(false)
			?>
		</div>
		<div class="col-md-3">
			<?=
				$form->field($searchModel, 'filter_to')->widget(DateTimePicker::classname(), [
					'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
					'layout' => '{picker}{input}{remove}',
					'removeButton' => ['position' => 'append'],
					'language' => 'ru',
					'pluginOptions' => [
						'autoclose' => true,
						'format' => 'yyyy-mm-dd',
						'startView' => 'month',
						'minView' => 'month',
						'maxView' => 'month',
					],
					'options' => [
						'autocomplete' => 'off',
						'placeholder' => 'До...',
						'class' => ' form-control input-sm'
					]
				])
					->label(false)
			?>
		</div>
		<?= $form->field($searchModel, 'part_id')->hiddenInput()->label(false) ?>
		<?= $form->field($searchModel, 'qty')->hiddenInput()->label(false) ?>
		<?= $form->field($searchModel, 'is_processed')->hiddenInput()->label(false) ?>
		<?= $form->field($searchModel, 'created_by')->hiddenInput()->label(false) ?>
		<?= $form->field($searchModel, 'updated_by')->hiddenInput()->label(false) ?>
		<div class="col-md-3">
			<div class="form-group">
				<?= Html::submitButton(Yii::t('app', 'btn-show'), ['class' => 'btn btn-primary btn-sm']) ?>

				<? if (Yii::$app->user->can('crushing-xls')) { ?>
					<?= Html::a(Yii::t('app', 'btn-download'), ['xls', 'CrushingSearch' => ($_GET['CrushingSearch'] ?? null)], ['class' => 'btn btn-info btn-sm']); ?>
				<? } ?>
			</div>
		</div>
		<div class="col-md-3">
			<p class="pull-right">
				<? if (Yii::$app->user->can('crushing-create')) { ?>
					<?= Html::a(Yii::t('app', 'btn-create'), ['create'], ['class' => 'btn btn-success btn-sm']) ?>
				<? } ?>

			</p>
		</div>
		<?php ActiveForm::end(); ?>
	</div>

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
						'headerOptions' => ['style' => 'width: 50px;text-align: center;color: #3c8dbc;'],
						'contentOptions' => ['style' => 'width: 50px;text-align: center;']
					],
					[
						'class' => 'yii\grid\ActionColumn',
						'header' => '<i class="fa fa-fw fa-gears"></i>',
						'headerOptions' => ['style' => 'width: 70px;text-align: center;vertical-align:middle;color: #3c8dbc;'],
						'contentOptions' => ['style' => 'width: 70px;text-align: center;vertical-align:middle;'],
						'template' => '{delete}',
						'buttons' => [
							'delete' => function ($url, $model) use ($canDelete) {
								if (!$canDelete) return false;
								if ($model->is_processed != 0) return false;
								$url = Url::toRoute(['crushing/delete', 'id' => $model->id]);
								return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>', $url, [
									'title' => Yii::t('app', 'Delete'),
									'data' => [
										'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
										'method' => 'post',
									],
								]) . '&nbsp;';
							},
						],
						'visible' => $canDelete
					],
					[
						'attribute' => 'part_id',
						'headerOptions' => ['style' => 'vertical-align:middle;'],
						'contentOptions' => ['style' => 'vertical-align:middle;'],
						'filter' => Html::activeDropDownList($searchModel, 'part_id', $parts, ['class' => 'form-control select2', 'prompt' => '...']),
						'content' => function ($model) use ($canUpdate) {
							if ($model->is_processed == 0 and $canUpdate)
								return Html::a($model->part->partinfo, Url::toRoute(['crushing/update', 'id' => $model->id]), ['title' => $model->part->part_name]);
							else
								return '<span title="' . Yii::t('app', 'You cannot edit this record') . '">' . $model->part->partinfo . '</span>';
						}
					],
					[
						'attribute' => 'qty',
						'headerOptions' => ['style' => 'width: 100px;vertical-align:middle;text-align: right;'],
						'contentOptions' => ['style' => 'width: 100px;vertical-align:middle;text-align: right;'],
						'content' => function ($model) {
							return number_format($model->qtyFormatted);
						},
					],
					[
						'attribute' => 'is_processed',
						'headerOptions' => ['style' => 'width: 100px;vertical-align:middle;text-align: center;'],
						'contentOptions' => ['style' => 'width: 100px;vertical-align:middle;text-align: center;'],
						'filter' => [1 => Yii::t('app', 'Yes'), 0 => Yii::t('app', 'No')],
						'content' => function ($model) {
							if ($model->is_processed == 1) $color = 'green';
							else $color = 'muted';
							return '<b class="text-' . $color . '">' . $model->isProcessedText . '</b>';
						},
						'format' => 'html'
					],
					[
						'attribute' => 'created_by',
						'headerOptions' => ['style' => 'width: 150px;vertical-align:middle;'],
						'contentOptions' => ['style' => 'width: 150px;vertical-align:middle;'],
						'filter' => Html::activeDropDownList($searchModel, 'created_by', $users, ['class' => 'form-control select2', 'prompt' => '...']),
						'content' => function ($model) {
							return $model->createdBy->fullname;
						}
					],
					[
						'attribute' => 'created_at',
						'headerOptions' => ['style' => 'width: 150px;vertical-align:middle;text-align: center;'],
						'contentOptions' => ['style' => 'width: 150px;vertical-align:middle;text-align: center;'],
						'filter' => false,
						'content' => function ($model) {
							return $model->createdAtFormatted;
						}
					],
					[
						'attribute' => 'updated_by',
						'headerOptions' => ['style' => 'width: 150px;vertical-align:middle;'],
						'contentOptions' => ['style' => 'width: 150px;vertical-align:middle;'],
						'filter' => Html::activeDropDownList($searchModel, 'updated_by', $users, ['class' => 'form-control select2', 'prompt' => '...']),
						'content' => function ($model) {
							return $model->updatedBy->fullname ?? null;
						}
					],
					[
						'attribute' => 'updated_at',
						'headerOptions' => ['style' => 'width: 150px;vertical-align:middle;text-align: center;'],
						'contentOptions' => ['style' => 'width: 150px;vertical-align:middle;text-align: center;'],
						'filter' => false,
						'content' => function ($model) {
							return $model->updatedAtFormatted ?? null;
						}
					],
				],
			]
		); ?>


</div>