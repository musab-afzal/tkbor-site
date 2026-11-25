<?php

namespace App\Http\Controllers\Admin;

use App\Models\Attendance;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class AttendanceController extends AdminResourceController
{
    protected string $model = Attendance::class;

    protected string $route = 'attendance';

    protected string $permission = 'attendance';

    protected string $singular = 'Attendance Entry';

    protected string $plural = 'Attendance';

    protected string $description = 'Daily attendance per employee.';

    protected string $icon = 'fa-calendar-check';

    protected array $searchable = ['notes'];

    protected array $with = ['employee'];

    protected string $defaultSort = 'date';

    protected function columns(): array
    {
        return [
            Column::date('date', 'Date'),
            Column::relation('employee.name', 'Employee'),
            Column::text('check_in', 'Check In'),
            Column::text('check_out', 'Check Out'),
            Column::number('worked_hours', 'Hours', ['decimals' => 2]),
            Column::badge('status', 'Status', Attendance::STATUSES),
            Column::text('notes', 'Notes', ['muted' => true]),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::select('employee_id', 'Employee', $this->options->employees(), ['required' => true]),
            Field::date('date', 'Date', ['required' => true]),
            Field::select('status', 'Status', Attendance::STATUSES, ['required' => true]),
            Field::time('check_in', 'Check In'),
            Field::time('check_out', 'Check Out'),
            Field::number('worked_hours', 'Worked Hours', ['attributes' => ['step' => '0.25', 'min' => '0']]),
            Field::textarea('notes', 'Notes'),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'employee_id', 'label' => 'Employee', 'options' => $this->options->employees()],
            ['key' => 'status', 'label' => 'Status', 'options' => Attendance::STATUSES],
            ['key' => 'from', 'label' => 'From', 'input' => 'date', 'type' => 'date_from', 'column' => 'date'],
            ['key' => 'to', 'label' => 'To', 'input' => 'date', 'type' => 'date_to', 'column' => 'date'],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'employee_id' => [
                'required',
                'exists:employees,id',
                // One row per employee per day.
                Rule::unique('attendances', 'employee_id')
                    ->where(fn ($query) => $query->where('date', request('date'))->whereNull('deleted_at'))
                    ->ignore($record?->getKey()),
            ],
            'date' => ['required', 'date'],
            'status' => ['required', Rule::in(array_keys(Attendance::STATUSES))],
            'check_in' => ['nullable', 'date_format:H:i'],
            'check_out' => ['nullable', 'date_format:H:i'],
            'worked_hours' => ['nullable', 'numeric', 'min:0', 'max:24'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
