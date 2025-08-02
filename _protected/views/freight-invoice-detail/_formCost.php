<?php
use yii\helpers\Html;
use app\enums\FreightInvoiceType;

/* @var $this yii\web\View
 * @var $invoicePaymentType   app\controllers\FreightInvoiceDetailController
 * @var $freightInvoice     app\controllers\FreightInvoiceDetailController
 */
$select2params = ['prompt' => '. . .', null, 'class' => 'form-control select2'];
?>

<div class="row">
	<div class="col-xs-12">
		<table class="sm_filter table table-striped table-bordered table-condensed table-sm-padding_2_0">
			<thead>
			<tr>
				<th><?=Yii::t('app', 'Payment type')?></th>
				<th style="width:150px"><?=Yii::t('app', 'Value')?></th>
				<th><?=Yii::t('app', 'Comment')?></th>
			</tr>
			</thead>
			<tbody>
      <?
			$i = 1;
			foreach($invoicePaymentType as $item) { ?>
			<?
			$disabled = false;
			if($freightInvoice->invoice_type == FreightInvoiceType::FREIGHT_TYPE_INBOUND){
				if($item['inout'] == 'out') {
					$disabled = true;
				}
			}else{
				if($item['inout'] == 'in') {
					$disabled = true;
				}
			}
			?>
				<tr>
					<td>
            <?=Html::hiddenInput('childCost['.$i.'][costType]',
              $item['key'], ['class' => 'form-control input-sm', 'style' => 'max-width: 150px']
            )?>
            <?=$item['title']?>
					</td>
					<td>
            <?=Html::textInput('childCost['.$i.'][value]',
              $item['value'], ['class' => 'form-control input-sm', 'style' => 'max-width: 150px','readonly' => $disabled]
            )?>
					</td>
					<td>
            <?=Html::input('text', 'childCost['.$i.'][comment]', $item['comment'], ['class' => 'form-control input-sm','readonly' => $disabled]);?>
					</td>
				</tr>
        <? $i++;
      } ?>
			</tbody>
		</table>
	</div>
</div>


