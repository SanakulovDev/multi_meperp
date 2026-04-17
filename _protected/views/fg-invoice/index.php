<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\FgInvoice */
/* @var $searchModel app\models\FgInvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/** @var TYPE_NAME $customers */
/** @var TYPE_NAME $factories */
/** @var TYPE_NAME $modelImport */
$this->title = Yii::t('app', 'FG Invoice (TTN)');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('fg-invoice-update');
$canDelete = Yii::$app->user->can('fg-invoice-delete');
$canView = Yii::$app->user->can('fg-invoice-view');
$canConfirm = Yii::$app->user->can('fg-invoice-confirm');
$canReject = Yii::$app->user->can('fg-invoice-reject');

// Warehouse permissions - barcha superadminlarga ochiq
$canWarehouseConfirm = Yii::$app->user->can('superadmin') || Yii::$app->user->can('fg-invoice-warehouse-confirm');
$canWarehouseReject = Yii::$app->user->can('superadmin') || Yii::$app->user->can('fg-invoice-warehouse-reject');
$canPrint = Yii::$app->user->can('fg-invoice-print');
// vd($_GET['FgInvoiceSearch']);
?>
<div class="fg-invoice-index">
  <div class="pull-right">
    <? if(Yii::$app->user->can('fg-invoice-upload-fginvoice')) { ?>
      <?=Html::a(
        Yii::t('app', 'btn-upload-file'),
        ['upload-fginvoice'],
        [
          'title' => Yii::t('app', 'Upload from excel'),
          'class' => 'btn btn-warning btn-sm',
          'data-intro' => Yii::t('intro', 'upload')
        ]
      )
      ?>
    <? } ?>
    <? if(Yii::$app->user->can('fg-invoice-create')) { ?>
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
    <? if(Yii::$app->user->can('fg-invoice-xls')) { ?>
      <?=Html::a(
        Yii::t('app', 'btn-download'),
        ['xls', 'FgInvoiceSearch' => ($_GET['FgInvoiceSearch'] ?: null)],
        [
          'class' => 'btn btn-info btn-sm',
          'data-intro' => Yii::t('intro', 'download-button')
        ]
      )
      ?>
    <? } ?>
  </div>
</div>


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
        'header' => '<i class="fa fa-warehouse" style="color: #e67e22;"></i>',
        'headerOptions' => ['style' => 'min-width:80px;text-align:center;vertical-align:middle;color:#e67e22;'],
        'contentOptions' => ['style' => 'min-width:80px;text-align:center;vertical-align:middle;'],
        'template' => '{warehouse_confirm}',
        'buttons' => [
          'warehouse_confirm' => function($url, $model) use ($canWarehouseConfirm, $canWarehouseReject) {
            if(!$canWarehouseConfirm or !$canWarehouseReject) return false;
            
            // Agar confirm qilingan bo'lsa, warehouse reject qilish mumkin emas
            if(!is_null($model->confirmed_by)) return false;
            
            if(!is_null($model->warehouse_confirmed_by)) {
              $icon = 'fa fa-times-circle';
              $icon_style = 'text-danger';
              $btn_class = 'btn btn-xs btn-outline-danger';
              $title = Yii::t('app', 'Warehouse Cancel');
              $url = Url::toRoute(['fg-invoice/warehouse-reject', 'id' => $model->id]);
            } else {
              $icon = 'fa fa-check-circle';
              $icon_style = 'text-info';
              $btn_class = 'btn btn-xs btn-outline-info';
              $title = Yii::t('app', 'Warehouse Confirm');
              $url = Url::toRoute(['fg-invoice/warehouse-confirm', 'id' => $model->id]);
            }
            return Html::a('<i class="'.$icon.' '.$icon_style.'"></i>', $url, [
                'class' => $btn_class,
                'title' => $title,
                'style' => 'margin: 2px; padding: 4px 8px;',
                'data' => [
                  'confirm' => Yii::t('app', 'Are you sure?'),
                  'method' => 'post',
                ],
              ]);
          },
          'confirm' => function($url, $model) use ($canConfirm, $canReject) {
            if(!$canConfirm or !$canReject) return false;
            
            // Faqat warehouse tasdiqlagandan keyin confirm qilish mumkin
            if(is_null($model->warehouse_confirmed_by)) return false;
            
            if(!is_null($model->confirmed_by)) {
              $icon = 'remove';
              $icon_style = 'text-warning';
              $title = Yii::t('app', 'Cancel Confirm');
              $url = Url::toRoute(['fg-invoice/reject', 'id' => $model->id]);
            } else {
              $icon = 'ok';
              $icon_style = 'text-success';
              $title = Yii::t('app', 'Confirm');
              $url = Url::toRoute(['fg-invoice/confirm', 'id' => $model->id]);
            }
            return Html::a('<span class="'.$icon_style.' glyphicon glyphicon-'.$icon.'" aria-hidden="true"></span>', $url, [
                'class' => $icon_style,
                'title' => $title,
                'data' => [
                  'confirm' => Yii::t('app', 'Are you sure?'),
                  'method' => 'post',
                ],
              ]).'&nbsp;';
          },
          'print' => function($url, $model) use ($canPrint) {
            if(!$canPrint) return false;
            $icon_style = 'text-primary';
            $title = Yii::t('app', 'Print');
            $url = Url::toRoute(['fg-invoice/print', 'id' => $model->id]);
            return Html::a('<span class="fa fa-print" aria-hidden="true"></span>', $url, [
                'title' => Yii::t('app', 'Print'),
                'class' => $icon_style
              ]).'&nbsp;';
          },
          'delete' => function($url, $model) use ($canDelete) {
            if(!$canDelete) return false;
            if(!is_null($model->confirmed_by)) return false;
            $icon_style = 'text-danger';
            $title = Yii::t('app', 'Delete');
            $url = Url::toRoute(['fg-invoice/delete', 'id' => $model->id]);
            return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>', $url, [
                'title' => Yii::t('app', 'Delete'),
                'data' => [
                  'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                  'method' => 'post',
                ],
                'class' => $icon_style
              ]).'&nbsp;';
          },
          'update' => function($url, $model) use ($canUpdate) {
            if(!$canUpdate) return false;
            if(!is_null($model->confirmed_by)) return false;
            $icon_style = 'text-warning';
            $title = Yii::t('app', 'Update');
            $url = Url::toRoute(['fg-invoice/update', 'id' => $model->id]);
            return Html::a('<span class="fa fa-pencil" aria-hidden="true"></span>', $url, [
                'title' => Yii::t('app', 'Update'),
                'class' => $icon_style
              ]).'&nbsp;';
          },
          'view' => function($url, $model) use ($canView) {
            if(!$canView) return false;
            $url = Url::toRoute(['fg-invoice/view', 'id' => $model->id]);
            return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>', $url, [
                'title' => Yii::t('app', 'View')
              ]).'&nbsp;';
          },
        ],
        'visible' => $canWarehouseConfirm || $canWarehouseReject
      ],
      
      [
        'class' => 'yii\grid\ActionColumn',
        'header' => '<i class="fa fa-fw fa-gears"></i>',
        'headerOptions' => ['style' => 'min-width:120px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
        'contentOptions' => ['style' => 'min-width:120px;text-align:center;vertical-align:middle;'],
        'template' => '{confirm}{print}{view}{delete}{update}',
        'buttons' => [
          'confirm' => function($url, $model) use ($canConfirm, $canReject) {
            if(!$canConfirm or !$canReject) return false;
            
            // Faqat warehouse tasdiqlagandan keyin confirm qilish mumkin
            if(is_null($model->warehouse_confirmed_by)) return false;
            
            if(!is_null($model->confirmed_by)) {
              $icon = 'remove';
              $icon_style = 'text-warning';
              $title = Yii::t('app', 'Cancel Confirm');
              $url = Url::toRoute(['fg-invoice/reject', 'id' => $model->id]);
            } else {
              $icon = 'ok';
              $icon_style = 'text-success';
              $title = Yii::t('app', 'Confirm');
              $url = Url::toRoute(['fg-invoice/confirm', 'id' => $model->id]);
            }
            return Html::a('<span class="'.$icon_style.' glyphicon glyphicon-'.$icon.'" aria-hidden="true"></span>', $url, [
                'class' => $icon_style,
                'title' => $title,
                'data' => [
                  'confirm' => Yii::t('app', 'Are you sure?'),
                  'method' => 'post',
                ],
              ]).'&nbsp;';
          },
          'print' => function($url, $model) use ($canPrint) {
            if(!$canPrint) return false;
            $icon_style = 'text-primary';
            $title = Yii::t('app', 'Print');
            $url = Url::toRoute(['fg-invoice/print', 'id' => $model->id]);
            return Html::a('<span class="fa fa-print" aria-hidden="true"></span>', $url, [
                'title' => Yii::t('app', 'Print'),
                'class' => $icon_style
              ]).'&nbsp;';
          },
          'delete' => function($url, $model) use ($canDelete) {
            if(!$canDelete) return false;
            if(!is_null($model->confirmed_by)) return false;
            $icon_style = 'text-danger';
            $title = Yii::t('app', 'Delete');
            $url = Url::toRoute(['fg-invoice/delete', 'id' => $model->id]);
            return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>', $url, [
                'title' => Yii::t('app', 'Delete'),
                'data' => [
                  'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                  'method' => 'post',
                ],
                'class' => $icon_style
              ]).'&nbsp;';
          },
          'update' => function($url, $model) use ($canUpdate) {
            if(!$canUpdate) return false;
            if(!is_null($model->confirmed_by)) return false;
            $icon_style = 'text-warning';
            $title = Yii::t('app', 'Update');
            $url = Url::toRoute(['fg-invoice/update', 'id' => $model->id]);
            return Html::a('<span class="fa fa-pencil" aria-hidden="true"></span>', $url, [
                'title' => Yii::t('app', 'Update'),
                'class' => $icon_style
              ]).'&nbsp;';
          },
          'view' => function($url, $model) use ($canView) {
            if(!$canView) return false;
            $url = Url::toRoute(['fg-invoice/view', 'id' => $model->id]);
            return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>', $url, [
                'title' => Yii::t('app', 'View')
              ]).'&nbsp;';
          },
        ],
        'visible' => $canUpdate || $canDelete || $canView || $canPrint || $canConfirm
      ],
      [
        'attribute' => 'confirmed_by',
        'headerOptions' => ['style' => 'width: 200px;text-align: center;vertical-align:middle;'],
        'contentOptions' => ['style' => 'width: 200px;text-align: center;vertical-align:middle;'],
        'content' => function($model) {
          $confirmed_by = ($model->confirmed_by) ? $model->confirmedBy->username : '';
          $confirmed_at = ($model->confirmed_at) ? date('d.m.Y (H:i:s)', $model->confirmed_at) : '';
          $title = $confirmed_by."(".$confirmed_at.")";
          return '<strong title = "'.$title.'" class="txt-'.(($model->confirmed_by == 0) ? 'warning' : 'primary').'">'.$model->statusName.'</strong>';
        },
        'filter' => [0 => Yii::t('app', 'Pending'), 1 => Yii::t('app', 'Confirmed')]
      ],
