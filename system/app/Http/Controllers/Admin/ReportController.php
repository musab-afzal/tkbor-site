<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Bug;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\WebsiteInquiry;
use App\Services\ReportBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * One reporting screen per subject, all driven by the same date/status/owner
 * filters and all exportable to CSV.
 */
class ReportController extends Controller
{
    public function __construct(protected ReportBuilder $reports) {}

    public function index(): View
    {
        $this->authorize('reports.view');

        return view('admin.reports.index', ['reports' => $this->catalogue()]);
    }

    public function show(Request $request, string $report): View|StreamedResponse
    {
        $this->authorize('reports.view');

        $catalogue = $this->catalogue();

        abort_unless(isset($catalogue[$report]), 404);

        ['from' => $from, 'to' => $to] = $this->reports->window($request->input('from'), $request->input('to'));

        $data = $this->build($report, $from, $to, $request);

        if ($request->boolean('export')) {
            $this->authorize('reports.export');

            return $this->export($report, $data['rows'], $data['headings']);
        }

        return view('admin.reports.show', array_merge($data, [
            'report' => $report,
            'meta' => $catalogue[$report],
            'reports' => $catalogue,
            'from' => $from,
            'to' => $to,
        ]));
    }

    /**
     * @return array{headings: array<int, string>, rows: array<int, array<int, mixed>>, summary: array<string, mixed>}
     */
    protected function build(string $report, Carbon $from, Carbon $to, Request $request): array
    {
        return match ($report) {
            'leads' => $this->leadsReport($from, $to),
            'lead-conversion' => $this->leadConversionReport($from, $to),
            'clients' => $this->clientsReport(),
            'projects' => $this->projectsReport(),
            'tasks' => $this->tasksReport($from, $to),
            'employee-workload' => $this->workloadReport(),
            'quotations' => $this->quotationsReport($from, $to),
            'sales' => $this->salesReport($from, $to),
            'invoices' => $this->invoicesReport($from, $to),
            'payments' => $this->paymentsReport($from, $to),
            'expenses' => $this->expensesReport($from, $to),
            'profit' => $this->profitReport($from, $to),
            'website-inquiries' => $this->inquiriesReport($from, $to),
            'support-tickets' => $this->ticketsReport($from, $to),
            'bugs' => $this->bugsReport($from, $to),
            'renewals' => $this->renewalsReport(),
            default => ['headings' => [], 'rows' => [], 'summary' => []],
        };
    }

    protected function leadsReport(Carbon $from, Carbon $to): array
    {
        $leads = Lead::with('assignee')->whereBetween('created_at', [$from, $to])->latest('id')->get();

        return [
            'headings' => ['Lead #', 'Name', 'Company', 'Source', 'Budget', 'Priority', 'Status', 'Assignee', 'Created'],
            'rows' => $leads->map(fn (Lead $lead) => [
                $lead->lead_number,
                $lead->name,
                $lead->company,
                Lead::SOURCES[$lead->source] ?? $lead->source,
                money($lead->estimated_budget),
                Lead::PRIORITIES[$lead->priority] ?? $lead->priority,
                Lead::STATUSES[$lead->status] ?? $lead->status,
                $lead->assignee?->name,
                $lead->created_at?->format('Y-m-d'),
            ])->all(),
            'summary' => [
                'Total leads' => $leads->count(),
                'Won' => $leads->where('status', 'won')->count(),
                'Lost' => $leads->where('status', 'lost')->count(),
                'Pipeline value' => money($leads->whereNotIn('status', ['won', 'lost'])->sum('estimated_budget')),
            ],
        ];
    }

    protected function leadConversionReport(Carbon $from, Carbon $to): array
    {
        $rows = [];
        $total = Lead::whereBetween('created_at', [$from, $to])->count();

        foreach (Lead::STATUSES as $status => $label) {
            $count = Lead::whereBetween('created_at', [$from, $to])->where('status', $status)->count();

            $rows[] = [$label, $count, $total > 0 ? round($count / $total * 100, 1).'%' : '0%'];
        }

        $won = Lead::whereBetween('created_at', [$from, $to])->where('status', 'won')->count();

        return [
            'headings' => ['Stage', 'Leads', 'Share'],
            'rows' => $rows,
            'summary' => [
                'Total leads' => $total,
                'Converted' => $won,
                'Conversion rate' => $total > 0 ? round($won / $total * 100, 1).'%' : '0%',
            ],
        ];
    }

