<?php
namespace app\services;

use app\models\Customer;
use app\models\FgInvoicePayment;
use app\models\SalesContract;
use yii\db\Query;

class FgInvoicePaymentService
{
    /**
     * Returns waybills linked to any FgInvoice of the given sales contract.
     * Chain: SalesContract → FgInvoice (matched by contract_no + customer_id)
     *        → FgInvoiceWaybill → Waybill
     *
     * Each row includes the aggregated invoice amount (SUM(qty*price)) for
     * all FgInvoice rows attached to the waybill.
     *
     * @return array [['id' => int, 'text' => string, 'amount' => float], ...]
     */
    public function getWaybillsByContract(int $contractId): array
    {
        $contract = SalesContract::findOne($contractId);
        if ($contract === null) {
            return [];
        }

        $rows = (new Query())
            ->select([
                'w.id',
                'w.waybill_no',
                'w.waybill_date',
                'amount' => 'COALESCE(SUM(fid.qty * fid.price), 0)',
            ])
            ->from(['fiw' => 'fg_invoice_waybill'])
            ->innerJoin(['fi' => 'fg_invoice'],
                'fi.id = fiw.fg_invoice_id AND fi.contract = :contract AND fi.customer_id = :customer',
                [
                    ':contract' => $contract->contract_no,
                    ':customer' => $contract->customer_id,
                ]
            )
            ->innerJoin(['w' => 'waybill'], 'w.id = fiw.waybill_id')
            ->leftJoin(['fid' => 'fg_invoice_detail'], 'fid.fg_invoice_id = fi.id')
            ->groupBy(['w.id', 'w.waybill_no', 'w.waybill_date'])
            ->orderBy(['w.waybill_date' => SORT_DESC, 'w.waybill_no' => SORT_DESC])
            ->all();

        return array_map(function ($row) {
            $date = !empty($row['waybill_date']) ? date('d.m.Y', strtotime($row['waybill_date'])) : '';
            $text = $row['waybill_no'] . ($date !== '' ? ' (' . $date . ')' : '');
            return [
                'id'     => (int) $row['id'],
                'text'   => $text,
                'amount' => (float) $row['amount'],
            ];
        }, $rows);
    }

    /**
     * Returns SalesContract with customer + currency for form auto-fill data.
     */
    public function getContract(int $contractId): ?SalesContract
    {
        return SalesContract::find()
            ->with(['customer', 'currency'])
            ->where(['id' => $contractId])
            ->one();
    }

    /**
     * Backward-compatible helper kept for existing tests/callers.
     */
    public function getCustomerByContract(int $contractId): ?Customer
    {
        $contract = $this->getContract($contractId);
        return $contract ? $contract->customer : null;
    }

    /**
     * Validates and saves the payment model.
     * Returns false if validation fails or DB write fails.
     */
    public function save(FgInvoicePayment $model): bool
    {
        if (!$model->validate()) {
            return false;
        }

        return $model->save(false);
    }
}
