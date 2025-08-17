<?php
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InvoiceDetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('app', 'invoice details');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-detail-index">
  <?php Pjax::begin(); ?>
  <p class="pull-right">
    <?=Html::a(Yii::t('app', 'btn-create'), ['create'], ['class' => 'btn btn-primary btn-sm'])?>
  </p>

  <?=$this->render('__details',
    [
      'model' => $model,
      'dataProvider' => $dataProvider,
      'filterModel' => $searchModel,
      'searchModel' => $searchModel
    ]
  );?>

  <?php Pjax::end(); ?>
</div>
