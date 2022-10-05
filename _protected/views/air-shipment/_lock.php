<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\AirShipment */
/* @var $form yii\widgets\ActiveForm */
?>
<form method="GET" action="<?=Url::to(['air-shipment/lock'])?>" class="form-inline">
    <div class="form-group">
        <?= Html::input('month', 'period', null, ['class'=>'form-control']); ?>
    </div>
    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-lock"></i>', ['name' => 'submit', 'value' => 'lock', 'title'=>Yii::t('app','Lock'), 'class'=>'btn btn-sm btn-danger']) ?>
        <?= Html::submitButton('<i class="fa fa-unlock"></i>', ['name' => 'submit', 'value' => 'unlock', 'title'=>Yii::t('app','Unlock'), 'class'=>'btn btn-sm btn-warning']) ?>
    </div>
</form>