    protected function clientsReport(): array
    {
        $clients = Client::withCount(['projects', 'invoices'])
            ->withSum(['payments as received' => fn (Builder $q) => $q->where('status', 'completed')], 'amount')
            ->orderByDesc('received')
            ->get();

        return [
            'headings' => ['Code', 'Client', 'Type', 'Country', 'Projects', 'Invoices', 'Received', 'Outstanding', 'Status'],
            'rows' => $clients->map(fn (Client $client) => [
                $client->client_code,
                $client->displayName(),
                Client::TYPES[$client->type] ?? $client->type,
                $client->country,
                $client->projects_count,
                $client->invoices_count,
                money($client->received),
                money($client->outstandingBalance()),
                $client->status ? 'Active' : 'Inactive',
            ])->all(),
            'summary' => [
                'Total clients' => $clients->count(),
                'Active' => $clients->where('status', true)->count(),
                'Total received' => money($clients->sum('received')),
            ],
        ];
    }

    protected function projectsReport(): array
    {
        $projects = Project::with(['client', 'manager'])->withCount('tasks')->latest('id')->get();

        return [
            'headings' => ['Project #', 'Project', 'Client', 'Manager', 'Budget', 'Start', 'Deadline', 'Progress', 'Tasks', 'Status'],
            'rows' => $projects->map(fn (Project $project) => [
                $project->project_number,
                $project->name,
                $project->client?->displayName(),
                $project->manager?->name,
                money($project->budget, $project->currency),
                $project->start_date?->format('Y-m-d'),
                $project->deadline?->format('Y-m-d'),
                $project->progress.'%',
                $project->tasks_count,
                Project::STATUSES[$project->status] ?? $project->status,
            ])->all(),
            'summary' => [
                'Total projects' => $projects->count(),
                'Active' => $projects->whereIn('status', ['planning', 'active', 'on_hold', 'testing'])->count(),
                'Completed' => $projects->where('status', 'completed')->count(),
                'Overdue' => $projects->filter(fn (Project $project) => $project->isOverdue())->count(),
                'Total budget' => money($projects->sum('budget')),
            ],
        ];
    }

    protected function tasksReport(Carbon $from, Carbon $to): array
    {
        $tasks = Task::with(['project', 'assignees'])
            ->whereBetween('created_at', [$from, $to])
            ->latest('id')
            ->get();

        return [
            'headings' => ['Task #', 'Title', 'Project', 'Assignees', 'Priority', 'Status', 'Due', 'Estimated h', 'Actual h'],
            'rows' => $tasks->map(fn (Task $task) => [
                $task->task_number,
                $task->title,
                $task->project?->name,
                $task->assignees->pluck('name')->join(', '),
                Task::PRIORITIES[$task->priority] ?? $task->priority,
                Task::STATUSES[$task->status] ?? $task->status,
                $task->due_date?->format('Y-m-d'),
                $task->estimated_hours,
                $task->actual_hours,
            ])->all(),
            'summary' => [
                'Total tasks' => $tasks->count(),
                'Completed' => $tasks->where('status', 'completed')->count(),
                'Overdue' => $tasks->filter(fn (Task $task) => $task->isOverdue())->count(),
                'Estimated hours' => round((float) $tasks->sum('estimated_hours'), 2),
                'Actual hours' => round((float) $tasks->sum('actual_hours'), 2),
            ],
        ];
    }

    protected function workloadReport(): array
    {
        $employees = Employee::active()
            ->with('department')
            ->withCount([
                'tasks as open_tasks' => fn (Builder $q) => $q->where('tasks.status', '!=', 'completed'),
                'tasks as overdue_tasks' => fn (Builder $q) => $q->where('tasks.status', '!=', 'completed')->whereDate('tasks.due_date', '<', today()),
                'projects as active_projects' => fn (Builder $q) => $q->whereIn('projects.status', ['planning', 'active', 'on_hold', 'testing']),
            ])
            ->orderByDesc('open_tasks')
            ->get();

        return [
            'headings' => ['Employee', 'Department', 'Designation', 'Open Tasks', 'Overdue', 'Active Projects'],
            'rows' => $employees->map(fn (Employee $employee) => [
                $employee->name,
                $employee->department?->name,
                $employee->designation,
                $employee->open_tasks,
                $employee->overdue_tasks,
                $employee->active_projects,
            ])->all(),
            'summary' => [
                'Employees' => $employees->count(),
                'Open tasks' => $employees->sum('open_tasks'),
                'Overdue tasks' => $employees->sum('overdue_tasks'),
            ],
        ];
    }

