<?php

namespace App\Http\Controllers\Admin;

use App\Models\Review;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;

class ReviewController extends AdminResourceController
{
    protected string $model = Review::class;

    protected string $route = 'reviews';

    protected string $permission = 'cms';

    protected string $singular = 'Testimonial';

    protected string $plural = 'Testimonials';

    protected string $description = 'Client testimonials shown across the public website.';

    protected string $icon = 'fa-star';

    protected string $uploadDirectory = 'reviews';

    protected array $searchable = ['client_name', 'company_name', 'project_name', 'designation', 'review'];

    protected string $defaultSort = 'sort_order';

    protected string $defaultDirection = 'asc';

    protected function columns(): array
    {
        return [
            Column::text('client_name', 'Client'),
            Column::text('project_name', 'Project'),
            Column::text('designation', 'Designation'),
            Column::text('company_name', 'Company'),
            Column::custom('rating', 'Rating', fn (Review $review) => str_repeat(
                '<i class="fas fa-star" style="color:#f59e0b"></i>',
                max(1, min(5, (int) $review->rating))
            )),
            Column::number('sort_order', 'Order'),
            Column::boolean('status', 'Status', ['true' => 'Active', 'false' => 'Inactive']),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::text('client_name', 'Client Name', ['required' => true]),
            Field::text('project_name', 'Project Name', ['help' => 'e.g. E-Commerce Redesign, SaaS Web App']),
            Field::text('designation', 'Designation', ['help' => 'e.g. CEO, Marketing Director']),
            Field::text('company_name', 'Company Name'),
            Field::select('rating', 'Rating', [
                5 => '5 ? - Excellent',
                4 => '4 ? - Very Good',
                3 => '3 ? - Good',
                2 => '2 ? - Fair',
                1 => '1 ? - Poor',
            ], ['required' => true]),
            Field::textarea('review', 'Feedback / Review', ['required' => true, 'rows' => 5]),
            Field::number('sort_order', 'Sort Order'),
            Field::checkbox('status', 'Active', ['help' => 'Visible on the public website.']),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'rating', 'label' => 'Rating', 'options' => [5 => '5 stars', 4 => '4 stars', 3 => '3 stars', 2 => '2 stars', 1 => '1 star']],
            ['key' => 'status', 'label' => 'Status', 'type' => 'boolean', 'options' => ['1' => 'Active', '0' => 'Inactive']],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'client_name' => ['required', 'string', 'max:255'],
            'project_name' => ['nullable', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'review' => ['required', 'string'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'status' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function toggleStatus(Review $review): RedirectResponse
    {
        $this->authorizeAction('edit');

        $review->update(['status' => ! $review->status]);

        return back()->with('success', 'Testimonial status updated successfully.');
    }
}
