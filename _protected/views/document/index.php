<?php

use app\models\DocumentType;
use app\models\Supplier;
use app\models\Warehouse;
use kartik\datetime\DateTimePicker;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DocumentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Document');
$this->params['breadcrumbs'][] = $this->title;

$canUpdate = Yii::$app->user->can('document-update');
$canDelete = Yii::$app->user->can('document-delete');
$canConfirm = Yii::$app->user->can('document-confirm');
$canPrint = Yii::$app->user->can('document-print');
$canHistory = Yii::$app->user->can('document-history');
$canUpdateAct = Yii::$app->user->can('document-update-act');
$canDeleteAct = Yii::$app->user->can('document-delete-act');
$canUpdateLocal = Yii::$app->user->can('document-update-local');
$canDeleteLocal = Yii::$app->user->can('document-delete-local');
$canUpdateLocalIssue = Yii::$app->user->can('document-update-local-issue');
$canDeleteLocalIssue = Yii::$app->user->can('document-delete-local-issue');
$canUpdateLocalKd = Yii::$app->user->can('document-update-local-kd');
$canDeleteLocalKd = Yii::$app->user->can('document-delete-local-kd');
$cansShopDisconfirm = Yii::$app->user->can('document-shop-disconfirm');


?>


<div class="document-index">


	<div class="downtime-search">


		<div class="row">
			<div class="col-md-12 pull-left">
				<div class="form-group">
					<? if (Yii::$app->user->can('document-create')) { ?>
						<?= Html::a(Yii::t('app', 'btn-create-doc'), ['create'], ['class' => 'btn btn-success  btn-sm']) ?>
					<? } ?>
					<? if (Yii::$app->user->can('document-create-local-issue')) { ?>
						<?= Html::a(Yii::t('app', 'btn-create-local-issue'), ['create-local-issue'], ['class' => 'btn btn-warning  btn-sm']) ?>
					<? } ?>
					<? if (Yii::$app->user->can('document-create-local')) { ?>
						<?= Html::a(Yii::t('app', 'btn-create-local'), ['create-local'], ['class' => 'btn btn-info  btn-sm']) ?>
					<? } ?>
					<? if (Yii::$app->user->can('document-create-local-kd')) { ?>
						<?= Html::a(Yii::t('app', 'btn-create-local-kd'), ['create-local-kd'], ['class' => 'btn btn-primary  btn-sm']) ?>
					<? } ?>
					<? if (Yii::$app->user->can('document-create-act')) { ?>
						<? if (Yii::$app->user->identity->act_access == 1) { ?>
							<?= Html::a(Yii::t('app', 'btn-create-act'), ['create-act'], ['class' => 'btn btn-danger  btn-sm']) ?>
						<? } ?>
					<? } ?>
          <?php if(Yii::$app->user->can('document-create-info')):?>
            <?= Html::a(Yii::t('app', 'btn-create-info'), ['create-info'], ['class' => 'btn btn-success  btn-sm']) ?>
          <?php endif;?>
				</div>
				<div class="form-group">
				<? if (Yii::$app->user->can('document-issue')) { ?>
						<?= Html::a(Yii::t('app', 'btn-issue'), ['issue'], ['class' => 'btn btn-success  btn-sm']) ?>
					<? } ?>
					<? if (Yii::$app->user->can('document-receipt-local-kd')) { ?>
						<?= Html::a(Yii::t('app', 'btn-receipt-local-kd'), ['receipt-local-kd'], ['class' => 'btn btn-primary  btn-sm']) ?>
					<? } ?>
					<? if (Yii::$app->user->can('document-receipt-local-con')) { ?>
						<?= Html::a(Yii::t('app', 'btn-receipt-local-con'), ['receipt-local-con'], ['class' => 'btn btn-info  btn-sm']) ?>
					<? } ?>
					<? if (Yii::$app->user->can('document-create-shop-consumption')) { ?>
						<?= Html::a(Yii::t('app', 'btn-create-shop-consumption'), ['create-shop-consumption-ver2'], ['class' => 'btn btn-success  btn-sm']) ?>
					<? } ?>
					<? if (Yii::$app->user->can('document-shop-confirm')) { ?>
						<?= Html::a(Yii::t('app', 'btn-shop-confirm'), ['shop-confirm-ver2'], ['class' => 'btn btn-info  btn-sm']) ?>
					<? } ?>

				</div>
			</div>
		</div>
		<div class="row">
			<?php
			$form = ActiveForm::begin([
				'action' => ['index'],
				'method' => 'get',
			]);
			?>

			<div class="col-md-3">
				<?= $form->field($searchModel, 'docnum')->textInput(['placeholder' => $searchModel->getAttributeLabel('docnum'), 'class' => ' form-control input-sm'])->label(false) ?>
			</div>
			<div class="col-md-3">

				<?=
					$form->field($searchModel, 'filter_from')->widget(DateTimePicker::classname(), [
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
			<?= $form->field($searchModel, 'document_type_id')->hiddenInput()->label(false) ?>
			<?= $form->field($searchModel, 'from_warehouse_id')->hiddenInput()->label(false) ?>
			<?= $form->field($searchModel, 'to_warehouse_id')->hiddenInput()->label(false) ?>
			<?= $form->field($searchModel, 'series')->hiddenInput()->label(false) ?>
			<?= $form->field($searchModel, 'status')->hiddenInput()->label(false) ?>
			<?= $form->field($searchModel, 'serial_number')->hiddenInput()->label(false) ?>
			<div class="col-md-3">
				<div class="form-group">
					<?= Html::submitButton(Yii::t('app', 'btn-show'), ['class' => 'btn btn-primary btn-sm']) ?>
					<? if (Yii::$app->user->can('document-xls')) { ?>
						<?= Html::a(Yii::t('app', 'btn-download'), ['xls', 'DocumentSearch' => ($_GET['DocumentSearch'] ?? null)], ['class' => 'btn btn-info btn-sm']); ?>
					<? } ?>
				</div>
			</div>
		</div>
		<?php ActiveForm::end(); ?>

	</div>


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
					'headerOptions' => ['style' => 'width: 40px;text-align: center;color: #3c8dbc;'],
					'contentOptions' => ['style' => 'width: 40px;text-align: center;']
				],
				[
					'class' => 'yii\grid\ActionColumn',
					'header' => '<i class="fa fa-fw fa-gears"></i>',
					'headerOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;color: #3c8dbc;'],
					'contentOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;'],
					'template' => '{update}{delete}{confirm}{print}{history}{update-act}{delete-act}{update-local}{delete-local}{update-local-issue}{delete-local-issue}{update-local-kd}{delete-local-kd}{shop-disconfirm}',
					'buttons' => [
						'update' => function ($url, $model) use ($canUpdate) {
							if (!$canUpdate) return false;
							if (!empty($model->serial_number)) return false;
							if ($model->document_type_id != 2 or $model->isLocal or $model->isLocalIssue) return false;
							if ($model->status == 1) return false;
					
							
							if (!in_array($model->from_warehouse_id, Yii::$app->user->identity->warehouseIds))
								return false;
							
							
							$url = Url::toRoute(['document/update', 'id' => $model->id]);
							return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url, [
								'title' => Yii::t('app', 'Edit')
							]) . '&nbsp;';
						},
						'delete' => function ($url, $model) use ($canDelete) {
							if (!$canDelete) return false;

							if (!empty($model->serial_number) and Yii::$app->user->identity->roleName != 'counter' )  return false;
							
							if ($model->document_type_id != 2 or $model->isLocal or $model->isLocalIssue) return false; 

							if ($model->status == 1) return false;

							
							if (!in_array($model->from_warehouse_id, Yii::$app->user->identity->warehouseIds))
								return false;
							

							if (empty($model->serial_number)) {
								$route = ['document/delete', 'id' => $model->id];
							} else {
								$route = ['document/delete-shop-consumption', 'id' => $model->id, 'view' => 'index'];
							};
							$url = Url::toRoute($route);
							return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>', $url, [
								'title' => Yii::t('app', 'Delete'),
								'data' => [
									'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
									'method' => 'post',
								],
							]) . '&nbsp;';
						},

						'confirm' => function ($url, $model) use ($canConfirm) {
							if (!$canConfirm) return false;
							if (!empty($model->serial_number)) return false;

							if (
								!in_array($model->document_type_id, [1, 2]) or
								$model->isLocalkd or
								$model->isLocal or
								$model->isLocalIssue or
								$model->isProdIssue
							)
								return false;

							if (Yii::$app->user->identity->roleName == 'mrp'){
								if (!in_array($model->to_warehouse_id, Yii::$app->user->identity->warehouseIds))
									return false;
							}

							if ($model->to_warehouse_id == Yii::$app->params['logxWhId'] and Yii::$app->params['logxWhId'] != Yii::$app->params['kdWhId'])
								return false;

							$url = Url::toRoute(['document/confirm', 'id' => $model->id]);

							if ($model->status == 1) {
								$icon = 'remove';
								$title = Yii::t('app', 'Cancel');
							} else {
								$icon = 'ok';
								$title = Yii::t('app', 'Confirm');
							}
							return Html::a('<span class="glyphicon glyphicon-' . $icon . '" aria-hidden="true"></span>', $url, [
								'title' => $title,
								'data' => [
									'confirm' => Yii::t('app', 'Are you sure?'),
									'method' => 'post',
								],
							]) . '&nbsp;';
						},

						'shop-disconfirm' => function ($url, $model) use ($cansShopDisconfirm) {
							if (!$cansShopDisconfirm) return false;
							if (empty($model->serial_number))
								return false;
							if ($model->status != 1)
								return false;
							if ($model->document_type_id != 2 or $model->isLocalkd or $model->isLocal or $model->isLocalIssue or $model->isProdIssue)
								return false;

							if (Yii::$app->user->identity->roleName == 'counter'){
								if (!in_array($model->to_warehouse_id, Yii::$app->user->identity->warehouseIds))
									return false;
							}

							$url = Url::toRoute(['document/shop-disconfirm', 'id' => $model->id, 'view' => 'index']);
							$icon = 'remove';
							$title = Yii::t('app', 'Cancel');
							return Html::a('<span class="glyphicon glyphicon-' . $icon . '" aria-hidden="true"></span>', $url, [
								'title' => $title,
								'data' => [
									'confirm' => Yii::t('app', 'Are you sure?'),
									'method' => 'post',
								],
							]) . '&nbsp;';
						},
						'print' => function ($url, $model) use ($canPrint) {
							if (!$canPrint) return false;
							
							if (Yii::$app->user->identity->roleName == 'mrp'){
								if (!in_array($model->from_warehouse_id, Yii::$app->user->identity->warehouseIds))
									return false;
							}

							$url = Url::toRoute(['document/print', 'id' => $model->id]);
							return Html::a('<span class="fa fa-print" aria-hidden="true"></span>', $url, [
								'title' => Yii::t('app', 'Print')
							]) . '&nbsp;';
						},
						'history' => function ($url, $model) use ($canHistory) {
							if (!$canHistory) return false;
							$url = Url::toRoute(['history-document/index', 'HistoryDocumentSearch[document_id]' => $model->id]);
							return Html::a('<span class="fa fa-clock-o" aria-hidden="true"></span>', $url, [
								'title' => Yii::t('app', 'History')
							]) . '&nbsp;';
						},
						// mfu buttons
						'update-act' => function ($url, $model) use ($canUpdateAct) {

							if (!$canUpdateAct) return false;

							if ($model->document_type_id != 3) return false;

							if (Yii::$app->user->identity->roleName == 'mrp')
								if 	(Yii::$app->user->identity->act_access == 1)
									return false;

							$url = Url::toRoute(['document/update-act', 'id' => $model->id]);

							return Html::a('<span class="glyphicon glyphicon-pencil text-danger" aria-hidden="true"></span>', $url, [
								'title' => Yii::t('app', 'Edit')
							]) . '&nbsp;';

						},
						'delete-act' => function ($url, $model) use ($canDeleteAct) {
							if (!$canDeleteAct) return false;
							if ($model->document_type_id != 3) return false;

							if (Yii::$app->user->identity->roleName == 'mrp')
								if 	(Yii::$app->user->identity->act_access == 1)
									return false;
							$url = Url::toRoute(['document/delete-act', 'id' => $model->id]);
							return Html::a('<span class="glyphicon glyphicon-trash text-danger" aria-hidden="true"></span>', $url, [
								'title' => Yii::t('app', 'Delete'),
								'data' => [
									'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
									'method' => 'post',
								],
							]) . '&nbsp;';
						},
						// local receipt
						'update-local' => function ($url, $model) use ($canUpdateLocal) {
							if (!$canUpdateLocal) return false;
							if (!$model->isLocal or $model->isProdIssue) return false;

							if (Yii::$app->user->identity->roleName == 'mrp'){
								if (!in_array($model->to_warehouse_id, Yii::$app->user->identity->warehouseIds))
									return false;
							}

							$url = Url::toRoute(['document/update-local', 'id' => $model->id]);
							return Html::a('<span class="glyphicon glyphicon-pencil text-warning" aria-hidden="true"></span>', $url, [
								'title' => Yii::t('app', 'Edit')
							]) . '&nbsp;';
						},
						'delete-local' => function ($url, $model) use ($canDeleteLocal) {
							if (!$canDeleteLocal) return false;
							if (!$model->isLocal or $model->isProdIssue)
								return false;
							if (Yii::$app->user->identity->roleName == 'mrp'){
								if (!in_array($model->to_warehouse_id, Yii::$app->user->identity->warehouseIds))
									return false;
							}
							$url = Url::toRoute(['document/delete-local', 'id' => $model->id]);
							return Html::a('<span class="glyphicon glyphicon-trash text-warning" aria-hidden="true"></span>', $url, [
								'title' => Yii::t('app', 'Delete'),
								'data' => [
									'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
									'method' => 'post',
								],
							]) . '&nbsp;';
						},
						// local issue
						'update-local-issue' => function ($url, $model) use ($canUpdateLocalIssue) {
							if (!$canUpdateLocalIssue) return false;
							if (!$model->isLocalIssue or $model->isProdIssue)
								return false;
							if (Yii::$app->user->identity->roleName == 'mrp'){
								if (!in_array($model->from_warehouse_id, Yii::$app->user->identity->warehouseIds))
									return false;
							}
							$url = Url::toRoute(['document/update-local-issue', 'id' => $model->id]);
							return Html::a('<span class="glyphicon glyphicon-pencil text-warning" aria-hidden="true"></span>', $url, [
								'title' => Yii::t('app', 'Edit')
							]) . '&nbsp;';
						},
						'delete-local-issue' => function ($url, $model) use ($canDeleteLocalIssue) {
							if (!$canDeleteLocalIssue) return false;
							if (!$model->isLocalIssue or $model->isProdIssue)
								return false;
							if (Yii::$app->user->identity->roleName == 'mrp'){
								if (!in_array($model->from_warehouse_id, Yii::$app->user->identity->warehouseIds))
									return false;
							}
							$url = Url::toRoute(['document/delete-local-issue', 'id' => $model->id]);
							return Html::a('<span class="glyphicon glyphicon-trash text-warning" aria-hidden="true"></span>', $url, [
								'title' => Yii::t('app', 'Delete'),
								'data' => [
									'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
									'method' => 'post',
								],
							]) . '&nbsp;';
						},
						// local receipt kd
						'update-local-kd' => function ($url, $model) use ($canUpdateLocalKd) {
							if (!$canUpdateLocalKd) return false;
							if (!$model->isLocalkd or $model->isProdIssue)
								return false;
							if (Yii::$app->user->identity->roleName == 'mrp'){
								if (!in_array($model->to_warehouse_id, Yii::$app->user->identity->warehouseIds))
									return false;
							}
							$url = Url::toRoute(['document/update-local-kd', 'id' => $model->id]);
							return Html::a('<span class="glyphicon glyphicon-pencil text-warning" aria-hidden="true"></span>', $url, [
								'title' => Yii::t('app', 'Edit')
							]) . '&nbsp;';
						},
						'delete-local-kd' => function ($url, $model) use ($canDeleteLocalKd) {
							if (!$canDeleteLocalKd) return false;
							if (!$model->isLocalkd or $model->isProdIssue) return false;
							if (Yii::$app->user->identity->roleName == 'mrp'){
								if (!in_array($model->to_warehouse_id, Yii::$app->user->identity->warehouseIds))
									return false;
							}
							$url = Url::toRoute(['document/delete-local-kd', 'id' => $model->id]);
							return Html::a('<span class="glyphicon glyphicon-trash text-warning" aria-hidden="true"></span>', $url, [
								'title' => Yii::t('app', 'Delete'),
								'data' => [
									'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
									'method' => 'post',
								],
							]) . '&nbsp;';
						},
					],
					'visible' => $canUpdate || $canDelete || $canConfirm || $canPrint || $canHistory || $canUpdateAct || $canDeleteAct || $canUpdateLocal || $canDeleteLocal || $canUpdateLocalIssue || $canDeleteLocalIssue || $canUpdateLocalKd || $canDeleteLocalKd || $cansShopDisconfirm
				],
				[
					'attribute' => 'docnum',
					'content' => function ($model) {
						return Html::a($model->docnum, Url::toRoute(['document/view', 'id' => $model->id]));
					},
				],
				[
					'attribute' => 'docdate',
					'content' => function ($model) {
						return date("d.m.Y", strtotime($model->docdate));
					},
				],
				[
					'attribute' => 'serial_number',
					'headerOptions' => ['style' => 'width: 200px;text-align: center;vertical-align:middle;'],
					'contentOptions' => ['style' => 'width: 200px;text-align: center;vertical-align:middle;'],
					'visible' => (in_array(Yii::$app->user->identity->roleName, ['mrpc', 'counter', 'admin', 'superadmin']))
				],
				[
					'attribute' => 'document_type_id',
					'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
					'contentOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
					'content' => function ($model) {
						return $model->documentType->name;
					},
					'filter' => yii\helpers\ArrayHelper::map(DocumentType::find()->all(), 'id', 'name')
				],
				[
					'attribute' => 'from_warehouse_id',
					'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
					'contentOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
					'content' => function ($model) {
						return $model->fromWarehouse->name;
					},
					'filter' => Html::activeDropDownList($searchModel, 'from_warehouse_id', yii\helpers\ArrayHelper::map(Warehouse::find()->all(), 'id', 'name'), ['class' => 'form-control select2', 'prompt' => '...']),
				],
				[
					'attribute' => 'to_warehouse_id',
					'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
					'contentOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
					'content' => function ($model) {
						return $model->toWarehouse->name;
					},
					'filter' => Html::activeDropDownList($searchModel, 'to_warehouse_id', yii\helpers\ArrayHelper::map(Warehouse::find()->all(), 'id', 'name'), ['class' => 'form-control select2', 'prompt' => '...']),
				],
				[
					'attribute' => 'status',
					'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
					'contentOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
					'content' => function ($model) {
						return '<strong class="txt-' . (($model->status == 1) ? 'primary' : 'warning') . '">' . $model->statusName . '</strong>';
					},
					'filter' => [0 => Yii::t('app', 'Pending'), 1 => Yii::t('app', 'Confirmed')]
				],
				[
					'attribute' => 'supplier_id',
					'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
					'contentOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
					'content' => function ($model) {
						return $model->supplier ? $model->supplier->name : '';
					},
					'filter' => Html::activeDropDownList($searchModel, 'supplier_id', yii\helpers\ArrayHelper::map(Supplier::find()->all(), 'id', 'name'), ['class' => 'form-control select2', 'prompt' => '...']),
				],
				[
					'attribute' => 'action',
					'headerOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;color:#3c8dbc;'],
					'contentOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;'],
					'content' => function ($model) {
						return '<b class="text-' . (($model->actionStatus == 1) ? 'aqua' : 'green') . '">
                                ' . $model->actionName . '
                            </b>';
					},
					'filter' => [1 => Yii::t('app', 'Receipt'), 0 => Yii::t('app', 'Issue')],
					'visible' => (Yii::$app->user->identity->roleName == 'mrp' || Yii::$app->user->identity->roleName == 'mrpc')
				],
				[
					'attribute' => 'adj',
					'header' => Yii::t('app', 'Adjust-t'),
					'headerOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;color:#3c8dbc;'],
					'contentOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;'],
					'content' => function ($model) {
						if ($model->document_type_id != 3)
							return false;
						return '<b class="text-' . (($model->from_warehouse_id == 99) ? 'aqua' : 'green') . '">
                                ' . $model->adjNameForList . '
                            </b>';
					},
					'filter' => [1 => Yii::t('app', 'Receipt'), 0 => Yii::t('app', 'Issue')],
					'visible' => (in_array(Yii::$app->user->identity->roleName, ['mfu', 'admin', 'superadmin']))
				],
				[
					'attribute' => 'comment',
					'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
					'contentOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
					// 'content' => function($model){
					// 	return $model->supplier->name;
					// },
					// 'filter' => Html::activeDropDownList($searchModel, 'supplier_id',yii\helpers\ArrayHelper::map(Supplier::find()->all(), 'id', 'name'), ['class' => 'form-control select2', 'prompt' => '...']),
				],
			],
		]
	);
?>
</div>