    protected function quotationsReport(Carbon $from, Carbon $to): array
    {
        $quotations = Quotation::with('client')->whereBetween('quotation_date', [$from, $to])->latest('id')->get();

        return [
            'headings' => ['Quotation #', 'Client', 'Project', 'Date', 'Valid Until', 'Total', 'Status'],
            'rows' => $quotations->map(fn (Quotation $quotation) => [
                $quotation->quotation_number,
                $quotation->client?->displayName(),
                $quotation->project_name,
                $quotation->quotation_date?->format('Y-m-d'),
                $quotation->valid_until?->format('Y-m-d'),
                money($quotation->grand_total, $quotation->currency),
                Quotation::STATUSES[$quotation->status] ?? $quotation->status,
            ])->all(),
            'summary' => [
                'Total quotations' => $quotations->count(),
                'Approved' => $quotations->where('status', 'approved')->count(),
                'Rejected' => $quotations->where('status', 'rejected')->count(),
                'Quoted value' => money($quotations->sum('grand_total')),
                'Approved value' => money($quotations->where('status', 'approved')->sum('grand_total')),
            ],
        ];
    }

    protected function salesReport(Carbon $from, Carbon $to): array
    {
        $approved = Quotation::with('client')
            ->where('status', 'approved')
            ->whereBetween('quotation_date', [$from, $to])
            ->get();

        return [
            'headings' => ['Quotation #', 'Client', 'Project', 'Approved On', 'Value', 'Converted Project'],
            'rows' => $approved->map(fn (Quotation $quotation) => [
                $quotation->quotation_number,
                $quotation->client?->displayName(),
                $quotation->project_name,
                $quotation->decided_at?->format('Y-m-d'),
                money($quotation->grand_total, $quotation->currency),
                $quotation->convertedProject?->project_number ?? 'Not converted',
            ])->all(),
            'summary' => [
                'Won deals' => $approved->count(),
                'Total value' => money($approved->sum('grand_total')),
                'Average deal' => money($approved->count() ? $approved->sum('grand_total') / $approved->count() : 0),
                'Converted to projects' => $approved->whereNotNull('converted_project_id')->count(),
            ],
        ];
    }

    protected function invoicesReport(Carbon $from, Carbon $to): array
    {
        $invoices = Invoice::with('client')->whereBetween('invoice_date', [$from, $to])->latest('id')->get();

        return [
            'headings' => ['Invoice #', 'Client', 'Date', 'Due', 'Total', 'Paid', 'Balance', 'Status'],
            'rows' => $invoices->map(fn (Invoice $invoice) => [
                $invoice->invoice_number,
                $invoice->client?->displayName(),
                $invoice->invoice_date?->format('Y-m-d'),
                $invoice->due_date?->format('Y-m-d'),
                money($invoice->total, $invoice->currency),
                money($invoice->paid_amount, $invoice->currency),
                money($invoice->balance, $invoice->currency),
                Invoice::STATUSES[$invoice->status] ?? $invoice->status,
            ])->all(),
            'summary' => [
                'Invoices' => $invoices->count(),
                'Invoiced' => money($invoices->sum('total')),
                'Received' => money($invoices->sum('paid_amount')),
                'Outstanding' => money($invoices->sum('balance')),
                'Overdue' => $invoices->filter(fn (Invoice $invoice) => $invoice->isOverdue())->count(),
            ],
        ];
    }

    protected function paymentsReport(Carbon $from, Carbon $to): array
    {
        $payments = Payment::with(['client', 'invoice'])->whereBetween('payment_date', [$from, $to])->latest('payment_date')->get();

        return [
            'headings' => ['Payment #', 'Invoice', 'Client', 'Date', 'Method', 'Reference', 'Amount', 'Status'],
            'rows' => $payments->map(fn (Payment $payment) => [
                $payment->payment_number,
                $payment->invoice?->invoice_number,
                $payment->client?->displayName(),
                $payment->payment_date?->format('Y-m-d'),
                Payment::METHODS[$payment->method] ?? $payment->method,
                $payment->reference,
                money($payment->amount, $payment->currency),
                Payment::STATUSES[$payment->status] ?? $payment->status,
            ])->all(),
            'summary' => [
                'Payments' => $payments->count(),
                'Total received' => money($payments->where('status', 'completed')->sum('amount')),
            ],
        ];
    }