//      [
//        'attribute' => 'factory_id',
//        'value' => 'factory.name',
//        'filter' => $factories
//      ],
      [
        'attribute' => 'invoice_no',
        'label' => Yii::t('app', 'FG Invoice no (TTN)'),
      ],
      [
        'attribute' => 'invoice_date',
        'label' => Yii::t('app', 'FG Invoice date (TTN)'),
      ],
      [
        'attribute' => 'customer_id',
        'value' => 'customer.name',
        'filter' => Html::activeDropDownList($searchModel, 'customer_id', $searchModel->getCustomerFilterOptions(), ['class' => 'form-control select2', 'prompt' => '...']),
        // 'filter' => $customers
      ],

      [
        'attribute' => 'fgInvoiceWaybills',
        'label' => Yii::t('app', 'Waybill no'),
        'headerOptions' => ['class' => "text-center", 'style' => 'text-align:center;width:80px;color:#3c8dbc'],
        'contentOptions' => ['class' => "text-center"],
        "format"=>"raw",
        'value' => function($data) {
          $fgInvoiceWaybill = $data->fgInvoiceWaybills[0] ?? null;
          return ($fgInvoiceWaybill) ? "<div title=\"".Yii::t('app', 'Date').": ".$fgInvoiceWaybill->waybill->waybill_date."\"><b>".$fgInvoiceWaybill->waybill->waybill_no."</b></div>" : '';
        },
      ],




      [
        'attribute' => 'contract',
        'format' => 'raw',
        'value' => function($model){
            if(mb_strlen($model->contract) > 30){
              return mb_substr($model->contract,0, 30);
            }
            return $model->contract;
        }
      ],

      [
        'attribute' => 'mark_id',
        'format' => 'html',
        'label' => 'Марка',
        'headerOptions' => ['style' => 'min-width:200px;'],
        'contentOptions' => ['style' => 'min-width:200px;'],
        'filter' => Html::activeDropDownList(
          $searchModel,
          'mark_id',
          $searchModel->getMarkFilterOptions(),
          ['class' => 'form-control select2', 'prompt' => '...']
        ),
        'value' => function($model){
          $html = '';
          if(isset($model->fgInvoiceDetails)){
            $items = $model->fgInvoiceDetails;
            foreach($items as $item){
              $label = \app\models\FgInvoiceSearch::buildMarkLabel($item->part_name ?? '', $item->part->part_color ?? '');
              $html .= Html::encode(\app\models\FgInvoiceSearch::buildMarkPreview($label))."<br\>";
            }
          }
          return $html;
        }
      ],
      
      [
        'attribute' => '',
        'format' => 'html',
        'label' => 'Количество',
        'value' => function($model){
          $html = '';
          if(isset($model->fgInvoiceDetails)){
            $items = $model->fgInvoiceDetails;
            foreach($items as $item){
              $html .= (divideString(round($item->qty*1), 3))."<br\>";
            }
          
          }
          return $html;
        }
      ],

