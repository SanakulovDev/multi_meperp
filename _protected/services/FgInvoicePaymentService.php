<?php
namespace app\services;

use app\models\Customer;
use app\models\FgInvoicePayment;
use app\models\FgInvoiceWaybill;
use app\models\SalesContract;

class FgInvoicePaymentService
{
    /**
     * Returns waybills linked to any FgInvoice of the given sales contract.
     * Chain: SalesContract → FgInvoice (matched by contract_no + customer_id)
     *        → FgInvoiceWaybill → Waybill
     *
     * @return array [['id' => int, 'text' => string], ...]
     */
    public function getWaybillsByContract(int $contractId): array
    {
        $contract = SalesContract::findOne($contractId);
        if ($contract === null) {
            return [];
        }

        $waybillRows = FgInvoiceWaybill::find()
            ->alias('fiw')
            ->innerJoin('fg_invoice fi', 'fi.id = fiw.fg_invoice_id AND fi.contract = :contract AND fi.customer_id = :customer', [
                ':contract'  => $contract->contract_no,
                ':customer'  => $contract->customer_id,
            ])
            ->innerJoin('waybill w', 'w.id = fiw.waybill_id')
            ->select(['w.id', 'w.waybill_no'])
            ->distinct()
            ->asArray()
            ->all();

        return array_map(function ($row) {
            return [
                'id'   => (int) $row['id'],
                'text' => $row['waybill_no'],
            ];
        }, $waybillRows);
    }

    /**
     * Returns the Customer linked to the given SalesContract.
     */
    public function getCustomerByContract(int $contractId): ?Customer
    {
        $contract = SalesContract::find()
            ->with('customer')
            ->where(['id' => $contractId])
            ->one();

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
