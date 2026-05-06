<?php
use app\components\Helpers;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\FgInvoicePaymentBulkForm */
/* @var $customer app\models\Customer|null */
/* @var $waybillRows array */

$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => false,
    'options' => ['data-pjax' => true, 'class' => 'modalForm'],
]);
?>

<?= $form->field($model, 'customer_id')->hiddenInput()->label(false) ?>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group has-success">
            <label class="control-label"><?= Yii::t('app', 'Customer') ?></label>
            <input type="text" class="form-control" readonly
                   value="<?= Html::encode($customer->name ?? '') ?>">
        </div>
    </div>
    <div class="col-sm-3">
        <?= $form->field($model, 'no')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-sm-3">
        <?= $form->field($model, 'date')->widget(\kartik\datetime\DateTimePicker::class, [
            'options' => ['placeholder' => 'YYYY-MM-DD'],
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'minView' => 2,
                'todayBtn' => true,
            ],
        ]) ?>
    </div>
</div>

<div class="form-group field-fginvoicepaymentbulkform-selected_keys">
    <label class="control-label"><?= Yii::t('app', 'Unpaid waybills') ?></label>
    <?php if (empty($waybillRows)): ?>
        <div class="alert alert-warning" style="margin-bottom:0;">
            <?= Yii::t('app', 'No unpaid waybills found for this customer.') ?>
        </div>
    <?php else: ?>
        <div class="well well-sm" style="margin-bottom:10px;">
            <strong><?= Yii::t('app', 'Selected') ?>:</strong>
            <span id="bulk-selected-count">0</span> ta TTH
        </div>
        <div style="max-height:320px; overflow:auto; border:1px solid #e5e5e5; border-radius:4px;">
            <table class="table table-bordered table-condensed" style="margin-bottom:0;">
                <thead>
                <tr>
                    <th style="width:45px; text-align:center;">№</th>
                    <th style="width:40px; text-align:center;">
                        <input type="checkbox" id="bulk-check-all" checked>
                    </th>
                    <th><?= Yii::t('app', 'Waybill (TTN)') ?></th>
                    <th><?= Yii::t('app', 'Currency') ?></th>
                    <th style="text-align:right;"><?= Yii::t('app', 'Amount') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($waybillRows as $index => $row): ?>
                    <tr>
                        <td style="text-align:center; vertical-align:middle;">
                            <?= $index + 1 ?>
                        </td>
                        <td style="text-align:center; vertical-align:middle;">
                            <input type="checkbox"
                                   class="bulk-waybill-check"
                                   name="FgInvoicePaymentBulkForm[selected_keys][]"
                                   value="<?= Html::encode($row['key']) ?>"
                                   data-amount="<?= Html::encode($row['unpaid_amount']) ?>"
                                   checked>
                        </td>
                        <td>
                            <strong><?= Html::encode($row['waybill_no']) ?></strong>
                            <?php if (!empty($row['waybill_date'])): ?>
                                <div style="font-size:12px; color:#777;">
                                    <?= date('d.m.Y', strtotime($row['waybill_date'])) ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?= Html::encode($row['currency_code']) ?></td>
                        <td style="text-align:right;"><?= Helpers::numberFormatRemoveZero($row['unpaid_amount']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="3" style="text-align:right;">
                        <strong><?= Yii::t('app', 'Total') ?></strong>
                        (<span id="bulk-total-count">0</span> ta TTH)
                    </td>
                    <td style="text-align:right;"><strong id="bulk-total-amount">0</strong></td>
                </tr>
                </tfoot>
            </table>
        </div>
    <?php endif; ?>
    <div class="help-block"></div>
</div>

<?php ActiveForm::end(); ?>

<?php
$script = <<<JS
(function () {
    function formatAmount(value) {
        var number = parseFloat(value || 0);
        if (!isFinite(number)) number = 0;
        return number.toLocaleString('en-US', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        }).replace(/,/g, ' ');
    }

    function updateTotal() {
        var total = 0;
        var count = 0;
        $('.bulk-waybill-check:checked').each(function () {
            total += parseFloat($(this).data('amount') || 0);
            count++;
        });
        $('#bulk-selected-count').text(count);
        $('#bulk-total-count').text(count);
        $('#bulk-total-amount').text(formatAmount(total));
    }

    $('#bulk-check-all').on('change', function () {
        $('.bulk-waybill-check').prop('checked', $(this).is(':checked'));
        updateTotal();
    });

    $(document).on('change', '.bulk-waybill-check', function () {
        var all = $('.bulk-waybill-check').length === $('.bulk-waybill-check:checked').length;
        $('#bulk-check-all').prop('checked', all);
        updateTotal();
    });

    updateTotal();
})();
JS;
$this->registerJs($script, \yii\web\View::POS_END);
?>
