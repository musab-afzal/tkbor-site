<?php

namespace App\Support;

use App\Models\Client;
use App\Models\Department;
use App\Models\Employee;
use App\Models\ExpenseCategory;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\LeaveType;
use App\Models\PricingCategory;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\SeoProject;
use App\Models\Service;

/**
 * Dropdown option lists shared by every form. Each list is resolved once per
 * request so a form with several selects does not re-query the same table.
 */
class Options
{
    /** @var array<string, array<int|string, string>> */
    protected array $cache = [];

    /** @return array<int, string> */
    public function employees(): array
    {
        return $this->remember('employees', fn () => Employee::active()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all());
    }

    /** @return array<int, string> */
    public function clients(): array
    {
        return $this->remember('clients', fn () => Client::query()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Client $client) => [$client->id => $client->displayName()])
            ->all());
    }

    /** @return array<int, string> */
    public function leads(): array
    {
        return $this->remember('leads', fn () => Lead::query()
            ->orderByDesc('id')
            ->limit(500)
            ->get()
            ->mapWithKeys(fn (Lead $lead) => [$lead->id => $lead->lead_number.' - '.$lead->name])
            ->all());
    }

    /** @return array<int, string> */
    public function projects(): array
    {
        return $this->remember('projects', fn () => Project::query()
            ->orderByDesc('id')
            ->get()
            ->mapWithKeys(fn (Project $project) => [$project->id => $project->project_number.' - '.$project->name])
            ->all());
    }

    /** @return array<int, string> */
    public function quotations(): array
    {
        return $this->remember('quotations', fn () => Quotation::query()
            ->orderByDesc('id')
            ->get()
            ->mapWithKeys(fn (Quotation $quotation) => [$quotation->id => $quotation->quotation_number.' - '.$quotation->project_name])
            ->all());
    }

    /** @return array<int, string> */
    public function invoices(): array
    {
        return $this->remember('invoices', fn () => Invoice::query()
            ->orderByDesc('id')
            ->get()
            ->mapWithKeys(fn (Invoice $invoice) => [$invoice->id => $invoice->invoice_number.' - '.money($invoice->total, $invoice->currency)])
            ->all());
    }

    /** @return array<int, string> */
    public function departments(): array
    {
        return $this->remember('departments', fn () => Department::orderBy('name')->pluck('name', 'id')->all());
    }

    /** @return array<int, string> */
    public function services(): array
    {
        return $this->remember('services', fn () => Service::orderBy('title')->pluck('title', 'id')->all());
    }

    /** @return array<int, string> */
    public function pricingCategories(): array
    {
        return $this->remember('pricing_categories', fn () => PricingCategory::orderBy('name')->pluck('name', 'id')->all());
    }

    /** @return array<int, string> */
    public function expenseCategories(): array
    {
        return $this->remember('expense_categories', fn () => ExpenseCategory::orderBy('name')->pluck('name', 'id')->all());
    }

    /** @return array<int, string> */
    public function leaveTypes(): array
    {
        return $this->remember('leave_types', fn () => LeaveType::orderBy('name')->pluck('name', 'id')->all());
    }

    /** @return array<int, string> */
    public function seoProjects(): array
    {
        return $this->remember('seo_projects', fn () => SeoProject::orderBy('name')->pluck('name', 'id')->all());
    }

    /** ISO currency codes the agency is likely to quote in. */
    public function currencies(): array
    {
        return [
            'USD' => 'USD - US Dollar',
            'EUR' => 'EUR - Euro',
            'GBP' => 'GBP - British Pound',
            'PKR' => 'PKR - Pakistani Rupee',
            'AED' => 'AED - UAE Dirham',
            'SAR' => 'SAR - Saudi Riyal',
            'INR' => 'INR - Indian Rupee',
            'AUD' => 'AUD - Australian Dollar',
            'CAD' => 'CAD - Canadian Dollar',
        ];
    }

    protected function remember(string $key, callable $resolver): array
    {
        return $this->cache[$key] ??= $resolver();
    }
}
