<?php

namespace App\Http\Controllers\Admin;

use App\Models\Portfolio;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;

/**
 * Website portfolio entries (formerly the "Projects" CMS module). Delivery
 * projects live in ProjectController.
 */
class PortfolioController extends AdminResourceController
{
    protected string $model = Portfolio::class;

    protected string $route = 'portfolios';

    protected string $permission = 'cms';

    protected string $singular = 'Portfolio Item';

    protected string $plural = 'Portfolio';

    protected string $description = 'Work showcased on the public portfolio pages.';

    protected string $icon = 'fa-images';

    protected string $uploadDirectory = 'portfolios';

    protected array $searchable = ['title', 'client_name', 'category'];

    protected function columns(): array
    {
        return [
            Column::image('featured_image', 'Image'),
            Column::text('title', 'Title'),
            Column::text('client_name', 'Client'),
            Column::text('category', 'Category'),
            Column::date('project_date', 'Date'),
            Column::boolean('is_featured', 'Featured'),
            Column::boolean('status', 'Status', ['true' => 'Active', 'false' => 'Inactive']),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::text('title', 'Title', ['required' => true]),
            Field::text('client_name', 'Client Name'),
            Field::text('category', 'Category'),
            Field::date('project_date', 'Project Date'),
            Field::url('project_url', 'Live Project URL'),
            Field::list('technologies', 'Technologies', ['placeholder' => 'Laravel, React, Stripe']),
            Field::text('short_description', 'Short Description', ['full' => true]),
            Field::textarea('description', 'Description', ['rows' => 6]),
            Field::file('featured_image', 'Featured Image', ['accept' => 'image/*']),
            Field::checkbox('is_featured', 'Featured', ['help' => 'Highlighted on the homepage and portfolio grid.']),
            Field::checkbox('status', 'Active', ['help' => 'Visible on the public website.']),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'category', 'label' => 'Category', 'input' => 'text', 'type' => 'like'],
            ['key' => 'is_featured', 'label' => 'Featured', 'type' => 'boolean', 'options' => ['1' => 'Featured', '0' => 'Not featured']],
            ['key' => 'status', 'label' => 'Status', 'type' => 'boolean', 'options' => ['1' => 'Active', '0' => 'Inactive']],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'project_url' => ['nullable', 'url', 'max:255'],
            'technologies' => ['nullable', 'string', 'max:1000'],
            'project_date' => ['nullable', 'date'],
            'status' => ['boolean'],
            'is_featured' => ['boolean'],
        ];
    }

    public function toggleStatus(Portfolio $portfolio): RedirectResponse
    {
        $this->authorizeAction('edit');

        $portfolio->update(['status' => ! $portfolio->status]);

        return back()->with('success', 'Portfolio status updated successfully.');
    }

    public function toggleFeatured(Portfolio $portfolio): RedirectResponse
    {
        $this->authorizeAction('edit');

        $portfolio->update(['is_featured' => ! $portfolio->is_featured]);

        return back()->with('success', 'Portfolio featured state updated successfully.');
    }
}
