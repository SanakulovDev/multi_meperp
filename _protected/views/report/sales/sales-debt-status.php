<?php
use app\components\Helpers;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $data array */
/* @var $customers array  id => name */
/* @var $customerId int */
/* @var $currencies array  id => code */
/* @var $currencyId int */
/* @var $type string|null  'debt' | 'credit' | 'zero' | null */
/* @var $country string|null  'local' | 'import' | null */

$this->title = Yii::t('app', 'Debt status by customers');
$this->params['breadcrumbs'][] = $this->title;
$canCreatePayment = Yii::$app->user->can('fg-invoice-payment-create');

$totalInv   = array_sum(array_column($data, 'total_inv'));
$totalPay   = array_sum(array_column($data, 'total_pay'));
$totalSaldo = array_sum(array_column($data, 'saldo'));

$typeFilters = [
    null     => 'Все',
    'debt'   => Yii::t('app', 'Only debt'),
    'credit' => Yii::t('app', 'Only credit'),
    'zero'   => Yii::t('app', 'Zero saldo'),
];

$buildUrl = function (?string $t) use ($customerId, $currencyId, $country) {
    $params = ['report/sales-debt-status'];
    if ($t !== null)       { $params['type'] = $t; }
    if ($customerId)       { $params['customer_id'] = $customerId; }
    if ($currencyId)       { $params['currency_id'] = $currencyId; }
    if ($country !== null) { $params['country'] = $country; }
    return Url::to($params);
};

$buildCountryUrl = function (?string $c) use ($customerId, $currencyId, $type) {
    $params = ['report/sales-debt-status'];
    if ($type !== null) { $params['type'] = $type; }
    if ($customerId)    { $params['customer_id'] = $customerId; }
    if ($currencyId)    { $params['currency_id'] = $currencyId; }
    if ($c !== null)    { $params['country'] = $c; }
    return Url::to($params);
};

$buildCurrencyUrl = function (?int $cur) use ($customerId, $type, $country) {
    $params = ['report/sales-debt-status'];
    if ($type !== null) { $params['type'] = $type; }
    if ($customerId)    { $params['customer_id'] = $customerId; }
    if ($country !== null) { $params['country'] = $country; }
    if ($cur)           { $params['currency_id'] = $cur; }
    return Url::to($params);
};

$countryFilters = [
    null     => 'Все',
    'local'  => Yii::t('app', 'Local'),
    'import' => Yii::t('app', 'Import'),
];

