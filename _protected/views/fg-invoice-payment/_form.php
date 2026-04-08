<?php
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this     yii\web\View */
/* @var $model    app\models\FgInvoicePayment */
/* @var $contracts array  id => contract_no */

$validationUrl = ['validate'];
if (!$model->isNewRecord) {
    $validationUrl['id'] = $model->id;
}

$form = ActiveForm::begin([
    'id'                   => $model->formName(),
    'enableAjaxValidation' => true,
    'validateOnType'       => false,
    'validationUrl'        => $validationUrl,
    'options'              => ['data-pjax' => true, 'class' => 'modalForm'],
]);
?>

<div class="row">
    <div class="col-sm-6">
        <?= $form->field($model, 'sales_contract_id')->dropDownList($contracts, [
            'id'     => 'fginvoicepayment-sales_contract_id',
            'class'  => 'select2',
            'prompt' => '',
        ]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'no')->textInput(['maxlength' => true]) ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group field-fginvoicepayment-waybill_id">
            <label class="control-label"><?= $model->getAttributeLabel('waybill_id') ?></label>
            <select id="fginvoicepayment-waybill_id" name="FgInvoicePayment[waybill_id]"
                    class="select2 form-control" style="width:100%">
                <option value=""></option>
            </select>
            <div class="help-block"></div>
        </div>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'date')->widget(\kartik\datetime\DateTimePicker::class, [
            'options'       => ['placeholder' => 'YYYY-MM-DD'],
            'pluginOptions' => [
                'autoclose' => true,
                'format'    => 'yyyy-mm-dd',
                'minView'   => 2,
                'todayBtn'  => true,
            ],
        ]) ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group has-success">
            <label class="control-label"><?= Yii::t('app', 'Customer') ?></label>
            <input type="text" id="fginvoicepayment-customer-name"
                   class="form-control" readonly aria-invalid="false">
        </div>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'amount')->textInput(['type' => 'number', 'step' => 'any']) ?>
    </div>
</div>

<?php if (!$model->isNewRecord): ?>
    <table class="table table-bordered table-condensed">
        <tr>
            <th><?= Yii::t('app', 'Created by') ?></th>
            <th><?= Yii::t('app', 'Created at') ?></th>
            <th><?= Yii::t('app', 'Updated by') ?></th>
            <th><?= Yii::t('app', 'Updated at') ?></th>
        </tr>
        <tr>
            <td><?= $model->createdBy->fullname ?? '' ?></td>
            <td><?= $model->createdAtFormatted ?></td>
            <td><?= $model->updatedBy->fullname ?? '' ?></td>
            <td><?= $model->updatedAtFormatted ?></td>
        </tr>
    </table>
<?php endif; ?>

<?php ActiveForm::end(); ?>

<?php
$ajaxUrl    = Url::to(['fg-invoice-payment/list-waybills-by-contract'], true);
$isUpdate   = $model->isNewRecord ? 0 : 1;
$contractId = (int) ($model->sales_contract_id ?? 0);
$selectedId = (int) ($model->waybill_id ?? 0);

$script = <<<JS
(function () {
    function loadWaybills(contractId, selectedId) {
        if (!contractId) return;
        $.get('$ajaxUrl', { id: contractId }, function (data) {
            var \$sel = $('#fginvoicepayment-waybill_id');
            \$sel.find('option:not(:first)').remove();
            if (data.customer) {
                $('#fginvoicepayment-customer-name').val(data.customer.name);
            }
            $.each(data.waybills, function () {
                var opt = new Option(this.text, this.id, this.id == selectedId, this.id == selectedId);
                \$sel.append(opt);
            });
            \$sel.trigger('change');
        });
    }

    $('#fginvoicepayment-sales_contract_id').on('select2:select', function (e) {
        loadWaybills(e.params.data.id, 0);
    });

    if ($isUpdate === 1) {
        loadWaybills($contractId, $selectedId);
    }
})();
JS;
$this->registerJs($script, \yii\web\View::POS_END);
