# FgInvoicePayment Module — Design Spec

**Date:** 2026-04-06  
**Goal:** Replace the existing `ReceiptWaybill` payment flow with a new, cleaner module. The new flow starts with contract selection, then filters TTN (waybill) numbers, captures a payment date and number, auto-shows customer name (readonly), and records an amount. The old `receipt_waybill` and `recept_control` tables are preserved as-is.

---

## 1. Database

### New table: `fg_invoice_payment`

| Column             | Type            | Notes                          |
|--------------------|-----------------|--------------------------------|
| `id`               | INT PK AI       |                                |
| `no`               | VARCHAR(100)    | Payment/receipt number         |
| `date`             | DATE            | Payment date                   |
| `sales_contract_id`| INT NOT NULL    | FK → `sales_contract.id`       |
| `amount`           | DECIMAL(15,2)   |                                |
| `created_at`       | INT             | TimestampBehavior               |
| `created_by`       | INT             | BlameableBehavior → `user.id`  |
| `updated_at`       | INT             |                                |
| `updated_by`       | INT             |                                |

`customer_id` is not stored — derived via `sales_contract.customer_id` to avoid redundancy.

### New table: `fg_invoice_payment_waybill` (pivot)

| Column       | Type     | Notes                              |
|--------------|----------|------------------------------------|
| `id`         | INT PK AI|                                    |
| `payment_id` | INT      | FK → `fg_invoice_payment.id`       |
| `waybill_id` | INT      | FK → `waybill.id`                  |

One payment → many waybills. One waybill → many payments (no uniqueness constraint).

Migration creates only these two tables. No changes to existing tables.

---

## 2. Architecture

### Files created

```
_protected/
  models/
    FgInvoicePayment.php            ActiveRecord → fg_invoice_payment
    FgInvoicePaymentWaybill.php     ActiveRecord → fg_invoice_payment_waybill
    FgInvoicePaymentSearch.php      Search model (extends FgInvoicePayment)
  controllers/
    FgInvoicePaymentController.php  extends AppController
  services/
    FgInvoicePaymentService.php
  views/fg-invoice-payment/
    index.php
    _form.php
  console/migrations/
    mXXXXXX_fg_invoice_payment.php
```

### Controller responsibilities

HTTP layer only. Actions:
- `actionIndex` — renders GridView with search/filter
- `actionCreate` — AJAX modal form
- `actionUpdate($id)` — AJAX modal form
- `actionDelete($id)` — POST only, JSON response
- `actionValidate($id = null)` — AJAX validation
- `actionXls` — Excel export via existing `codemix/excelexport` pattern
- `actionListWaybillsByContract($id)` — AJAX endpoint; returns waybill list + customer name for given `contract_id`

### Service responsibilities (`FgInvoicePaymentService`)

Business logic, no HTTP:

- `getWaybillsByContract(int $contractId): array`  
  Traverses `SalesContract` → `FgInvoice` → `FgInvoiceWaybill` → `Waybill`. Returns `[['id' => ..., 'text' => waybill_no], ...]`.

- `getCustomerByContract(int $contractId): ?Customer`  
  Returns the `Customer` linked to the given `SalesContract`.

- `save(FgInvoicePayment $model, array $waybillIds): bool`  
  Runs in a DB transaction: saves `FgInvoicePayment`, then inserts one `FgInvoicePaymentWaybill` row per `waybillId`. Rolls back on any failure.

---

## 3. Form UI (`_form.php`)

**Field layout:**

```
Row 1: [Sales Contract (select2)]        [Payment No (text)]
Row 2: [Waybills (multi-select2)]        [Date (datepicker)]
Row 3: [Customer Name (readonly input, auto-populated from contract)]
Row 4: [Amount (number input)]
```

**JS behaviour:**
- On contract `select2:select` → fires AJAX to `action-list-waybills-by-contract?id=X`
- Response populates waybill multi-select options and fills customer readonly input
- On update load: existing `sales_contract_id` triggers the same AJAX call; existing `waybill_ids` are pre-selected in the multi-select
- Form submits via AJAX modal (same pattern as existing controllers)

---

## 4. Index View (`index.php`)

**GridView columns:**

| # | Actions | Customer | Contract No | Payment No | Date | Waybills | Amount |
|---|---------|----------|-------------|------------|------|----------|--------|

- **Customer** — via `salesContract.customer.name`
- **Waybills** — comma-separated `waybill_no` values from pivot
- **Amount** — right-aligned, formatted via `Helpers::numberFormatRemoveZero`
- Filterable: customer name, contract_no, payment no, date, amount

**Buttons:** Create (modal), Download Excel — both RBAC-guarded.

---

## 5. Validation & Error Handling

### Model-level validation
- `no`, `date`, `sales_contract_id`, `amount` — required
- `amount` — positive number
- `waybill_ids` — custom validator: at least one waybill must be selected
- AJAX validation via `actionValidate` (same pattern as existing controllers)

### Service-level
- `save()` uses a DB transaction; rolls back if pivot insert fails
- Returns `false` on failure; controller reads model errors and returns JSON `{status: 0, errors: {...}}`

### Controller-level
- `actionListWaybillsByContract` returns `{customer: null, waybills: []}` if contract not found; JS handles empty state gracefully (clears fields, shows no options)
- Delete action restricted to POST via VerbFilter

### Edge case
- If a contract has no linked FgInvoice/Waybill yet, the waybill multi-select is empty. User must select a different contract. No automatic error — the empty state is self-explanatory.

---

## 6. RBAC Permissions

Following the `{controller-id}-{action-id}` convention enforced by `AppController::beforeAction`:

- `fg-invoice-payment-index`
- `fg-invoice-payment-create`
- `fg-invoice-payment-update`
- `fg-invoice-payment-delete`
- `fg-invoice-payment-xls`

The `action-list-waybills-by-contract` action is in the RBAC bypass list in `AppController` (same as other `list-by-*` actions).
