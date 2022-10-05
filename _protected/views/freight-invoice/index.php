<?php
use kartik\datetime\DateTimePicker;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/**
 * @var $this          yii\web\View
 * @var $searchModel   app\models\FreightInvoiceSearch
 * @var $dataProvider  yii\data\ActiveDataProvider
 * @var $invoiceType   app\controllers\FreightInvoiceController
 * @var $routes        app\controllers\FreightInvoiceController
 * @var $carriers      app\controllers\FreightInvoiceController
 * @var $deliveryTerms app\controllers\FreightInvoiceController
 * @var $currencies    app\controllers\FreightInvoiceController
 */
$this->title = Yii::t('app', 'Freight invoices');
$this->params['breadcrumbs'][] = $this->title;
$canView = Yii::$app->user->can('freight-invoice-view');
$canUpdate = Yii::$app->user->can('freight-invoice-update');
$canDelete = Yii::$app->user->can('freight-invoice-delete');

$select2prompt = Yii::t('app', 'All');
?>
<div class="freight-invoice-index">
	<div class="pull-right">
    <? if(Yii::$app->user->can('freight-invoice-create')) { ?>
      <?=Html::a(
        Yii::t('app', 'btn-create'),
        ['create'],
        [
          'class' => 'btn btn-success btn-sm',
          'data-intro' => Yii::t('intro', 'add-new-record')
        ]
      )
      ?>
    <? } ?>
	</div>
	<div class="clearfix"></div>
	<br>

  <?php Pjax::begin(); ?>
  <?=GridView::widget([
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
        'headerOptions' => ['style' => 'width:30px;text-align: center;color: #3c8dbc;'],
        'contentOptions' => ['style' => 'width:30px;text-align: center;']
      ],
      [
        'class' => 'yii\grid\ActionColumn',
        'header' => '<i class="fa fa-fw fa-gears"></i>',
        'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
        'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
        'template' => '{view}{update}{delete}',
        'buttons' => [
          'view' => function($url, $model) use ($canView) {
            if(!$canView) return false;
            $url = Url::toRoute(['freight-invoice/view', 'id' => $model->id]);
            return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>', $url, [
                'title' => Yii::t('app', 'View')
              ]).'&nbsp;';
          },
          'update' => function($url, $model) use ($canUpdate) {
            if(!$canUpdate) return false;
            $icon_style = 'text-warning';
            $title = Yii::t('app', 'Update');
            $url = Url::toRoute(['freight-invoice/update', 'id' => $model->id]);
            return Html::a('<span class="fa fa-pencil" aria-hidden="true"></span>', $url, [
                'title' => Yii::t('app', 'Update'),
                'class' => $icon_style
              ]).'&nbsp;';
          },
          'delete' => function($url, $model) use ($canDelete) {
            if(!$canDelete) return false;
            $icon_style = 'text-danger';
            $title = Yii::t('app', 'Delete');
            $url = Url::toRoute(['freight-invoice/delete', 'id' => $model->id]);
            return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>', $url, [
                'title' => Yii::t('app', 'Delete'),
                'data' => [
                  'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                  'method' => 'post',
                ],
                'class' => $icon_style
              ]).'&nbsp;';
          },
        ],
        'visible' => $canView || $canUpdate || $canDelete
      ],
      [
        'attribute' => 'invoice_type',
        'filter' => Html::activeDropDownList(
          $searchModel,
          'invoice_type',
          $invoiceType,
          ['class' => 'form-control select2', 'prompt' => $select2prompt]
        ),
        'content' => function($model) {
          return $model->freightInvoiceType;
        },
        'contentOptions' => function($model, $column) {
          return ['class' => 'td-nowrap'];
        }
      ],
      'invoice_no',
      [
        'attribute' => 'invoice_date',
        'filter' =>
          DateTimePicker::widget([
            'size' => 'xs',
            'removeButton' => ['position' => 'append'],
            'type' => DateTimePicker::TYPE_INPUT,
            'name' => 'FreightInvoiceSearch[invoice_date]',
            'model' => $searchModel,
            'attribute' => 'invoice_date',
            'language' => 'ru',
            'pluginOptions' => [
              'autoclose' => true,
              'format' => 'yyyy-mm-dd',
              'startView' => 'month',
              'minView' => 'month',
              'maxView' => 'month',
            ],
            'options' => [
              'autocomplete' => 'off',
              'placeholder' => 'YYYY-MM-DD',
              'class' => 'form-control input-sm'
            ]
          ]),
        'format' => 'html',
        'contentOptions' => function($model, $column) {
          return ['class' => 'td-nowrap'];
        }
      ],
      [
        'attribute' => 'carrier_id',
        'value' => 'carrier.company_name',
        'filter' => Html::activeDropDownList(
          $searchModel,
          'carrier_id',
          $carriers,
          ['class' => 'form-control select2', 'prompt' => $select2prompt]
        ),
        'contentOptions' => function($model, $column) {
          return ['class' => 'td-nowrap'];
        }
      ],
      'contract',
      [
        'attribute' => 'route_id',
        'value' => 'route.name',
        'filter' => Html::activeDropDownList(
          $searchModel,
          'route_id',
          $routes,
          ['class' => 'form-control select2', 'prompt' => $select2prompt]
        ),
        'contentOptions' => function($model, $column) {
          return ['class' => 'td-nowrap'];
        }
      ],
      [
        'attribute' => 'currency_id',
        'value' => 'currency.code',
        'filter' => Html::activeDropDownList(
          $searchModel,
          'currency_id',
          $currencies,
          ['class' => 'form-control select2', 'prompt' => $select2prompt]
        ),
        'contentOptions' => function($model, $column) {
          return ['class' => 'td-nowrap'];
        }
      ],
      [
        'attribute' => 'delivery_term_id',
        'value' => 'deliveryTerm.name',
        'filter' => Html::activeDropDownList(
          $searchModel,
          'delivery_term_id',
          $deliveryTerms,
          ['class' => 'form-control select2', 'prompt' => $select2prompt]
        ),
        'contentOptions' => function($model, $column) {
          return ['class' => 'td-nowrap'];
        }
      ],
    ],
  ]);?>

  <?php Pjax::end(); ?>

</div>