    protected function expensesReport(Carbon $from, Carbon $to): array
    {
        $expenses = Expense::with(['category', 'project'])->whereBetween('expense_date', [$from, $to])->latest('expense_date')->get();

        return [
            'headings' => ['Expense #', 'Title', 'Category', 'Project', 'Vendor', 'Date', 'Amount', 'Status'],
            'rows' => $expenses->map(fn (Expense $expense) => [
                $expense->expense_number,
                $expense->title,
                $expense->category?->name,
                $expense->project?->name,
                $expense->vendor,
                $expense->expense_date?->format('Y-m-d'),
                money($expense->amount, $expense->currency),
                ucfirst($expense->status),
            ])->all(),
            'summary' => [
                'Expenses' => $expenses->count(),
                'Total' => money($expenses->where('status', 'approved')->sum('amount')),
            ],
        ];
    }

    protected function profitReport(Carbon $from, Carbon $to): array
    {
        $monthly = $this->reports->monthlyBreakdown($from, $to);
        $summary = $this->reports->financialSummary($from, $to);

        return [
            'headings' => ['Period', 'Revenue', 'Expenses', 'Profit'],
            'rows' => collect($monthly)->map(fn (array $row) => [
                $row['period'],
                money($row['revenue']),
                money($row['expenses']),
                money($row['profit']),
            ])->all(),
            'summary' => [
                'Revenue' => money($summary['revenue']),
                'Expenses' => money($summary['expenses']),
                'Profit' => money($summary['profit']),
                'Margin' => $summary['margin'].'%',
            ],
        ];
    }

    protected function inquiriesReport(Carbon $from, Carbon $to): array
    {
        $inquiries = WebsiteInquiry::with('assignee')->whereBetween('created_at', [$from, $to])->latest('id')->get();

        return [
            'headings' => ['Inquiry #', 'Name', 'Email', 'Service', 'Source Page', 'Status', 'Assignee', 'Received'],
            'rows' => $inquiries->map(fn (WebsiteInquiry $inquiry) => [
                $inquiry->inquiry_number,
                $inquiry->name,
                $inquiry->email,
                $inquiry->service,
                $inquiry->source_page,
                WebsiteInquiry::STATUSES[$inquiry->status] ?? $inquiry->status,
                $inquiry->assignee?->name,
                $inquiry->created_at?->format('Y-m-d H:i'),
            ])->all(),
            'summary' => [
                'Inquiries' => $inquiries->count(),
                'Converted' => $inquiries->whereNotNull('converted_lead_id')->count(),
                'Unread' => $inquiries->whereNull('read_at')->count(),
                'Spam' => $inquiries->where('status', 'spam')->count(),
            ],
        ];
    }

    protected function ticketsReport(Carbon $from, Carbon $to): array
    {
        $tickets = Ticket::with(['client', 'assignee'])->whereBetween('created_at', [$from, $to])->latest('id')->get();

        return [
            'headings' => ['Ticket #', 'Subject', 'Client', 'Category', 'Priority', 'Assignee', 'Status', 'Opened', 'Resolved'],
            'rows' => $tickets->map(fn (Ticket $ticket) => [
                $ticket->ticket_number,
                $ticket->subject,
                $ticket->client?->displayName(),
                Ticket::CATEGORIES[$ticket->category] ?? $ticket->category,
                Ticket::PRIORITIES[$ticket->priority] ?? $ticket->priority,
                $ticket->assignee?->name,
                Ticket::STATUSES[$ticket->status] ?? $ticket->status,
                $ticket->created_at?->format('Y-m-d'),
                $ticket->resolved_at?->format('Y-m-d'),
            ])->all(),
            'summary' => [
                'Tickets' => $tickets->count(),
                'Open' => $tickets->whereNotIn('status', ['resolved', 'closed'])->count(),
                'Resolved' => $tickets->whereIn('status', ['resolved', 'closed'])->count(),
            ],
        ];
    }

    protected function bugsReport(Carbon $from, Carbon $to): array
    {
        $bugs = Bug::with(['project', 'assignee'])->whereBetween('created_at', [$from, $to])->latest('id')->get();

        return [
            'headings' => ['Bug #', 'Title', 'Project', 'Severity', 'Assignee', 'Status', 'Reported', 'Resolved'],
            'rows' => $bugs->map(fn (Bug $bug) => [
                $bug->bug_number,
                $bug->title,
                $bug->project?->name,
                Bug::SEVERITIES[$bug->severity] ?? $bug->severity,
                $bug->assignee?->name,
                Bug::STATUSES[$bug->status] ?? $bug->status,
                $bug->reported_date?->format('Y-m-d'),
                $bug->resolved_date?->format('Y-m-d'),
            ])->all(),
            'summary' => [
                'Bugs' => $bugs->count(),
                'Open' => $bugs->whereNotIn('status', ['resolved', 'closed', 'wont_fix'])->count(),
                'Critical' => $bugs->whereIn('severity', ['critical', 'blocker'])->count(),
            ],
        ];
    }

