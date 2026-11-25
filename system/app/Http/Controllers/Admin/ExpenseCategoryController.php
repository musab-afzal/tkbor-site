<?php

namespace App\Http\Controllers\Admin;

use App\Models\ExpenseCategory;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategoryController extends AdminResourceController
{
    protected string $model = ExpenseCategory::class;

    protected string $route = 'expense-categories';

    protected string $permission = 'expense_categories';

    protected string $singular = 'Expense Category';

    protected string $plural = 'Expense Categories';

    protected string $description = 'How agency spending is grouped in reports.';

    protected string $icon = 'fa-folder-tree';

    protected array $searchable = ['name'];

    protected array $withCount = ['expenses'];

    protected string $defaultSort = 'name';

    protected string $defaultDirection = 'asc';

    protected function columns(): array
    {
        return [
            Column::text('name', 'Name'),
            Column::text('description', 'Description', ['muted' => true]),
            Column::number('expenses_count', 'Expenses'),
            Column::boolean('status', 'Status', ['true' => 'Active', 'false' => 'Inactive']),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::text('name', 'Name', ['required' => true]),
            Field::checkbox('status', 'Active'),
            Field::text('description', 'Description', ['full' => true]),
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['boolean'],
        ];
    }
}
