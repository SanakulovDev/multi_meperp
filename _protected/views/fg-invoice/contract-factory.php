<?php
use yii\grid\GridView;
use yii\helpers\Html;
use app\models\Customer;
use app\models\FgInvoice;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\FgInvoice */
/* @var $searchModel app\models\FgInvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/** @var TYPE_NAME $customers */
/** @var TYPE_NAME $factories */
/** @var TYPE_NAME $modelImport */
$this->title = Yii::t('app', 'Группа Счёт фактура');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('fg-invoice-update');
$canDelete = Yii::$app->user->can('fg-invoice-delete');
$canView = Yii::$app->user->can('fg-invoice-view');
$canConfirm = Yii::$app->user->can('fg-invoice-confirm');
$canReject = Yii::$app->user->can('fg-invoice-reject');
$canPrint = Yii::$app->user->can('fg-invoice-print');
?>


<?php Pjax::begin(); ?>
<?=GridView::widget(
  [
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
    'rowOptions' => ['style' => 'white-space:nowrap; vertical-align:middle;'],
    'options' => ['style' => 'overflow:auto;clear:both'],
    'emptyText' => Yii::t('app', 'No results found.'),
    'tableOptions' => [
      'class' => 'sm_filter table table-striped table-bordered table-condensed table-sm-padding_2_0',
      'data-step' => 4,
      'data-intro' => Yii::t('intro', 'data-table')
    ],
    'filterRowOptions' => ['data-step' => 5, 'data-intro' => Yii::t('intro', 'filter')],
    'pager' => [
      'class' => '\yii\widgets\LinkPager',
      'options' => [
        'class' => 'pagination',
        'data-step' => 6,
        'data-intro' => Yii::t('intro', 'pagination')
      ],
    ],
    'columns' => [
      [
        'class' => 'yii\grid\SerialColumn',
        'header' => '№',
        'headerOptions' => ['style' => 'width:100px;text-align: center;color: #3c8dbc;'],
        'contentOptions' => ['style' => 'width:100px;text-align: center;']
      ],
      [
        'class' => 'yii\grid\ActionColumn',
        'header' => '<i class="fa fa-fw fa-gears"></i>',
        'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
        'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
        'template' => '{view}',
        'buttons' => [
          
          'view' => function($url, $model) use ($canView) {
            if(!$canView) return false;
            $url = Url::toRoute(['fg-invoice/contract-factory-view', 'contract' => $model->contract]);
            return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>', $url, [
                'title' => Yii::t('app', 'View'),
                'target' => '_blank'
              ]).'&nbsp;';
          },
        ],
        'visible' => $canView
      ],
      'contract',
      [
        'attribute' => 'contract_factory',
        'label' => Yii::t('app', 'Счет фактура'),
        'value' => function($model) use ($contracts) {
          if(isset($contracts) && isset($contracts[$model->contract]['waybill_no'])){
            if(!empty($contracts[$model->contract]['waybill_no'])){
              // vd($contracts[$model->contract]['waybill_no']);
              
              $arr = implode(', ', $contracts[$model->contract]['waybill_no']);
              return $arr;
            }
          }
            return null;
        },
      ],

      [
        'attribute' => '',
        'label' => Yii::t('app', 'Клиент'),
        'value' => function($model) use ($contracts) {
          if(isset($contracts) && isset($contracts[$model->contract]['customer'])){
            if(!empty($contracts[$model->contract]['customer'])){
              return $contracts[$model->contract]['customer'];
            }
          }
          return null;
        },
      ]
      
    ],
  ]
);?>

<?php Pjax::end(); ?>

</div>
