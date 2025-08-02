<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin(['id' => 'form_read','action' => Url::toRoute(['part/pop'])]); ?>
    <input type="hidden" name="PopForm[part_id]" value="<?=$row['part_id']?>">
    <?= Html::a($row['part_no'], 'javascript:void(0)', $options = ['onclick' => '$(this).parent().attr("target", "_blank").submit()'])  ?>
  <?php ActiveForm::end(); ?>
