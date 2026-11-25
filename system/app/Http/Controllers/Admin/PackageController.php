<?php

namespace App\Http\Controllers\Admin;

use App\Models\Package;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Package pricing. These records drive the public packages page, so editing one
 * here changes what visitors see.
 */
class PackageController extends AdminResourceController
{
    protected string $model = Package::class;

    protected string $route = 'packages';

    protected string $permission = 'packages';

    protected string $singular = 'Package';

    protected string $plural = 'Packages';

    protected string $description = 'Tiered packages shown on the public packages page.';

    protected string $icon = 'fa-box-open';

    protected array $searchable = ['name', 'short_description'];

    protected array $with = ['service'];

    protected string $defaultSort = 'sort_order';

    protected string $defaultDirection = 'asc';

    protected function columns(): array
    {
        return [
            Column::text('name', 'Package'),
            Column::relation('service.title', 'Service'),
            Column::badge('tier', 'Tier', Package::TIERS),
            Column::money('price', 'Price'),
            Column::money('discount_price', 'Discounted'),
            Column::number('delivery_days', 'Delivery Days'),
            Column::boolean('is_recommended', 'Recommended'),
            Column::boolean('status', 'Status', ['true' => 'Active', 'false' => 'Inactive']),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::heading('Package'),
            Field::text('name', 'Package Name', ['required' => true]),
            Field::select('tier', 'Tier', Package::TIERS, ['required' => true]),
            Field::select('service_id', 'Linked Service', $this->options->services()),
            Field::text('short_description', 'Short Description', ['full' => true]),

            Field::heading('Pricing'),
            Field::money('price', 'Price', ['required' => true]),
            Field::money('discount_price', 'Discounted Price', [
                'help' => 'Leave blank when there is no offer. Must be below the price.',
            ]),
            Field::select('currency', 'Currency', $this->options->currencies()),
            Field::text('billing_period', 'Billing Period', ['placeholder' => 'one-time / month / year']),
            Field::number('delivery_days', 'Delivery Days'),
            Field::number('revisions', 'Revisions Included'),

            Field::heading('Presentation'),
            Field::textarea('features', 'Features', [
                'rows' => 8,
                'help' => 'One feature per line. Each line becomes a bullet on the website.',
                'value' => implode("\n", (array) ($this->currentRecord?->features ?? [])),
            ]),
            Field::checkbox('is_recommended', 'Show "Recommended" badge'),
            Field::checkbox('status', 'Active', ['help' => 'Visible on the public packages page.']),
            Field::number('sort_order', 'Sort Order'),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'tier', 'label' => 'Tier', 'options' => Package::TIERS],
            ['key' => 'service_id', 'label' => 'Service', 'options' => $this->options->services()],
            ['key' => 'status', 'label' => 'Status', 'type' => 'boolean', 'options' => ['1' => 'Active', '0' => 'Inactive']],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'tier' => ['required', Rule::in(array_keys(Package::TIERS))],
            'service_id' => ['nullable', 'exists:services,id'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'currency' => ['nullable', 'string', 'size:3'],
            'billing_period' => ['nullable', 'string', 'max:50'],
            'delivery_days' => ['nullable', 'integer', 'min:0', 'max:3650'],
            'revisions' => ['nullable', 'integer', 'min:0', 'max:255'],
            'features' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_recommended' => ['boolean'],
            'status' => ['boolean'],
        ];
    }

    /** Features are entered one per line and stored as a JSON array. */
    protected function mutateData(array $data, Request $request, Model $record): array
    {
        $data['features'] = collect(preg_split('/\r\n|\r|\n/', (string) ($data['features'] ?? '')))
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->values()
            ->all() ?: null;

        return $data;
    }
}
