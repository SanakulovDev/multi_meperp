<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\CalculateProduct;
?>
<!-- sweetalert min css -->

<!-- renderpartial -->
<?php echo $this->render('_form', [
    'models' => $models
]); ?>

<div class="dashboard">
    <div class="row  " >
        <div class="col-md-10 hidden  dashboard-row">

        </div>
    </div>
</div>

<div class="connection-tables hide">

</div>

<?php ob_start();?>
$(function(){
    $('body').on('click','.submit-btn', function(e){
        e.preventDefault();
        $('.dashboard-row').html('');
        //$(this).addClass('hidden');
        //$(this).attr('disabled', true);
        let part_ids = [];
        $('.part_id').each(function(index, item){
            let val = $(item).val();
            if(val != ''){
                part_ids.push(val);
            }
        });
        if(part_ids.length == 0){
            alert('Please select at least one product');
            return false;
        }
        let quantities = [];
        $('.quantity').each(function(index, item){
            let val = $(item).val();
            if(val != ''){
                quantities.push(val);
            }
        }); 
        if(quantities.length == 0){
            alert('Please enter at least one quantity');
            return false;
        }
        let url = '<?= Yii::$app->urlManager->createUrl(['calculate-product/report-table']) ?>';
        let param = {
            'part_ids': JSON.stringify(part_ids),
            'quantities': JSON.stringify(quantities),
        }
        let type = 'POST';
        ajaxxRequest(url, param, type, function(response){
            let data = JSON.parse(response);
            $('.dashboard-row').html(data.data1);
            $('.dashboard-row').append(data.data2);
            $('.dashboard-row').removeClass('hidden');
        });
        
    })



    function ajaxxRequest(url, param, type, callback){
        $.ajax({
            url: url,
            type: type,
            data: param,
            success: function(data){
                callback(data);
            }
        })
    }

    function swal(title, text, icon){
        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            confirmButtonText: 'Ok'
        })
    }
    $('.btn-download').on('click', function(){
        var mergedTable = $('<table id="mergedtable">');
        $('.connection-tables').html('');
        // Beshta jadvalni tanlash
        var tables = $('.table1');

        // Har bir jadvalni olib, ularning barcha qatorlarini yangi jadvalga qo'shamiz
        tables.each(function() {
            var rows = $(this).find('tr');
            mergedTable.append(rows.clone());
        });

        // Yangi jadvalni HTML ga qo'shamiz
        $('.connection-tables').append(mergedTable);
        exportExcel('mergedtable');
       
    })
    function exportExcel(){
        var excel = $JExcel.new("Calibri light 10");            
        excel.set( {sheet:0,value:"Sheet 1" } );
        
        var table = document.getElementById('mergedtable');
        var limit = table.rows.length;
        var cells = table.rows[0].cells.length;

        // alert(cells);

        var headers = [];
        var conditions = [
          '<div class="bg-primaries">',
          '<div class="bg-lighties">',
          '</div>',
          '<div class="bg-lighties" style="background-color:#faa2a2">',
          '<div class="bg-lighties" style="background-color:lightgreen">',
          '<div class="bg-primaries" style="margin:0px!important;">'
        ];
        for (var i = 0; i < cells; i++) {
          let text1 = table.rows[0].cells[i].innerHTML;
          $.each(conditions, function(index, element){
            text1 = text1.replace(element, '');
          })
          console.log(text1); 
          headers.push(text1);
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
                let text1 = '';
                if(table.rows[i].cells[j] !== undefined){
                  text1 = table.rows[i].cells[j].innerHTML;
                  $.each(conditions, function(index, element){
                    text1 = text1.replace(element, '');
                  })
                  console.log(text1); 
                  excel.set(0,j,i,text1);                    // This column is a TEXT
                }
            }
        }

        excel.generate("КАЛЬКУЛЯТОР ВОЗМОЖНОСТИ-<?= date('Y-m-d H:i:s', time())?>.xlsx");    


        $(".tbl-plan").tableFixer({'left' : 3, 'head': false, 'foot': false});
    }   
})
<?php $this->registerJs(ob_get_clean()); ?>
<?php $this->registerJsFile('/themes/excel/jquery-3.5.1.min.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/myexcel.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/jszip.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/myscript.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/FileSaver.js', ['position' => \yii\web\View::POS_HEAD]); ?>