    protected function renewalsReport(): array
    {
        $assets = Asset::with('client')->whereNotNull('expiry_date')->orderBy('expiry_date')->get();

        return [
            'headings' => ['Name', 'Type', 'Client', 'Provider', 'Expiry', 'Days Left', 'Cost', 'Auto Renew', 'Status'],
            'rows' => $assets->map(fn (Asset $asset) => [
                $asset->name,
                Asset::TYPES[$asset->type] ?? $asset->type,
                $asset->client?->displayName() ?? 'Agency',
                $asset->provider,
                $asset->expiry_date?->format('Y-m-d'),
                $asset->daysUntilExpiry(),
                money($asset->cost, $asset->currency),
                $asset->auto_renew ? 'Yes' : 'No',
                Asset::STATUSES[$asset->status] ?? $asset->status,
            ])->all(),
            'summary' => [
                'Tracked assets' => $assets->count(),
                'Expiring in 30 days' => $assets->filter(fn (Asset $asset) => ! $asset->isExpired() && $asset->daysUntilExpiry() <= 30)->count(),
                'Expired' => $assets->filter(fn (Asset $asset) => $asset->isExpired())->count(),
                'Annual cost' => money($assets->where('status', 'active')->sum('cost')),
            ],
        ];
    }

    /**
     * @param  array<int, array<int, mixed>>  $rows
     * @param  array<int, string>  $headings
     */
    protected function export(string $report, array $rows, array $headings): StreamedResponse
    {
        return response()->streamDownload(function () use ($rows, $headings) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, $headings);

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $report.'-report-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv']);
    }

    /** @return array<string, array{label: string, description: string, icon: string}> */
    protected function catalogue(): array
    {
        return [
            'leads' => ['label' => 'Leads', 'description' => 'Every lead created in the period.', 'icon' => 'fa-bullseye'],
            'lead-conversion' => ['label' => 'Lead Conversion', 'description' => 'Pipeline stages and conversion rate.', 'icon' => 'fa-filter'],
            'clients' => ['label' => 'Clients', 'description' => 'Client list with billing totals.', 'icon' => 'fa-user-tie'],
            'projects' => ['label' => 'Projects', 'description' => 'Project status, budget and progress.', 'icon' => 'fa-diagram-project'],
            'tasks' => ['label' => 'Tasks', 'description' => 'Task delivery and effort.', 'icon' => 'fa-list-check'],
            'employee-workload' => ['label' => 'Employee Workload', 'description' => 'Open work per team member.', 'icon' => 'fa-users'],
            'quotations' => ['label' => 'Quotations', 'description' => 'Quoted value and outcomes.', 'icon' => 'fa-file-invoice'],
            'sales' => ['label' => 'Sales', 'description' => 'Deals won and their value.', 'icon' => 'fa-trophy'],
            'invoices' => ['label' => 'Invoices', 'description' => 'Invoiced, received and outstanding.', 'icon' => 'fa-file-invoice-dollar'],
            'payments' => ['label' => 'Payments', 'description' => 'Money received by method.', 'icon' => 'fa-money-bill-transfer'],
            'expenses' => ['label' => 'Expenses', 'description' => 'Spending by category and project.', 'icon' => 'fa-receipt'],
            'profit' => ['label' => 'Revenue & Profit', 'description' => 'Revenue against expenses by month.', 'icon' => 'fa-chart-line'],
            'website-inquiries' => ['label' => 'Website Inquiries', 'description' => 'Enquiries received and converted.', 'icon' => 'fa-inbox'],
            'support-tickets' => ['label' => 'Support Tickets', 'description' => 'Ticket volume and resolution.', 'icon' => 'fa-headset'],
            'bugs' => ['label' => 'Bugs', 'description' => 'Defects raised and resolved.', 'icon' => 'fa-bug'],
            'renewals' => ['label' => 'Renewals', 'description' => 'Domains, hosting and licences expiring.', 'icon' => 'fa-server'],
        ];
    }
}
