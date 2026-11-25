<?php

namespace App\Http\Controllers\Admin;

use App\Models\Career;
use App\Models\Employee;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class CareerController extends AdminResourceController
{
    protected string $model = Career::class;

    protected string $route = 'careers';

    protected string $permission = 'cms';

    protected string $singular = 'Job Opening';

    protected string $plural = 'Careers';

    protected string $description = 'Vacancies published on the careers page.';

    protected string $icon = 'fa-briefcase';

    protected array $searchable = ['title', 'location', 'description'];

    protected array $with = ['department'];

    protected function columns(): array
    {
        return [
            Column::text('title', 'Position'),
            Column::relation('department.name', 'Department'),
            Column::text('location', 'Location'),
            Column::badge('employment_type', 'Type', Employee::EMPLOYMENT_TYPES),
            Column::text('experience_level', 'Level'),
            Column::date('closing_date', 'Closes'),
            Column::boolean('status', 'Status', ['true' => 'Open', 'false' => 'Closed']),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::text('title', 'Position Title', ['required' => true]),
            Field::select('department_id', 'Department', $this->options->departments()),
            Field::text('location', 'Location', ['placeholder' => 'Remote / Lahore']),
            Field::select('employment_type', 'Employment Type', Employee::EMPLOYMENT_TYPES, ['required' => true]),
            Field::select('experience_level', 'Experience Level', Career::EXPERIENCE_LEVELS),
            Field::text('salary_range', 'Salary Range', ['placeholder' => 'Negotiable']),
            Field::date('closing_date', 'Closing Date'),
            Field::checkbox('status', 'Open', ['help' => 'Listed on the public careers page.']),
            Field::textarea('description', 'Role Description', ['rows' => 5]),
            Field::textarea('responsibilities', 'Responsibilities', ['rows' => 5]),
            Field::textarea('requirements', 'Requirements', ['rows' => 5]),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'department_id', 'label' => 'Department', 'options' => $this->options->departments()],
            ['key' => 'status', 'label' => 'Status', 'type' => 'boolean', 'options' => ['1' => 'Open', '0' => 'Closed']],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'location' => ['nullable', 'string', 'max:150'],
            'employment_type' => ['required', Rule::in(array_keys(Employee::EMPLOYMENT_TYPES))],
            'experience_level' => ['nullable', Rule::in(array_keys(Career::EXPERIENCE_LEVELS))],
            'salary_range' => ['nullable', 'string', 'max:150'],
            'closing_date' => ['nullable', 'date'],
            'status' => ['boolean'],
            'description' => ['nullable', 'string'],
            'responsibilities' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
        ];
    }
}
