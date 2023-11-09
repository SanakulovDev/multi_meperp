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
        <div class="col-md-3">
            <?= $qty?> кг
        </div>
        <p class="pull-right" style="margin: 0px">
        <?=Html::a(Yii::t('app', 'btn-download'), ['#'], ['class' => 'btn btn-info btn-sm', 'id' => 'btnDownload'])?>
      </p>
      </div>
      
            <div style="clear: both;"></div>
  </div>
  <div class="main-content">
    <table class="table tbl_plan" id="tbl_plan2">
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
            <td class="bg-ligties text-right"><?= round((round($item['amount'], 2)/$qty)*100) ?></td>
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

<?php ob_start();?>
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

			excel.generate("<?= $part->part_name?>-<?= date('Y-m-d H:i:s')?>.xlsx");    


			$(".tbl-plan").tableFixer({'left' : 3});
		}
		$('#btnDownload').on('click', function(e){
      e.preventDefault();
      let tableId = $('.tbl_plan').attr('id');
			exportExcel(tableId);
    })	

<?php $this->registerJs(ob_get_clean()); ?>
<?php $this->registerJsFile('/themes/excel/jquery-3.5.1.min.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/myexcel.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/jszip.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/myscript.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/FileSaver.js', ['position' => \yii\web\View::POS_HEAD]); ?>