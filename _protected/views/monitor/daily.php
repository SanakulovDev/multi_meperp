<?php

use app\components\Helpers;
use yii\widgets\LinkPager;

/* @var TYPE_NAME $productLine */

function backgroundColor($value) {
    return $value==0 ? '' : ($value>0 ? 'bg-blue' : 'bg-red');
}

function formatNumber($value) {
    if($value && $value!='-')
      return Helpers::numberFormatRemoveZero((float)$value, 2, '.', " ", true);
    return ' ';
}
?>
<style>
    .table-bordered {
        font-size: 30px;
    };
    .td-nowrap{
         overflow: hidden;
         text-overflow: ellipsis;
         white-space: nowrap;
     }
</style>
<div class="panel">
    <div class="panel-heading">

    </div>
</div>

<div class="content" style="min-height: 960px;">
    <header class="main-header text-center">
        <span class="text-bold pull-left"
              style="font-size: 50px;"><?=$productLine ? $productLine->linename : '-'?></span>
        <span class="" style="font-size: 50px;"><?=$need_date?></span>
        <span class="pull-right" style="font-size: 50px;" id="clock"></span>
    </header>
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row panel panel-primary">
            <table class="table table-striped table-bordered table-condensed table-sm-padding_2_0">
                <thead>
                <tr>
                    <th rowspan="3" class="text-center"><?=Yii::t('app', 'Product no')?></th>
                    <th colspan="3" class="text-center">1-shift</th>
                    <th colspan="3" class="text-center">2-shift</th>
                    <th colspan="3" class="text-center">All</th>
                </tr>
                <tr>
                  <?php for ($r = 1; $r <= 3; $r++): ?>
                      <th class="text-center"><?=Yii::t('app', 'plan')?></th>
                      <th class="text-center"><?=Yii::t('app', 'actual')?></th>
                      <th class="text-center b_<?=$r?>"><?=Yii::t('app', 'balance')?></th>
                  <?php endfor; ?>
                </tr>
                </thead>
                <tbody>
                <?php
                $qq = 1;
                foreach ($data as $prod_list){
                  ?>
                    <tr>
                        <td class="midtext td-nowrap">
                          <?=$prod_list['part_no']?> (<span style='font-size:70%'><?=$prod_list['part_name']?></span>)
                        </td>
                        <td class="midtext text-right"><?=formatNumber($prod_list['d_plan1_qty'])?></td>
                        <td class="midtext text-right"><?=formatNumber($prod_list['actual1'])?></td>
                        <td class="midtext text-right f_bold b_1 <?=backgroundColor($prod_list['d_balance1'])?>">
                          <?=formatNumber($prod_list['d_balance1'])?>
                        </td>
                        <td class="midtext text-right"> <?=formatNumber($prod_list['d_plan2_qty'])?> </td>
                        <td class="midtext text-right"> <?=formatNumber($prod_list['actual2'])?> </td>
                        <td class="midtext text-right f_bold b_2 <?=backgroundColor($prod_list['d_balance2'])?>">
                          <?=formatNumber($prod_list['d_balance2'])?>
                        </td>
                        <td class="midtext text-right"> <?=formatNumber($prod_list['d_all_plan'])?> </td>
                        <td class="midtext text-right"> <?=formatNumber($prod_list['d_all_actual'])?> </td>
                        <td class="midtext text-right f_bold b_3 <?=backgroundColor($prod_list['d_all_balance'])?>">
                          <?=formatNumber($prod_list['d_all_balance'])?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
    <!--	--><?=LinkPager::widget([
    'pagination' => $pages,
  ]);?>
</div>
<script>
    setInterval(function () {
        var ul = $('ul.pagination');
        if (ul.length) {
            var li = $('ul.pagination > li.next');
            if (li.is('.next.disabled')) {
                $('ul.pagination li:nth-child(2) a:first')[0].click()
            } else {
                li.find('a')[0].click()
            }
        } else {
            location.reload()
        }
    }, 60000);

    function startTime() {
        var today = new Date();
        var h = today.getHours();
        var m = today.getMinutes();
        var s = today.getSeconds();
        m = checkTime(m);
        s = checkTime(s);
        document.getElementById('clock').innerHTML =
            h + ':' + m + ':' + s;
        var t = setTimeout(startTime, 500)
    }

    function checkTime(i) {
        if (i < 10) {
            i = '0' + i
        }
        // add zero in front of numbers < 10
        return i
    }

    startTime()
</script>
