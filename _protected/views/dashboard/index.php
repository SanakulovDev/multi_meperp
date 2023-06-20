<?php
/* @var $this yii\web\View */
use app\models\Dashboard;
$yesterday = date('d.m.Y',strtotime(Dashboard::runDate()));

ob_start(); ?>

    table thead{
        background-color: #DDEBF6!important;
    }
    td, th {
        border: 2px solid #000000!important;
    }

<?php $this->registerCss(ob_get_clean());?>
<div class="row" style="display:flex; align-items:center; justify-content:space-between">
    <div class="col-md-6">
        <h2 class="text-uppercase font-weight-bold">Материальный отчет на <span class="general-date"><?= $yesterday?></span></h2>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="date" class="control-label">
                Дата производства
            </label>
            <input type="date" id="date" value="<?= $yesterday?>"   class="form-control date">
        </div>
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

        console.log(formattedDate);
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
})
<?php $this->registerJs(ob_get_clean(), \yii\web\View::POS_READY); ?>
<?php //$this->registerJsFile('/themes/excel/myexcel.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>