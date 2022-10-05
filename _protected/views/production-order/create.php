<?php
/* @var $this yii\web\View */
/* @var $model app\models\ProductionOrder */
/** @var TYPE_NAME $modelsToPrint */
/** @var TYPE_NAME $parts_withptnm */
/** @var TYPE_NAME $models */
/** @var TYPE_NAME $flocs */
/** @var TYPE_NAME $prev_shift */
$this->title = Yii::t('app', 'Create production');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Production order'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="production-order-create">
    <div class="panel panel-body panel-default">
      <?=$this->render('_form', [
        'model' => $model,
        'parts' => $parts,
        'parts_withptnm' => $parts_withptnm,
        'options' => $options,
        'models' => $models,
        'flocs' => $flocs,
        'prev_shift' => $prev_shift,
      ])?>
    </div>

  <?
  $queryParams = Yii::$app->request->queryParams;
  if(isset($queryParams['ProductionOrderSearch']['ids'])){
    ?>
      <div class="panel panel-default">
          <div class="panel-heading">
            <?=Yii::t('app', 'Created barcodes')?>
          </div>
          <div class="panel-body">
            <?=$this->render('print', [
              'model' => $modelsToPrint,
              'action' => 'create'
            ]);
            ?>
          </div>
      </div>

  <? } ?>

</div>
