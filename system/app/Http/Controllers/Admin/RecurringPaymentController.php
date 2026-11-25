<?php

namespace App\Http\Controllers\Admin;

use App\Models\RecurringPayment;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RecurringPaymentController extends AdminResourceController
{
    protected string $model = RecurringPayment::class;

    protected string $route = 'recurring-payments';

    protected string $permission = 'recurring_payments';

    protected string $singular = 'Recurring Payment';

    protected string $plural = 'Recurring Payments';

    protected string $description = 'Hosting, software, licences, salaries and retainers that repeat.';

    protected string $icon = 'fa-rotate';

    protected array $searchable = ['title', 'vendor', 'category'];

    protected array $with = ['client', 'expenseCategory'];

    protected string $defaultSort = 'next_due_date';

    protected string $defaultDirection = 'asc';

    protected function columns(): array
    {
        return [
            Column::text('title', 'Title'),
            Column::badge('direction', 'Direction', RecurringPayment::DIRECTIONS),
            Column::relation('expenseCategory.name', 'Category'),
            Column::text('vendor', 'Vendor'),
            Column::money('amount', 'Amount'),
            Column::badge('frequency', 'Frequency', RecurringPayment::FREQUENCIES),
            Column::custom('next', 'Next Due', function (RecurringPayment $payment) {
                if (! $payment->next_due_date) {
                    return '<span class="admin-cell-muted">—</span>';
                }

                $label = e($payment->next_due_date->format('M d, Y'));

                return $payment->isOverdue()
                    ? '<span class="admin-badge admin-badge--overdue">'.$label.'</span>'
                    : $label;
            }),
            Column::badge('status', 'Status', RecurringPayment::STATUSES),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::text('title', 'Title', ['required' => true]),
            Field::select('direction', 'Direction', RecurringPayment::DIRECTIONS, ['required' => true]),
            Field::select('expense_category_id', 'Expense Category', $this->options->expenseCategories()),
            Field::select('client_id', 'Client', $this->options->clients(), ['help' => 'For recurring income such as a retainer.']),
            Field::text('vendor', 'Vendor / Provider'),
            Field::money('amount', 'Amount', ['required' => true]),
            Field::select('currency', 'Currency', $this->options->currencies()),
            Field::select('frequency', 'Frequency', RecurringPayment::FREQUENCIES, ['required' => true]),
            Field::date('start_date', 'Start Date'),
            Field::date('next_due_date', 'Next Due Date'),
            Field::date('end_date', 'End Date'),
            Field::number('reminder_days', 'Remind me (days before)'),
            Field::select('status', 'Status', RecurringPayment::STATUSES, ['required' => true]),
            Field::checkbox('auto_renew', 'Auto renews'),
            Field::textarea('notes', 'Notes'),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'direction', 'label' => 'Direction', 'options' => RecurringPayment::DIRECTIONS],
            ['key' => 'frequency', 'label' => 'Frequency', 'options' => RecurringPayment::FREQUENCIES],
            ['key' => 'status', 'label' => 'Status', 'options' => RecurringPayment::STATUSES],
            ['key' => 'due', 'label' => 'Due', 'options' => ['1' => 'Due in 30 days']],
        ];
    }

    protected function scopeQuery(Builder $query, Request $request): Builder
    {
        return $request->boolean('due') ? $query->dueSoon(30) : $query;
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'direction' => ['required', Rule::in(array_keys(RecurringPayment::DIRECTIONS))],
            'expense_category_id' => ['nullable', 'exists:expense_categories,id'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'vendor' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'frequency' => ['required', Rule::in(array_keys(RecurringPayment::FREQUENCIES))],
            'start_date' => ['nullable', 'date'],
            'next_due_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'reminder_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'status' => ['required', Rule::in(array_keys(RecurringPayment::STATUSES))],
            'auto_renew' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /** Marks the current period paid and rolls the due date forward. */
    public function markPaid(RecurringPayment $recurringPayment): RedirectResponse
    {
        $this->authorizeAction('edit');

        $recurringPayment->advanceDueDate();

        return back()->with('success', 'Marked as paid. Next due date is '
            .($recurringPayment->next_due_date?->format('M d, Y') ?? 'not set').'.');
    }
}
