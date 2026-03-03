<?
/**
 * @var $parentModel app\controllers\FreightInvoiceController
 */
?>
<div class="row ">
	<div class="col-lg-3">
		<span class="text-bold"><?=Yii::t('app', 'Invoice type')?>:</span>
		<span><?=$parentModel->freightInvoiceType?></span>
	</div>
	<div class="col-lg-3">
		<span class="text-bold"><?=Yii::t('app', 'Invoice no')?>:</span>
		<span><?=$parentModel->invoice_no." (".$parentModel->invoice_date.")"?></span>
	</div>
	<div class="col-lg-4">
		<span class="text-bold"><?=Yii::t('app', 'Contract')?>:</span>
		<span><?=$parentModel->contract."(".$parentModel->deliveryTerm->name.") - ".$parentModel->carrier->company_name?></span>
	</div>
	<div class="col-lg-2">
		<span class="text-bold"><?=Yii::t('app', 'Currency')?>:</span>
		<span><?=$parentModel->currency->code?></span>
	</div>
</div>
