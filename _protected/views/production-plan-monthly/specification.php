<?php

use yii\helpers\Html;
?>
<div class="monthly-specification">
  <div class="row">
    <div class="col-md-6">
      <h4><?= Yii::t('app', 'Default')?></h4>
      <table class="table">
        <thead>
          <tr>
            <th><?= Yii::t('app', 'Part')?></th>
            <th><?= Yii::t('app', 'Quantity')?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($items1 as $key => $item): ?>
            <tr>
              <td><?=$item['part_no']?></td>
              <td><?=$item['quantity']?></td>
            </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    </div>
    <div class="col-md-6">
      <h4><?= Yii::t('app', 'Current status')?></h4>
      <table class="table">
        <thead>
          <tr>
            <th><?= Yii::t('app', 'Part')?></th>
            <th><?= Yii::t('app', 'Quantity')?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($items2 as $key => $item): ?>
            <tr>
              <td><?=$item['part_name']?></td>
              <td><?= round($item['quantity'], 2)?></td>
            </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    </div>
  </div>
</div>