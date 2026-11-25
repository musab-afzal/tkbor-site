<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\DashboardMetrics;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(protected DashboardMetrics $metrics) {}

    public function index(): View
    {
        return view('admin.dashboard', array_merge(
            [
                'kpis' => $this->metrics->kpis(),
                'workload' => $this->metrics->employeeWorkload(),
                'monthly' => $this->metrics->monthlySummary(),
                'activities' => ActivityLog::with('admin')->latest('id')->take(10)->get(),
            ],
            $this->metrics->panels(),
        ));
    }
}
