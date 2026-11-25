<?php

namespace App\Http\Controllers\Admin;

use App\Models\Expense;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class ExpenseController extends AdminResourceController
{
    protected string $model = Expense::class;

    protected string $route = 'expenses';

    protected string $permission = 'expenses';

    protected string $singular = 'Expense';

    protected string $plural = 'Expenses';

    protected string $description = 'Everything the agency spends, optionally tied to a project.';

    protected string $icon = 'fa-receipt';

    protected string $uploadDirectory = 'expenses';

    protected array $searchable = ['expense_number', 'title', 'vendor', 'description'];

    protected array $with = ['category', 'project'];

    protected string $defaultSort = 'expense_date';

    protected function columns(): array
    {
        return [
            Column::code('expense_number', 'Expense #'),
            Column::text('title', 'Title'),
            Column::relation('category.name', 'Category'),
            Column::relation('project.name', 'Project'),
            Column::text('vendor', 'Vendor'),
            Column::money('amount', 'Amount'),
            Column::date('expense_date', 'Date'),
            Column::badge('status', 'Status', Expense::STATUSES),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::text('title', 'Title', ['required' => true]),
            Field::select('expense_category_id', 'Category', $this->options->expenseCategories()),
            Field::select('project_id', 'Project', $this->options->projects(), ['help' => 'Optional — links the cost to a project for profitability.']),
            Field::select('employee_id', 'Employee', $this->options->employees(), ['help' => 'For reimbursements or salary entries.']),
            Field::text('vendor', 'Vendor / Paid To'),
            Field::money('amount', 'Amount', ['required' => true]),
            Field::select('currency', 'Currency', $this->options->currencies()),
            Field::date('expense_date', 'Date', ['required' => true]),
            Field::select('method', 'Payment Method', Expense::METHODS, ['required' => true]),
            Field::select('status', 'Status', Expense::STATUSES, ['required' => true]),
            Field::file('receipt', 'Receipt', ['accept' => 'image/*,.pdf']),
            Field::textarea('description', 'Description'),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'expense_category_id', 'label' => 'Category', 'options' => $this->options->expenseCategories()],
            ['key' => 'project_id', 'label' => 'Project', 'options' => $this->options->projects()],
            ['key' => 'status', 'label' => 'Status', 'options' => Expense::STATUSES],
            ['key' => 'from', 'label' => 'From', 'input' => 'date', 'type' => 'date_from', 'column' => 'expense_date'],
            ['key' => 'to', 'label' => 'To', 'input' => 'date', 'type' => 'date_to', 'column' => 'expense_date'],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'expense_category_id' => ['nullable', 'exists:expense_categories,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'vendor' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'expense_date' => ['required', 'date'],
            'method' => ['required', Rule::in(array_keys(Expense::METHODS))],
            'status' => ['required', Rule::in(array_keys(Expense::STATUSES))],
            'receipt' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'description' => ['nullable', 'string'],
        ];
    }
}
