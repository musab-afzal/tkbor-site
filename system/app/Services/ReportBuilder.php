<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Aggregations shared by the financial reports screen and the reports hub.
 * Everything is derived from live records for the requested date window.
 */
class ReportBuilder
{
    /** @return array{from: Carbon, to: Carbon} */
    public function window(?string $from, ?string $to): array
    {
        return [
            'from' => $from ? Carbon::parse($from)->startOfDay() : now()->startOfYear(),
            'to' => $to ? Carbon::parse($to)->endOfDay() : now()->endOfDay(),
        ];
    }

    /** @return array<string, float> */
    public function financialSummary(Carbon $from, Carbon $to): array
    {
        $revenue = (float) Payment::where('status', Payment::STATUS_COMPLETED)
            ->whereBetween('payment_date', [$from, $to])
            ->sum('amount');

        $expenses = (float) Expense::where('status', 'approved')
            ->whereBetween('expense_date', [$from, $to])
            ->sum('amount');

        $invoiced = (float) Invoice::whereNotIn('status', ['draft', 'cancelled'])
            ->whereBetween('invoice_date', [$from, $to])
            ->sum('total');

        return [
            'revenue' => round($revenue, 2),
            'expenses' => round($expenses, 2),
            'profit' => round($revenue - $expenses, 2),
            'margin' => $revenue > 0 ? round(($revenue - $expenses) / $revenue * 100, 1) : 0.0,
            'invoiced' => round($invoiced, 2),
            'outstanding' => round((float) Invoice::unpaid()->sum('balance'), 2),
            'overdue' => round((float) Invoice::unpaid()->whereDate('due_date', '<', today())->sum('balance'), 2),
        ];
    }

    /**
     * Revenue, expenses and profit grouped by calendar month in the window.
     *
     * @return array<int, array<string, mixed>>
     */
    public function monthlyBreakdown(Carbon $from, Carbon $to): array
    {
        $revenue = Payment::selectRaw("DATE_FORMAT(payment_date, '%Y-%m') as period, SUM(amount) as total")
            ->where('status', Payment::STATUS_COMPLETED)
            ->whereBetween('payment_date', [$from, $to])
            ->groupBy('period')
            ->pluck('total', 'period');

        $expenses = Expense::selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as period, SUM(amount) as total")
            ->where('status', 'approved')
            ->whereBetween('expense_date', [$from, $to])
            ->groupBy('period')
            ->pluck('total', 'period');

        $periods = $revenue->keys()->merge($expenses->keys())->unique()->sort()->values();

        return $periods->map(function (string $period) use ($revenue, $expenses) {
            $in = (float) ($revenue[$period] ?? 0);
            $out = (float) ($expenses[$period] ?? 0);

            return [
                'period' => Carbon::createFromFormat('Y-m', $period)->format('M Y'),
                'revenue' => $in,
                'expenses' => $out,
                'profit' => round($in - $out, 2),
            ];
        })->all();
    }

    /**
     * Money in and out per client for the window.
     *
     * @return Collection<int, Client>
     */
    public function clientProfitability(Carbon $from, Carbon $to): Collection
    {
        return Client::query()
            ->withSum(
                ['payments as received' => fn ($query) => $query->where('status', Payment::STATUS_COMPLETED)
                    ->whereBetween('payment_date', [$from, $to])],
                'amount'
            )
            ->withSum(
                ['invoices as invoiced' => fn ($query) => $query->whereNotIn('status', ['draft', 'cancelled'])
                    ->whereBetween('invoice_date', [$from, $to])],
                'total'
            )
            ->having('received', '>', 0)
            ->orHaving('invoiced', '>', 0)
            ->orderByDesc('received')
            ->limit(25)
            ->get();
    }

    /**
     * Budget against real cost per project.
     *
     * @return Collection<int, Project>
     */
    public function projectProfitability(): Collection
    {
        return Project::query()
            ->with('client')
            ->withSum(['invoices as invoiced' => fn ($query) => $query->whereNotIn('status', ['draft', 'cancelled'])], 'total')
            ->withSum(['invoices as received' => fn ($query) => $query->whereNotIn('status', ['draft', 'cancelled'])], 'paid_amount')
            ->withSum(['expenses as spent' => fn ($query) => $query->where('status', 'approved')], 'amount')
            ->withSum('timesheets as logged_hours', 'hours')
            ->orderByDesc('id')
            ->limit(50)
            ->get();
    }
}
