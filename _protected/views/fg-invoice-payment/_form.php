<?php
use yii\helpers\Url;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

/* @var $this     yii\web\View */
/* @var $model    app\models\FgInvoicePayment */
/* @var $currencies array id => code */
/* @var $waybillOptions array */

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
        <?= $form->field($model, 'fg_invoice_id')->dropDownList([], [
            'id' => 'fginvoicepayment-fg_invoice_id',
            'class' => 'select2',
            'prompt' => '',
        ])->label(Yii::t('app', 'Invoice no')) ?>
        <?= $form->field($model, 'sales_contract_id')->hiddenInput([
            'id' => 'fginvoicepayment-sales_contract_id',
        ])->label(false) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'no')->textInput(['maxlength' => true]) ?>
    </div>
</div>

<div class="row">
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
    <div class="col-sm-6">
        <div class="form-group has-success">
            <label class="control-label"><?= Yii::t('app', 'Customer') ?></label>
            <input type="text" id="fginvoicepayment-customer-name"
                   class="form-control" readonly aria-invalid="false">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-4">
        <?= $form->field($model, 'currency_id')->dropDownList($currencies, [
            'id' => 'fginvoicepayment-currency_id',
            'class' => 'select2',
            'prompt' => '',
        ]) ?>
    </div>
    <div class="col-sm-4">
        <?= $form->field($model, 'amount')->textInput([
            'inputmode' => 'decimal',
            'class'     => 'form-control text-right',
            'autocomplete' => 'off',
        ]) ?>
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
$selectedId = (int) ($model->fg_invoice_id ?? 0);
$formId     = $model->formName();
$waybillOptionsJson = Json::htmlEncode($waybillOptions);

$script = <<<JS
(function () {
    var \$amount = $('#fginvoicepayment-amount');
    var waybillOptions = $waybillOptionsJson;

    function stripSeparators(value) {
        return String(value == null ? '' : value).replace(/\s+/g, '');
    }

    function formatAmount(value) {
        var raw = stripSeparators(value);
        if (raw === '' || raw === '-') return raw;
        var neg = raw.charAt(0) === '-';
        if (neg) raw = raw.substring(1);
        var parts = raw.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        return (neg ? '-' : '') + parts.join('.');
    }

    function applyFormat(\$el) {
        if (!\$el.length) return;
        var formatted = formatAmount(\$el.val());
        if (formatted !== \$el.val()) \$el.val(formatted);
    }

    // Format on user input (preserve caret at end — acceptable for small amounts).
    \$amount.on('input', function () { applyFormat($(this)); });

    // Initial formatting for existing values (update mode).
    applyFormat(\$amount);

    // Strip spaces before Yii per-attribute AJAX validation fires on blur.
    \$amount.on('blur', function () {
        $(this).val(stripSeparators($(this).val()));
    });

    // Normalize value before whole-form validation / submit, reformat if form stays open.
    var \$form = $('#$formId');
    \$form.on('beforeValidate beforeValidateAttribute beforeSubmit submit', function () {
        \$amount.val(stripSeparators(\$amount.val()));
    });
    \$form.on('afterValidate', function () {
        applyFormat(\$amount);
    });
    \$form.on('afterValidateAttribute', function (e, attr) {
        if (attr.id === 'fginvoicepayment-amount') {
            applyFormat(\$amount);
        }
    });

    function renderInvoiceOptions(selectedId) {
        var \$sel = $('#fginvoicepayment-fg_invoice_id');
        \$sel.find('option:not(:first)').remove();

        $.each(waybillOptions, function () {
            var opt = new Option(this.text, this.id, this.id == selectedId, this.id == selectedId);
            \$(opt)
                .attr('data-amount', this.amount)
                .attr('data-unpaid-amount', this.unpaid_amount || this.amount)
                .attr('data-customer-name', this.customer_name || '')
                .attr('data-contract-no', this.contract_no || '')
                .attr('data-sales-contract-id', this.sales_contract_id || '')
                .attr('data-currency-id', this.currency_id || '');
            \$sel.append(opt);
        });

        \$sel.trigger('change');
    }

    function applyInvoiceData(fillAmount) {
        var \$selected = $('#fginvoicepayment-fg_invoice_id').find('option:selected');
        var contractId = \$selected.attr('data-sales-contract-id') || '';
        var currencyId = \$selected.attr('data-currency-id') || '';

        $('#fginvoicepayment-sales_contract_id').val(contractId);
        $('#fginvoicepayment-customer-name').val(\$selected.attr('data-customer-name') || '');
        $('#fginvoicepayment-currency_id').val(currencyId).trigger('change');

        if (fillAmount === true) {
            var amt = \$selected.attr('data-amount');
            if (amt !== undefined && amt !== '' && amt !== null) {
                \$amount.val(amt);
                applyFormat(\$amount);
            }
        }

        if (!contractId) {
            $('#fginvoicepayment-customer-name').val('');
            $('#fginvoicepayment-currency_id').val('').trigger('change');
        }
    }

    $('#fginvoicepayment-fg_invoice_id').on('select2:select', function () {
        applyInvoiceData(true);
    });

    $('#fginvoicepayment-fg_invoice_id').on('change', function () {
        if (!$(this).val()) {
            $('#fginvoicepayment-sales_contract_id').val('');
            $('#fginvoicepayment-customer-name').val('');
            $('#fginvoicepayment-currency_id').val('').trigger('change');
        }
    });

    renderInvoiceOptions($selectedId);

    if ($selectedId > 0) {
        applyInvoiceData(false);
    }

    if (!$selectedId) {
        var amt = \$amount.val();
        if (amt !== undefined && amt !== '' && amt !== null) {
            \$amount.val(amt);
            applyFormat(\$amount);
        }
    }
})();
JS;
$this->registerJs($script, \yii\web\View::POS_END);
