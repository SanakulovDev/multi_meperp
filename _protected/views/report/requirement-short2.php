<?php
	use app\components\Helpers;
    use app\models\Part;
    use app\models\Stock;
    use yii\helpers\Html;

    $this->title = Yii::t('app', 'Part requirement Short');
	  $this->params['breadcrumbs'][] = $this->title;
	  
?>
<div class="req-index">
    <div class="panel">
        <div class="panel-heading">
                <img style="height:28px;" src="/img/mep1.jpg" title="<?php echo Yii::$app->params['comp_name'] ?>" class="pull-left"/>
                <h3 class="pull-left" style="margin: 5px 0px -5px 10px;">
                <?=Yii::t('app', $this->title)?>
                <span id="calc_at" style="font-size: 14px;color: #a29393;"><?=$loading?></span>
            </h3>
            <p class="pull-right" style="margin: 0px">
              <?=Html::a(Yii::t('app', 'btn-download'), ['#'], ['class' => 'btn btn-info btn-sm', 'id' => 'btnDownload'])?>
            </p>
            <div style="clear: both;"></div>
        </div>

      <div class="">
        <a href="<?= \yii\helpers\Url::to(['report/requirement-short', 'filter' => 1])?>" class="btn btn-success">Фильтр</a>
        <a href="<?= \yii\helpers\Url::to(['report/requirement-short'])?>" class="btn btn-danger">Очистить фильтра</a>
      </div>    </div>

    <div class="main-content" style="max-height: 80vh;
    overflow: scroll;
    position: relative;">

      <table class="table table-req" id="fix_table_d">
        <thead style="    position: sticky;
    top: 0;">
                  <tr class="tr_head">
                      <th style="width: 30px;" class="text-center">№</th>
                      <th style="width: 100px;" class="text-center"><?=Yii::t('app', 'Part')?></th>
                      <th style="width: 100px;" class="text-center"><?=Yii::t('app', 'Part color')?></th>
                      <th style="width: 100px;" class="text-center"><?=mb_strtoupper(Yii::t('app', 'Part name'))?></th>
                      <th style="width: 100px;" class="text-center"><?=Yii::t('app', 'Type')?></th>
                      <!-- <th style="width: 100px;" class="text-center"><?php //echo Yii::t('app', 'Average usage')?></th> -->
                      <th style="width: 100px;" class="text-center">Количество остатка</th>
                      <!-- <th style="width: 100px;" class="text-center">1 нед</th>
                      <th style="width: 100px;" class="text-center">Баланс</th>
                      <th style="width: 100px;" class="text-center">след нед</th>
                      <th style="width: 100px;" class="text-center">Баланс</th> -->
                      <th style="width: 100px;" class="text-center">1месяц</th>
                      <th class="balance" style="width: 100px;" class="text-center">Баланс</th>
                  </tr>
        </thead>
        
        <tbody>
            <?php $i = 0; ?>
            <?php foreach($data_daily as $row):?>
              <?php $averageUsage =  round(Part::findOne($row['part_id'])->averageUsage)?>
              <?php 
                if($filter == 1){
                  if($averageUsage == 0){
                    continue;
                  }
                }
                ?>
                <?php $i++; ?>
              <tr <?=($i%2 == 0) ? 'class="tr_odd"' : ''?>>
                  <td class="text-center"><?=$i?></td>
                  <td class="text-center"><?=$row['part_no']?></td>
                  <td class="text-center" title="<?=$row['remark']?>"><?=$row['part_color']?></td>
                  <td style="max-width: 150px;" class="td-nowrap"><?=mb_strtoupper($row['part_name'])?></td>
                  <td class="text-center"><?=$row['csourse']?></td>
                  <!-- <td style="text-align: center" class="text-right"><?php //echo $averageUsage?></td> -->
                  <td style="text-align: center"><?php echo number_format($row['stock'], 0, ',', ' ')?></td>
              
                  <!-- <td style="text-align: center"><?php //echo number_format($row['current_week']*1, 0, ',', ' ') ;  ?></td> -->
                  <!-- <td class="balance" data-cash="<?php //echo $row['currentWeekBalance']?>" style="text-align: center"><?php //echo number_format($row['currentWeekBalance'], 0, ',', ' ') ;  ?></td> -->
                  <!-- <td style="text-align: center"><?php //echo number_format($row['next_week']*1, 0, ',', ' ') ;  ?></td> -->
                  <!-- <td   class="balance" data-cash="<?php //echo $row['nextWeekBalance']?>" style="text-align: center"><?php //echo number_format($row['nextWeekBalance'], 0, ',', ' ') ;  ?></td> -->
                  <td style="text-align: center"><?php echo number_format($row['current_month'], 0, ',', ' ') ?></td>
                  <td class="balance" data-cash="<?=$row['currentMonthBalance']?>" style="text-align: center"><?php echo number_format($row['currentMonthBalance'], 0, ',', ' ') ?></td>
              </tr>
            <?php endforeach; ?>
        </tbody>


      </table>
    </div>
</div>
<?

	ob_start();?>
	
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
		$('#btnDownload').on('click', function(e){
      e.preventDefault();
      let tableId = $('.table-req').attr('id');
			exportExcel(tableId);
    })		




    $(document).ready(function(){
      $('.balance').each(function(){
        let cash = $(this).data('cash');
        if(cash < 0){
          $(this).css('background-color', '#e46c6c');
          $(this).css('color', 'white');
          $(this).css('font-weight', 'bold');
        }
      })
    })

	
<?php $this->registerJs(ob_get_clean());?>
<?php $this->registerJsFile('/themes/excel/jquery-3.5.1.min.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/myexcel.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/jszip.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/myscript.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/FileSaver.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<style>
:root {
  --color-bg: #458;
  --color-switch-thumb: #ccc;
  --color-switch-bg: #777;
  --color-switch-bg-active: #245;
  
  --switch-size: 40px;
}
.switch-input {
  display: none;
}
.switch {
  --switch-width: var(--switch-size);
  --switch-height: calc(var(--switch-width) / 2);
  --switch-border: calc(var(--switch-height) / 10);
  --switch-thumb-size: calc(var(--switch-height) - var(--switch-border) * 2);
  --switch-width-inside: calc(var(--switch-width) - var(--switch-border) * 2);
  display: block;
  box-sizing: border-box;
  width: var(--switch-width);
  height: var(--switch-height);
  border: var(--switch-border) solid var(--color-switch-bg);
  border-radius: var(--switch-height);
  background-color: var(--color-switch-bg);
  cursor: pointer;
  margin: var(--switch-margin) 0;
  transition: 300ms 100ms;
  
  position: relative;
}
.switch::before {
  content: '';
  background-color: var(--color-switch-thumb);
  height: var(--switch-thumb-size);
  width: var(--switch-thumb-size);
  border-radius: var(--switch-thumb-size);
  
  position: absolute;
  top: 0;
  left: 0;
  
  transition: 300ms, width 600ms;
}
.switch-input:checked + .switch {
  background-color: var(--color-switch-bg-active);
  border-color: var(--color-switch-bg-active);
}
.switch:active::before {
  width: 80%;
}
.switch-input:checked + .switch::before {
  left: 100%;
  transform: translateX(-100%);
}
</style>