//      'rec_person_fullname',
//      'rec_person_regno',
//      'driver',
//      'truck',
//      'manager',
//      'account',
//      'sender',
//      [
//        'attribute' => 'vat',
//        'contentOptions' => ['style' => 'text-align: center; vertical-align:middle;']
//      ],
      'comment',
      [
        'attribute' => 'created_at',
        'value' => function($data) {
          $create_val = ($data->created_at) ? date('d.m.Y (H:i:s)', $data->created_at) : '-';
          return $create_val;
        },
        'contentOptions' => function($model, $key, $index, $column) {
          return [
            'style' => 'text-align: center; vertical-align:middle;',
            'title' => (!empty($model->createdBy)) ? $model->createdBy->username : '-'
          ];
        }
      ],
//      [
//        'attribute' => 'updated_at',
//        'value' => function($data) {
//          $update_val = ($data->updated_at) ? date('d.m.Y (H:i:s)', $data->updated_at) : '-';
//          return $update_val;
//        },
//        'contentOptions' => function($model, $key, $index, $column) {
//          return [
//            'style' => 'text-align: center; vertical-align:middle;',
//            'title' => (!empty($model->updatedBy)) ? $model->updatedBy->username : '-'
//          ];
//        }
//      ],
    ],
  ]
);?>

<?php Pjax::end(); ?>

</div>
