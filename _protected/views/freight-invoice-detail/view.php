<?php
use yii\helpers\Html;
use yii\web\YiiAsset;

/**
 * @var $this                   yii\web\View
 * @var $model                  app\models\FreightInvoiceDetail
 * @var $parentModel            app\controllers\FreightInvoiceDetailController
 * @var $freightInvoices        app\controllers\FreightInvoiceDetailController
 * @var $containers             app\controllers\FreightInvoiceDetailController
 * @var $invoices               app\controllers\FreightInvoiceDetailController
 * @var $selectedInvoices       app\controllers\FreightInvoiceDetailController
 * @var $invoicePaymentType     app\controllers\FreightInvoiceDetailController
 */
$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Freight invoice details'), 'url' => ['../freight-invoice/view', 'id' => $model->freight_invoice_id]];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="freight-invoice-detail-view">
	<div class="row ">
		<div class="col-sm-2">
      <?=Html::a(Yii::t('app', 'btn-back'), ['../freight-invoice/view', 'id' => $model->freight_invoice_id],
        ['class' => 'btn btn-default btn-sm'])?>
		</div>
		<div class="col-sm-10">
			<div class="pull-right">
        <?
        if(Yii::$app->user->can('freight-invoice-detail-update')) {
          echo Html::a(Yii::t('app', 'btn-update'), ['update', 'id' => $model->id],
            ['class' => 'btn btn-primary btn-sm']
          );
        }
        if(Yii::$app->user->can('freight-invoice-detail-delete')) {
          echo "&nbsp;".Html::a(Yii::t('app', 'btn-delete'), ['delete', 'id' => $model->id],
              ['class' => 'btn btn-danger btn-sm',
                'data' => [
                  'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                  'method' => 'post',
                ]
              ]);
        }
        ?>
			</div>
		</div>
	</div>
	<div class="row" style="margin-top: 10px; text-align:center">
		<div class="col-sm-12">
      <?=$this->render(
        '../freight-invoice/freight-invoice-header',
        [
          'parentModel' => $parentModel ?? null,
        ]
      )
      ?>
		</div>
	</div>
	<hr class="hr_style1">

	<div class="row">
		<div class="col-sm-12">
			<table class="table table-bordered">
				<thead>
				<tr>
					<th colspan="4" style="background-color:#F9F9F9">
						<strong style="font-size:120%"> <?=Yii::t('app', 'Invoice')?> </strong>
					</th>
				</tr>
				<tr>
					<th><?=Yii::t('app', 'Container')?></th>
					<th><?=Yii::t('app', 'Invoices')?></th>
					<th><?=Yii::t('app', 'Comment')?></th>
					<?if($model->freightInvoice->isInbound){?>
						<th><?=Yii::t('app', 'Outbound invoice')?></th>
					<?}?>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td><?=$model->container->container_no." - (".$model->container->container_type.")"?></td>
					<td><?=$selectedInvoices?></td>
					<td><?=$model->comment?></td>
					<?if($model->freightInvoice->isInbound){?>
						<td><?=$model->outboundInvoiceDetail->freightInvoice->invoiceInfo?></td>
					<?}?>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

  <? if(count($invoicePaymentType) > 0) { ?>
		<div class="row">
			<div class="col-sm-12">
				<table class="table table-bordered">
					<thead>
					<tr>
						<th colspan="4" style="background-color:#F9F9F9">
							<strong style="font-size:120%;"><?=Yii::t('app', 'Payment')?></strong></th>
					</tr>
					<tr>
						<th><?=Yii::t('app', 'Payment type')?></th>
						<th><?=Yii::t('app', 'Value')?></th>
						<th><?=Yii::t('app', 'Comment')?></th>
					</tr>
					</thead>
					<tbody>
          <?
          $i = 1;
          foreach($invoicePaymentType as $item) { ?>
						<tr>
							<td><?=$item['title']?></td>
							<td><?=$item['value']?></td>
							<td><?=$item['comment']?></td>
						</tr>
            <? $i++;
          } ?>
					</tbody>
				</table>
			</div>
		</div>
  <? } ?>
