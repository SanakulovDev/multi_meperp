<?php
use yii\helpers\Html;
?>
<?php ob_start(); ?>
    th, td{
        border: 2px solid black!important;
        text-align:center;
        padding: 5px 10px;
    }
    
    .main-content{
        height: 80vh;
        overflow-x: auto;
    }

    .bg-primaries{
        background-color: #DDEBF6;
        border: 2px solid black; margin: 5px 10px;
        text-transform: capitalize;
    }
    .bg-lighties{
        border: 1px solid black; 
        margin: 5px 10px;
    } 

    .cell-1 {
        border-spacing: 0 4em;
        cursor: pointer;
    }
    .cell-2{
        border-spacing: 0 4em;
        background-clip: padding-box;
    }
    .text-wrapp{
        text-wrap: nowrap;
        display: block;
        margin: 0px;
        padding: 0px;
    }
    thead{
        border: 2px  solid black;
        position: sticky;
        top: 0;
    }
    tfoot td{
        font-weight: bold;
    }
    .tbl-plan{
        width: 100%;
        border-collapse: collapse;
        height: 80vh;
        overflow: scroll;
    }

    .left-sticky{
        position: sticky;
        left: 0px;
    }

<?php $this->registerCss(ob_get_clean()); ?>


<div class="additional-monthly-requirement-short">
  <div class="header">
      <div class="row" style="font-size: 25px; font-weight: bold;">
        <div class="col-md-6">
          <span><?= $part->part_no?></span>
          <span><?= $part->part_name?></span>
        </div>
        <div class="col-md-6">
            <?= $qty?> кг
        </div>
      </div>
  </div>
  <div class="main-content">
    <table class="table tbl_plan">
      <thead>
          <tr>
            <th class="bg-primaries">№ гп</th>
            <th class="bg-primaries"><?= Yii::t('app', 'Calculation name')?></th>
            <th class="bg-primaries"><?= Yii::t('app', 'Part color')?></th>
            <th class="bg-primaries"><?= Yii::t('app', 'plan')?></th>
            <th class="bg-primaries"><?= Yii::t('app', 'Remark')?></th>
            <th class="bg-primaries"><?= Yii::t('app', 'Норма %')?></th>
            <th class="bg-primaries"><?= Yii::t('app', 'Quantity')?></th>
          </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $item):?>
          <tr>
            <td class="bg-ligties"><?= $item["part_no"]?></td>
            <td class="bg-ligties"><?= $item["part_name"]?></td>
            <td class="bg-ligties"><?= $item["part_color"]?></td>
            <td class="bg-ligties"><?= $item["qty"]?></td>
            <td class="bg-ligties"><?= $item["remark"]?></td>
            <td class="bg-ligties text-right"><?= (round($item['amount'], 2)/round($item['qty'], 2))*100?></td>
            <td class="bg-ligties text-right"><?= round($item['amount'], 2)?></td>
          </tr>
        <?php endforeach;?>
      </tbody>  
      <tfoot>
        <tr>
          <td><?= Yii::t('app',  'Total')?></td>
          <td colspan="6" class="bg-ligties text-right"><?= $qty?></td>
        </tr>
      </tfoot>
    </table>
  </div>
  
</div>