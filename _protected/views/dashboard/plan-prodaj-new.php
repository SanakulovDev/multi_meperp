<?php 

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Plan -Fakt Prodaj');
$planSum = 0;
$faktSum = 0;
$balanceSum = 0;

$generalSum = 0;
$footerLists = [];
?>
<?php ob_start(); ?>
    th, td{
        border: 2px solid black!important;
        text-align:center;
        padding: 5px 10px;
    }
    td{
        width: 200px;
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
       // border: 1px solid black; 
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
    .tbl-plan{
        width: 100%;
        border-collapse: collapse;
        height: 80vh;
        overflow: scroll;
    }

<?php $this->registerCss(ob_get_clean()); ?>

<div class="dashboard-plan-prodaj">
    <div class="row" style="">
        <div class="col-md-3">
            <h3 style="margin:0px; padding: 0px;"><?= Yii::t('app', 'Plan -Fakt Prodaj')?> - <?=$year?></h3>
        </div>
        <div class="pull-right">
            <div class="btn-group  bg-primaries" style="cursor:pointer;">
                <span class="dropdown-toggle " style="padding: 5px 10px;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="caret"></span>
                    <?= Yii::t('app', 'Year')?>
                </span>
                <ul class="dropdown-menu">
                    <?php foreach($years as $item):?>
                        <li>
                            <a href="javascript:void(0)" data-year="<?= $item?>" class="item-year"><?= $item?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <button class="excel-export btn btn-info btn-sm"><?= Yii::t('app','btn-download')?></button>
        </div>
    </div>


    <div class="main-content">
         <table class="tbl-plan " id="fix_table">
            <thead>
                <tr>
                        <td class="bg-primaries">№</td>
                        <td class="bg-primaries text-wrapp">
                                <?= Yii::t('app', 'Part name')?>
                        </td>
                        <td class="bg-primaries">
                                <?= Yii::t('app', 'Part color')?>
                        </td>
                    <?php foreach($headers as $key => $item): ?>
                        <td class="bg-primaries" style="text-transform: capitalize;"><?= $item['name']?></td>
                    <?php endforeach; ?>

                </tr>
            </thead>
            <tbody>
                <?php $inc1 = 1;?>
                    <?php foreach($models as $customer_id => $model):?>
                        <tr class="cell-1" data-toggle="collapse" data-target=".demo-<?=$customer_id?>" aria-expanded="false">
                            <td><?= $inc1?></td>
                            <td  class="bg-lighties text-wrapp">
                                    <?= $model['customer_name']?>
                            </td>
                            <td></td>
                            <?php foreach($model['planfaktbalance'] as $key2 => $item): ?>
                                <?php 
                                    $footerLists[$key2]['plan'] = isset($footerLists[$key2]['plan']) ? $footerLists[$key2]['plan'] + $item['plan'] : $item['plan'];
                                ?>
                                <td class="bg-lighties text-wrapp">
                                        <?= divideString(round($item['plan']), 3)?>
                                </td>
                            <?php endforeach; ?>

                        </tr>
                        <?php if(isset($model['parts']) && !empty($model['parts'])):?>
                            <?php $inc = 1;?>

                            <?php foreach($model['parts'] as $part_id => $part):?>
                                <tr class="collapse bg-primaries demo-<?=$customer_id?>">
                                    <td class="bg-primariess text-wrapp"><?= $inc1.'.'.$inc;?></td>  
                                    <?php $inc++;?>
                                    <td class="bg-primariess text-wrapp">
                                            <?= $part['part_name']?>
                                    </td>  
                                    <td class="bg-primariess text-wrapp">
                                            <?= $part['part_color']?>
                                    </td>
                                    <?php foreach($part['list'] as $key2 => $part_item): ?>
                                            <td class="bg-primariess text-wrapp">
                                                <?= divideString(round($part_item['plan']), 3)?>
                                            </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php $inc1++;?>
                    <?php endforeach; ?>
            </tbody>
            <!-- tfooter -->
            <tfoot>
                <tr>
                    <td class="bg-primaries" colspan="3">Итого</td>
                    <?php foreach($footerLists as $item):?>
                        <td class="bg-primaries text-wrapp">
                                <?=  divideString(round($item['plan']), 3)?>
                        </td>

                    <?php endforeach; ?>
                </tr>                      
            </tfoot>
         </table>
    </div>
</div>
<?php ob_start(); ?>

$(function(){
    // firstType each
    $('.first-type').each(function(){
        let id = $(this).data('id');
        if(id == '<?=$firstType?>'){
            $(this).css('background-color', 'rgb(51 237 52)');
        }
    })

    // secondType each
    $('.second-type').each(function(){
        let id = $(this).data('id');
        if(id == '<?=$secondType?>'){
            $(this).css('background-color', 'rgb(51 237 52)');
        }
    })
    $('.first-type').on('click', function(){
        let id = $(this).data('id');
        window.location.href = '<?=Url::to(['dashboard/plan-prodaj-new'])?>?firstType='+id+'&secondType=<?=$secondType?>'+'&year=<?=$year?>';
    })

    // secondType click
    $('.second-type').on('click', function(){
        let id = $(this).data('id');
        window.location.href = '<?=Url::to(['dashboard/plan-prodaj-new'])?>?firstType=<?=$firstType?>&secondType='+id+'&year=<?=$year?>';
    })

    $('.cell-1').on('click', function(){
        let target = $(this).data('target');
       $(target).find('*').addClass('in');
    })

    $('.balance').each(function(){
        let price = $(this).data('price');
        if(price < 0){
            $(this).css('color', 'white');
            $(this).css('background-color', '#f58484');
        }
        else if(price > 0){
            $(this).css('color', 'white');
            $(this).css('background-color', '#83d783');
        }
    })

    // item-year-click

    $('.item-year').on('click', function(){
        let year = $(this).data('year');
        console.log(year);
        window.location.href = '<?=Url::to(['dashboard/plan-prodaj-new'])?>?firstType=<?=$firstType?>&secondType=<?=$secondType?>&year='+year;
    })




    // excel-export
    $('.excel-export').on('click', function(){
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

        excel.generate("Report-<?= time()?>.xlsx");    


        $(".tbl-plan").tableFixer({'left' : 3, 'head': false, 'foot': false});
    }
})

<?php $this->registerJs(ob_get_clean(), \yii\web\View::POS_READY); ?>
<?php $this->registerJsFile('/themes/excel/jquery-3.5.1.min.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/myexcel.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/jszip.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/myscript.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/FileSaver.js', ['position' => \yii\web\View::POS_HEAD]); ?>