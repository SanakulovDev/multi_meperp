<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InvoicePartProblemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('app', 'Invoice Part Problems');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('invoice-detail-fix-problem');
?>
<div class="invoice-part-problem-index">

  <?=Html::a(Yii::t('app', 'btn-back'), ['/container-invoice/index'], ['class' => 'btn btn-default btn-xs'])?>

  <?php Pjax::begin(); ?>
  <?=GridView::widget(
    [
      'dataProvider' => $dataProvider,
      'filterModel' => $searchModel,
      'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
      'options' => ['style' => 'overflow:auto;clear:both'],
      'emptyText' => Yii::t('app', 'No results found.'),
      'tableOptions' => [
        'class' => 'table-td-nowrap sm_filter table table-striped table-bordered table-condensed table-sm-padding_2_0',
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
        [
          'class' => 'yii\grid\SerialColumn',
          'header' => '№',
          'headerOptions' => ['style' => 'width: auto;text-align: center;color: #3c8dbc;'],
          'contentOptions' => ['style' => 'width: auto;text-align: center;']
        ],
        //        [
        //          'class' => 'yii\grid\ActionColumn',
        //          'template' => '{update}',
        //          'header' => '<i class="fa fa-fw fa-gears"></i>',
        //          'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
        //          'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
        //        ],
        [
          'class' => 'yii\grid\ActionColumn',
          'template' => '{update}',
          'header' => '<i class="fa fa-fw fa-gears"></i>',
          'headerOptions' => ['style' => 'width:auto; text-align: center;vertical-align:middle;color: #3c8dbc;'],
          'contentOptions' => ['style' => 'width:auto; text-align: center;vertical-align:middle;'],
          'buttons' => [
            'update' => function($url, $model) use ($canUpdate) {
              //              if (!$canUpdate)
              //                return false;
              $url = Url::toRoute(['invoice-detail/fix-problem', 'id' => $model->inv_detail_id]);

              return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url, [
                'title' => Yii::t('app', 'Fix problem')
              ]);
            }
          ],
          //          'visible' => $canUpdate
        ],
        [
          'attribute' => 'inv_detail_id',
          'value' => function($data) {
            $inv_detail = (!empty($data->invDetail)) ?
              $data->invDetail->contInv->invoice->invoice_no." (".
              $data->invDetail->contInv->container->container_no.") - ".
              $data->invDetail->part->part_no
              : '-';

            return $inv_detail;
          },
          'contentOptions' => function($model, $key, $index, $column) {
            $content_with_title = ['title' => $model->inv_detail_id];

            return $content_with_title;
          }
        ],
        'part_order_no',
        'contract_no',
        [
          'attribute' => 'created_by',
          'value' => function($data) {
            $created_val = (!empty($data->createdBy)) ? $data->createdBy->username : '-';

            return $created_val;
          },
          'contentOptions' => function($model, $key, $index, $column) {
            $content_with_title = (!empty($model->createdBy)) ? ['style' => 'text-align: center;', 'title' => date('d.m.Y (H:i:s)', $model->created_at)] : ['style' => 'text-align: center;'];

            return $content_with_title;
          }
        ],
        [
          'attribute' => 'updated_by',
          'value' => function($data) {
            $updated_val = (!empty($data->updatedBy)) ? $data->updatedBy->username : '-';

            return $updated_val;
          },
          'contentOptions' => function($model, $key, $index, $column) {
            $content_with_title = (!empty($model->updatedBy)) ? ['style' => 'text-align: center;', 'title' => date('d.m.Y (H:i:s)', $model->updated_at)] : ['style' => 'text-align: center;'];

            return $content_with_title;
          }
        ]
      ],
    ]);?>

  <?php Pjax::end(); ?>

</div>
