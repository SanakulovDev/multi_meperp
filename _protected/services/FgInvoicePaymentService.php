<?php
namespace app\services;

use app\models\Customer;
use app\models\FgInvoicePayment;
use app\models\FgInvoicePaymentBulkForm;
use app\models\SalesContract;
use Yii;
use yii\db\Query;

class FgInvoicePaymentService
{
    private function buildSelectionKey(int $salesContractId, int $waybillId): string
    {
        return $salesContractId . ':' . $waybillId;
    }

    /**
     * Returns selectable TTN rows enriched with customer / contract / currency metadata.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getSelectableWaybills(): array
    {
        $rows = (new Query())
            ->select([
                'waybill_id' => 'w.id',
                'w.waybill_no',
                'w.waybill_date',
                'sales_contract_id' => 'sc.id',
                'contract_no' => 'sc.contract_no',
                'currency_id' => 'sc.currency_id',
                'currency_code' => 'cu.code',
                'customer_name' => 'c.name',
                'amount' => 'COALESCE(SUM(fid.qty * fid.price), 0)',
            ])
            ->from(['fiw' => 'fg_invoice_waybill'])
            ->innerJoin(['fi' => 'fg_invoice'], 'fi.id = fiw.fg_invoice_id')
            ->innerJoin(['w' => 'waybill'], 'w.id = fiw.waybill_id')
            ->innerJoin(['sc' => 'sales_contract'], 'sc.contract_no = fi.contract AND sc.customer_id = fi.customer_id')
            ->innerJoin(['c' => 'customer'], 'c.id = sc.customer_id')
            ->leftJoin(['cu' => 'currency'], 'cu.id = sc.currency_id')
            ->leftJoin(['fid' => 'fg_invoice_detail'], 'fid.fg_invoice_id = fi.id')
            ->groupBy([
                'w.id',
                'w.waybill_no',
                'w.waybill_date',
                'sc.id',
                'sc.contract_no',
                'sc.currency_id',
                'cu.code',
                'c.name',
            ])
            ->orderBy([
                'w.waybill_date' => SORT_DESC,
                'w.waybill_no' => SORT_DESC,
                'c.name' => SORT_ASC,
            ])
            ->all();

        return array_map(function ($row) {
            $date = !empty($row['waybill_date']) ? date('d.m.Y', strtotime($row['waybill_date'])) : '';
            $text = trim($row['waybill_no'] . ($date !== '' ? ' (' . $date . ')' : '') . ' - ' . $row['customer_name']);

            return [
                'id' => (int) $row['waybill_id'],
                'text' => $text,
                'amount' => (float) $row['amount'],
                'sales_contract_id' => (int) $row['sales_contract_id'],
                'contract_no' => $row['contract_no'],
                'currency_id' => (int) $row['currency_id'],
                'currency_code' => $row['currency_code'],
                'customer_name' => $row['customer_name'],
            ];
        }, $rows);
    }

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
     * Returns unpaid customer TTNs with exact unpaid amount for bulk settlement.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getUnpaidWaybillsByCustomer(int $customerId): array
    {
        $rows = (new Query())
            ->select([
                'sales_contract_id' => 'sc.id',
                'contract_no' => 'sc.contract_no',
                'currency_id' => 'sc.currency_id',
                'currency_code' => 'cu.code',
                'waybill_id' => 'w.id',
                'w.waybill_no',
                'w.waybill_date',
                'invoice_amount' => 'SUM(fid.price * fid.qty)',
                'paid_amount' => 'COALESCE(wp.pay_amt, 0)',
                'unpaid_amount' => 'SUM(fid.price * fid.qty) - COALESCE(wp.pay_amt, 0)',
            ])
            ->from(['fiw' => 'fg_invoice_waybill'])
            ->innerJoin(['fi' => 'fg_invoice'], 'fi.id = fiw.fg_invoice_id')
            ->innerJoin(['sc' => 'sales_contract'], 'sc.contract_no = fi.contract AND sc.customer_id = fi.customer_id')
            ->innerJoin(['w' => 'waybill'], 'w.id = fiw.waybill_id')
            ->leftJoin(['cu' => 'currency'], 'cu.id = sc.currency_id')
            ->innerJoin(['fid' => 'fg_invoice_detail'], 'fid.fg_invoice_id = fi.id')
            ->leftJoin([
                'wp' => (new Query())
                    ->select([
                        'waybill_id',
                        'pay_amt' => 'SUM(amount)',
                    ])
                    ->from('fg_invoice_payment')
                    ->where(['not', ['waybill_id' => null]])
                    ->groupBy(['waybill_id']),
            ], 'wp.waybill_id = w.id')
            ->where(['fi.customer_id' => $customerId])
            ->groupBy([
                'sc.id',
                'sc.contract_no',
                'sc.currency_id',
                'cu.code',
                'w.id',
                'w.waybill_no',
                'w.waybill_date',
                'wp.pay_amt',
            ])
            ->having('SUM(fid.price * fid.qty) - COALESCE(wp.pay_amt, 0) > 0.01')
            ->orderBy([
                'w.waybill_date' => SORT_ASC,
                'w.waybill_no' => SORT_ASC,
                'sc.contract_no' => SORT_ASC,
            ])
            ->all();

        return array_map(function ($row) {
            return [
                'key' => $this->buildSelectionKey((int) $row['sales_contract_id'], (int) $row['waybill_id']),
                'sales_contract_id' => (int) $row['sales_contract_id'],
                'contract_no' => $row['contract_no'],
                'currency_id' => (int) $row['currency_id'],
                'currency_code' => $row['currency_code'],
                'waybill_id' => (int) $row['waybill_id'],
                'waybill_no' => $row['waybill_no'],
                'waybill_date' => $row['waybill_date'],
                'invoice_amount' => (float) $row['invoice_amount'],
                'paid_amount' => (float) $row['paid_amount'],
                'unpaid_amount' => (float) $row['unpaid_amount'],
            ];
        }, $rows);
    }

    /**
     * Backward-compatible helper kept for existing tests/callers.
     */
    public function getCustomerByContract(int $contractId): ?Customer
    {
        $contract = $this->getContract($contractId);
        return $contract ? $contract->customer : null;
    }

    public function createBulkPayments(FgInvoicePaymentBulkForm $form): bool
    {
        if (!$form->validate()) {
            return false;
        }

        $availableRows = $this->getUnpaidWaybillsByCustomer((int) $form->customer_id);
        $availableMap = [];
        foreach ($availableRows as $row) {
            $availableMap[$row['key']] = $row;
        }

        $selectedRows = [];
        foreach ((array) $form->selected_keys as $key) {
            if (isset($availableMap[$key])) {
                $selectedRows[] = $availableMap[$key];
            }
        }

        if (empty($selectedRows)) {
            $form->addError('selected_keys', Yii::t('app', 'No unpaid waybills selected.'));
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($selectedRows as $row) {
                $payment = new FgInvoicePayment();
                $payment->no = $form->no;
                $payment->date = $form->date;
                $payment->sales_contract_id = $row['sales_contract_id'];
                $payment->currency_id = $row['currency_id'];
                $payment->waybill_id = $row['waybill_id'];
                $payment->amount = $row['unpaid_amount'];

                if (!$payment->save()) {
                    $form->addErrors($payment->getErrors());
                    $transaction->rollBack();
                    return false;
                }
            }

            $transaction->commit();
            return true;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
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
