<?php 

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<?php ob_start(); ?>
    th, td{
        border: 2px solid black!important;
        text-align:center;
    }
    td{
        width: 200px;
    }
    .main-content{
        overflow-x: auto;
    }

    .bg-primaries{
        background-color: #DDEBF6!important;
        border: 2px solid black; margin: 5px 10px;
        padding: 5px 10px;
    }
    .bg-lighties{
        border: 1px solid black; 
        margin: 5px 10px;
        padding: 5px 10px;
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
        position: sticky;
        top: 0;
    }

<?php $this->registerCss(ob_get_clean()); ?>

<div class="dashboard-plan-prodaj">
    <div class="row" style="display:flex: align-items:center; justify-content:space-between;">
        <div class="col-md-4">
            <h3 style="margin:0px; padding: 0px;">Plan -Fakt Prodaj</h3>
        </div>
        <div class="col-md-4">
            <button class="first-type btn " data-id="1"><?= Yii::t('app', 'Month')?></button>
            <button class="first-type btn " data-id="2">Квартал</button>
        </div>
        <div class="col-md-4">
            <button class="second-type btn" data-id="1">Обём</button>
            <button class="second-type btn" data-id="2">Сумма</button>
        </div>
    </div>


    <div class="main-content">
         <table class=" ">
            <thead>
                <tr>
                        <td class="bg-primaries" rowspan="2">№</td>
                        <td class="bg-primaries" rowspan="2">
                            <p class="text-wrapp">
                                <?= Yii::t('app', 'Part Name')?>
                            </p>
                        </td>
                        <td class="bg-primaries" rowspan="2">
                            <p class="text-wrapp">
                                <?= Yii::t('app', 'Part Color')?>
                            </p>
                        </td>
                    <?php foreach($headers as $key => $item): ?>
                        <td colspan="3" class="bg-primaries" style="text-transform: capitalize;"><?= $item['name']?></td>
                    <?php endforeach; ?>

                </tr>
                <tr>
                    <?php foreach($headers as $key => $item): ?>
                        <td class="bg-primaries">
                            <p class="text-wrapp">    
                                <?= Yii::t('app', 'Plan')?>
                            </p>
                        </td>
                        <td class="bg-primaries">
                            <p class="text-wrapp">    
                                <?= Yii::t('app', 'Fakt')?>
                            </p>
                        </td>
                        <td class="bg-primaries">
                            <p class="text-wrapp">    
                                <?= Yii::t('app', 'Balance')?>
                            </p>
                        </td>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                    <?php foreach($models as $customer_id => $model):?>
                        <tr class="cell-1" data-toggle="collapse" data-target=".demo-<?=$customer_id?>" aria-expanded="false">
                            <td colspan="3" class="bg-lighties">
                                <p class="text-wrapp">
                                    <?= $model['customer_name']?>
                                </p>
                            </td>
                            <?php foreach($model['planfaktbalance'] as $key2 => $item): ?>
                                <td class="bg-lighties">
                                    <p class="text-wrapp">
                                        <?= divideString(round($item['plan'])*1, 3)?>
                                    </p>
                                </td>
                                <td class="bg-lighties">
                                    <p class="text-wrapp">
                                        <?= divideString(round($item['fakt'])*1, 3)?>
                                    </p>
                                </td>
                                <td class="bg-lighties balance" data-price="<?=$item['balance']?>">
                                    <p class="text-wrapp">
                                        <?= divideString(round($item['balance'])*1, 3)?>
                                    </p>
                                </td>
                            <?php endforeach; ?>

                        </tr>
                        <?php if(isset($model['parts']) && !empty($model['parts'])):?>
                            <?php $inc = 1;?>

                            <?php foreach($model['parts'] as $part_id => $part):?>
                                <tr class="collapse demo-<?=$customer_id?>">
                                    <td class="bg-primaries"><?= $inc++;?></td>  
                                    <td class="bg-primaries">
                                        <p class="text-wrapp">
                                            <?= $part['part_name']?>
                                        </p>
                                    </td>  
                                    <td class="bg-primaries">
                                        <p class="text-wrapp">
                                            <?= $part['part_color']?>
                                        </p>
                                    </td>
                                    <?php foreach($part['list'] as $key2 => $part_item): ?>
                                            <td class="bg-primaries">
                                                <?= divideString($part_item['plan']*1, 3)?></td>
                                            <td class="bg-primaries">
                                                <?= divideString($part_item['fakt']*1, 3)?></td>
                                            <td class="bg-primaries balance" data-price="<?=$part_item['balance']?>"><?= divideString($part_item['balance']*1, 3)?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
            </tbody>
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
        window.location.href = '<?=Url::to(['dashboard/plan-prodaj'])?>?firstType='+id+'&secondType=<?=$secondType?>';
    })

    // secondType click
    $('.second-type').on('click', function(){
        let id = $(this).data('id');
        window.location.href = '<?=Url::to(['dashboard/plan-prodaj'])?>?firstType=<?=$firstType?>&secondType='+id;
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
})

<?php $this->registerJs(ob_get_clean(), \yii\web\View::POS_READY); ?>