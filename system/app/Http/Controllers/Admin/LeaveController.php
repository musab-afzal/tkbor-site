<?php

namespace App\Http\Controllers\Admin;

use App\Models\Leave;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LeaveController extends AdminResourceController
{
    protected string $model = Leave::class;

    protected string $route = 'leaves';

    protected string $permission = 'leaves';

    protected string $singular = 'Leave Request';

    protected string $plural = 'Leave';

    protected string $description = 'Leave requests and their approval status.';

    protected string $icon = 'fa-plane-departure';

    protected array $searchable = ['reason', 'remarks'];

    protected array $with = ['employee', 'leaveType', 'approver'];

    protected string $defaultSort = 'from_date';

    protected function columns(): array
    {
        return [
            Column::relation('employee.name', 'Employee'),
            Column::relation('leaveType.name', 'Type'),
            Column::date('from_date', 'From'),
            Column::date('to_date', 'To'),
            Column::number('total_days', 'Days'),
            Column::text('reason', 'Reason', ['muted' => true]),
            Column::badge('status', 'Status', Leave::STATUSES),
            Column::relation('approver.name', 'Approved By'),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::select('employee_id', 'Employee', $this->options->employees(), ['required' => true]),
            Field::select('leave_type_id', 'Leave Type', $this->options->leaveTypes()),
            Field::date('from_date', 'From Date', ['required' => true]),
            Field::date('to_date', 'To Date', ['required' => true]),
            Field::select('status', 'Status', Leave::STATUSES, ['required' => true]),
            Field::textarea('reason', 'Reason'),
            Field::textarea('remarks', 'Remarks'),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'employee_id', 'label' => 'Employee', 'options' => $this->options->employees()],
            ['key' => 'leave_type_id', 'label' => 'Type', 'options' => $this->options->leaveTypes()],
            ['key' => 'status', 'label' => 'Status', 'options' => Leave::STATUSES],
            ['key' => 'from', 'label' => 'From', 'input' => 'date', 'type' => 'date_from', 'column' => 'from_date'],
            ['key' => 'to', 'label' => 'To', 'input' => 'date', 'type' => 'date_to', 'column' => 'to_date'],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'employee_id' => ['required', 'exists:employees,id'],
            'leave_type_id' => ['nullable', 'exists:leave_types,id'],
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
            'status' => ['required', Rule::in(array_keys(Leave::STATUSES))],
            'reason' => ['nullable', 'string'],
            'remarks' => ['nullable', 'string'],
        ];
    }

    /** Total days is derived from the range, never typed in. */
    protected function mutateData(array $data, Request $request, Model $record): array
    {
        $from = Carbon::parse($data['from_date']);
        $to = Carbon::parse($data['to_date']);

        $data['total_days'] = $from->diffInDays($to) + 1;

        return $data;
    }

    public function approve(Leave $leave): RedirectResponse
    {
        $this->authorize('leaves.approve');

        $leave->update([
            'status' => Leave::STATUS_APPROVED,
            'approved_by' => Auth::guard('admin')->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Leave approved.');
    }

    public function reject(Leave $leave): RedirectResponse
    {
        $this->authorize('leaves.approve');

        $leave->update([
            'status' => Leave::STATUS_REJECTED,
            'approved_by' => Auth::guard('admin')->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Leave rejected.');
    }
}
