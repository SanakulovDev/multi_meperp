<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Current stock information');
$this->params['breadcrumbs'][] = $this->title;

// echo '<pre>';
// print_r((array_shift($data)));
// echo '</pre>';



?>
<style>
	.table-doh th,
	.table-doh td {
		vertical-align: middle !important;
		border-color: #dcdcda !important;
	}


	.table-doh th{
		background-color: #e1f4f7;
		text-align: center;
	}
	.table-doh .tr-total th{
		background-color: #bfc2c5;
	}
	.tr-total{
		font-weight: bold;
		background-color: #fbe3e7;
	}
	.mln{
		text-align: right;
		font-size: 12px;
	}

  .tr_doh td{
    font-weight: bold;
    font-size: 18px;
  }
  .doh_sum{
    font-size: 28px !important;
    font-weight: normal !important;
    text-align: center;
  }
  .tr_space td{
    border: 0px !important;
    height: 5px !important;
    padding: 0px !important;
  }
  .table-doh{
    border: 0px !important; 
  }
	.table-doh>thead>tr>th, .table-doh>thead>tr>td {
    border-bottom-width: 1px !important;
  }
  .td-red{
    color: red;
  }
</style>
<div class="row">
	<div class="col-md-2 col-lg-2">
		<?=Html::a(Yii::t('app', 'btn-back'), ['index'], ['class' => 'btn btn-default btn-xs'])?>
	</div>
	<div class="col-md-10 col-lg-10">
		<div class="pull-right">
			<?=Html::button(Yii::t('app', 'btn-download-delivery-plan'), ['class' => 'btn btn-info btn-xs', 'id' => 'download-xls'])?>
		</div>
	</div>
</div>
	<br>
<div class="req-index">
	<div class="row">
		<div id="div_fix_table" class="col-lg-10 col-lg-offset-1">
			<table id="fix_table" class="table table-bordered table-doh">
				<thead>
					<tr>
            <th rowspan="2"><?=Yii::t('app','Warehouse')?></th>
            <th colspan="6"><?=Yii::t('app','Current value')?></th>
          </tr>
					<tr>
            <th style="width: 150px;" >1000 UZS</th>
            <th style="width: 150px;" >USD</th>
            <th style="width: 150px;" >EUR</th>
            <th style="width: 150px;" >RUB</th>
            <th style="width: 150px;" ><?=Yii::t('app', 'Total')?> (<?=Yii::t('app', 'in 1000 UZS')?>)</th>
          </tr>
				</thead> 
				<tbody>
					
					<?foreach ($data as $row) {?>
            <tr>
              <td><?=(count($row['items']) > 1) ? Html::a($row['group_name'], '#',[
                'data-toggle' => 'modal', 
                'data-target' => '#modal-stock',
                'data-row' => json_encode($row),
                'class' => 'link-group'
                ]) : Yii::t('app', $row['group_name']);?></td>
              <td style="text-align: right;"><?=$row['UZS']?></td>
              <td style="text-align: right;"><?=$row['USD']?></td>
              <td style="text-align: right;"><?=$row['EUR']?></td>
              <td style="text-align: right;"><?=$row['RUB']?></td>
              <td style="text-align: right;"><?=$row['TUZS']?></td>
            </tr>
          <?}?>					
					
					
					<tr class="tr-total">
            <td><?=Yii::t('app', 'Total')?></td>
            <td style="text-align: right;"><?=number_format($total['UZS'],0,'',' ')?></td>
            <td style="text-align: right;"><?=number_format($total['USD'],0,'',' ')?></td>
            <td style="text-align: right;"><?=number_format($total['EUR'],0,'',' ')?></td>
            <td style="text-align: right;"><?=number_format($total['RUB'],0,'',' ')?></td>
            <td style="text-align: right;"><?=number_format($total['TUZS'],0,'',' ')?></td>
					</tr>
					
				</tbody>
				
				
					
					
			</table>
		</div>
	</div>


</div>

<div class="modal fade" id="modal-stock" style="display: none;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span></button>
				<h4 class="modal-title"><?=Yii::t('app', 'Direct material warehouse')?></h4>
			</div>
			<div class="modal-body">
				
			
			<table class="table table-bordered table-doh table-modal">
				<thead>
					<tr>
            <th rowspan="2"><?=Yii::t('app','Warehouse')?></th>
            <th colspan="5"><?=Yii::t('app','Current value')?></th>
          </tr>
					<tr>
            <th style="width: 120px;" >1000 UZS</th>
            <th style="width: 120px;" >USD</th>
            <th style="width: 120px;" >EUR</th>
            <th style="width: 120px;" >RUB</th>
            <th style="width: 120px;" ><?=Yii::t('app', 'Total')?><br>(<?=Yii::t('app', 'in 1000 UZS')?>)</th>
          </tr>
				</thead> 
				<tbody class="modal-tbody">
					
				
					
			
					
          </tbody>

					<tr class="tr-total">
            <td><?=Yii::t('app', 'Total')?></td>
            <td style="text-align: right;" class="td-total-uzs">434220</td>
            <td style="text-align: right;" class="td-total-usd">232</td>
            <td style="text-align: right;" class="td-total-eur">23</td>
            <td style="text-align: right;" class="td-total-rub">2333</td>
            <td style="text-align: right;" class="td-total-tuzs">223</td>
					</tr>
					
				
				
				
					
					
			</table>


			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

<?php
$docReadyJs = <<< JS
$(document).ready(function() {

	$('#download-xls').on('click', function (e) {
		html_xls_export('fix_table', '$downloadFileName');
  });	

  $('.link-group').on('click', function (e) {
    var objGroup = $.parseJSON($(this).attr('data-row'));
    $('.modal-title').html(objGroup.group_name);
    $('.td-total-uzs').html(objGroup.UZS);
    $('.td-total-usd').html(objGroup.USD);
    $('.td-total-eur').html(objGroup.EUR);
    $('.td-total-rub').html(objGroup.RUB);
    $('.td-total-tuzs').html(objGroup.TUZS);
    
    $('.modal-tbody').html('');
    
    var td = tds = ''
    $.each(objGroup.items, function(k, v) {
      td = "<tr>";
      td += "<td>"+v.wh_name+"</td>";
      td += "<td style=\"text-align: right;\">"+v.UZS+"</td>";
      td += "<td style=\"text-align: right;\">"+v.USD+"</td>";
      td += "<td style=\"text-align: right;\">"+v.EUR+"</td>";
      td += "<td style=\"text-align: right;\">"+v.RUB+"</td>";
      td += "<td style=\"text-align: right;\">"+v.TUZS+"</td>";
      tds += td;

		});
    $('.modal-tbody').html(tds);
            
            
    

		console.log(objGroup);
  });	
  
  
}) 
JS;
$this->registerJs($docReadyJs);
?>