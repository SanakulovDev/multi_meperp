<?php
	use app\components\Helpers;
	use kartik\datetime\DateTimePicker;
	use yii\grid\GridView;
	use yii\helpers\Html;
	use yii\helpers\Url;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\Api */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="api-form">

	<?php $form = ActiveForm::begin(); ?>
	<div class="row">
		<div class="col-lg-3">
			<?=
				$form->field($model, 'inventory_date')->widget(DateTimePicker::classname(), [
					'pluginOptions' => [
						'language' => 'ru',
						'autoclose' => true,
						'format' => 'yyyy-mm-dd',
						'minView' => 'month',
						'maxView' => 'month',
					],
					'options' => [
						'autocomplete' => 'off'
					]
				])
			?>
		</div>
		<div class="col-lg-3">
			<?=
				$form->field($model, 'stock_date')->widget(DateTimePicker::classname(), [
					'pluginOptions' => [
						'language' => 'ru',
						'autoclose' => true,
						'format' => 'yyyy-mm-dd',
						'minView' => 'month',
						'maxView' => 'month',
					],
					'options' => [
						'autocomplete' => 'off'
					]
				])
			?>
		</div>
		<div class="col-lg-2">
			<div class="form-group">
				<label class="control-label" style="margin-top: 17px;"></label>
				<div class="">
					<? //= Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm']) ?>
					<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-primary btn-sm'])?>
				</div>
			</div>
		</div>
		<?php ActiveForm::end(); ?>
		<? if(!$model->isNewRecord){ ?>
			<div class="col-lg-4">
				<div class="form-group pull-right">
					<label class="control-label" style="margin-top: 17px;"></label>
					<div class="">
						<?=Html::a(Yii::t('app', 'btn-download-template'), '/public/inventory_detail_template.xlsx', ['class' => 'btn btn-info btn-sm', 'data-intro' => Yii::t('intro', 'btn-download-template')])?>
						<?=Html::a(Yii::t('app', 'btn-upload-txt'), ['inventory-detail/upload', 'api_id' => $model->id], ['class' => 'btn btn-warning btn-sm'])?>
						<?=Html::a(Yii::t('app', 'btn-add-part'), ['inventory-detail/create', 'api_id' => $model->id], ['class' => 'btn btn-success btn-sm'])?>
					</div>
				</div>
			</div>
		<? } ?>
	</div>


	<? if(!$model->isNewRecord){ ?>
		<div class="panel panel-default">
			<div class="panel-body">
				<?=
					GridView::widget([
						                 'dataProvider' => $dataProvider,
						                 'filterModel' => $searchModel,
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
						                 'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
						                 'options' => ['style' => 'overflow-x:scroll;clear:both'],
						                 'columns' => [
							                 [
								                 'class' => 'yii\grid\SerialColumn',
								                 'header' => '№',
								                 'headerOptions' => ['style' => 'width: 40px;text-align: center;color: #3c8dbc;'],
								                 'contentOptions' => ['style' => 'width: 40px;text-align: center;']
							                 ],
							                 [
								                 'class' => 'yii\grid\ActionColumn',
								                 'template' => '{update} {delete} ',
								                 'header' => '<i class="fa fa-fw fa-gears"></i>',
								                 'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
								                 'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
								                 'buttons' => [
									                 'update' => function($url, $model){
										                 $url = Url::toRoute(['inventory-detail/update', 'id' => $model->id]);
										                 return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url, [
												                 'title' => Yii::t('app', 'Edit')
											                 ]).'&nbsp;';
									                 },
									                 'delete' => function($url, $model){
										                 $url = Url::toRoute(['inventory-detail/delete', 'id' => $model->id]);
										                 return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>', $url, [
											                 'title' => Yii::t('app', 'Delete'),
											                 'data' => [
												                 'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
												                 'method' => 'post',
											                 ],
										                 ]);
									                 },
								                 ]
							                 ],
							                 //                    [
							                 //                        'attribute' => 'api_id',
							                 //                        'headerOptions' => ['style' => 'width:200px;text-align: center;vertical-align:middle;'],
							                 //                        'contentOptions' => ['style' => 'width:200px;text-align: center;vertical-align:middle;'],
							                 //                        'value' => function($model){
							                 //                            return $model->api->invinfo;
							                 //                        },
							                 //                    ],
							                 [
								                 'attribute' => 'part_id',
								                 'headerOptions' => ['style' => 'width:200px;text-align: left;vertical-align:middle;'],
								                 'contentOptions' => ['style' => 'width:200px;text-align: left;vertical-align:middle;'],
								                 'content' => function($model){
									                 return $model->part->part_no;
								                 },
								                 'filter' => Html::activeDropDownList($searchModel, 'part_id', $parts, ['class' => 'form-control select2', 'prompt' => '...']),
							                 ],
							                 [
								                 'attribute' => 'inventory_qty',
								                 'headerOptions' => ['style' => 'text-align: right;vertical-align:middle;'],
								                 'contentOptions' => ['style' => 'text-align: right;vertical-align:middle;'],
								                 'content' => function($model){
									                 return Helpers::formatRemoveDecimal($model->inventory_qty);
								                 },
							                 ],
							                 [
								                 'attribute' => 'stock_qty',
								                 'headerOptions' => ['style' => 'text-align: right;vertical-align:middle;'],
								                 'contentOptions' => ['style' => 'text-align: right;vertical-align:middle;'],
								                 'content' => function($model){
									                 return Helpers::formatRemoveDecimal($model->stock_qty);
								                 },
							                 ],
							                 [
								                 'attribute' => 'uom',
								                 'headerOptions' => ['style' => 'width:150px;text-align: left;vertical-align:middle;'],
								                 'contentOptions' => ['style' => 'width:150px;text-align: left;vertical-align:middle;'],
								                 'content' => function($model){
									                 return $model->part->unit->unit_value;
								                 },
								                 'filter' => Html::activeDropDownList($searchModel, 'uom', $uoms, ['class' => 'form-control select2', 'prompt' => '...']),
							                 ],
						                 ],
					                 ]);
				?>
			</div>
		</div>
	<? } ?>


</div>
