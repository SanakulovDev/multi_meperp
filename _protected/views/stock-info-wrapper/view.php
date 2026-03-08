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

    <div class="row">
      <div class="col-md-6">
        <table class="table table-bordered">
          <tr>
            <th><?= Yii::t('app', 'Quantity') ?> (kg)</th>
            <td><?= $model->qty ?></td>
          </tr>
          <tr>
            <th><?= Yii::t('app', 'Quantity') ?> (m)</th>
            <td><?= $model->qty_meter !== null ? number_format($model->qty_meter, 4) : '-' ?></td>
          </tr>
        </table>
      </div>
    </div>

    <?php if($model->stockInfos):?>
        <table id="w0" class="table table-striped table-bordered detail-view">
          <thead>
            <tr>
              <th class="text-center"><?= Yii::t('app', '#')?></th>
              <th class="text-center"><?= Yii::t('app', 'Part')?></th>
              <th class="text-center"><?= Yii::t('app', 'Quantity')?></th>
              <?php if($model->stockInfos):?>
                <?php 
                $count1 = $model->countPOrder1($model->id); 
                $count2 = $model->countPOrder2($model->id); 
                ?>
                <?php if($count1 > 0):?>
                  <?php for($i= 0; $i <$count1; $i++):?>
                      <th colspan="2" class="text-center"><?= Yii::t('app', 'Fakt')?>-<?= $i+1?></th>
                  <?php endfor;?>
                <?php endif;?>
                <?php if($count2 > 0):?>
                  <?php for($i= 0; $i <$count2; $i++):?>
                      <th class="text-center"><?= Yii::t('app', 'Mix quantity')?>-<?= $i+1?></th>
                  <?php endfor;?>
                <?php endif;?>


              <?php endif;?>
              <th class="text-center"><?= Yii::t('app', 'Quantity')?></th>

            </tr>
          </thead>
          <tbody>
            <!--  -->
          <?php foreach($model->stockInfos as $key => $item):?>
              <tr>
                <td class="text-center"><?= $key+1?></td>
                <td><?= $item->part?$item->part->partinfo:' --- '?></td>
                <td class="text-center"><?= $item->qty?></td>
                <?php if($item->subs):?>
                  <?php foreach($item->subs as $i => $sub):?>
                    <?php if(in_array($sub->status, [1])):?>
                      <td class="text-center"><?= $sub->qty?></td>
                      <td class="text-center"><?= $sub->percent?>%</td>
                    <?php endif;?>
                  <?php endforeach;?>
                  
                  <?php foreach($item->subs as $i => $sub):?>
                    <?php if(in_array($sub->status, [2])):?>
                      <td class="text-center"><?= $sub->qty?></td>
                    <?php endif;?>
                  <?php endforeach;?>

                <?php endif;?>
                <td class="text-center"><?= $item->old_qty?></td>

              </tr>
              <?php endforeach;?>
            </tbody>
        </table>
    <?php endif;?>

</div>
