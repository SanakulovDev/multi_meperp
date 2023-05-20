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

<h1 class="text-uppercase font-weight-bold">Материальный отчет на <?= $yesterday?></h1>

<div class="dashboard-1 row">

</div>
<div class="dashboard-2 row">

</div>
<div class="dashboard-3 row">

</div>
<div class="dashboard-4 row">
    <h3 class="text-uppercase" style="font-weight: bold; margin-left: 20px">Норма расхода</h3>
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