$waybillPreview = 3;
?>
<style>
.debt-wrap { background:#fff; border:1px solid #e0e0e0; border-radius:4px; padding:10px; }

.debt-toolbar {
    display:flex; flex-wrap:wrap; align-items:center; gap:10px;
    padding:8px 4px; margin-bottom:10px;
    border-bottom:1px solid #eee;
}
.debt-pills { display:inline-flex; border:1px solid #ccc; border-radius:4px; overflow:hidden; }
.debt-pills a {
    padding:6px 14px; font-size:13px; text-decoration:none; color:#333;
    background:#f6f6f6; border-right:1px solid #ccc; line-height:1.3;
}
.debt-pills a:last-child { border-right:0; }
.debt-pills a:hover { background:#e8f4ff; }
.debt-pills a.active { background:#3c8dbc; color:#fff; font-weight:600; }

.debt-customer-box { display:flex; align-items:center; gap:6px; min-width:360px; }
.debt-customer-box .select2-container { min-width:320px !important; }
.debt-customer-box .select2-selection--single {
    height:34px !important; padding:2px 0; border:1px solid #ccc;
}
.debt-customer-box .select2-selection__rendered { line-height:30px !important; font-size:13px; }
.debt-customer-box .select2-selection__arrow { height:32px !important; }

/* Scroll inside the table container — thead/tfoot stay pinned while body scrolls. */
.debt-scroll {
    overflow:auto;
    max-height:calc(100vh - 210px);
    min-height:300px;
    border:1px solid #e0e0e0;
    border-radius:3px;
    background:#fff;
}

.debt-tbl { width:100%; border-collapse:separate; border-spacing:0; font-size:13px; }
.debt-tbl th, .debt-tbl td {
    border-bottom:1px solid #e0e0e0;
    border-right:1px solid #f0f0f0;
    padding:6px 8px; vertical-align:top;
}
.debt-tbl thead th {
    background:#3c8dbc; color:#fff; font-weight:600; text-align:center;
    white-space:nowrap;
    position:sticky; top:0; z-index:5;
    box-shadow:0 2px 2px rgba(0,0,0,0.08);
}
.debt-tbl tfoot td {
    background:#eaf4fb; font-weight:700;
    position:sticky; bottom:0; z-index:4;
    box-shadow:0 -2px 2px rgba(0,0,0,0.08);
}
.debt-tbl tbody tr:nth-child(even) { background:#fafafa; }
.debt-tbl tbody tr:hover { background:#fff7e0; }
.debt-tbl td.num { text-align:right; white-space:nowrap; font-variant-numeric:tabular-nums; }
.debt-tbl td.ctr { text-align:center; white-space:nowrap; }
.debt-tbl td.cust { font-weight:500; }

.saldo-pos { color:#c0392b; font-weight:700; }
.saldo-neg { color:#27ae60; font-weight:700; }
.saldo-zero{ color:#888; }

.overdue-badge {
    display:inline-block; padding:1px 7px; border-radius:10px;
    font-size:11px; font-weight:600; white-space:nowrap;
}
.overdue-low  { background:#eafaf1; color:#27ae60; }
.overdue-mid  { background:#fff3cd; color:#b7791f; }
.overdue-high { background:#fdecea; color:#c0392b; }

.waybill-list { font-size:11.5px; color:#333; line-height:1.5; word-break:break-all; }
.waybill-list .waybill-more { display:none; }
.waybill-list.expanded .waybill-more { display:inline; }
.waybill-list .toggle-waybills {
    cursor:pointer; color:#3c8dbc; font-weight:700; user-select:none;
    padding:0 4px; border-radius:3px;
}
.waybill-list .toggle-waybills:hover { background:#e8f4ff; }

</style>

<div class="debt-wrap">

    <div class="debt-toolbar">
        <?= Html::a('<i class="fa fa-arrow-left"></i>',
            ['index'], ['class' => 'btn btn-default btn-sm', 'title' => Yii::t('app', 'btn-back')]) ?>

        <form method="get" action="<?= Url::to(['report/sales-debt-status']) ?>"
              class="debt-customer-box" id="debt-filter-form">
            <?php if ($type !== null): ?>
                <input type="hidden" name="type" value="<?= Html::encode($type) ?>">
            <?php endif; ?>
            <?php if ($country !== null): ?>
                <input type="hidden" name="country" value="<?= Html::encode($country) ?>">
            <?php endif; ?>
            <?php if ($currencyId): ?>
                <input type="hidden" name="currency_id" value="<?= (int) $currencyId ?>">
            <?php endif; ?>
            <?= Html::dropDownList('customer_id', $customerId, $customers, [
                'class'  => 'form-control select2',
                'prompt' => Yii::t('app', 'All') . ' — ' . Yii::t('app', 'Customer'),
                'id'     => 'debt-customer-select',
                'style'  => 'width:320px;',
            ]) ?>
            <?php if ($customerId): $resetParams = ['report/sales-debt-status']; if ($type !== null) { $resetParams['type'] = $type; } if ($country !== null) { $resetParams['country'] = $country; } if ($currencyId) { $resetParams['currency_id'] = $currencyId; } ?>
                <?= Html::a('<i class="fa fa-times"></i>', $resetParams,
                    ['class' => 'btn btn-default btn-sm', 'title' => Yii::t('app', 'Reset')]) ?>
            <?php endif; ?>
        </form>

        <div class="debt-pills">
            <?php foreach ($countryFilters as $key => $label): ?>
                <a href="<?= $buildCountryUrl($key) ?>"
                   class="<?= ($country === null && $key === '') || $country === $key ? 'active' : '' ?>"><?= Html::encode($label) ?></a>
            <?php endforeach; ?>
        </div>

        <div class="debt-pills">
            <a href="<?= $buildCurrencyUrl(null) ?>"
               class="<?= !$currencyId ? 'active' : '' ?>"><?= Yii::t('app', 'All') ?></a>
            <?php foreach ($currencies as $curId => $curCode): ?>
                <a href="<?= $buildCurrencyUrl((int) $curId) ?>"
                   class="<?= $currencyId === (int) $curId ? 'active' : '' ?>"><?= Html::encode($curCode) ?></a>
            <?php endforeach; ?>
        </div>

        <div class="debt-pills">
            <?php foreach ($typeFilters as $key => $label): ?>
                <a href="<?= $buildUrl($key) ?>"
                   class="<?= ($type === null && $key === '') || $type === $key ? 'active' : '' ?>"><?= Html::encode($label) ?></a>
            <?php endforeach; ?>
        </div>

        <div style="margin-left:auto;">
            <?= Html::button('<i class="fa fa-file-excel-o"></i> ' . Yii::t('app', 'btn-download-delivery-plan'),
                ['class' => 'btn btn-info btn-sm', 'id' => 'btn-xls']) ?>
        </div>
    </div>

    <div class="debt-scroll">
        <table class="debt-tbl" id="debt-tbl">
            <thead>
                <tr>
                    <th style="width:52px;"><i class="fa fa-fw fa-gears"></i></th>
                    <th style="width:40px;">№</th>
                    <th style="min-width:240px; text-align:left; padding-left:12px;"><?= Yii::t('app', 'Customer') ?></th>
                    <th style="width:140px;"><?= Yii::t('app', 'Shipped amount') ?></th>
                    <th style="width:140px;"><?= Yii::t('app', 'Receipts') ?></th>
                    <th style="width:140px;"><?= Yii::t('app', 'Balance (debt)') ?></th>
                    <th style="width:115px;"><?= Yii::t('app', 'First unpaid date') ?></th>
                    <th style="width:95px;"><?= Yii::t('app', 'Days passed') ?></th>
                    <th style="width:115px;"><?= Yii::t('app', 'Last payment date') ?></th>
                    <th style="min-width:260px;"><?= Yii::t('app', 'Unpaid invoices') ?></th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($data)): ?>
                <tr><td colspan="10" class="ctr" style="padding:20px; color:#888;">
                    <?= Yii::t('app', 'No results found.') ?>
                </td></tr>
            <?php else: foreach ($data as $i => $row):
                $saldo     = (float) $row['saldo'];
                $saldoCls  = abs($saldo) < 0.01 ? 'saldo-zero'
                           : ($saldo > 0 ? 'saldo-pos' : 'saldo-neg');

                $overdue   = isset($row['overdue_days']) ? (int) $row['overdue_days'] : null;
                $overdueCls = 'overdue-low';
                if ($overdue === null)       $overdueCls = '';
                elseif ($overdue >= 60)      $overdueCls = 'overdue-high';
                elseif ($overdue >= 30)      $overdueCls = 'overdue-mid';

                $firstDate = !empty($row['first_unpaid_date'])
                    ? date('d.m.Y', strtotime($row['first_unpaid_date']))
                    : '';

                $waybillStr = trim((string) ($row['unpaid_waybills'] ?? ''));
                $waybillArr = $waybillStr !== '' ? array_map('trim', explode(',', $waybillStr)) : [];
                $total      = count($waybillArr);
                $head       = array_slice($waybillArr, 0, $waybillPreview);
                $tail       = array_slice($waybillArr, $waybillPreview);
            ?>
                <tr>
                    <td class="ctr">
                        <?php if ($canCreatePayment && $saldo > 0.01 && !empty($waybillArr)): ?>
                            <?= Html::a('<span class="glyphicon glyphicon-usd"></span> ' . Yii::t('app', 'Pay all unpaid invoices'), ['/fg-invoice-payment/create-bulk-by-customer', 'customer_id' => $row['customer_id']], [
                                'class' => 'btn btn-success btn-xs form-modal',
                                'title' => Yii::t('app', 'Pay all unpaid invoices'),
                            ]) ?>
                        <?php endif; ?>
                    </td>
                    <td class="ctr"><?= $i + 1 ?></td>
                    <td class="cust" style="padding-left:12px;"><?= Html::encode($row['customer_name']) ?></td>
                    <td class="num"><?= Helpers::numberFormatRemoveZero($row['total_inv']) ?></td>
                    <td class="num"><?= Helpers::numberFormatRemoveZero($row['total_pay']) ?></td>
                    <td class="num <?= $saldoCls ?>"><?= Helpers::numberFormatRemoveZero($saldo) ?></td>
                    <td class="ctr"><?= Html::encode($firstDate) ?></td>
                    <td class="ctr">
                        <?php if ($overdue !== null): ?>
                            <span class="overdue-badge <?= $overdueCls ?>">
                                <?= $overdue ?> <?= Yii::t('app', 'days') ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="ctr"><?= !empty($row['last_payment_date']) ? date('d.m.Y', strtotime($row['last_payment_date'])) : '<span style="color:#aaa;">—</span>' ?></td>
                    <td class="waybill-list">
                        <?php if ($total === 0): ?>
                            <span style="color:#aaa;">—</span>
                        <?php else: ?>
                            <span class="waybill-head"><?= Html::encode(implode(', ', $head)) ?></span>
                            <?php if (!empty($tail)): ?>
                                <span class="waybill-more">, <?= Html::encode(implode(', ', $tail)) ?></span>
                                <span class="toggle-waybills"
                                      data-total="<?= $total ?>"
                                      title="<?= Yii::t('app', 'Show all') ?> (<?= $total ?>)">…</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td colspan="2" style="padding-left:12px;"><?= Yii::t('app', 'Total') ?></td>
                    <td class="num"><?= Helpers::numberFormatRemoveZero($totalInv) ?></td>
                    <td class="num"><?= Helpers::numberFormatRemoveZero($totalPay) ?></td>
                    <td class="num <?= abs($totalSaldo) < 0.01 ? 'saldo-zero' : ($totalSaldo > 0 ? 'saldo-pos' : 'saldo-neg') ?>">
                        <?= Helpers::numberFormatRemoveZero($totalSaldo) ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php
$this->registerJs(<<<JS
$('#btn-xls').on('click', function () {
    html_xls_export('debt-tbl', 'sales-debt-status');
});

// Auto-submit customer filter on change (preserves the active type pill via hidden input).
$('#debt-customer-select').on('change', function () {
    $('#debt-filter-form').trigger('submit');
});

// Expand / collapse unpaid-waybills list per row.
$(document).on('click', '.toggle-waybills', function () {
    var \$cell = $(this).closest('.waybill-list');
    \$cell.toggleClass('expanded');
    $(this).text(\$cell.hasClass('expanded') ? '×' : '…');
});
JS
);
?>
