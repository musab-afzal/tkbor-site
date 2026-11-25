<?php

namespace App\Http\Controllers\Admin;

use App\Models\SaasProduct;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Software offered on the public SaaS page, each with its own free trial. */
class SaasProductController extends AdminResourceController
{
    protected string $model = SaasProduct::class;

    protected string $route = 'saas-products';

    protected string $permission = 'saas';

    protected string $singular = 'SaaS Product';

    protected string $plural = 'SaaS Products';

    protected string $description = 'Software customers can trial and subscribe to from the public SaaS page.';

    protected string $icon = 'fa-cloud';

    protected string $uploadDirectory = 'saas';

    protected array $searchable = ['name', 'short_description'];

    protected array $withCount = ['subscriptions'];

    protected string $defaultSort = 'sort_order';

    protected string $defaultDirection = 'asc';

    protected function columns(): array
    {
        return [
            Column::image('image', 'Logo'),
            Column::text('name', 'Name'),
            Column::money('price', 'Price'),
            Column::text('billing_period', 'Per', ['muted' => true]),
            Column::number('trial_days', 'Trial Days'),
            Column::number('subscriptions_count', 'Customers'),
            Column::boolean('status', 'Status', ['true' => 'Published', 'false' => 'Hidden']),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::heading('Software'),
            Field::text('name', 'Name', ['required' => true]),
            Field::text('slug', 'URL Slug', ['help' => 'Leave blank to generate from the name.']),
            Field::url('app_url', 'Software Link', ['help' => 'Default login page customers are sent to. You can override it per customer on the subscription.']),
            Field::file('image', 'Logo / Screenshot', ['accept' => 'image/*']),
            Field::text('short_description', 'Short Description', ['full' => true]),
            Field::textarea('description', 'Full Description', ['rows' => 5]),
            Field::list('features', 'Features', ['help' => 'Comma separated, e.g. Unlimited users, Cloud backup, Email support']),

            Field::heading('Pricing & Trial'),
            Field::money('price', 'Price', ['required' => true]),
            Field::select('currency', 'Currency', $this->options->currencies(), ['placeholder' => 'Agency default']),
            Field::select('billing_period', 'Billing Period', ['month' => 'Monthly', 'year' => 'Yearly'], ['required' => true]),
            Field::number('billing_days', 'Days Added per Payment', ['help' => 'How long one approved payment extends access, e.g. 30 or 365.']),
            Field::number('trial_days', 'Free Trial Days', ['help' => 'Defaults to 7.']),

            Field::heading('Display'),
            Field::number('sort_order', 'Sort Order'),
            Field::checkbox('status', 'Published', ['help' => 'Visible on the public SaaS page.']),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'status', 'label' => 'Status', 'type' => 'boolean', 'options' => ['1' => 'Published', '0' => 'Hidden']],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'alpha_dash', 'max:255', Rule::unique('saas_products', 'slug')->ignore($record?->id)],
            'app_url' => ['nullable', 'url', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'features' => ['nullable', 'string', 'max:3000'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'billing_period' => ['required', 'in:month,year'],
            'billing_days' => ['nullable', 'integer', 'min:1', 'max:3650'],
            'trial_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['boolean'],
        ];
    }

    protected function mutateData(array $data, Request $request, Model $record): array
    {
        $data['trial_days'] = $data['trial_days'] ?? 7;
        $data['billing_days'] = $data['billing_days'] ?? ($data['billing_period'] === 'year' ? 365 : 30);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }
}
