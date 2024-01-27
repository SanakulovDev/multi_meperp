<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\StockInfoWrapper */

$this->title = $model->code;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stock Info Wrappers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="stock-info-wrapper-view">

    <h1><?= Html::encode($model->code) ?></h1>

    <?php if($model->stockInfos):?>
        <table id="w0" class="table table-striped table-bordered detail-view">
          <thead>
            <tr>
              <th class="text-center"><?= Yii::t('app', '#')?></th>
              <th><?= Yii::t('app', 'Part')?></th>
              <th><?= Yii::t('app', 'Quantity')?></th>
            </tr>
          </thead>
          <?php foreach($model->stockInfos as $key => $item):?>
            <tbody>
              <tr>
                <th class="text-center"><?= $key+1?></th>
                <th><?= $item->part?$item->part->partinfo:' --- '?></th>
                <th><?= $item->qty?></th>
              </tr>
            </tbody>
          <?php endforeach;?>
        </table>
    <?php endif;?>

</div>
