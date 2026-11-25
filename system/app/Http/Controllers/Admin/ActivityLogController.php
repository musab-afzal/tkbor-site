<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('audit_logs.view');

        $logs = ActivityLog::query()
            ->with('admin')
            ->when($request->filled('search'), function (Builder $query) use ($request) {
                $term = '%'.$request->string('search').'%';

                $query->where(fn (Builder $q) => $q->where('description', 'like', $term)
                    ->orWhere('subject_label', 'like', $term)
                    ->orWhere('admin_name', 'like', $term));
            })
            ->when($request->filled('action'), fn (Builder $q) => $q->where('action', $request->input('action')))
            ->when($request->filled('admin_id'), fn (Builder $q) => $q->where('admin_id', $request->input('admin_id')))
            ->when($request->filled('subject_type'), fn (Builder $q) => $q->where('subject_type', $request->input('subject_type')))
            ->when($request->filled('from'), fn (Builder $q) => $q->whereDate('created_at', '>=', $request->input('from')))
            ->when($request->filled('to'), fn (Builder $q) => $q->whereDate('created_at', '<=', $request->input('to')))
            ->latest('id')
            ->paginate(40)
            ->withQueryString();

        return view('admin.audit-logs.index', [
            'logs' => $logs,
            'actions' => ActivityLog::query()->distinct()->orderBy('action')->pluck('action', 'action'),
            'subjects' => ActivityLog::query()
                ->whereNotNull('subject_type')
                ->distinct()
                ->pluck('subject_type')
                ->mapWithKeys(fn (string $type) => [$type => class_basename($type)]),
            'admins' => Admin::orderBy('name')->pluck('name', 'id'),
        ]);
    }
}
