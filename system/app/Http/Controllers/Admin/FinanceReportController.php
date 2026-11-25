<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Invoice;
use App\Services\ReportBuilder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanceReportController extends Controller
{
    public function __construct(protected ReportBuilder $reports) {}

    public function index(Request $request): View
    {
        $this->authorize('finance_reports.view');

        ['from' => $from, 'to' => $to] = $this->reports->window($request->input('from'), $request->input('to'));

        return view('admin.finance.reports', [
            'from' => $from,
            'to' => $to,
            'summary' => $this->reports->financialSummary($from, $to),
            'monthly' => $this->reports->monthlyBreakdown($from, $to),
            'clients' => $this->reports->clientProfitability($from, $to),
            'projects' => $this->reports->projectProfitability(),
            'expensesByCategory' => Expense::query()
                ->selectRaw('expense_category_id, SUM(amount) as total')
                ->with('category')
                ->where('status', 'approved')
                ->whereBetween('expense_date', [$from, $to])
                ->groupBy('expense_category_id')
                ->orderByDesc('total')
                ->get(),
            'outstandingInvoices' => Invoice::with('client')
                ->unpaid()
                ->orderBy('due_date')
                ->limit(25)
                ->get(),
        ]);
    }
}
