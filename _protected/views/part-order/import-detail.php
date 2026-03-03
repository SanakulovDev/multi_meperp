<?php /** @noinspection ALL */
	use app\models\PartOrderDetailSearch;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\ContainerInvoice */
	/* @var $model app\models\Contract */
	/* @var $err_text */
	/* @var $insert_ok_text */
	/** @var TYPE_NAME $modelImport */
	$this->title = Yii::t('app', 'Add detail: {nameAttribute}', [
		'nameAttribute' => $model->order_no.'('.$model->contract->contract_no.')',
	]);
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Orders Supplier'), 'url' => ['index']];
?>
<div class="container-invoice-view">

	<?=$this->render(
		'__header',
		[
			'model' => $model,
			'err_text' => $err_text,
			'insert_ok_text' => $insert_ok_text,
		]
	);
	?>
	<hr>
	<!-- FILE IMPORT FORM -->
	<? $form = ActiveForm::begin(
		[
			'action' => ['import-detail'],
			'options' => [
				'enctype' => 'multipart/form-data',
			]
		]
	);
	?>
	<!--	--><? //=Html::input('hidden', 'id', $model->id)?>
	<?=Html::input('hidden', 'shu_id', $model->id)?>
	<div class="row">
		<div class="col-md-9">
			<?=$form->field($modelImport, 'fileImport')->fileInput()?>
		</div>
		<div class="col-md-3 pull-right text-right">
			<?=Html::a(Yii::t('app', 'btn-download-template'),'/public/Order-detail.xlsx');?>
			<br class="-text-height">
			<?=Html::submitButton(Yii::t('app', 'btn-upload').' '.Yii::t('app', 'Upload detail'), ['class' => 'btn btn-success btn-sm']);?>
		</div>
	</div>

	<?php ActiveForm::end() ?>
	<!-- FILE IMPORT FORM -->

	<?
		if(strlen(trim($err_text)) > 1){
			?>
			<div class='alert alert-danger'>
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
				<strong><?=Yii::t('app', 'Error').'!!!<br>'?></strong> <?=$err_text;?>
			</div>
			<?
		}
		if(strlen(trim($insert_ok_text)) > 1){
			?>
			<div class='alert alert-success'>
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
				<strong><?=Yii::t('app', 'Success').'!!! '?></strong> <?=$insert_ok_text;?>
			</div>
			<?
		}
	?>
	<hr>
	<?
		$queryParams = array_merge([], Yii::$app->request->queryParams);
		$queryParams["PartOrderDetailSearch"]["part_order_id"] = $model->id;
		$searchModel = new PartOrderDetailSearch();
		$dataProvider = $searchModel->search($queryParams);
		$dataProvider->pagination->pageSize = 1500000;
		$invoiceDetailModel = $model->invoiceDetails;
		echo $this->render(
			'../part-order-detail/index',
			[
				'dataProvider' => $dataProvider,
				'searchModel' => $searchModel,
			]
		);
	?>
</div>

