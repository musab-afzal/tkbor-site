<?php

namespace App\Http\Controllers\Admin;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\InvoiceCalculator;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Recording or removing a payment always re-runs the invoice calculator so the
 * paid amount, balance and status can never drift from the payment rows.
 */
class PaymentController extends AdminResourceController
{
    protected string $model = Payment::class;

    protected string $route = 'payments';

    protected string $permission = 'payments';

    protected string $singular = 'Payment';

    protected string $plural = 'Payments';

    protected string $description = 'Money received against invoices.';

    protected string $icon = 'fa-money-bill-transfer';

    protected string $uploadDirectory = 'receipts';

    protected array $searchable = ['payment_number', 'reference', 'notes'];

    protected array $with = ['client', 'invoice'];

    protected bool $hasShow = true;

    protected string $defaultSort = 'payment_date';

    protected function columns(): array
    {
        return [
            Column::code('payment_number', 'Payment #'),
            Column::relation('invoice.invoice_number', 'Invoice'),
            Column::relation('client.company_name', 'Client'),
            Column::money('amount', 'Amount'),
            Column::date('payment_date', 'Date'),
            Column::badge('method', 'Method', Payment::METHODS),
            Column::text('reference', 'Reference', ['muted' => true]),
            Column::badge('status', 'Status', Payment::STATUSES),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::select('client_id', 'Client', $this->options->clients(), ['required' => true]),
            Field::select('invoice_id', 'Invoice', $this->options->invoices(), [
                'help' => 'Leave blank for an advance not yet tied to an invoice.',
            ]),
            Field::money('amount', 'Amount', ['required' => true]),
            Field::select('currency', 'Currency', $this->options->currencies()),
            Field::date('payment_date', 'Payment Date', ['required' => true]),
            Field::select('method', 'Method', Payment::METHODS, ['required' => true]),
            Field::select('status', 'Status', Payment::STATUSES, ['required' => true]),
            Field::text('reference', 'Transaction / Reference Number'),
            Field::file('receipt', 'Receipt', ['accept' => 'image/*,.pdf']),
            Field::textarea('notes', 'Notes'),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'client_id', 'label' => 'Client', 'options' => $this->options->clients()],
            ['key' => 'method', 'label' => 'Method', 'options' => Payment::METHODS],
            ['key' => 'status', 'label' => 'Status', 'options' => Payment::STATUSES],
            ['key' => 'from', 'label' => 'From', 'input' => 'date', 'type' => 'date_from', 'column' => 'payment_date'],
            ['key' => 'to', 'label' => 'To', 'input' => 'date', 'type' => 'date_to', 'column' => 'payment_date'],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'invoice_id' => ['nullable', 'exists:invoices,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['nullable', 'string', 'size:3'],
            'payment_date' => ['required', 'date'],
            'method' => ['required', Rule::in(array_keys(Payment::METHODS))],
            'status' => ['required', Rule::in(array_keys(Payment::STATUSES))],
            'reference' => ['nullable', 'string', 'max:255'],
            'receipt' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'notes' => ['nullable', 'string'],
        ];
    }

    protected function afterSave(Model $record, Request $request, bool $created): void
    {
        $this->refreshInvoices($record, $request);
    }

    /** Removing a payment must give the invoice its balance back. */
    public function destroy(): RedirectResponse
    {
        $this->authorizeAction('delete');

        $payment = $this->resolveRecord();
        $invoiceId = $payment->invoice_id;

        $response = $this->deleteRecord($payment);

        if ($invoiceId && $invoice = Invoice::find($invoiceId)) {
            app(InvoiceCalculator::class)->recalculate($invoice);
        }

        return $response;
    }

    public function show(Payment $payment): View
    {
        $this->authorizeAction('view');

        $payment->load(['client', 'invoice.items']);

        return view('admin.payments.show', [
            'payment' => $payment,
            'settings' => settings(),
        ]);
    }

    /** Rebuild the invoice this payment belongs to, and the one it left. */
    protected function refreshInvoices(Model $payment, Request $request): void
    {
        $calculator = app(InvoiceCalculator::class);

        $ids = array_filter([
            $payment->invoice_id,
            $payment->getOriginal('invoice_id'),
        ]);

        foreach (array_unique($ids) as $invoiceId) {
            if ($invoice = Invoice::find($invoiceId)) {
                $calculator->recalculate($invoice);
            }
        }
    }
}
