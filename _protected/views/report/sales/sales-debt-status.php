<?php
use app\components\Helpers;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $data array */
/* @var $customers array  id => name */
/* @var $customerId int */

$this->title = Yii::t('app', 'Debt status by customers');
$this->params['breadcrumbs'][] = $this->title;

$totalInv  = array_sum(array_column($data, 'total_inv'));
$totalPay  = array_sum(array_column($data, 'total_pay'));
$totalSaldo = array_sum(array_column($data, 'saldo'));
?>
<style>
.debt-tbl { width: 100%; border-collapse: collapse; font-size: 13px; }
.debt-tbl th, .debt-tbl td { border: 1px solid #ccc; padding: 3px 6px; vertical-align: top; }
.debt-tbl thead th { background: #b6e8ff; font-weight: bold; text-align: center; white-space: nowrap; }
.debt-tbl tfoot td { background: #b6e8ff; font-weight: bold; }
.debt-tbl tbody tr:nth-child(even) { background: #f5f5f5; }
.num  { text-align: right; white-space: nowrap; }
.ctr  { text-align: center; white-space: nowrap; }
.red  { color: #c0392b; font-weight: bold; }
.inv-list { font-size: 11px; color: #555; word-break: break-all; }
</style>

<div style="margin-bottom:8px; display:flex; align-items:center; gap:6px;">
    <?= Html::a('<i class="fa fa-arrow-left"></i>',
        ['index'], ['class' => 'btn btn-default btn-xs', 'title' => Yii::t('app', 'btn-back')]) ?>

    <form method="get" action="<?= Url::to(['report/sales-debt-status']) ?>" style="display:flex;align-items:center;gap:4px;">
        <?= Html::dropDownList('customer_id', $customerId, $customers, [
            'class' => 'form-control input-xs select2',
            'prompt' => Yii::t('app', 'All'),
            'style' => 'width:300px;font-size:12px;height:24px;padding:2px 6px;',
        ]) ?>
        <?= Html::submitButton('<i class="fa fa-filter"></i>',
            ['class' => 'btn btn-primary btn-xs', 'title' => Yii::t('app', 'Filter')]) ?>
        <?php if ($customerId): ?>
            <?= Html::a('<i class="fa fa-times"></i>',
                ['report/sales-debt-status'], ['class' => 'btn btn-default btn-xs', 'title' => Yii::t('app', 'Reset')]) ?>
        <?php endif; ?>
    </form>

    <div style="margin-left:auto;">
        <?= Html::button('<i class="fa fa-file-excel-o"></i> ' . Yii::t('app', 'btn-download-delivery-plan'),
            ['class' => 'btn btn-info btn-xs', 'id' => 'btn-xls']) ?>
    </div>
</div>

<table class="debt-tbl" id="debt-tbl">
    <thead>
        <tr>
            <th rowspan="2" style="width:30px;">№</th>
            <th rowspan="2"><?= Yii::t('app', 'Customer') ?></th>
            <th rowspan="2" style="width:150px;"><?= Yii::t('app', 'Shipped amount') ?></th>
            <th rowspan="2" style="width:100px;"><?= Yii::t('app', 'First unpaid date') ?></th>
            <th rowspan="2" style="width:150px;"><?= Yii::t('app', 'Receipts') ?></th>
            <th rowspan="2" style="width:150px;"><?= Yii::t('app', 'Balance (debt)') ?></th>
            <th><?= Yii::t('app', 'Unpaid invoices') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $i => $row): ?>
        <tr>
            <td class="ctr"><?= $i + 1 ?></td>
            <td><?= Html::encode($row['customer_name']) ?></td>
            <td class="num"><?= Helpers::numberFormatRemoveZero($row['total_inv']) ?></td>
            <td class="ctr"><?= Html::encode($row['first_unpaid_date'] ?? '') ?></td>
            <td class="num"><?= Helpers::numberFormatRemoveZero($row['total_pay']) ?></td>
            <td class="num red"><?= Helpers::numberFormatRemoveZero($row['saldo']) ?></td>
            <td class="inv-list"><?= Html::encode($row['unpaid_invoices'] ?? '') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2"><?= Yii::t('app', 'Total') ?></td>
            <td class="num"><?= Helpers::numberFormatRemoveZero($totalInv) ?></td>
            <td></td>
            <td class="num"><?= Helpers::numberFormatRemoveZero($totalPay) ?></td>
            <td class="num red"><?= Helpers::numberFormatRemoveZero($totalSaldo) ?></td>
            <td></td>
        </tr>
    </tfoot>
</table>

<?php
$this->registerJs(<<<JS
$('#btn-xls').on('click', function () {
    html_xls_export('debt-tbl', 'sales-debt-status');
});
JS
);
?>
