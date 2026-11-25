<?php

namespace App\Http\Controllers\Admin;

use App\Models\PricingItem;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

/**
 * The reusable price book. Quotation and invoice lines are populated from these
 * records but keep their own editable copy of the numbers.
 */
class PricingItemController extends AdminResourceController
{
    protected string $model = PricingItem::class;

    protected string $route = 'pricing';

    protected string $permission = 'pricing';

    protected string $singular = 'Pricing Item';

    protected string $plural = 'Service Pricing';

    protected string $description = 'Standard prices for the modules and services you quote.';

    protected string $icon = 'fa-tags';

    protected array $searchable = ['name', 'short_description', 'detailed_features'];

    protected array $with = ['category', 'service'];

    protected string $defaultSort = 'sort_order';

    protected string $defaultDirection = 'asc';

    protected function columns(): array
    {
        $columns = [
            Column::text('name', 'Service / Module'),
            Column::relation('category.name', 'Category'),
            Column::money('default_price', 'Default Price'),
            Column::custom('range', 'Range', function (PricingItem $item) {
                if ($item->min_price === null && $item->max_price === null) {
                    return '<span class="admin-cell-muted">—</span>';
                }

                return e(money($item->min_price ?? 0).' – '.money($item->max_price ?? $item->default_price));
            }),
            Column::badge('unit', 'Unit', PricingItem::UNITS),
            Column::number('estimated_days', 'Days'),
        ];

        if (auth('admin')->user()?->hasPermission('pricing.view_cost')) {
            $columns[] = Column::money('internal_cost', 'Internal Cost');
        }

        $columns[] = Column::boolean('status', 'Status', ['true' => 'Active', 'false' => 'Inactive']);

        return $columns;
    }

    protected function fields(): array
    {
        $fields = [
            Field::heading('Item'),
            Field::text('name', 'Service / Module Name', ['required' => true]),
            Field::select('pricing_category_id', 'Category', $this->options->pricingCategories()),
            Field::select('service_id', 'Linked Website Service', $this->options->services(), [
                'help' => 'Optional — links this price to a service already shown on the site.',
            ]),
            Field::text('short_description', 'Short Description', ['full' => true]),
            Field::textarea('detailed_features', 'Detailed Features', [
                'rows' => 6,
                'help' => 'One feature per line. Shown on the quotation.',
            ]),

            Field::heading('Pricing'),
            Field::money('default_price', 'Default Price', ['required' => true]),
            Field::money('min_price', 'Minimum Price'),
            Field::money('max_price', 'Maximum Price'),
            Field::select('unit', 'Unit', PricingItem::UNITS, ['required' => true]),
            Field::number('estimated_days', 'Estimated Duration (days)'),
            Field::list('technologies', 'Technologies', ['placeholder' => 'Laravel, Vue, MySQL']),

            Field::heading('Options'),
            Field::checkbox('is_taxable', 'Taxable', ['help' => 'Tax applies to this item.']),
            Field::checkbox('discount_allowed', 'Discount Allowed', ['help' => 'Discounts may be applied.']),
            Field::checkbox('is_optional', 'Optional by Default', ['help' => 'Added to quotations as an optional extra.']),
            Field::checkbox('is_free', 'Free Service', ['help' => 'Offered free — value is shown but not charged.']),
            Field::checkbox('status', 'Active', ['help' => 'Available when building a quotation.']),
            Field::list('addons', 'Optional Add-ons', ['placeholder' => 'Extra language, Priority support']),
            Field::number('sort_order', 'Sort Order'),
        ];

        if (auth('admin')->user()?->hasPermission('pricing.view_cost')) {
            $fields[] = Field::heading('Internal');
            $fields[] = Field::money('internal_cost', 'Internal Cost', [
                'help' => 'Never shown on client-facing documents.',
            ]);
            $fields[] = Field::textarea('notes', 'Internal Notes');
        }

        return $fields;
    }

    protected function filters(): array
    {
        return [
            ['key' => 'pricing_category_id', 'label' => 'Category', 'options' => $this->options->pricingCategories()],
            ['key' => 'unit', 'label' => 'Unit', 'options' => PricingItem::UNITS],
            ['key' => 'status', 'label' => 'Status', 'type' => 'boolean', 'options' => ['1' => 'Active', '0' => 'Inactive']],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'pricing_category_id' => ['nullable', 'exists:pricing_categories,id'],
            'service_id' => ['nullable', 'exists:services,id'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'detailed_features' => ['nullable', 'string'],
            'default_price' => ['required', 'numeric', 'min:0'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'gte:min_price'],
            'internal_cost' => ['nullable', 'numeric', 'min:0'],
            'unit' => ['required', Rule::in(array_keys(PricingItem::UNITS))],
            'estimated_days' => ['nullable', 'integer', 'min:0', 'max:3650'],
            'technologies' => ['nullable', 'string', 'max:1000'],
            'addons' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_taxable' => ['boolean'],
            'discount_allowed' => ['boolean'],
            'is_optional' => ['boolean'],
            'is_free' => ['boolean'],
            'status' => ['boolean'],
        ];
    }
}
