<?php
/* @var $this yii\web\View */
/* @var $model app\models\FgInvoice */
/* @var $errorlist app\controllers\FgInvoiceController */
//$this->title = Yii::t('app', 'Create FG Invoice');
$this->title = Yii::t('app', 'Create FG Invoice TTN');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'FG Invoice'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fg-invoice-create">
  <?=
  $this->render('_form', [
    'model' => $model,
    'errorlist' => $errorlist ?? null,
  ])?>
</div>
