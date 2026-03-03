<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Formulation */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Formulations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="formulation-view">

    <h1><?= Html::encode("")//Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'formulation_base_id',
            'amount',
            'customer_id',
            'order_no',
            'ulock',
            'due_at',
            'start_at',
            'finish_at',
            'act_rate',
            'grind',
            'packages:ntext',
        ],
    ]) ?>


    
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
                <?php foreach ($specificList as $row) : ?>
                    <tr>
                        <td class="Item"><?php echo $row->item; ?></td>
                        <td class="Min"><?php echo $row->min; ?></td>
                        <td class="Max"><?php echo $row->max; ?></td>
                        <td class="Result"><?php echo $row->result; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </fieldset>
    </div>
    <br/>
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
                        Order usage
                    </th>
                    <th class="text-center">
                        Actual order usage
                    </th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($titleList as $row) : ?>
                <tr>
                    <td class="Code"><?php echo $row->part_id; ?></td>
                    <td class="Lot_No"><?php echo $row->std_value; ?></td>
                    <td class="Usage"><?php echo $row->actual_value; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </fieldset>
    </div>
</div>
