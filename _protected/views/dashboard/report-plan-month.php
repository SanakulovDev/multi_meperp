<?php
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Резултат продаж за '.$monthName.' месяц '.$year.' г';
$this->params['breadcrumbs'][] = $this->title;
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

<?php $this->registerCss(ob_get_clean()); ?>

<div class="pull-right1" style="display: flex;">
    <!-- dropdown month list -->
    <div class="btn-group" style="margin:auto; margin-right: 0px;">
        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
            <?=$monthName?> <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" role="menu">
        <?php foreach($monthList as $key=> $month):?>
            <li>
                <?=Html::a($month, Url::to(['dashboard/report-plan-month', 'month' => $key]))?>
            </li>
            <?php endforeach;?>
        </ul>
    </div>
</div>
<div class="dashboard-report-plan-month">


    <div class="main-content">
        <table class="table tbl_plan">
            <thead>
                <tr>
                    <th class="bg-primaries" rowspan="2">№</th>
                    <th class="bg-primaries" rowspan="2">
                        <p class="text-wrapp">
                            <?= Yii::t('app', 'Part name')?>
                        </p>
                    </th>
                    <th class="bg-primaries" rowspan="2">
                        <p class="text-wrapp">
                            <?= Yii::t('app', 'Part color')?>
                        </p>
                    </th>
                    <th class="bg-primaries" colspan="3">
                        Планиоровано
                    </th>
                    <th class="bg-primaries" colspan="3">
                        Факт продаж
                    </th>
                    <th class="bg-primaries" colspan="3">
                        Разница
                    </th>
                </tr>
                <tr>
                    <?php for($i=0; $i<3;$i++):?>
                        <th class="bg-primaries">
                            Кол-во (кг)
                        </th>
                        
                        <th class="bg-primaries">
                            Цена UZS
                        </th>
                        <th class="bg-primaries">
                            Сумма 
                        </th>

                    <?php endfor;?>
                </tr>
            </thead>

            <tbody>
                <?php if(!empty($models)):?>
                    <?php $inx=1;?>
                <?php foreach($models as $key => $model):?>
                    <?php if(empty($model['parts'])){
                        continue;
                    } ?>
                    <tr class="cell-1" data-toggle="collapse" data-target=".demo-<?=$key?>">
                        <td class="bg-lighties"><?= $inx++?></td>
                        <td class="bg-lighties" colspan="2"><?= substr($model['customer_name'], 0,40)?></td>
                        <td class="bg-lighties"><?= divideString(round($model['plan']['quantity']),3)?></td>
                        <td class="bg-lighties"><?= divideString(round($model['plan']['price']),3)?></td>
                        <td class="bg-lighties"><?= divideString(round($model['plan']['sum']),3)?></td>

                        <td class="bg-lighties"><?= divideString(round($model['fakt']['quantity']),3)?></td>
                        <td class="bg-lighties"><?= divideString(round($model['fakt']['price']),3)?></td>
                        <td class="bg-lighties"><?= divideString(round($model['fakt']['sum']),3)?></td>

                        <td class="bg-lighties balance" data-price="<?= round($model['balance']['quantity']*1)?>"><?= divideString(round($model['balance']['quantity']*1),3)?></td>
                        <td class="bg-lighties balance" data-price="<?= round($model['balance']['price']*1)?>"><?= divideString(round($model['balance']['price']),3)?></td>
                        <td class="bg-lighties balance" data-price="<?= round($model['balance']['sum']*1)?>"><?= divideString(round($model['balance']['sum']),3)?></td>
                    </tr>
                    <?php if(empty($model['parts'])):?>
                        <tr class="collapse bg-primaries demo-<?=$key?>">
                            <td class="bg-primaries" colspan="12">
                                <?=Yii::t('app', 'No results found.')?>
                            </td>
                        </tr>
                    <?php else:?>
                        <?php $inx1 = 1;?>
                        <?php foreach($model['parts'] as $key1 => $part):?>
                            <tr class="collapse bg-primaries demo-<?=$key?>">
                                <td class="bg-primaries"><?= $inx++?></td>
                                <td class="bg-primaries"><?= substr($part['part_name'], 0, 40)?></td>
                                <td class="bg-primaries"><?=$part['part_color']?></td>
                                <td class="bg-primaries"><?= divideString(round($part['plan']['quantity']),3)?></td>
                                <td class="bg-primaries"><?= divideString(round($part['plan']['price']),3)?></td>
                                <td class="bg-primaries"><?= divideString(round($part['plan']['sum']),3)?></td>

                                <td class="bg-primaries"><?= divideString(round($part['fakt']['quantity']),3)?></td>
                                <td class="bg-primaries"><?= divideString(round($part['fakt']['price']),3)?></td>
                                <td class="bg-primaries"><?= divideString(round($part['fakt']['sum']),3)?></td>

                                <td class="bg-primaries balance" data-price="<?= round($part['balance']['quantity']*1)?>"><?= divideString(round($part['balance']['quantity']),3)?></td>
                                <td class="bg-primaries balance" data-price="<?= round($part['balance']['price']*1)?>"><?= divideString(round($part['balance']['price']),3)?></td>
                                <td class="bg-primaries balance" data-price="<?= round($part['balance']['sum']*1)?>"><?= divideString(round($part['balance']['sum']),3)?></td>


                            </tr>
                         <?php endforeach;?>
                    <?php endif;?>
                <?php endforeach;?>
                <?php else:?>
                <tr class="collapse bg-primaries demo-<?=$key?>">
                    <td class="bg-primaries" colspan="12">
                        <?=Yii::t('app', 'No results found.')?>
                    </td>
                </tr>
                <?php endif;?>
            </tbody>
        </table>
    </div>

</div>

<?php ob_start(); ?>
    
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

    $(".tbl-plan").tableFixer({'left' : 3, 'head': false, 'foot': false});



<?php $this->registerJs(ob_get_clean()); ?>