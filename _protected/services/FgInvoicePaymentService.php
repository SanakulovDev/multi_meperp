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
    private function invoiceAmountExpression(string $invoiceAlias = 'fi', string $detailAlias = 'fid'): string
    {
        $defaultVat = (float) (Yii::$app->params['vat'] ?? 0);
        return $detailAlias . '.price * ' . $detailAlias . '.qty * (1 + COALESCE(' . $invoiceAlias . '.vat, ' . $defaultVat . ') / 100)';
    }

    private function buildSelectionKey(int $salesContractId, int $fgInvoiceId): string
    {
        return $salesContractId . ':' . $fgInvoiceId;
    }

    public function getInvoiceFilterOptions(): array
    {
        $rows = (new Query())
            ->select(['id', 'invoice_no'])
            ->from('fg_invoice')
            ->orderBy(['invoice_no' => SORT_ASC])
            ->all();

        $options = [];
        foreach ($rows as $row) {
            $options[(int) $row['id']] = $row['invoice_no'];
        }

        return $options;
    }

    /**
     * Returns selectable unpaid FG invoices enriched with customer / contract / currency metadata.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getSelectableWaybills(?int $currentFgInvoiceId = null): array
    {
        $amountExpr = $this->invoiceAmountExpression();
        $invoiceAmountSql = 'COALESCE(SUM(' . $amountExpr . '), 0)';
        $unpaidAmountSql = $invoiceAmountSql . ' - COALESCE(ip.pay_amt, 0)';
        $having = $unpaidAmountSql . ' > 0.01';
        $params = [];
        if ($currentFgInvoiceId) {
            $having = '(' . $having . ' OR fi.id = :current_fg_invoice_id)';
            $params[':current_fg_invoice_id'] = $currentFgInvoiceId;
        }

        $rows = (new Query())
            ->select([
                'fg_invoice_id' => 'fi.id',
                'invoice_no' => 'fi.invoice_no',
                'invoice_date' => 'fi.invoice_date',
                'sales_contract_id' => 'sc.id',
                'contract_no' => 'sc.contract_no',
                'currency_id' => 'sc.currency_id',
                'currency_code' => 'cu.code',
                'customer_name' => 'c.name',
                'amount' => $invoiceAmountSql,
                'paid_amount' => 'COALESCE(ip.pay_amt, 0)',
                'unpaid_amount' => $unpaidAmountSql,
            ])
            ->from(['fi' => 'fg_invoice'])
            ->innerJoin(['fid' => 'fg_invoice_detail'], 'fid.fg_invoice_id = fi.id')
            ->innerJoin(['sc' => 'sales_contract'], 'sc.contract_no = fi.contract AND sc.customer_id = fi.customer_id')
            ->innerJoin(['c' => 'customer'], 'c.id = fi.customer_id')
            ->leftJoin(['cu' => 'currency'], 'cu.id = sc.currency_id')
            ->leftJoin([
                'ip' => (new Query())
                    ->select([
                        'fg_invoice_id',
                        'pay_amt' => 'SUM(amount)',
                    ])
                    ->from('fg_invoice_payment')
                    ->where(['not', ['fg_invoice_id' => null]])
                    ->groupBy(['fg_invoice_id']),
            ], 'ip.fg_invoice_id = fi.id')
            ->groupBy([
                'fi.id',
                'fi.invoice_no',
                'fi.invoice_date',
                'sc.id',
                'sc.contract_no',
                'sc.currency_id',
                'cu.code',
                'c.name',
                'ip.pay_amt',
            ])
            ->having($having, $params)
            ->orderBy([
                'c.name' => SORT_ASC,
                'fi.invoice_date' => SORT_ASC,
                'fi.invoice_no' => SORT_ASC,
            ])
            ->all();

        return array_map(function ($row) {
            $date = !empty($row['invoice_date']) ? date('d.m.Y', strtotime($row['invoice_date'])) : '';
            $text = trim($row['invoice_no'] . ($date !== '' ? ' (' . $date . ')' : '') . ' - ' . $row['customer_name']);

            return [
                'id' => (int) $row['fg_invoice_id'],
                'text' => $text,
                'fg_invoice_id' => (int) $row['fg_invoice_id'],
                'invoice_no' => $row['invoice_no'],
                'invoice_date' => $row['invoice_date'],
                'amount' => (float) $row['amount'],
                'paid_amount' => (float) $row['paid_amount'],
                'unpaid_amount' => (float) $row['unpaid_amount'],
                'sales_contract_id' => (int) $row['sales_contract_id'],
                'contract_no' => $row['contract_no'],
                'currency_id' => (int) $row['currency_id'],
                'currency_code' => $row['currency_code'],
                'customer_name' => $row['customer_name'],
            ];
        }, $rows);
    }

    /**
     * Backward-compatible endpoint helper: returns unpaid FG invoices for a contract.
     */
    public function getWaybillsByContract(int $contractId): array
    {
        $contract = SalesContract::findOne($contractId);
        if ($contract === null) {
            return [];
        }

        $amountExpr = $this->invoiceAmountExpression();
        $invoiceAmountSql = 'COALESCE(SUM(' . $amountExpr . '), 0)';
        $unpaidAmountSql = $invoiceAmountSql . ' - COALESCE(ip.pay_amt, 0)';

        $rows = (new Query())
            ->select([
                'id' => 'fi.id',
                'invoice_no' => 'fi.invoice_no',
                'invoice_date' => 'fi.invoice_date',
                'amount' => $invoiceAmountSql,
                'paid_amount' => 'COALESCE(ip.pay_amt, 0)',
                'unpaid_amount' => $unpaidAmountSql,
            ])
            ->from(['fi' => 'fg_invoice'])
            ->innerJoin(['fid' => 'fg_invoice_detail'], 'fid.fg_invoice_id = fi.id')
            ->leftJoin([
                'ip' => (new Query())
                    ->select([
                        'fg_invoice_id',
                        'pay_amt' => 'SUM(amount)',
                    ])
                    ->from('fg_invoice_payment')
                    ->where(['not', ['fg_invoice_id' => null]])
                    ->groupBy(['fg_invoice_id']),
            ], 'ip.fg_invoice_id = fi.id')
            ->where([
                'fi.contract' => $contract->contract_no,
                'fi.customer_id' => $contract->customer_id,
            ])
            ->groupBy(['fi.id', 'fi.invoice_no', 'fi.invoice_date', 'ip.pay_amt'])
            ->having($unpaidAmountSql . ' > 0.01')
            ->orderBy(['fi.invoice_date' => SORT_ASC, 'fi.invoice_no' => SORT_ASC])
            ->all();

        return array_map(function ($row) {
            $date = !empty($row['invoice_date']) ? date('d.m.Y', strtotime($row['invoice_date'])) : '';
            return [
                'id' => (int) $row['id'],
                'text' => $row['invoice_no'] . ($date !== '' ? ' (' . $date . ')' : ''),
                'invoice_no' => $row['invoice_no'],
                'amount' => (float) $row['amount'],
                'paid_amount' => (float) $row['paid_amount'],
                'unpaid_amount' => (float) $row['unpaid_amount'],
            ];
        }, $rows);
    }

    public function getContract(int $contractId): ?SalesContract
    {
        return SalesContract::find()
            ->with(['customer', 'currency'])
            ->where(['id' => $contractId])
            ->one();
    }

    /**
     * Returns unpaid customer FG invoices with exact unpaid amount for bulk settlement.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getUnpaidWaybillsByCustomer(int $customerId): array
    {
        $amountExpr = $this->invoiceAmountExpression();

        $rows = (new Query())
            ->select([
                'sales_contract_id' => 'sc.id',
                'contract_no' => 'sc.contract_no',
                'currency_id' => 'sc.currency_id',
                'currency_code' => 'cu.code',
                'fg_invoice_id' => 'fi.id',
                'invoice_no' => 'fi.invoice_no',
                'invoice_date' => 'fi.invoice_date',
                'invoice_amount' => 'SUM(' . $amountExpr . ')',
                'paid_amount' => 'COALESCE(ip.pay_amt, 0)',
                'unpaid_amount' => 'SUM(' . $amountExpr . ') - COALESCE(ip.pay_amt, 0)',
            ])
            ->from(['fi' => 'fg_invoice'])
            ->innerJoin(['fid' => 'fg_invoice_detail'], 'fid.fg_invoice_id = fi.id')
            ->innerJoin(['sc' => 'sales_contract'], 'sc.contract_no = fi.contract AND sc.customer_id = fi.customer_id')
            ->leftJoin(['cu' => 'currency'], 'cu.id = sc.currency_id')
            ->leftJoin([
                'ip' => (new Query())
                    ->select([
                        'fg_invoice_id',
                        'pay_amt' => 'SUM(amount)',
                    ])
                    ->from('fg_invoice_payment')
                    ->where(['not', ['fg_invoice_id' => null]])
                    ->groupBy(['fg_invoice_id']),
            ], 'ip.fg_invoice_id = fi.id')
            ->where(['fi.customer_id' => $customerId])
            ->groupBy([
                'sc.id',
                'sc.contract_no',
                'sc.currency_id',
                'cu.code',
                'fi.id',
                'fi.invoice_no',
                'fi.invoice_date',
                'ip.pay_amt',
            ])
            ->having('SUM(' . $amountExpr . ') - COALESCE(ip.pay_amt, 0) > 0.01')
            ->orderBy([
                'fi.invoice_date' => SORT_ASC,
                'fi.invoice_no' => SORT_ASC,
                'sc.contract_no' => SORT_ASC,
            ])
            ->all();

        return array_map(function ($row) {
            return [
                'key' => $this->buildSelectionKey((int) $row['sales_contract_id'], (int) $row['fg_invoice_id']),
                'sales_contract_id' => (int) $row['sales_contract_id'],
                'contract_no' => $row['contract_no'],
                'currency_id' => (int) $row['currency_id'],
                'currency_code' => $row['currency_code'],
                'fg_invoice_id' => (int) $row['fg_invoice_id'],
                'invoice_no' => $row['invoice_no'],
                'invoice_date' => $row['invoice_date'],
                'invoice_amount' => (float) $row['invoice_amount'],
                'paid_amount' => (float) $row['paid_amount'],
                'unpaid_amount' => (float) $row['unpaid_amount'],
            ];
        }, $rows);
    }

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
            $form->addError('selected_keys', Yii::t('app', 'No unpaid invoices selected.'));
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
                $payment->fg_invoice_id = $row['fg_invoice_id'];
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

    public function save(FgInvoicePayment $model): bool
    {
        if (!$model->validate()) {
            return false;
        }

        return $model->save(false);
    }
}
