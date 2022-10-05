<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PartActiveLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('app', 'Part Active Logs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="part-active-log-index">
  <h1><?=Html::encode($this->title)?></h1>
  <p>
    <?=Html::a(Yii::t('app', 'Create Part Active Log'), ['create'], ['class' => 'btn btn-success'])?>
  </p>

  <?php Pjax::begin(); ?>

  <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
      ['class' => 'yii\grid\SerialColumn'],
      'id',
      'part_no',
      'begin_date',
      'end_date',
      'status',
      'is_plan',
      ['class' => 'yii\grid\ActionColumn'],
    ],
  ]);?>

  <?php Pjax::end(); ?>

</div>
