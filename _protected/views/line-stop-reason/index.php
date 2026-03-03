<?php
use app\rbac\models\AuthItem;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LineStopReasonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Line stop reason');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('line-stop-reason-update');
$canDelete = Yii::$app->user->can('line-stop-reason-delete');
$roles = ArrayHelper::map(AuthItem::find()->where(['type'=>AuthItem::TYPE_ROLE])->all(),'name','name');
?>
<div class="pack-level-index">
  <p class="pull-right">
    <?php
    if (Yii::$app->user->can('line-stop-reason-create'))
      echo Html::a(Yii::t('app', 'btn-create'), ['create'],
                   [
                     'class' => 'btn btn-success btn-sm form-modal',
                     'data-intro' => Yii::t('intro', 'add-new-record')
                   ]);
    ?>
  </p>
  <?php Pjax::begin(['id' => 'pjaxGrid']); ?>

  <?= GridView::widget([
                         'dataProvider' => $dataProvider,
                         'filterModel' => $searchModel,
                         'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
                         'options' => ['style' => 'overflow:auto;clear:both'],
                         'emptyText' => Yii::t('app', 'No results found.'),
                         'tableOptions' => [
                           'class' => 'sm_filter table table-striped table-bordered table-condensed table-sm-padding_2_0',
                           'data-intro' => Yii::t('intro', 'data-table')
                         ],
                         'filterRowOptions' => ['data-intro' => Yii::t('intro', 'filter')],
                         'pager' => [
                           'class' => '\yii\widgets\LinkPager',
                           'options' => [
                             'class' => 'pagination',
                             'data-intro' => Yii::t('intro', 'pagination')
                           ],
                         ],
                         'columns' => [
                           ['class' => 'yii\grid\SerialColumn'],
                           [
                             'class' => 'yii\grid\ActionColumn',
                             'template' => '{update} {delete} ',
                             'header' => '<i class="fa fa-fw fa-gears"></i>',
                             'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
                             'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
                             'buttons' => [
                               'update' => function ($url, $model) use ($canUpdate) {
                                 if (!$canUpdate) return false;
                                 return Html::a(
                                   '<span  class="glyphicon glyphicon-pencil"></span>',
                                   false,
                                   [
                                     'class' => 'modalButtonUpdate',
                                     'value' => $url,
                                     'title' => Yii::t('app', 'Update')
                                   ]
                                 );
                               },
                               'delete' => function ($url, $model) use ($canDelete) {
                                 if (!$canDelete) return false;
                                 return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                                                false,
                                                [
                                                  'class' => 'modalButtonDelete',
                                                  'data-href' => $url,
                                                  'data-grid' => 'pjaxGrid',
                                                  'title' => Yii::t('app', 'Delete')
                                                ]);
                               }
                             ],
                             'visible' => $canDelete || $canUpdate
                           ],
                           [
                             'attribute' => 'type',
                             'filter' => Html::activeDropDownList($searchModel, 'type', $searchModel->getTypes(), ['class' => 'form-control select2', 'prompt' => '...']),
                             'content' => function ($model) {
                               return $model->getTypes()[$model->type];
                             }
                           ],
                           [
                             'attribute' => 'auth_item_name',
                             'filter' => Html::activeDropDownList($searchModel, 'auth_item_name', $roles, ['class' => 'form-control select2', 'prompt' => '...']),
                             'content' => function ($model) {
                               return $model->auth_item_name;
                             },
                           ],
                           'name',
                         ],
                       ]); ?>

  <?php Pjax::end(); ?>

</div>
