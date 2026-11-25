<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\Employee;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Internal HR records. Salary fields are only rendered and validated for admins
 * holding employees.view_salary.
 */
class EmployeeController extends AdminResourceController
{
    protected string $model = Employee::class;

    protected string $route = 'employees';

    protected string $permission = 'employees';

    protected string $singular = 'Employee';

    protected string $plural = 'Employees';

    protected string $description = 'Your team, their departments, skills and assignments.';

    protected string $icon = 'fa-id-badge';

    protected string $uploadDirectory = 'employees';

    protected array $searchable = ['employee_code', 'name', 'email', 'designation'];

    protected array $with = ['department', 'admin'];

    protected bool $hasShow = true;

    protected function canSeeSalary(): bool
    {
        return auth('admin')->user()?->hasPermission('employees.view_salary') ?? false;
    }

    protected function columns(): array
    {
        $columns = [
            Column::code('employee_code', 'Code'),
            Column::image('photo', 'Photo'),
            Column::text('name', 'Name'),
            Column::text('designation', 'Designation'),
            Column::relation('department.name', 'Department'),
            Column::text('email', 'Email', ['muted' => true]),
            Column::date('joining_date', 'Joined'),
        ];

        if ($this->canSeeSalary()) {
            $columns[] = Column::money('salary', 'Salary');
        }

        $columns[] = Column::badge('status', 'Status', Employee::STATUSES);

        return $columns;
    }

    protected function fields(): array
    {
        $fields = [
            Field::heading('Profile'),
            Field::text('name', 'Full Name', ['required' => true]),
            Field::text('designation', 'Designation'),
            Field::select('department_id', 'Department', $this->options->departments()),
            Field::email('email', 'Email'),
            Field::tel('phone', 'Phone'),
            Field::tel('whatsapp', 'WhatsApp'),
            Field::file('photo', 'Photo', ['accept' => 'image/*']),

            Field::heading('Employment'),
            Field::select('employment_type', 'Employment Type', Employee::EMPLOYMENT_TYPES, ['required' => true]),
            Field::select('status', 'Status', Employee::STATUSES, ['required' => true]),
            Field::date('joining_date', 'Joining Date'),
            Field::date('exit_date', 'Exit Date'),
            Field::list('skills', 'Skills', ['placeholder' => 'Laravel, Figma, SEO']),
            Field::select('admin_id', 'Linked Login Account', $this->adminOptions(), [
                'help' => 'Links this employee to an admin login so "My Tasks" works for them.',
            ]),

            Field::heading('Other'),
            Field::textarea('address', 'Address', ['rows' => 2]),
            Field::textarea('notes', 'Notes'),
        ];

        if ($this->canSeeSalary()) {
            array_splice($fields, count($fields) - 3, 0, [
                Field::heading('Compensation'),
                Field::money('salary', 'Monthly Salary'),
                Field::money('hourly_rate', 'Hourly Rate', ['help' => 'Used to value billable timesheet hours.']),
            ]);
        }

        return $fields;
    }

    protected function filters(): array
    {
        return [
            ['key' => 'department_id', 'label' => 'Department', 'options' => $this->options->departments()],
            ['key' => 'status', 'label' => 'Status', 'options' => Employee::STATUSES],
            ['key' => 'employment_type', 'label' => 'Type', 'options' => Employee::EMPLOYMENT_TYPES],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:150'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('employees', 'email')->ignore($record?->getKey())->whereNull('deleted_at')],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'employment_type' => ['required', Rule::in(array_keys(Employee::EMPLOYMENT_TYPES))],
            'status' => ['required', Rule::in(array_keys(Employee::STATUSES))],
            'joining_date' => ['nullable', 'date'],
            'exit_date' => ['nullable', 'date', 'after_or_equal:joining_date'],
            'skills' => ['nullable', 'string', 'max:1000'],
            'admin_id' => ['nullable', 'exists:admins,id', Rule::unique('employees', 'admin_id')->ignore($record?->getKey())->whereNull('deleted_at')],
            'address' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string'],
        ];

        if ($this->canSeeSalary()) {
            $rules['salary'] = ['nullable', 'numeric', 'min:0'];
            $rules['hourly_rate'] = ['nullable', 'numeric', 'min:0'];
        }

        return $rules;
    }

    /** Employee codes are generated, never entered. */
    protected function mutateData(array $data, Request $request, Model $record): array
    {
        if (! $record->exists) {
            $data['employee_code'] = $this->nextCode();
        }

        return $data;
    }

    public function show(Employee $employee): View
    {
        $this->authorizeAction('view');

        $employee->load([
            'department', 'admin',
            'projects' => fn ($query) => $query->with('client')->latest('projects.id'),
            'managedProjects.client',
            'tasks' => fn ($query) => $query->with('project')->where('tasks.status', '!=', 'completed')->orderBy('due_date'),
            'timesheets' => fn ($query) => $query->latest('date')->limit(20),
            'leaves' => fn ($query) => $query->with('leaveType')->latest('from_date')->limit(10),
        ]);

        return view('admin.employees.show', [
            'employee' => $employee,
            'canSeeSalary' => $this->canSeeSalary(),
            'stats' => [
                'open_tasks' => $employee->tasks->count(),
                'overdue_tasks' => $employee->tasks->filter(fn ($task) => $task->isOverdue())->count(),
                'active_projects' => $employee->projects->whereIn('status', ['planning', 'active', 'on_hold', 'testing'])->count(),
                'hours_this_month' => (float) $employee->timesheets()
                    ->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])
                    ->sum('hours'),
            ],
        ]);
    }

    protected function nextCode(): string
    {
        $last = Employee::withTrashed()->orderByDesc('id')->value('employee_code');
        $sequence = $last ? (int) Str::afterLast($last, '-') : 0;

        return 'EMP-'.str_pad((string) ($sequence + 1), 4, '0', STR_PAD_LEFT);
    }

    /** @return array<int, string> */
    protected function adminOptions(): array
    {
        return Admin::orderBy('name')->pluck('name', 'id')->all();
    }
}
