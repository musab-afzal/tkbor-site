<?php

namespace App\Http\Controllers\Admin;

use App\Models\PricingCategory;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;

class PricingCategoryController extends AdminResourceController
{
    protected string $model = PricingCategory::class;

    protected string $route = 'pricing-categories';

    protected string $permission = 'pricing_categories';

    protected string $singular = 'Pricing Category';

    protected string $plural = 'Pricing Categories';

    protected string $description = 'Groups for the service price book.';

    protected string $icon = 'fa-layer-group';

    protected array $searchable = ['name'];

    protected array $withCount = ['items'];

    protected string $defaultSort = 'sort_order';

    protected string $defaultDirection = 'asc';

    protected function columns(): array
    {
        return [
            Column::text('name', 'Name'),
            Column::text('description', 'Description', ['muted' => true]),
            Column::number('items_count', 'Pricing Items'),
            Column::number('sort_order', 'Order'),
            Column::boolean('status', 'Status', ['true' => 'Active', 'false' => 'Inactive']),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::text('name', 'Name', ['required' => true]),
            Field::number('sort_order', 'Sort Order'),
            Field::text('description', 'Description', ['full' => true]),
            Field::checkbox('status', 'Active'),
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['boolean'],
        ];
    }
}
