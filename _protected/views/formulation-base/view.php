<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Part;

/* @var $this yii\web\View */
/* @var $model app\models\FormulationBase */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Formulation Bases'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="formulation-base-view">

    <p>
        <? if (Yii::$app->user->can('formulation-base-update')) { ?>
            <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <? } ?>
        <? if (Yii::$app->user->can('formulation-base-delete')) { ?>
            <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        <? } ?>
    </p>
    <?php
    $part_name = Part::findBySql("select part_no from part where id = $model->part_id")->One();
    $model->part_id = $part_name->part_no;
    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'part_id',
            'pack',
            'version',
            'status',
            'std_rate',
            // 'items:ntext',
            // 'specifications:ntext',
            // 'instructions:ntext',
        ],
    ]) ?>

    <style>
        #formulationbase-instructions {
            display: block;
            margin: auto;
            font-size: 16px;
            width: 100%;
            padding: 10px 0;
            line-height: 37px;
            background-image: linear-gradient(#eee 1px, transparent 1px);
            background-size: 100% 37px;
            border: 1px solid lightgray;
            outline: 0;
        }
    </style>

    <div class="table-responsive">
        <fieldset class="scheduler-border">
            <legend class="scheduler-border">
                <span>Specification table</span>
            </legend>
            <table class="table table-bordered table-striped table-hover table-condensed  text-center">
                <thead class="bg-success">
                    <tr>
                        <th class="text-center">
                            Item
                        </th>
                        <th class="text-center">
                            Min
                        </th>
                        <th class="text-center">
                            Max
                        </th>
                        <th class="text-center">
                            Result
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach ($specificList as $row) : ?>
                        <tr>
                            <td class="Item"><? echo $row->Item; ?></td>
                            <td class="Min"><? echo $row->Min; ?></td>
                            <td class="Max"><? echo $row->Max; ?></td>
                            <td class="Result"><? echo $row->Result; ?></td>
                        </tr>
                    <? endforeach; ?>
                </tbody>
            </table>
        </fieldset>
    </div>
    <br />
    <div class="table-responsive">
        <fieldset class="scheduler-border">
            <legend class="scheduler-border">
                <span>Items table</span>
            </legend>
            <table class="table table-bordered table-striped table-hover table-condensed  text-center">
                <thead class="bg-success">
                    <tr>
                        <th class="text-center">
                            Code
                        </th>
                        <th class="text-center">
                            Lot No
                        </th>
                        <th class="text-center">
                            Usage
                        </th>
                        <th class="text-center">
                            Order usage
                        </th>
                        <th class="text-center">
                            Actual order usage
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach ($titleList as $row) : ?>
                        <tr>
                            <td class="Code"><? echo $row->Code; ?></td>
                            <td class="Lot_No"><? echo $row->Lot_No; ?></td>
                            <td class="Usage"><? echo $row->Usage; ?></td>
                            <td class="Order_usage"><? echo $row->Order_usage; ?></td>
                            <td class="Actual_order_usage"><? echo $row->Actual_order_usage; ?></td>
                        </tr>
                    <? endforeach; ?>
                </tbody>
            </table>
        </fieldset>
    </div>
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'instructions')->textarea(['rows' => 5]) ?>
    <?php ActiveForm::end(); ?>
</div>