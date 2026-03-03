<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\FgInvoice */
/* @var $searchModel app\models\FgInvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/** @var TYPE_NAME $customers */
/** @var TYPE_NAME $factories */
/** @var TYPE_NAME $modelImport */
/** @var TYPE_NAME $err_text */
/** @var TYPE_NAME $insert_ok_text */
$this->title = Yii::t('app', 'FG Invoice');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fg-invoice-index">
  <div class="row">
    <div class="col-md-9">
      <!-- FILE IMPORT FORM -->
      <? $form = ActiveForm::begin(
        [
          'action' => ['upload-fginvoice'],
          'options' => [
            'enctype' => 'multipart/form-data',
          ]
        ]
      );
      ?>
      <?=$form->field($modelImport, 'fileImport')->fileInput()?>
    </div>
    <div class="col-md-3 pull-right text-center">
      <?=Html::a(Yii::t('app', 'btn-template'), '/public/FG-Invoice_template.xlsx')?>
      <br>
      <?=Html::submitButton(Yii::t('app', 'btn-upload').' '.Yii::t('app', 'UploadFgInvoice'), ['class' => 'btn btn-warning btn-sm']);?>
    </div>
    <?php ActiveForm::end() ?>
    <!-- FILE IMPORT FORM -->
  </div>
</div>

<?
if(strlen(trim($err_text)) > 1) {
  ?>
  <div class='alert alert-danger'>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span>
    </button>
    <p><strong><?=Yii::t('app', 'Error').'!!!'?></strong></p>
    <p><?=$err_text?></p>
  </div>
  <?
}
if(strlen(trim($insert_ok_text)) > 1) {
  ?>
  <div class='alert alert-success'>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span>
    </button>
    <p><strong><?=Yii::t('app', 'Success').'!!! '?></strong></p>
    <p><?=$insert_ok_text;?></p>
  </div>
  <?
}
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
        'visible' => (Yii::$app->user->can('sales') || Yii::$app->user->can('shipper')),
        'template' => '{confirm} {print} {view} {delete} {update}',
        'buttons' => [
          'confirm' => function($url, $model) {
            if(Yii::$app->user->can('sales')) {
              if(!is_null($model->confirmed_by)) {
                if(Yii::$app->user->can('admin')) {
                  $icon = 'remove';
                  $icon_style = 'text-warning';
                  $title = Yii::t('app', 'Cancel');
                  $url = Url::toRoute(['fg-invoice/reject', 'id' => $model->id]);
                } else return false;
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
              ]);
            }
          },
          'print' => function($url, $model) {
            if(Yii::$app->user->can('sales')) {
//								if(is_null($model->confirmed_by)){
//									return false;
//								}
              $icon_style = 'text-primary';
              $title = Yii::t('app', 'Print');
              $url = Url::toRoute(['fg-invoice/print', 'id' => $model->id]);
              return Html::a('<span class="fa fa-print" aria-hidden="true"></span>', $url, [
                'title' => Yii::t('app', 'Print'),
                'class' => $icon_style
              ]);
            }
          },
          'delete' => function($url, $model) {
            if(Yii::$app->user->can('sales')) {
              if(!is_null($model->confirmed_by)) {
                return false;
              }
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
              ]);
            }
          },
          'update' => function($url, $model) {
            if(Yii::$app->user->can('sales')) {
              if(!is_null($model->confirmed_by)) {
                return false;
              }
              $icon_style = 'text-warning';
              $title = Yii::t('app', 'Update');
              $url = Url::toRoute(['fg-invoice/update', 'id' => $model->id]);
              return Html::a('<span class="fa fa-pencil" aria-hidden="true"></span>', $url, [
                'title' => Yii::t('app', 'Update'),
                'class' => $icon_style
              ]);
            }
          },
        ]
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
      [
        'attribute' => 'factory_id',
        'value' => 'factory.name',
        'filter' => $factories
      ],
      'invoice_no',
      'invoice_date',
      [
        'attribute' => 'customer_id',
        'value' => 'customer.name',
        'filter' => $customers
      ],
      'contract',
//			'rec_person_fullname',
//			'rec_person_regno',
//			'driver',
//			'truck',
//			'manager',
//			'account',
//			'sender',
      [
        'attribute' => 'vat',
        'contentOptions' => ['style' => 'text-align: center; vertical-align:middle;']
      ],
      [
        'attribute' => 'excise',
        'contentOptions' => ['style' => 'text-align: center; vertical-align:middle;']
      ],
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
      [
        'attribute' => 'updated_at',
        'value' => function($data) {
          $update_val = ($data->updated_at) ? date('d.m.Y (H:i:s)', $data->updated_at) : '-';
          return $update_val;
        },
        'contentOptions' => function($model, $key, $index, $column) {
          return [
            'style' => 'text-align: center; vertical-align:middle;',
            'title' => (!empty($model->updatedBy)) ? $model->updatedBy->username : '-'
          ];
        }
      ],
    ],
  ]);?>

<?php Pjax::end(); ?>

</div>
