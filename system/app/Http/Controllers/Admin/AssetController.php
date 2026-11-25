<?php

namespace App\Http\Controllers\Admin;

use App\Models\Asset;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Domains, hosting, SSL, licences and subscriptions share one renewal shape, so
 * they share one screen filtered by type.
 */
class AssetController extends AdminResourceController
{
    protected string $model = Asset::class;

    protected string $route = 'assets';

    protected string $permission = 'assets';

    protected string $singular = 'Asset';

    protected string $plural = 'Assets & Renewals';

    protected string $description = 'Domains, hosting, SSL, licences and subscriptions with their renewal dates.';

    protected string $icon = 'fa-server';

    protected array $searchable = ['name', 'provider', 'account_reference'];

    protected array $with = ['client'];

    protected string $defaultSort = 'expiry_date';

    protected string $defaultDirection = 'asc';

    protected function columns(): array
    {
        return [
            Column::text('name', 'Name'),
            Column::badge('type', 'Type', Asset::TYPES),
            Column::relation('client.company_name', 'Client'),
            Column::text('provider', 'Provider'),
            Column::custom('expiry', 'Expiry', function (Asset $asset) {
                if (! $asset->expiry_date) {
                    return '<span class="admin-cell-muted">—</span>';
                }

                $label = e($asset->expiry_date->format('M d, Y'));

                if ($asset->isExpired()) {
                    return '<span class="admin-badge admin-badge--expired">'.$label.'</span>';
                }

                return $asset->isExpiringSoon()
                    ? '<span class="admin-badge admin-badge--warning">'.$label.'</span>'
                    : $label;
            }),
            Column::money('cost', 'Cost'),
            Column::badge('billing_cycle', 'Cycle', Asset::BILLING_CYCLES),
            Column::boolean('auto_renew', 'Auto Renew'),
            Column::badge('status', 'Status', Asset::STATUSES),
        ];
    }

    protected function fields(): array
    {
        $fields = [
            Field::heading('Asset'),
            Field::text('name', 'Name', ['required' => true, 'placeholder' => 'example.com']),
            Field::select('type', 'Type', Asset::TYPES, ['required' => true]),
            Field::select('client_id', 'Client / Company', $this->options->clients(), [
                'help' => 'Leave blank for an asset the agency owns itself.',
            ]),
            Field::select('project_id', 'Project', $this->options->projects()),
            Field::text('provider', 'Provider', ['placeholder' => 'Namecheap, AWS, Adobe...']),
            Field::url('provider_url', 'Provider URL'),
            Field::text('account_reference', 'Account Reference', ['help' => 'Account or order number with the provider.']),

            Field::heading('Renewal'),
            Field::date('purchase_date', 'Purchase Date'),
            Field::date('start_date', 'Start Date'),
            Field::date('expiry_date', 'Expiry / Renewal Date'),
            Field::money('cost', 'Cost'),
            Field::select('currency', 'Currency', $this->options->currencies()),
            Field::select('billing_cycle', 'Billing Cycle', Asset::BILLING_CYCLES, ['required' => true]),
            Field::number('reminder_days', 'Remind me (days before)'),
            Field::select('status', 'Status', Asset::STATUSES, ['required' => true]),
            Field::checkbox('auto_renew', 'Auto renews'),
        ];

        $fields[] = Field::heading('Notes');

        if (auth('admin')->user()?->hasPermission('assets.view_credentials')) {
            $fields[] = Field::textarea('credentials_note', 'Credentials Reference', [
                'help' => 'Where the credentials are stored — never paste passwords here.',
            ]);
        }

        $fields[] = Field::textarea('notes', 'Notes');

        return $fields;
    }

    protected function filters(): array
    {
        return [
            ['key' => 'type', 'label' => 'Type', 'options' => Asset::TYPES],
            ['key' => 'client_id', 'label' => 'Client', 'options' => $this->options->clients()],
            ['key' => 'status', 'label' => 'Status', 'options' => Asset::STATUSES],
            ['key' => 'expiring', 'label' => 'Renewal', 'options' => [
                'soon' => 'Expiring in 30 days',
                'overdue' => 'Already expired',
            ]],
        ];
    }

    protected function scopeQuery(Builder $query, Request $request): Builder
    {
        return match ($request->string('expiring')->toString()) {
            'soon' => $query->expiringWithin(30),
            'overdue' => $query->overdue(),
            default => $query,
        };
    }

    protected function rules(?Model $record = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(array_keys(Asset::TYPES))],
            'client_id' => ['nullable', 'exists:clients,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'provider' => ['nullable', 'string', 'max:150'],
            'provider_url' => ['nullable', 'url', 'max:255'],
            'account_reference' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['nullable', 'date'],
            'start_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'billing_cycle' => ['required', Rule::in(array_keys(Asset::BILLING_CYCLES))],
            'reminder_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'status' => ['required', Rule::in(array_keys(Asset::STATUSES))],
            'auto_renew' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ];

        if (auth('admin')->user()?->hasPermission('assets.view_credentials')) {
            $rules['credentials_note'] = ['nullable', 'string', 'max:2000'];
        }

        return $rules;
    }

    /** Type-scoped listing used by the Domains / Hosting / SSL sidebar links. */
    public function ofType(Request $request, string $type): View
    {
        $this->authorizeAction('view');

        abort_unless(array_key_exists($type, Asset::TYPES), 404);

        $request->merge(['type' => $type]);

        $records = $this->applyFilters($this->baseQuery(), $request)
            ->orderBy('expiry_date')
            ->paginate($this->perPage)
            ->withQueryString();

        return view($this->indexView, array_merge($this->sharedViewData(), [
            'records' => $records,
            'columns' => $this->columns(),
            'filterControls' => $this->filters(),
            'plural' => Asset::TYPES[$type].'s',
            'moduleDescription' => 'All '.strtolower(Asset::TYPES[$type]).' records and their renewal dates.',
        ]));
    }

    /** Pushes the expiry date forward by one billing cycle. */
    public function renew(Asset $asset): RedirectResponse
    {
        $this->authorizeAction('edit');

        if (! $asset->expiry_date) {
            return back()->with('error', 'Set an expiry date before renewing this asset.');
        }

        $next = match ($asset->billing_cycle) {
            'monthly' => $asset->expiry_date->copy()->addMonth(),
            'quarterly' => $asset->expiry_date->copy()->addMonths(3),
            'half_yearly' => $asset->expiry_date->copy()->addMonths(6),
            'biennial' => $asset->expiry_date->copy()->addYears(2),
            'one_time' => $asset->expiry_date,
            default => $asset->expiry_date->copy()->addYear(),
        };

        $asset->update(['expiry_date' => $next, 'status' => 'active']);

        return back()->with('success', 'Renewed until '.$next->format('M d, Y').'.');
    }
}
