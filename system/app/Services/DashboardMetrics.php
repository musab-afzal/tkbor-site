<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\FollowUp;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectMilestone;
use App\Models\Quotation;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\WebsiteInquiry;
use Illuminate\Support\Collection;

/**
 * Every dashboard figure is computed here from live records — nothing on the
 * dashboard is hard-coded or cached stale.
 */
class DashboardMetrics
{
    /** @return array<string, int|float> */
    public function kpis(): array
    {
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        return [
            'total_leads' => Lead::count(),
            'new_leads_today' => Lead::whereDate('created_at', today())->count(),
            'total_clients' => Client::count(),
            'active_clients' => Client::where('status', true)->count(),
            'active_projects' => Project::active()->count(),
            'completed_projects' => Project::where('status', Project::STATUS_COMPLETED)->count(),
            'pending_quotations' => Quotation::pending()->count(),
            'approved_quotations' => Quotation::where('status', Quotation::STATUS_APPROVED)->count(),
            'pending_payments' => (float) Invoice::unpaid()->sum('balance'),
            'monthly_revenue' => (float) Payment::where('status', Payment::STATUS_COMPLETED)
                ->whereBetween('payment_date', [$monthStart, $monthEnd])
                ->sum('amount'),
            'monthly_expenses' => (float) Expense::where('status', 'approved')
                ->whereBetween('expense_date', [$monthStart, $monthEnd])
                ->sum('amount'),
            'tasks_due_today' => Task::dueToday()->count(),
            'upcoming_deadlines' => Project::active()
                ->whereNotNull('deadline')
                ->whereBetween('deadline', [today(), today()->addDays(14)])
                ->count(),
            'overdue_tasks' => Task::overdue()->count(),
            'open_tickets' => Ticket::open()->count(),
            'website_inquiries' => WebsiteInquiry::whereNotIn('status', [WebsiteInquiry::STATUS_SPAM])->count(),
            'new_inquiries' => WebsiteInquiry::unread()->count(),
            'expiring_renewals' => Asset::expiringWithin(30)->count(),
            'overdue_invoices' => Invoice::unpaid()->whereDate('due_date', '<', today())->count(),
            'overdue_follow_ups' => FollowUp::overdue()->count(),
        ];
    }

    /** @return array<string, Collection<int, mixed>> */
    public function panels(): array
    {
        return [
            'recentLeads' => Lead::with('assignee')->latest('id')->take(6)->get(),
            'recentQuotations' => Quotation::with('client')->latest('id')->take(6)->get(),
            'recentPayments' => Payment::with(['client', 'invoice'])->latest('payment_date')->take(6)->get(),
            'recentInquiries' => WebsiteInquiry::latest('id')->take(6)->get(),
            'tasksDueToday' => Task::with(['project', 'assignees'])->dueToday()->orderBy('due_time')->take(8)->get(),
            'upcomingTasks' => Task::with(['project', 'assignees'])->upcoming(7)->orderBy('due_date')->take(8)->get(),
            'overdueTasks' => Task::with(['project', 'assignees'])->overdue()->orderBy('due_date')->take(8)->get(),
            'projectDeadlines' => Project::with('client')->active()
                ->whereNotNull('deadline')
                ->orderBy('deadline')
                ->take(8)
                ->get(),
            'upcomingMilestones' => ProjectMilestone::with('project')
                ->where('status', '!=', ProjectMilestone::STATUS_COMPLETED)
                ->whereNotNull('due_date')
                ->orderBy('due_date')
                ->take(6)
                ->get(),
            'dueFollowUps' => FollowUp::with(['lead', 'client', 'assignee'])
                ->pending()
                ->where('scheduled_at', '<=', now()->addDay())
                ->orderBy('scheduled_at')
                ->take(8)
                ->get(),
            'expiringAssets' => Asset::with('client')
                ->where('status', 'active')
                ->whereNotNull('expiry_date')
                ->whereDate('expiry_date', '<=', today()->addDays(45))
                ->orderBy('expiry_date')
                ->take(8)
                ->get(),
            'openTickets' => Ticket::with(['client', 'assignee'])->open()->latest('id')->take(6)->get(),
        ];
    }

    /**
     * Open task and project counts per employee, heaviest load first.
     *
     * @return Collection<int, Employee>
     */
    public function employeeWorkload(int $limit = 8): Collection
    {
        return Employee::active()
            ->withCount([
                'tasks as open_tasks_count' => fn ($query) => $query->where('tasks.status', '!=', Task::STATUS_COMPLETED),
                'tasks as overdue_tasks_count' => fn ($query) => $query->where('tasks.status', '!=', Task::STATUS_COMPLETED)
                    ->whereDate('tasks.due_date', '<', today()),
                'projects as active_projects_count' => fn ($query) => $query->whereIn('projects.status', ['planning', 'active', 'on_hold', 'testing']),
            ])
            ->orderByDesc('open_tasks_count')
            ->take($limit)
            ->get();
    }

    /**
     * Revenue and expense totals for the last N months, oldest first.
     *
     * @return array<int, array{label: string, revenue: float, expenses: float, profit: float}>
     */
    public function monthlySummary(int $months = 6): array
    {
        $summary = [];

        for ($offset = $months - 1; $offset >= 0; $offset--) {
            $start = now()->copy()->subMonths($offset)->startOfMonth();
            $end = $start->copy()->endOfMonth();

            $revenue = (float) Payment::where('status', Payment::STATUS_COMPLETED)
                ->whereBetween('payment_date', [$start, $end])
                ->sum('amount');

            $expenses = (float) Expense::where('status', 'approved')
                ->whereBetween('expense_date', [$start, $end])
                ->sum('amount');

            $summary[] = [
                'label' => $start->format('M Y'),
                'revenue' => $revenue,
                'expenses' => $expenses,
                'profit' => round($revenue - $expenses, 2),
            ];
        }

        return $summary;
    }
}
