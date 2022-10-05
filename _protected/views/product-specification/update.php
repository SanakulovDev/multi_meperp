<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ProductSpecification */

$this->title = Yii::t('app', 'Product specification').': '.$model->code;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product specification'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->code, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

?>
<div class="product-specification-update">
    <?= $this->render('_form', compact('model','items','errorlist')) ?>

    <div class="">
        <table class="table table-bordered table-condensed">
            <tr>
                <th><?=Yii::t('app', 'Updated by')?></th>
                <td><?=$model->updatedBy->fullname?></td>
                <th><?=Yii::t('app', 'Updated at')?></th>
                <td><?=$model->updatedAtFormatted?></td>
            </tr>
        </table>
    </div>
</div>
