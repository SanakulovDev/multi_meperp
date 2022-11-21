<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \app\models\Posts */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app',Yii::t('app','Posts')), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="photos-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app','Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app','Add Image'), ['upload', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app','Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app','Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="row">
      <div class="col-md-6">
        <?= DetailView::widget([
                                 'model' => $model,
                                 'attributes' => [
                                   'id',
                                   'weight',
                                   'material',
                                   'is_where',
                                   'date',
                                   'comment',
                                 ],
                               ]) ?>
      </div>
      <div class="col-md-6">
        <div class="row">
          <?php
          foreach ($model->getImages() as $image)
          {
            ?>
            <div class="col-md-4" style="margin-right: 1rem;">
              <?php
               echo Html::img('/img/posts/'.$image->path,['width'=>'300px','style'=>'margin-bottom:1rem;']);

              ?>
              <?= Html::a(Yii::t('app','Delete'), ['delete-image', 'id' => $image->id], ['class' => 'btn btn-danger']) ?>

            </div>
            <?php
          }
          ?>
        </div>

      </div>
    </div>



</div>
