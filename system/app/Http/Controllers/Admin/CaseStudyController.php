<?php

namespace App\Http\Controllers\Admin;

use App\Models\CaseStudy;
use App\Models\Portfolio;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;

class CaseStudyController extends AdminResourceController
{
    protected string $model = CaseStudy::class;

    protected string $route = 'case-studies';

    protected string $permission = 'cms';

    protected string $singular = 'Case Study';

    protected string $plural = 'Case Studies';

    protected string $description = 'In-depth stories of the work you have delivered.';

    protected string $icon = 'fa-book-open';

    protected string $uploadDirectory = 'case-studies';

    protected array $searchable = ['title', 'industry', 'short_description'];

    protected array $with = ['client', 'portfolio'];

    protected function columns(): array
    {
        return [
            Column::image('featured_image', 'Image'),
            Column::text('title', 'Title'),
            Column::relation('client.company_name', 'Client'),
            Column::text('industry', 'Industry'),
            Column::boolean('is_featured', 'Featured'),
            Column::boolean('status', 'Status', ['true' => 'Active', 'false' => 'Inactive']),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::heading('Overview'),
            Field::text('title', 'Title', ['required' => true]),
            Field::select('client_id', 'Client', $this->options->clients()),
            Field::select('portfolio_id', 'Portfolio Item', $this->portfolioOptions()),
            Field::text('industry', 'Industry'),
            Field::text('short_description', 'Short Description', ['full' => true]),
            Field::file('featured_image', 'Featured Image', ['accept' => 'image/*']),
            Field::list('technologies', 'Technologies', ['placeholder' => 'Laravel, React']),

            Field::heading('Story'),
            Field::textarea('challenge', 'The Challenge', ['rows' => 5]),
            Field::textarea('solution', 'Our Solution', ['rows' => 5]),
            Field::textarea('results', 'The Results', ['rows' => 5]),

            Field::heading('Publishing'),
            Field::checkbox('is_featured', 'Featured'),
            Field::checkbox('status', 'Active', ['help' => 'Visible on the public website.']),
            Field::number('sort_order', 'Sort Order'),
            Field::text('meta_title', 'Meta Title'),
            Field::text('meta_description', 'Meta Description', ['full' => true]),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'client_id', 'label' => 'Client', 'options' => $this->options->clients()],
            ['key' => 'status', 'label' => 'Status', 'type' => 'boolean', 'options' => ['1' => 'Active', '0' => 'Inactive']],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'portfolio_id' => ['nullable', 'exists:portfolios,id'],
            'industry' => ['nullable', 'string', 'max:150'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'technologies' => ['nullable', 'string', 'max:1000'],
            'challenge' => ['nullable', 'string'],
            'solution' => ['nullable', 'string'],
            'results' => ['nullable', 'string'],
            'is_featured' => ['boolean'],
            'status' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ];
    }

    /** @return array<int, string> */
    protected function portfolioOptions(): array
    {
        return Portfolio::orderBy('title')->pluck('title', 'id')->all();
    }
}
