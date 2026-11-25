<?php

namespace App\Http\Controllers\Admin;

use App\Models\Campaign;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class CampaignController extends AdminResourceController
{
    protected string $model = Campaign::class;

    protected string $route = 'campaigns';

    protected string $permission = 'campaigns';

    protected string $singular = 'Campaign';

    protected string $plural = 'Campaigns';

    protected string $description = 'Paid and organic campaigns with budget and results.';

    protected string $icon = 'fa-bullhorn';

    protected array $searchable = ['name', 'objective', 'results'];

    protected array $with = ['client', 'project', 'manager'];

    protected function columns(): array
    {
        return [
            Column::text('name', 'Campaign'),
            Column::relation('client.company_name', 'Client'),
            Column::badge('platform', 'Platform', Campaign::PLATFORMS),
            Column::money('budget', 'Budget'),
            Column::money('spent', 'Spent'),
            Column::custom('ctr', 'CTR', fn (Campaign $campaign) => $campaign->clickThroughRate() !== null
                ? $campaign->clickThroughRate().'%'
                : '<span class="admin-cell-muted">—</span>'),
            Column::date('end_date', 'Ends'),
            Column::badge('status', 'Status', Campaign::STATUSES),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::heading('Campaign'),
            Field::text('name', 'Campaign Name', ['required' => true]),
            Field::select('client_id', 'Client', $this->options->clients()),
            Field::select('project_id', 'Project', $this->options->projects()),
            Field::select('platform', 'Platform', Campaign::PLATFORMS, ['required' => true]),
            Field::select('objective', 'Objective', Campaign::OBJECTIVES),
            Field::select('managed_by', 'Managed By', $this->options->employees()),
            Field::select('status', 'Status', Campaign::STATUSES, ['required' => true]),

            Field::heading('Budget & Dates'),
            Field::money('budget', 'Budget'),
            Field::money('spent', 'Spent to Date'),
            Field::select('currency', 'Currency', $this->options->currencies()),
            Field::date('start_date', 'Start Date'),
            Field::date('end_date', 'End Date'),

            Field::heading('Results'),
            Field::number('impressions', 'Impressions'),
            Field::number('clicks', 'Clicks'),
            Field::number('conversions', 'Conversions'),
            Field::textarea('results', 'Results Summary'),
            Field::textarea('notes', 'Notes'),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'platform', 'label' => 'Platform', 'options' => Campaign::PLATFORMS],
            ['key' => 'status', 'label' => 'Status', 'options' => Campaign::STATUSES],
            ['key' => 'client_id', 'label' => 'Client', 'options' => $this->options->clients()],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'platform' => ['required', Rule::in(array_keys(Campaign::PLATFORMS))],
            'objective' => ['nullable', Rule::in(array_keys(Campaign::OBJECTIVES))],
            'managed_by' => ['nullable', 'exists:employees,id'],
            'status' => ['required', Rule::in(array_keys(Campaign::STATUSES))],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'spent' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'impressions' => ['nullable', 'integer', 'min:0'],
            'clicks' => ['nullable', 'integer', 'min:0'],
            'conversions' => ['nullable', 'integer', 'min:0'],
            'results' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
