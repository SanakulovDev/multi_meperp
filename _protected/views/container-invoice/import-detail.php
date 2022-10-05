<?php
	use app\models\InvoiceDetailSearch;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\ContainerInvoice */
	/** @var TYPE_NAME $errorlist */
	/** @var TYPE_NAME $modelImport */
	/** @var TYPE_NAME $err_text */
	/** @var TYPE_NAME $insert_ok_text */
	$this->title = Yii::t('app', 'Add detail: {nameAttribute}', [
		'nameAttribute' => $model->invoice->invoice_no.'('.$model->container->container_no.')',
	]);
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Container Invoices'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->invoice->invoice_no.'('.$model->container->container_no.')', 'url' => ['view', 'id' => $model->id]];
?>
<div class="container-invoice-view">
	<?
		if(count($errorlist) > 0){ ?>
		<div class="alert alert-danger alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-ban"></i> <?=Yii::t('app', 'Correct the following errors.')?></h4>
			<?
				foreach($errorlist as $key => $errList){
					if(!in_array($key, ['no_item', 'cant_delete']))
						echo '<b>'.$key.':</b><br/>';
					foreach($errList as $err){
						foreach($err as $e){
							echo ' - '.Yii::t('app', $e).'<br/>';
						}
					}
					echo "<br/>";
				}
			?>
		</div>
	<? } ?>
	<?=
		$this->render(
			'cont_inv_header',
			['model' => $model,
			 'err_text' => $err_text,
			 'insert_ok_text' => $insert_ok_text
			]
		);
	?>

	<hr>
	<!-- EXAMPLE FORM IMPORT -->
	<? $form = ActiveForm::begin(
		[
			// 'method'  => 'get',
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
			<?=Html::a(Yii::t('app', 'btn-download-template'),
			           '/public/InvoiceContainerDetail.xlsx')
			?>
			<br class="-text-height">
			<?=Html::submitButton(Yii::t('app', 'btn-upload').' '.
			                      Yii::t('app', 'Upload detail'),
			                      ['class' => 'btn btn-success btn-sm']);?>
		</div>
	</div>

	<?php ActiveForm::end() ?>
	<!-- EXAMPLE FORM IMPORT -->

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
		$queryParams["InvoiceDetailSearch"]["cont_inv_id"] = $_POST['shu_id'];
		$searchModel = new InvoiceDetailSearch();
		$dataProvider = $searchModel->search($queryParams);
		$dataProvider->pagination->pageSize = 1500000;
		$invoiceDetailModel = $model->invoiceDetails;
		echo $this->render(
			'../invoice-detail/__details',
			[
				'dataProvider' => $dataProvider,
				'searchModel' => $searchModel,
			]
		);
	?>
</div>

