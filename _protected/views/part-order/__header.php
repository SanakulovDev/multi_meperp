<?
use app\components\Helpers;

?>
<div class="row">
    <div class="col-lg-2">
        <span class="text-bold"><?=Yii::t('app', 'Issued date')?>:</span>
        <span><?=$model->iss_dt?></span>
    </div>
    <div class="col-lg-2">
        <span class="text-bold"><?=Yii::t('app', 'For month')?>:</span>
        <span><?=$model->for_month?></span>
    </div>
    <div class="col-lg-2">
        <span class="text-bold"><?=Yii::t('app', 'Order type')?>:</span>
        <span><?=$model->orderTypeText?></span>
    </div>
    <div class="col-lg-4">
        <span class="text-bold"><?=Yii::t('app', 'Contract no')?>:</span>
        <span><?=$model->contract->contract_no?></span>
        <input type="hidden" id="cont_id" value="<?=$model->contract_id?>" />
    </div>
    <div class="col-lg-3">
        <span class="text-bold"><?=Yii::t('app', 'Order amount')?>:</span>
        <span><?=number_format(Helpers::formatRemoveDecimal($model->amount, 3), 3, '.', ' ').'&nbsp;&nbsp;'.$model->contract->currency->code?></span>
    </div>
    <div class="col-lg-2">
        <span class="text-bold"><?=Yii::t('app', 'Delivery term')?>:</span>
        <span><?=$model->deliveryTerm->name?></span>
    </div>
</div>

