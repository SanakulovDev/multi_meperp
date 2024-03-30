<?php
	  use app\components\Helpers;
    use app\models\Part;
    use app\models\Stock;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\bootstrap\Modal;

    $this->title = Yii::t('app', 'Monthly Requirement Short');
	  $this->params['breadcrumbs'][] = $this->title;
	  
?>
<div class="req-index">
    <div class="panel">
        <div class="row">
              <div class="col-md-8 row" style="display: flex; flex-direction: row; justify-content:start; align-items:center;">
                <div class="col-md-6">
                  <?= Html::dropdownList('part_id', $part_id, $partList, [
                  'class' => 'select2 form-control requirement-part',
                  'prompt' =>'-----',
                  // 'style'=>'display: inline-block; width: 300px!important;'
                  ])?>
                </div>
                <div class="col-md-2">
                  <a href="<?= \yii\helpers\Url::to(['report/monthly-requirement-short', 'filter' => 1])?>" class="btn btn-success">Фильтр</a>
                </div>
                <div class="col-md-2">
                  <a href="<?= \yii\helpers\Url::to(['report/monthly-requirement-short'])?>" class="btn btn-danger">Очистить фильтра</a>
                </div>
              </div>
            <div class="col-md-4" style="display: flex; justify-content: end;">
                <?=Html::a(Yii::t('app', 'btn-download'), ['#'], ['class' => 'btn btn-info btn-sm', 'id' => 'btnDownload'])?>
            </div>
            <div style="clear: both;"></div>
        </div>

          
    </div>

    <div class="man-content" style="max-height: 80vh;
    overflow: scroll;
    position: relative;">

      <table class="table table-req" id="fix_table_d">
        <thead style="    position: sticky;top: 0;">
                  <tr class="tr_head" style="background-color: #c7c0c0;">
                      <th style="width: 30px;" class="text-center">№</th>
                      <th style="width: 100px;" class="text-center"><?=Yii::t('app', 'Part')?></th>
                      <th style="width: 100px;" class="text-center"><?=Yii::t('app', 'Part color')?></th>
                      <th style="width: 100px;" class="text-center"><?=mb_strtoupper(Yii::t('app', 'Part name'))?></th>
                      <th style="width: 100px;" class="text-center"><?=Yii::t('app', 'Type')?></th>
                      <th style="width: 100px;" class="text-center"><?=Yii::t('app', 'Next Arrival')?></th>
                      <th style="width: 100px;" class="text-center"><?=Yii::t('app', 'Date')?></th>
                      <th style="width: 100px;" class="text-center">Количество остатка</th>
                      <th style="width: 100px;" class="text-center">1месяц</th>
                      <th class="balance" style="width: 100px;" class="text-center">Баланс</th>
                      <th style="width: 80px;" class="edit text-center"><?= Yii::t('app', 'Edit')?></th>
                  </tr>
        </thead>
        
        <tbody>
            <?php $i = 0; ?>
            <?php foreach($data_daily as $row):?>
              <?php 
                if($filter == 1){
                  if($row['currentMonthBalance'] == 0){
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
                  <td style="text-align: center">
                        <?php echo number_format($row['arrived_qty'], 0, ',', ' ')?>    
                  </td>

                  <td style="text-align: center">
                      <?php echo $row['arrived_at']?>    
                  </td>
                  <td style="text-align: center"><?php echo number_format($row['stock'], 0, ',', ' ')?></td>
                  <td style="text-align: center" class="current-month" data-part-id="<?= $row['part_id']?>" data-qty="<?=$row['current_month']?>">
                      <?= number_format($row['current_month'], 0, ',', ' ')?>
                    <?php   //echo Html::a(number_format($row['current_month'], 0, ',', ' '), ['additional-monthly-requirement-short', 'part_id'=>$row['part_id'], 'qty' => $row['current_month']], ['target'=>'_blank']) ?>
                  </td>
                  <td class="balance" data-cash="<?=$row['currentMonthBalance']?>" style="text-align: center"><?php echo number_format($row['currentMonthBalance'], 0, ',', ' ') ?></td>
                  <td class="text-center">
                    <?= Html::a('<i class="glyphicon glyphicon-pencil"></i>', ['#'], ['class'=>'btn pencil-edit', 'data-id'=>$row['part_id']])?>
                  </td>
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
			excel.set( {sheet:0,value:"<?=$this->title?> 1" } );
			
			var table = document.getElementById(tableId);
			var limit = table.rows.length;
			var cells = table.rows[0].cells.length;

			// alert(cells);

			var headers = [];

			for (var i = 0; i < cells; i++) {
        if(i == 10){
          continue;
        }
				headers.push(table.rows[0].cells[i].innerHTML);
			}


		
			var formatHeader=excel.addStyle({
				border: "none,none,none,thin #333333",
        font: "Calibri 12 #000 BOLD",
        fill: "#D9E1F2",
        align: "center"
      });
      var formatBody = excel.addStyle({
        border: "thin,thin, thin,thin #333333",
        font: "Calibri 12 #000", // Normal font for body
        align: "center", // Text alignment (center),
        fill: "#E7E6E6",
      });                                                         

			for (var i=0;i< headers.length;i++){              // Loop headers
				excel.set(0,i,0,headers[i],formatHeader);    // Set CELL header text & header format
				excel.set(0,i,undefined,"auto");             // Set COLUMN width to auto 
			}
						
			for (var i=1; i < limit; i++){                                    // Generate 50 rows
				for(var j = 0; j < cells; j++){
          if(j == 10){
            continue;
          }
					if(table.rows[i].cells[j] !== undefined)
					excel.set(0,j,i,table.rows[i].cells[j].innerHTML, formatBody);                    // This column is a TEXT
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
      $('.requirement-part').on('change', function(e){
          e.preventDefault();
          let id = $(this).val();
          console.log(id);
          window.location.href = '/report/monthly-requirement-short?part_id='+id;
          
      })

      $('.balance').each(function(){
        let cash = $(this).data('cash');
        if(cash < 0){
          $(this).css('background-color', '#e46c6c');
          $(this).css('color', 'white');
          $(this).css('font-weight', 'bold');
        }
      })

      $('.current-month').on('click', function(){
        console.log($(this).data('qty'));
        window.location.href= 'additional-monthly-requirement-short?part_id='+$(this).data('part-id')+'&qty='+$(this).data('qty');
      })

      $('.pencil-edit').on('click', function(e){
        e.preventDefault();
        let id = $(this).data('id');
        let url = '/report/monthly-vputi?part_id='+id;
        $.ajax({
          url: url,
          type:'POST',
          data:{},
          success:function(data){
            $('#modal').modal('show').find('#modalContent').html(data);
          },
          error: function(){
            alert('Xatolik sodir bo`ldi');
          }
        })
      })
    })

	
<?php $this->registerJs(ob_get_clean());?>
<?php $this->registerJsFile('/themes/excel/jquery-3.5.1.min.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/myexcel.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/jszip.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/myscript.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/FileSaver.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php ob_start();?>
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
.current-month{
  cursor:pointer;
  color: #234;
}
<?php $this->registerCss(ob_get_clean());?>
