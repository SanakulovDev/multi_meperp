<?php
/* @var $this yii\web\View */
/* @var $model app\models\InvoiceDetail */
$this->title = Yii::t('app', 'Create invoice detail');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'invoice details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-detail-create">
  <?=$this->render('_form', [
    'model' => $model,
  ])?>

</div>
