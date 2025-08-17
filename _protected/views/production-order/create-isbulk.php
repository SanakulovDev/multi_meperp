<?php
/* @var $this yii\web\View */
/* @var $model app\models\ProductionOrder */
/** @var TYPE_NAME $modelsToPrint */
$this->title = Yii::t('app', 'Create Production order(sticker)');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Production order'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="production-order-create">
    <div class="panel panel-body panel-default">
      <?=$this->render('_form-isbulk', [
        'model' => $model,
        'parts' => $parts,
        'options' => $options
      ])?>
    </div>

  <?
  $queryParams = Yii::$app->request->queryParams;
  if (isset($queryParams['ProductionOrderSearch']['ids'])){
    ?>
      <div class="panel panel-default">
          <div class="panel-heading">
            <?=Yii::t('app', 'Created barcodes')?>
          </div>
          <div class="panel-body">
            <?=$this->render('print-isbulk', [
              'modelsToPrint' => $modelsToPrint??null,
              'action' => 'create-isbulk'
            ]);
            ?>
          </div>
      </div>

  <? } ?>

</div>
