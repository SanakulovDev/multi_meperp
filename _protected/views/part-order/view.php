<?php
	use app\models\Part;
	use app\models\PartOrderDetailSearch;
	use yii\helpers\ArrayHelper;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\ContainerInvoice */
	$this->title = Yii::t('app', 'Order no').': '.$model->order_no;
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Orders Supplier'), 'url' => ['index']];
	//	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="part-order-view">
  <?php $form = ActiveForm::begin(['action' => ['import-detail']]); ?>
    <div class="row ">
        <div class="col-lg-12">
            <div class="pull-right">
              <?php echo Html::input('hidden', 'shu_id', $model->id);
              if (empty($model->document_id)) {
                echo Html::a(Yii::t('app', 'btn-update'), ['update', 'id' => $model->id],
                  [
                    'class' => 'btn btn-primary btn-sm',
                    'style' => 'margin-right: 5px',
                    'data-intro' => Yii::t('intro', 'update-record')
                  ]);
                if (Yii::$app->user->can('part-order-import-detail'))
                  echo Html::submitButton(Yii::t('app', 'btn-upload-file') . " " . Yii::t('app', 'Upload detail'),
                    [
                      'class' => 'btn btn-success btn-sm',
                      'data-intro' => Yii::t('intro', 'upload-file-button')
                    ]);
              } ?>
              <?php
              if (Yii::$app->user->can('part-order-to-xlsx'))
                echo Html::a(Yii::t('app', 'btn-download'), ['to-xlsx', 'id' => $model->id],
                  [
                    'class' => 'btn btn-info btn-sm',
                    'data-intro' => Yii::t('intro', 'download-button')
                  ]);
              ?>
            </div>
        </div>
    </div>
  <?php ActiveForm::end(); ?>
  <?
  echo $this->render('__header', ['model' => $model]);
  echo $this->render(
    '../part-order-detail/_create_form',
    [
      'model' => $model,
    ]
  );
  ?>
    <br>
  <? //
  $param = Yii::$app->request->queryParams;
  $searchModel = new PartOrderDetailSearch(['part_order_id' => $param['id']]);
  $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
  // $dataProvider->setPagination(['pageSize' => 10]);
  $parts = ArrayHelper::map(Part::find()->where(['status' => Part::STATUS_ACTIVE])->all(), 'id', 'partinfo');
  echo $this->render(
    '../part-order-detail/index',
    [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel,
      'parts' => $parts,
    ]
  );
  ?>
  <div class="row">
    <div class="col-lg-12 text-right my-2">
      <button class="btn btn-success btn-sm" onclick="onNext()">Далее</button>
    </div>
  </div>
</div>

<div id="multistepsform">
  <!-- progressbar -->
  <ul id="progressbar">
    <li>Создание контракта</li>
    <li class="active" >Создание заказа</li>
    <li>Создание счёт фактуры</li>
  </ul>
</div>
<style>
#multistepsform {
	 width: 640px;
	 margin: 50px auto;
	 text-align: center;
	 position: relative;
}
 #multistepsform #progressbar {
	 margin-bottom: 30px;
	 overflow: hidden;
	 counter-reset: step;
}
 #multistepsform #progressbar li {
	 list-style-type: none;
	 color: #679b9b;
	 text-transform: uppercase;
	 font-size: 12px;
	 width: 33.33%;
	 float: left;
	 position: relative;
}
 #multistepsform #progressbar li:before {
	 content: counter(step);
	 counter-increment: step;
	 width: 45px;
	 line-height: 20px;
	 display: block;
	 font-size: 10px;
	 color: #fff;
	 background: #ff9a76;
	 border-radius: 3px;
	 margin: 0 auto 5px auto;
}
 #multistepsform #progressbar li:after {
	 content: "";
	 width: 100%;
	 height: 2px;
	 background: #ff9a76;
	 position: absolute;
	 left: -50%;
	 top: 9px;
	 z-index: -1;
}
 #multistepsform #progressbar li:first-child:after {
	 content: none;
}
 #multistepsform #progressbar li.active {
	 color: #00a65a;
}
 #multistepsform #progressbar li.active:before, #multistepsform #progressbar li.active:after {
    background-color: #00a65a;
	 color: white;
}
</style>

<?php
	$add_item = <<< JS
	function onNext () {
		window.location.href = "/container-invoice/create?nomer_order=" + $model->id
	}
JS;
	$this->registerJs($add_item, yii\web\View::POS_END);
?>
