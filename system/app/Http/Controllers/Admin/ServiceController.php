<?php

namespace App\Http\Controllers\Admin;

use App\Models\Service;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;

class ServiceController extends AdminResourceController
{
    protected string $model = Service::class;

    protected string $route = 'services';

    protected string $permission = 'cms';

    protected string $singular = 'Service';

    protected string $plural = 'Services';

    protected string $description = 'Services your agency offers, shown on the public website.';

    protected string $icon = 'fa-layer-group';

    protected string $uploadDirectory = 'services';

    protected array $searchable = ['title', 'short_description'];

    protected string $defaultSort = 'sort_order';

    protected string $defaultDirection = 'asc';

    protected function columns(): array
    {
        return [
            Column::image('image', 'Image'),
            Column::text('title', 'Title'),
            Column::text('short_description', 'Short Description', ['muted' => true]),
            Column::number('sort_order', 'Order'),
            Column::boolean('status', 'Status', ['true' => 'Active', 'false' => 'Inactive']),
            Column::date('created_at', 'Created'),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::text('title', 'Title', ['required' => true]),
            Field::text('icon', 'Icon Class', ['placeholder' => 'e.g. icon-code', 'help' => 'Icon font class used on the frontend.']),
            Field::text('short_description', 'Short Description', ['full' => true, 'help' => 'One short sentence with a couple of comma-separated points — also used as bullet points on service cards.']),
            Field::textarea('description', 'Description', ['required' => true, 'rows' => 6]),

            Field::heading('Service Detail Page'),
            Field::list('benefits', 'Key Benefits', ['help' => 'Comma separated. Each becomes a bullet point.']),
            Field::list('features', 'Main Features', ['help' => 'Comma separated. Each becomes a bullet point.']),
            Field::text('cta_label', 'CTA Button Text', ['placeholder' => 'e.g. Get a Free Quote']),
            Field::text('cta_url', 'CTA Button Link', ['placeholder' => '/contact.html', 'help' => 'Leave blank to link to the Contact page.']),

            Field::file('image', 'Image', ['accept' => 'image/*']),
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
            'title' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['required', 'string'],
            'benefits' => ['nullable', 'string'],
            'features' => ['nullable', 'string'],
            'cta_label' => ['nullable', 'string', 'max:100'],
            'cta_url' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'status' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function toggleStatus(Service $service): RedirectResponse
    {
        $this->authorizeAction('edit');

        $service->update(['status' => ! $service->status]);

        return back()->with('success', 'Service status updated successfully.');
    }
}
