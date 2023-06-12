<?php 

use yii\helpers\Html;

$optionsBarcode = [
	'sf' => 2,
	'h' => 80,
];
$text = $url;
$generator = new app\components\BarcodeGenerator();
$image = $generator->render_image('qr', $text, [
    'f' => 'png',
    'sf' => 10,
]);
ob_start();
imagepng($image);
$image = ob_get_contents();
ob_end_clean();
?>
<style>
* {
    font-family: "Arial";
    font-size: 10px;
    margin: 0;
    padding: 0;
    font-weight: bold;
}

.absolute {
    position: absolute;
    right: 3px;
    font-weight: bold;
    text-transform: capitalize;
    /* margin-top: 2px; */
}

.head-title {
    text-transform: uppercase;
    font-weight: bold;
    /* margin-top: 2px; */
}

.card{
    /* page-break-inside: avoid; */
    border: 2px double black; 
    width: 160px; 
    height: 120px; 
    position: relative;  
    margin-right: 10px; 
    margin-bottom: 10px; 
    float: left;
    transform: rotate(270deg);
    display: block;
    top: 30px;
}   
.absolute-qrcode img{
    width: 37px; 
    transform: translate(-50px, -20px);
}
.general-title{
    margin-bottom: 5px;
}
.card-block{
    width: 140px;
    height: 200px;
    position: relative;
    float: left;
    display: block;
}
.card-block:nth-child(4n){
    margin-right: 0;
}
</style>
<?php 
    
?>
<div class="container" style="display:block; width: 100vw;">
    <?php for($i=1;$i<=$count; $i++):?>
        <div class="card-block">

            <div class="card"
                style="">
                <p class="general-title">
                    <span class="head-title">Grade:</span>
                    <span class="absolute"><?= substr($model->part?$model->part->part_name:'', 0, 13)?></span>
                </p>
                <p class="general-title">
                    <span class="head-title">Color:</span>
                    <span class="absolute"><?= $model->partColor?$model->partColor->name:''?></span>
                </p>
                <p class="general-title">
                    <span class="head-title">Lot №:</span>
                    <span class="absolute"><?= $model->number_lot?></span>
                </p>
                <p class="general-title">
                    <span class="head-title">DATE:</span>
                    <span class="absolute"><?= date('d.m.Y',strtotime($model->date))?></span>
                </p>
                <p class="general-title">
                    <span class="head-title">Netto:</span>
                    <span class="absolute"><?= $model->weight_netto?:0?>KG</span>
                </p>
                <p class="general-title">
                    <span class="head-title">Brutto:</span>
                    <span class="absolute"><?= $model->weight_brutto?:0?>KG</span>
                </p>
                <span class="absolute absolute-qrcode">
                    <img style=""
                        src="data:image/png;base64,<?= base64_encode($image) ?>" alt=""></span>

                <p style="transform: translateY(2px);">
                    <span style="font-size: 4px;"><?= $model->comment?></span>
                </p>
                <p style="font-size: 4px; transform: translateY(4px);">Срок хранения компаунда -12 месяцев со дня производство
                </p>
            </div>
        </div>
        
    <?php endfor;?>
</div>

<script>
window.print();
setTimeout(function() {
    window.close();
}, 1000);
</script>