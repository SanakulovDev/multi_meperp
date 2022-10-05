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
</div>
