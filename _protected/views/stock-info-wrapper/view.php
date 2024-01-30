<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\StockInfoWrapper */

$this->title = $model->part?$model->part->partinfo:$model->code;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stock Info'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->part?$model->part->partinfo:$this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="stock-info-wrapper-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if($model->stockInfos):?>
        <table id="w0" class="table table-striped table-bordered detail-view">
          <thead>
            <tr>
              <th class="text-center"><?= Yii::t('app', '#')?></th>
              <th class="text-center"><?= Yii::t('app', 'Part')?></th>
              <th class="text-center"><?= Yii::t('app', 'Quantity')?></th>
              <?php if($model->stockInfos):?>
                <?php $count = $model->countPOrder($model->id);?>
                <?php if($count > 0):?>
                  <?php for($i= 0; $i <$count; $i++):?>
                      <th colspan="2" class="text-center"><?= Yii::t('app', 'Fakt')?>-<?= $i+1?></th>
                  <?php endfor;?>
                <?php endif;?>
              <?php endif;?>
            </tr>
          </thead>
          <tbody>
          <?php foreach($model->stockInfos as $key => $item):?>
              <tr>
                <td class="text-center"><?= $key+1?></td>
                <td><?= $item->part?$item->part->partinfo:' --- '?></td>
                <td class="text-center"><?= $item->qty?></td>
                <?php //vd($item->subs[0]->qty);?>
                <?php if($item->subs):?>
                  <?php foreach($item->subs as $i => $sub):?>
                    <?php if($sub->status == 1):?>
                      <td class="text-center"><?= $sub->qty?></td>
                      <td class="text-center"><?= $sub->percent?>%</td>
                    <?php endif;?>
                  <?php endforeach;?>
                <?php endif;?>

              </tr>
              <?php endforeach;?>
            </tbody>
        </table>
    <?php endif;?>

</div>
