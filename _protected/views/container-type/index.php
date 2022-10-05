<?php
/* @var $this yii\web\View */

$this->title = Yii::t('app', 'Container types');
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    table.container-type tr th, table.container-type tr td{
        text-align: center;
    }
</style>
<div class="row">
        <div class="col-md-6">
     
              <table class="table table-hover container-type">
                <tbody><tr>
                  <th>#</th>
                  <th><?=Yii::t('app', 'Code')?></th>
                  <th><?=Yii::t('app', 'Capacity')?> (m<sup>3</sup>)</th>
                  <th><?=Yii::t('app', 'Available load')?> (kg)</th>
                </tr>
                <?
                $i = 0;
                foreach ($data as $item) {
                    ?>
                <tr>
                  <td><?=++$i?></td>
                  <td><?=$item['name']?></td>
                  <td><?=$item['capacity']?></td>
                  <td><?=number_format($item['load'],0,'.',' ')?></td>
                </tr>
                <?
                }?>
              </tbody></table>
        </div>
      </div>
