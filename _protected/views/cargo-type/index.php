<?php
/* @var $this yii\web\View */

$this->title = Yii::t('app', 'Cargo types');
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    /* table.cargo-type tr th, table.cargo-type tr td{
        text-align: center;
    } */
</style>
<div class="row">
        <div class="col-md-6">
     
              <table class="table table-hover cargo-type">
                <tbody><tr>
                  <th style="width: 50px;">#</th>
                  <th style="width: 180px;"><?=Yii::t('app', 'Name')?></th>
                  <th><?=Yii::t('app', 'Description')?></th>
                </tr>
                <?
                $i = 0;
                foreach ($data as $item) {
                    ?>
                <tr>
                  <td><?=++$i?></td>
                  <td><?=$item['name']?></td>
                  <td><?=$item['desc']?></td>
                </tr>
                <?
                }?>
              </tbody></table>
        </div>
      </div>
