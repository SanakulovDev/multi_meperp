<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\CalculateProduct;
ob_start();
?>
 .dashboard td{
    padding: 8px;
 }
 <?php $this->registerCss(ob_get_clean());?>
<!-- sweetalert min css -->
<h2 class="text-uppercase" style="font-weight: bold;">Availability for shipment</h2>

<!-- renderpartial -->
<?php echo $this->render('_form', [
    'models' => $models
]); ?>

<div class="dashboard">
    <div class="row">
        <div class="col-md-12"></div>
    </div>
</div>

<?php ob_start();?>
$(function(){
    $(document)
        .ajaxStart(function () {
            $('.loader-ajax').removeClass('hide');
        })
        .ajaxStop(function () {
            $('.loader-ajax').addClass('hide');
            $(this).removeClass('hide');
    });
    $('body').on('click','.submit-btn', function(e){
        e.preventDefault();
        $('.dashboard').html('');
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
            $('.dashboard').html(response);
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
})
<?php $this->registerJs(ob_get_clean()); ?>