<?php

namespace App\Http\Controllers\Admin;

use App\Models\Blog;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BlogController extends AdminResourceController
{
    protected string $model = Blog::class;

    protected string $route = 'blog';

    protected string $permission = 'cms';

    protected string $singular = 'Blog Post';

    protected string $plural = 'Blog';

    protected string $description = 'Articles published on the public blog.';

    protected string $icon = 'fa-newspaper';

    protected string $uploadDirectory = 'blogs';

    protected array $searchable = ['title', 'short_description', 'author_name'];

    protected function columns(): array
    {
        return [
            Column::image('featured_image', 'Image'),
            Column::text('title', 'Title'),
            Column::text('author_name', 'Author'),
            Column::badge('status', 'Status', Blog::STATUSES),
            Column::datetime('published_at', 'Published'),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::heading('Content'),
            Field::text('title', 'Title', ['required' => true]),
            Field::text('author_name', 'Author'),
            Field::text('short_description', 'Excerpt', ['full' => true]),
            Field::editor('content', 'Content', ['required' => true, 'rows' => 14]),
            Field::file('featured_image', 'Featured Image', ['accept' => 'image/*']),

            Field::heading('Publishing'),
            Field::select('status', 'Status', Blog::STATUSES, ['required' => true]),
            Field::datetime('published_at', 'Publish At', [
                'help' => 'Leave blank to publish immediately once the status is Published.',
            ]),

            Field::heading('SEO'),
            Field::text('meta_title', 'Meta Title'),
            Field::text('meta_description', 'Meta Description', ['full' => true]),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'status', 'label' => 'Status', 'options' => Blog::STATUSES],
            ['key' => 'from', 'label' => 'From', 'input' => 'date', 'type' => 'date_from', 'column' => 'published_at'],
            ['key' => 'to', 'label' => 'To', 'input' => 'date', 'type' => 'date_to', 'column' => 'published_at'],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'author_name' => ['nullable', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
            'status' => ['required', Rule::in(array_keys(Blog::STATUSES))],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ];
    }

    /** Publishing without an explicit date means "now". */
    protected function mutateData(array $data, Request $request, Model $record): array
    {
        if (($data['status'] ?? null) === Blog::STATUS_PUBLISHED && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $data;
    }
}
