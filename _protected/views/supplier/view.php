<?php

use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Supplier */
$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Supplier info'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="supplier-view">
    <p>
      <?= Html::a(Yii::t('app', 'btn-update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
      <?= Html::a(Yii::t('app', 'btn-delete'), ['delete', 'id' => $model->id], [
        'class' => 'btn btn-danger',
        'data' => [
          'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
          'method' => 'post',
        ],
      ]) ?>
      <?= Html::a(Yii::t('app', 'btn-back'), ['index'], ['class' => 'btn btn-default']) ?>
    </p>

  <?= DetailView::widget([
    'model' => $model,
    'attributes' => [
      'name',
      'duns',
      'alias',
      'address',
      'city',
      'postal',
      'transit_time',
//      'country',
//      'country_code',
      [
        'attribute' => 'country_code_id',
        'value' => $model->countryCode ? $model->countryCode->name : '',
      ],
      'contact_name',
      'contact_position',
      'contact_email:email',
      'contact_phone',
      'contact_cellular',
    ],
  ]) ?>

</div>
