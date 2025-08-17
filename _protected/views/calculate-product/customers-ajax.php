<?php 

use yii\helpers\Html;
?>
<?php ob_start(); ?>
	
	.customer-type{
		color: #000;
		font-weight:bold;
		padding: 5px 10px!important;
		
	}
	.bg-warning{
		background-color: #6abdff!important;
		font-weight: bold;
	}
	th, td{
        border: 2px solid grey!important;
        text-align:center;
        padding: 5px 10px;
		    text-wrap: nowrap;
    }
  .text-left{
    text-align:left!important;
  }
  .text-right{
    text-align: right!important;
  }
	thead {
		position: sticky;
		top: 0;
	}
	tfoot{
		position: sticky;
		bottom: 0;
	}
	.bg-primaries{
        background-color: #DDEBF6;
        border: 2px solid black; margin: 5px 10px;
  }
  .bg-lighties{
      border: 1px solid black; 
      margin: 5px 10px;
      text-align: left;
  } 
	.main-content{
      height: 70vh;
      overflow-x: auto;
    }
	.tbl-plan{
    width: 100%;
    border-collapse: collapse;
    max-height: 80vh;
    overflow: scroll;
  }
  .left-sticky{
    position: sticky;
    left: 0px;
  }
  .absolute-bg{
    border-top: none!important;
  }
  .absolute-bg2{
    border-bottom: none!important;
  }
  .center-th{
    border-left: none!important;
    border-right: none!important;
  }
  .center-th-left{
    border-left: none!important;
  }
  .center-th-right{
    border-right: none!important;
  }
  
<?php $this->registerCss(ob_get_clean()); ?>
<div class="calculate-product-customers-table main-content">
  <table id="fix_table" class="table tbl_plan">
    <thead>
      <tr>
        <th class="bg-primaries">№</th>
        <?php if($term == 1):?>
          <th class="bg-primaries"><?= Yii::t('app', 'PartName')?></th>
          <th class="bg-primaries"><?= Yii::t('app', 'Code')?></th>
          <th class="bg-primaries"><?= Yii::t('app', 'Qty')?></th>
          <th class="bg-primaries"><?= Yii::t('app', 'Price')?></th>
          <th class="bg-primaries"><?= Yii::t('app', 'VAT')?></th>
          <th class="bg-primaries"><?= Yii::t('app', 'Price')?> c <?= Yii::t('app', 'VAT')?></th>
          <th class="bg-primaries"><?= Yii::t('app', 'FG Invoice no (TTN)')?></th>
          <th class="bg-primaries"><?= Yii::t('app', 'FG Invoice date (TTN)')?></th>
        <?php endif;?>
        <th class="bg-primaries"><?= Yii::t('app', 'Waybill no')?></th>
        <th class="bg-primaries"><?= Yii::t('app', 'Waybill date')?></th>
        <th class="bg-primaries"><?= Yii::t('app', 'need_month')?></th>
        <?php if($term == 2):?>
          <th class="bg-primaries"><?= Yii::t('app', 'Amount')?></th>
          <th class="bg-primaries"><?= Yii::t('app', 'Amount')?> <?= Yii::t('app', 'VAT')?></th>
          <th class="bg-primaries"><?= Yii::t('app', 'Amount')?> c<?= Yii::t('app', 'VAT')?></th>
        <?php endif;?>
        <th class="bg-primaries"><?= Yii::t('app', 'Contract no')?></th>
        <th class="bg-primaries"><?= Yii::t('app', 'Customers')?></th>
      </tr>
    </thead>
    <tbody>
        <?php if(!empty($items)):?>
          <?php foreach($items as $key => $item): ?>
          <tr>
            <?php 
              if(mb_strlen($item['contract']) > 30){
                $item['contract'] = mb_substr($item['contract'],0, 30);
              }

              if(mb_strlen($item['part_name']) > 30){
                $item['part_name'] = mb_substr($item['part_name'],0, 30);
              }
              $item['price']      = round($item['price']*1);
              $item['nds']        = Yii::$app->params['vat'] * $item['price'];
              $item['nds_price']  = $item['price'] + $item['nds'];

              $item['price_sum']  = round($item['price']*1) * $item['qty'];
              $item['nds_sum']    =  Yii::$app->params['vat'] * $item['price_sum'];
              $item['nds_price_sum'] = $item['price_sum'] + $item['nds_sum'];
              
            ?>
            <td class="bg-lighties"><?= $key+1?></td>
            <?php if($term == 1):?>
              <td class="bg-lighties"><?= $item['part_name']?></td>
              <td class="bg-lighties"><?= $item['part_no']?></td>
              <td class="bg-lighties"><?= $item['qty']*1?></td>
              <td class="bg-lighties text-right"><?= divideString($item['price'], 3)?></td>
              <td class="bg-lighties text-right"><?= divideString($item['nds'], 3)?></td>
              <td class="bg-lighties text-right"><?= divideString($item['nds_price'], 3)?></td>
              <td class="bg-lighties"><?= $item['invoice_no']?></td>
              <td class="bg-lighties"><?= $item['invoice_date']?></td>
            <?php endif;?>
            <td class="bg-lighties"><?= $item['waybill_no']?></td>
            <td class="bg-lighties"><?= $item['waybill_date']?></td>
            <td class="bg-lighties"><?= date('F', strtotime($item['waybill_date']))?></td>
            <?php if($term == 2):?>
              <td class="bg-lighties text-right"><?= divideString($item['price_sum'], 3)?></td>
              <td class="bg-lighties text-right"><?= divideString($item['nds_sum'], 3)?></td>
              <td class="bg-lighties text-right"><?= divideString($item['nds_price_sum'], 3)?></td>
            <?php endif;?>
            <td class="bg-lighties"><?= $item['contract']?></td>
            <td class="bg-lighties"><?= $item['name']?></td>
          </tr>
          <?php endforeach;?>
        <?php endif;?>
      </tbody>
  </table>
</div>