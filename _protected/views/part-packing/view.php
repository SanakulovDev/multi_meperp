<?php
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\PartPacking */
$this->title = $model->part->partinfo.' - '.$model->pack->code;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Component'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="part-packing-view">

  <p>
    <?=Html::a(Yii::t('app', 'btn-update'), ['update', 'id' => $model->id], ['class' => 'btn btn-sm btn-primary'])?>
    <?=Html::a(Yii::t('app', 'btn-delete'), ['delete', 'id' => $model->id], [
      'class' => 'btn btn-sm btn-danger',
      'data' => [
        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
        'method' => 'post',
      ],
    ])?>
  </p>

  <?=DetailView::widget([
    'model' => $model,
    'attributes' => [
      'id',
      [
        'attribute' => 'part_id',
        'value' => $model->part ? $model->part->partInfo : null
      ],
      [
        'attribute' => 'supplier_id',
        'value' => $model->supplier ? $model->supplier->name.' '.$model->supplier->duns : null
      ],
      [
        'attribute' => 'returnable',
        'value' => $model->returnableFormatted
      ],
      [
        'attribute' => 'pack_qty',
        'value' => $model->pack_qty + 0
      ],
      [
        'attribute' => 'piece_weight',
        'value' => $model->piece_weight + 0
      ],
      'netto',
      'brutto',
      [
        'attribute' => 'created_by',
        'value' => $model->createdBy->fullname,
      ],
      [
        'attribute' => 'created_at',
        'value' => $model->createdAtFormatted
      ],
      [
        'attribute' => 'updated_by',
        'value' => $model->updatedBy->fullname,
      ],
      [
        'attribute' => 'updated_at',
        'value' => $model->updatedAtFormatted
      ]
    ],
  ])?>

  <table class="table table-bordered">
    <thead>
    <th><?=Yii::t('app', 'Pack')?></th>
    <th><?=Yii::t('app', 'Quantity')?></th>
    <th><?=Yii::t('app', 'Net weight')?></th>
    <th><?=Yii::t('app', 'Gross weight')?></th>
    <!--            <th>--><? //=Yii::t('app', 'Version')?><!--</th>-->
    </thead>
    <tbody>
    <?php foreach($packLevels as $level): ?>
      <tr>
        <td><?=$level->inPack->code?></td>
        <td><?=$level->quantity?></td>
        <td><?=$model->netto*$level->quantity?></td>
        <td><?=$model->brutto*$level->quantity + $level->inPack->weight?></td>
        <!--                <td>--><? //=$level->version?><!--</td>-->
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
