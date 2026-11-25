<?php

namespace App\Http\Controllers\Admin;

use App\Models\Company;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;

class CompanyController extends AdminResourceController
{
    protected string $model = Company::class;

    protected string $route = 'companies';

    protected string $permission = 'cms';

    protected string $singular = 'Company';

    protected string $plural = 'Companies';

    protected string $description = 'Partner and brand logos shown on the website.';

    protected string $icon = 'fa-building';

    protected string $uploadDirectory = 'companies';

    protected array $searchable = ['name'];

    protected string $defaultSort = 'sort_order';

    protected string $defaultDirection = 'asc';

    protected function columns(): array
    {
        return [
            Column::image('logo', 'Logo'),
            Column::text('name', 'Name'),
            Column::text('website_url', 'Website', ['muted' => true]),
            Column::number('sort_order', 'Order'),
            Column::boolean('status', 'Status', ['true' => 'Active', 'false' => 'Inactive']),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::text('name', 'Name', ['required' => true]),
            Field::url('website_url', 'Website URL'),
            Field::file('logo', 'Logo', ['accept' => 'image/*']),
            Field::textarea('description', 'Description'),
            Field::number('sort_order', 'Sort Order'),
            Field::checkbox('status', 'Active', ['help' => 'Visible on the public website.']),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'status', 'label' => 'Status', 'type' => 'boolean', 'options' => ['1' => 'Active', '0' => 'Inactive']],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'description' => ['nullable', 'string'],
            'status' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function toggleStatus(Company $company): RedirectResponse
    {
        $this->authorizeAction('edit');

        $company->update(['status' => ! $company->status]);

        return back()->with('success', 'Company status updated successfully.');
    }
}
