<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Dashboard;
$this->title = 'План отгрузки на '.$monthName;
// $this->params['breadcrumbs'][] = $this->title;

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
    }
    .bg-lighties{
        border: 1px solid black; 
        margin: 5px 10px;
        text-align: left;
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
    tfoot{
        border: 2px  solid black;
        position: sticky;
        bottom: 0;
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
    .customer-type{
        color: #000;
        font-weight:bold;
        padding: 5px 10px!important;
    }

<?php $this->registerCss(ob_get_clean()); ?>



<div class="row" style="display: flex; align-items:center; justify-content:start; margin-bottom: 15px;">
    <div class="col-md-6">
        <h3 style="margin:0; padding:0; font-weight: bold;"><?= $this->title?></h3>
    </div>
    <div class="col-md-2">
        <button class="customer-type btn-secondary " data-id="1">Внутренный рынок</button>
        <button class="customer-type btn" data-id="2">Экспорт</button>
    </div>
    
    <div class="pull-right1" style="display: flex;">
        <!-- dropdown month list -->
        <button class="btn btn-info" id="excel-export"><?=Yii::t('app', 'btn-download')?></button>
    </div>
</div>
<div class="dashboard-report-plan-month">


    <div class="main-content">
        <table class="table tbl_plan" id="fix_table">
            <thead>
                <tr>
                    <th class="bg-primaries">№</th>
                    <th class="bg-primaries">Клиент</th>
                    <th class="bg-primaries">Наименование продукта</th>
                    <th class="bg-primaries">План</th>
                </tr>
            </thead>

            <tbody>
                <?php $index = 1;?>
                <?php foreach($models as $key => $model):?>
                    <?php if(empty($model['parts'])){
                        continue;
                    } ?>
                    <tr>
                        <th  class="bg-lighties"><?=$index++?></th>
                        <td class="bg-lighties" rowspan="<?=$model['part_count']+1?>"><?=$model['customer_name']?></td>
                       <?php if(isset($model['parts'][0])):?>
                            <td class="bg-lighties"><?= $model['parts'][0]['part_name']?></td>
                            <td class="bg-lighties"><?= divideString($model['parts'][0]['plan']['quantity']*1, 3)?></td>
                        <?php endif;?>
                    </tr>
                    <?php foreach($model['parts'] as $key => $part):?>
                        <tr>
                            <?php if($key == 0 || $part['plan']['quantity'] == 0) continue;?>
                            <th  class="bg-lighties"><?=$index++?></th>
                            <td class="bg-lighties"><?= $part['part_name']?></td>
                            <td class="bg-lighties"><?= divideString($part['plan']['quantity']*1, 3)?></td>

                        </tr>
                    <?php endforeach;?>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>

</div>

<?php ob_start(); ?>
    
    $(function(){
        $('.customer-type').on('click', function(){
            var customer_type_id = $(this).data('id');
            window.location.href = '<?=Url::to(['report/customer-types-plan'])?>?customer_type_id='+customer_type_id;
        });

        $('.customer-type').each(function(){
            let id = $(this).data('id');
            if(id == <?=$customer_type_id?>){
                $(this).css('background-color', '#31ff2a')
            }
        })

        $('#excel-export').on('click', function(){
            exportExcel();
        });

        function exportExcel(){
            var excel = $JExcel.new("Calibri light 10");            
            excel.set( {sheet:0,value:"Sheet 1" } );
            
            var table = document.getElementById('fix_table');
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

            excel.generate("План отгрузки на <?=$monthName?>.xlsx");    


            $(".tbl-plan").tableFixer({'left' : 3, 'head': false, 'foot': false});
        }
    })



<?php $this->registerJs(ob_get_clean()); ?>
<?php $this->registerJsFile('/themes/excel/jquery-3.5.1.min.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/myexcel.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/jszip.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/myscript.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/FileSaver.js', ['position' => \yii\web\View::POS_HEAD]); ?>