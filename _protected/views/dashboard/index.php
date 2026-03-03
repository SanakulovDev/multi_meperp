<?php
/* @var $this yii\web\View */
use app\models\Dashboard;
$this->title = Yii::t('app', 'Материальный отчет');
$yesterday = date('d.m.Y',strtotime(Dashboard::runDate()));

ob_start(); ?>

    table thead{
        background-color: #DDEBF6!important;
    }
    td, th {
        border: 2px solid #000000!important;
    }

<?php $this->registerCss(ob_get_clean());?>
        <h2 class="text-uppercase font-weight-bold" style="display: inline-block; ">Материальный отчет на <span class="general-date"><?= $yesterday?></span></h2>
    <div class=" pull-right" style="display: flex;
                            flex-wrap: nowrap;
                            justify-content: space-between;
                            align-items: center;
                ">
        <div class="form-group">
            <label for="date" class="control-label">
                Дата производства
            </label>
            <input type="date" id="date" value="<?= $yesterday?>"   class="form-control date">
        </div>
        <button class="btn-download btn btn-info" type="button"><?= Yii::t('app', 'btn-download')?></button>
    </div>
</div>
<div class="" id="report-analytic">
    <div class="dashboard-1 row dashboard-row">

    </div>
    <div class="dashboard-2 row dashboard-row">

    </div>
    <div class="dashboard-3 row dashboard-row">

    </div>
    <div class="dashboard-4 row dashboard-row">
        <h3 class="text-uppercase" style="font-weight: bold; margin-left: 20px">Норма расхода</h3>
    </div>
    <div class="dashboard-5 hide">
    </div>
</div>

<?php ob_start(); ?>
$(function(){
    let date = '<?= $yesterday?>';
    let arr = [
        'fakt',
        'ttn',
        'prixod',
        'norma-rasxod'
    ];
    $.each(arr, function(index, value){
        ajaxRequst(value, date);
    });
    $('.date').on('change', function(){
        let currentDate = new Date($(this).val());
        let dateformat = 'dd.mm.yyyy';
        let formattedDate = ("0" + currentDate.getDate()).slice(-2) + "." + ("0" + (currentDate.getMonth() + 1)).slice(-2) + "." + currentDate.getFullYear();

        $('.general-date').text(formattedDate);
        $('.dashboard-row').empty();
        $.each(arr, function(index, value){
            ajaxRequst(value, formattedDate);
        });
    });

    function ajaxRequst(url, date){
        $.ajax({
            url: url,
            type: 'POST',
            data: {date:date},
            success: function(data){
                data = JSON.parse(data);
                let data_id = data.id;
                $('.dashboard-'+data_id).append(data.html);
            }
        });
    }

    
    // excel-export
    let classList = [
        'table-fakt',
        'table-ttn',
        'table-prixod',
        'table-norma-rasxod'
        ];
    $('.btn-download').on('click', function(){
        var mergedTable = $('<table id="mergedtable">');

        // Beshta jadvalni tanlash
        var tables = $('table');

        // Har bir jadvalni olib, ularning barcha qatorlarini yangi jadvalga qo'shamiz
        tables.each(function() {
            var rows = $(this).find('tr');
            mergedTable.append(rows.clone());
        });

        // Yangi jadvalni HTML ga qo'shamiz
        $('.dashboard-5').append(mergedTable);
        exportExcel();
       
    })
    function exportExcel(){
        var excel = $JExcel.new("Calibri light 10");            
        excel.set( {sheet:0,value:"Sheet 1" } );
        
        var table = document.getElementById('mergedtable');
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

        excel.generate("Материальный отчет-<?= $yesterday?>.xlsx");    


        $(".tbl-plan").tableFixer({'left' : 3, 'head': false, 'foot': false});
    }     
    
})
<?php $this->registerJs(ob_get_clean(), \yii\web\View::POS_READY); ?>
<?php $this->registerJsFile('/themes/excel/myexcel.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('/themes/excel/jszip.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('/themes/excel/myscript.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('/themes/excel/FileSaver.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>