<?php
	use app\models\GtdInvoiceSearch;
	use yii\helpers\Html;
	use yii\web\YiiAsset;

	/* @var $this yii\web\View */
	/* @var $model app\models\Gtd */
	$this->title = $model->gtd_no;
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customs declaration'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
	YiiAsset::register($this);
?>
<div class="gtd-view">
	<div class="pull-right">
		<?=Html::a(Yii::t('app', 'btn-update'), ['gtd/update', 'id' => $model->id],
		           [
			           'class' => 'btn btn-success btn-sm m-r',
			           'data-intro' => Yii::t('intro', 'update-button')
		           ]
		)?>

		<?=Html::a(Yii::t('app', 'btn-download'), ['../gtd-invoice/xls', 'GtdInvoiceSearch' => ($_GET['GtdInvoiceSearch'] ?? null)],
		           [
			           'class' => 'btn btn-info btn-sm m-r pjax_download_btn',
			           'data-intro' => Yii::t('intro', 'download-button')
		           ]
		)?>
	</div>
	<?=$this->render(
		'__header', ['model' => $model ?? null,]
	)
	?>
	<hr>
	<?
		$param = Yii::$app->request->queryParams;
		$searchModel = new GtdInvoiceSearch(['gtd_id' => $param['id']]);
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		echo $this->render(
			'../gtd-invoice/__details',
			[
				'dataProvider' => $dataProvider,
				'searchModel' => $searchModel,
			]
		);
	?>
</div>


<?
	$view_script = <<< JS
	$(document).ready(function() {	 
	  
	  $(document).on('click', '.pjax_download_btn', function (event) {
			event.preventDefault();
			let url = window.location.href;
			window.location = url.replace('/view','-invoice/xls');
		});
	  
	});
JS;
	$this->registerJs($view_script, yii\web\View::POS_END);
?>


