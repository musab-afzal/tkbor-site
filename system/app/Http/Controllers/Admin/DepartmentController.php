<?php

namespace App\Http\Controllers\Admin;

use App\Models\Department;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;

class DepartmentController extends AdminResourceController
{
    protected string $model = Department::class;

    protected string $route = 'departments';

    protected string $permission = 'departments';

    protected string $singular = 'Department';

    protected string $plural = 'Departments';

    protected string $description = 'How the team is organised.';

    protected string $icon = 'fa-sitemap';

    protected array $searchable = ['name', 'description'];

    protected array $with = ['manager'];

    protected array $withCount = ['employees'];

    protected string $defaultSort = 'name';

    protected string $defaultDirection = 'asc';

    protected function columns(): array
    {
        return [
            Column::text('name', 'Department'),
            Column::relation('manager.name', 'Manager'),
            Column::number('employees_count', 'Employees'),
            Column::text('description', 'Description', ['muted' => true]),
            Column::boolean('status', 'Status', ['true' => 'Active', 'false' => 'Inactive']),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::text('name', 'Name', ['required' => true]),
            Field::select('manager_id', 'Manager', $this->options->employees()),
            Field::text('description', 'Description', ['full' => true]),
            Field::checkbox('status', 'Active'),
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'manager_id' => ['nullable', 'exists:employees,id'],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['boolean'],
        ];
    }
}
