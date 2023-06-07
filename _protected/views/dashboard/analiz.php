<?php 

use yii\helpers\ArrayHelper;
use  yii\helpers\Html;
use yii\helpers\Url;
?>
<?php ob_start();?>
body{
    background-color: #000!important;
    font-size: 30px;
}
.row{
    display: flex;
    align-items: center;
    justify-content: center;
}
.header{
    margin-top: 60px;
    position:sticky;
    padding: 0 25px 30px 25px;
    z-index: 10000;
    background-color: #000;
    top:0px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.color-primary{
    color: #0BB5F3!important;
    font-weight: bold;
    font-size: 26px;
}
.color-success{
    color: #01AE53!important;
    font-weight: bold;
    font-size: 60px;
}
.color-danger{
    color: #FD0002!important;
    font-weight: bold;
    font-size: 60px;
}
.dropdown-toggle, .refresh{
    cursor: pointer;
}

.item-border-right{
    border-right: 4px solid #3A5424;
}
.item-quantity{
    margin: 25px;
    height: 160px;
    text-align: center;
    display: flex;
    align-items: center;
    flex-direction: column;
    justify-content: center;
}
.item-quantity-title{
    font-size: 30px;
    transform: translateY(-20px);
}
<?php $this->registerCss(ob_get_clean());?>
<div class="container" style="text-align:center">

    <div class="row header">
        <div class="col-md-5  text-right">
            <span class="color-primary">Результат производства</span>
        </div>
        <div class="col-md-3  text-right">
            <!-- <span class="color-primary">Rezultat proizvodstva</span> -->
            <div class="btn-group">
                <span  class="dropdown-toggle color-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="caret"></span>
                    <?= \Yii::t('app', 'Line')?> 
                </span>
                <ul class="dropdown-menu">
                    <?php foreach($lines as $key => $line):?>

                        <li><a href="javascript:void()" data-id="<?= $key?>" class="line"><?= $line?></a></li>
                    <?php endforeach;?>
                </ul>
            </div>
        </div>
        <div class="col-md-4 text-right">
            <span class="color-primary time"></span>
        </div>
        <div class="col-md-2 text-left">
            <span class="color-primary refresh"><i class="fa fa-refresh fa-2x" style="transform:rotate(0deg);"></i></span>
        </div>
    </div>
    <div class="analiz"></div>
</div>

<?php ob_start();?>
$(function(){
    let deg  = 0;
    let analizUrl = '<?= Url::to(['dashboard/analiz-ajax'])?>';
    ajaxFunc(analizUrl, {}, 'POST', function(data){
        let response = JSON.parse(data);
        $('.analiz').html(response.html);
        $('.time').html(response.nowTime);
    });
    $('.refresh').on('click', function(e){
        deg += 360;
        $(this).find('i').css({
            'transform': 'rotate('+deg+'deg)',
            'transition': 'transform 1s ease-in-out'
        });
        ajaxFunc(analizUrl, {}, 'POST', function(data){
            let response = JSON.parse(data);
            $('.analiz').html(response.html);
            $('.time').html(response.nowTime);
        });
    })

    function ajaxFunc(url, param, type, callback){
        $.ajax({
            url: url,
            type: type,
            data: param,
            success: function(data){
                callback(data);
            }
        })
    }
})

<?php $this->registerJs(ob_get_clean());?>
