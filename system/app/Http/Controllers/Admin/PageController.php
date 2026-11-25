<?php

namespace App\Http\Controllers\Admin;

use App\Models\Page;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;

class PageController extends AdminResourceController
{
    protected string $model = Page::class;

    protected string $route = 'pages';

    protected string $permission = 'cms';

    protected string $singular = 'Page';

    protected string $plural = 'Pages';

    protected string $description = 'Standalone content pages such as privacy policy or terms.';

    protected string $icon = 'fa-file-lines';

    protected string $uploadDirectory = 'pages';

    protected array $searchable = ['title', 'slug', 'content'];

    protected function columns(): array
    {
        return [
            Column::text('title', 'Title'),
            Column::code('slug', 'Slug'),
            Column::text('meta_title', 'Meta Title', ['muted' => true]),
            Column::boolean('status', 'Status', ['true' => 'Published', 'false' => 'Hidden']),
            Column::date('updated_at', 'Updated'),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::text('title', 'Title', ['required' => true]),
            Field::text('slug', 'Slug', ['help' => 'Leave blank to generate it from the title.']),
            Field::editor('content', 'Content', ['rows' => 16]),
            Field::file('featured_image', 'Featured Image', ['accept' => 'image/*']),
            Field::checkbox('status', 'Published'),

            Field::heading('SEO'),
            Field::text('meta_title', 'Meta Title'),
            Field::text('meta_keywords', 'Meta Keywords'),
            Field::text('meta_description', 'Meta Description', ['full' => true]),
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
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash'],
            'content' => ['nullable', 'string'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'status' => ['boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
