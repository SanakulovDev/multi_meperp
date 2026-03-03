<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this                yii\web\View
 * @var $model               app\models\FreightInvoiceDetail
 * @var $form                yii\widgets\ActiveForm
 * @var $freightInvoices     app\controllers\FreightInvoiceDetailController
 * @var $parentModel         app\controllers\FreightInvoiceDetailController
 * @var $containers          app\controllers\FreightInvoiceDetailController
 * @var $invoices            app\controllers\FreightInvoiceDetailController
 * @var $selectedInvoices    app\controllers\FreightInvoiceDetailController
 * @var $invoicePaymentType  app\controllers\FreightInvoiceDetailController
 */
?>
<div class="freight-invoice-detail-form">
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
  <?php $form = ActiveForm::begin(); ?>
  <?=$form->field($model, 'freight_invoice_id')->hiddenInput(['value' => $parentModel->id])->label(false);?>
  <?=$this->render('_form2',
    [
      'model' => $model,
      'form' => $form,
      'containers' => $containers,
      'outContainers' => $outContainers,
      'outInvoices' => $outInvoices,
      'invoices' => $invoices,
      'selectedInvoices' => $selectedInvoices,
      'freightInvoice' => $parentModel,
    ]
  );?>
  <?=$this->render('_formCost',
    [
      'invoicePaymentType' => $invoicePaymentType,
      'freightInvoice' => $parentModel,
    ]
  );?>


	<div class="form-group pull-right">
		<input type="hidden" name="<?=Yii::$app->request->csrfParam;?>" value="<?=Yii::$app->request->getCsrfToken();?>"/>
    <?=Html::a(Yii::t('app', 'btn-cancel'), ['freight-invoice/view', 'id' => $parentModel->id], ['class' => 'btn btn-default btn-sm'])?>
    <?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
	</div>

  <?php ActiveForm::end(); ?>

</div>
