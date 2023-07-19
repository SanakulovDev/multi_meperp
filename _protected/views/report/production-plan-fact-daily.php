<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'План - Факт производства на '.$date;
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

<div class="production-plan-fact-daily">
  <div class="header row">

    <h3 style="display: inline-block; margin:0; padding:0;"><?= $this->title?></h3>
    <div class="pull-right">
      <?=Html::button(Yii::t('app', 'btn-download-delivery-plan'), ['class' => 'btn btn-info', 'id' => 'download_xls_jv'])?>
    </div>
  </div>

  <div class="main-content row">
    <table id="fix_table" class="table tbl_plan">
        <thead>
        <tr>
						<th class="bg-primaries absolute-bg2" ><?=Yii::t('app', 'Line')?></th>
						<th class="bg-primaries absolute-bg2" ><?=Yii::t('app', 'Product no')?></th>
						<th class="bg-primaries absolute-bg2" ><?= Yii::t('app', 'Calculation name')?></th>
						<th class="bg-primaries absolute-bg2" ><?=Yii::t('app', 'Part color')?></th>

            <th class="bg-primaries center-th-right"></th>
            <th class="bg-primaries center-th"><?=Yii::t('app', 'Day')?></th>
            <th class="bg-primaries center-th-left"></th>
            
            
            <th class="bg-primaries center-th-right"></th>
            <th class="bg-primaries center-th">1-<?=Yii::t('app', 'Shift')?></th>
            <th class="bg-primaries center-th-left"></th>
            
            
            <th class="bg-primaries center-th-right"></th>
            <th class="bg-primaries center-th">2-<?=Yii::t('app', 'Shift')?></th>
            <th class="bg-primaries center-th-left"></th>
            
            <th class="bg-primaries center-th-right"></th>
            <th class="bg-primaries center-th"><?=Yii::t('app', 'План - 3 дня')?></th>
            <th class="bg-primaries center-th-left"></th>
          </tr>
          <tr>
            <th class="absolute-bg bg-primaries"></th>
            <th class="absolute-bg bg-primaries"></th>
            <th class="absolute-bg bg-primaries"></th>
            <th class="absolute-bg bg-primaries"></th>
            <?php for($i =0; $i <= 2; $i++):?>  
              <th class="bg-primaries"><?=Yii::t('app', 'Plan')?></th>
              <th class="bg-primaries"><?=Yii::t('app', 'Fakt')?></th>
              <th class="bg-primaries"><?=Yii::t('app', 'Balance')?></th>
            <?php endfor;?>

            <?php foreach($dateList as $date):?>
              <th class="bg-primaries"><?= $date?></th>
            <?php endforeach;?>

        </tr>
        </thead>
        <tbody>
          <?php foreach($data as $key => $part):?>
              <tr>
                <td><?= $part['line']?></td>
                <td class="text-left"><?= $part['part_no']?></td>
                <td class="text-left"><?= substr($part['part_name'], 0, 25)?></td>
                <td class="text-left"><?= $part['part_color']?></td>
                <?php foreach($part['yesterDay'] as $item):?>
                  <td><?= divideString($item['plan']*1, 3)?></td>
                  <td><?= divideString($item['fact']*1, 3)?></td>
                  <td class="balance" data-cash ="<?=$item['diff']?>"><?= divideString($item['diff']*1, 3)?></td>
                <?php endforeach;?>
                <?php foreach($part['days'] as $item):?>
                  <td><?= divideString($item['plan']*1, 3)?></td>
                <?php endforeach;?>
              </tr>
          <?php endforeach;?>
        </tbody>
    </table>
  </div>
</div>

<?php ob_start(); ?>
$(function(){
  $('.balance').each(function(){
    let cash = $(this).data('cash');
    if(cash < 0){
      $(this).css('background-color', 'rgb(246 81 81)');
      $(this).css('color', '#fff');
      $(this).css('font-weight', 'bold');
    }
  })


    $('#download_xls_jv').on('click', function(e){
        let tableId = $('.tbl_plan').attr('id');
        exportExcel(tableId);
        //window.open('data:application/vnd.ms-excel,' + $('#'+tableId).html());
        //e.preventDefault();
    });

	function exportExcel(tableId){
		var excel = $JExcel.new("Calibri light 10");            
		excel.set( {sheet:0,value:"Sheet 1" } );
		
		var table = document.getElementById(tableId);
		var limit = table.rows.length;
		var cells = table.rows[0].cells.length;
		// alert(cells);
		var headers = [];
		for (var i = 0; i < cells; i++) {
			headers.push(table.rows[0].cells[i].innerHTML);
		}
	
		var formatHeader=excel.addStyle({
			border: "none,none,none,thin #333333",font: "Calibri 12 #000 B"}
		);                                                         
		for (var i=0;i< headers.length;i++){              // Loop headers
			excel.set(0,i,0,headers[i],formatHeader);    // Set CELL header text & header format
			excel.set(0,i,undefined,"auto");             // Set COLUMN width to auto 
		}
					
		for (var i=1; i < limit; i++){                                    // Generate 50 rows
			for(var j = 0; j < cells; j++){
				if(table.rows[i].cells[j] !== undefined)
				excel.set(0,j,i,table.rows[i].cells[j].innerHTML);                    // This column is a TEXT
			}
		}
		excel.generate("<?=$this->title?>-<?= date('Y-m-d H:i:s')?>.xlsx");    
		$(".tbl-plan").tableFixer({'left' : 3});
	}

})

<?php $this->registerJs(ob_get_clean()); ?>
<?php $this->registerJsFile('/themes/excel/jquery-3.5.1.min.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/myexcel.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/jszip.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/myscript.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/FileSaver.js', ['position' => \yii\web\View::POS_HEAD]); ?>