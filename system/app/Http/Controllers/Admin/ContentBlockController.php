<?php

namespace App\Http\Controllers\Admin;

use App\Models\ContentBlock;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;

class ContentBlockController extends AdminResourceController
{
    protected string $model = ContentBlock::class;

    protected string $route = 'content-blocks';

    protected string $permission = 'cms';

    protected string $singular = 'Content Block';

    protected string $plural = 'Content Blocks';

    protected string $description = 'Counters, "why choose us" cards, about highlights and the scrolling marquee shown on the website.';

    protected string $icon = 'fa-shapes';

    protected string $uploadDirectory = 'content-blocks';

    protected array $searchable = ['title', 'subtitle', 'description'];

    protected string $defaultSort = 'sort_order';

    protected string $defaultDirection = 'asc';

    protected int $perPage = 25;

    protected function columns(): array
    {
        return [
            Column::badge('section', 'Section', ContentBlock::SECTIONS),
            Column::text('title', 'Title'),
            Column::custom('value', 'Value', fn (ContentBlock $block) => $block->value === null || $block->value === ''
                ? '—'
                : e($block->value.$block->suffix)),
            Column::text('subtitle', 'Subtitle', ['muted' => true]),
            Column::number('sort_order', 'Order'),
            Column::boolean('status', 'Status', ['true' => 'Visible', 'false' => 'Hidden']),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::select('section', 'Section', ContentBlock::SECTIONS, [
                'required' => true,
                'help' => 'Where on the website this block appears.',
            ]),
            Field::text('title', 'Title', [
                'required' => true,
                'help' => 'Counters: the label under the number. Marquee: the phrase that scrolls past.',
            ]),
            Field::text('subtitle', 'Subtitle', ['help' => 'Marquee blocks use this as the highlighted first word.']),
            Field::textarea('description', 'Description', ['help' => 'Shown on "Why Choose Us" cards.']),
            Field::text('value', 'Counter Value', ['help' => 'Numbers only, e.g. 28 — the site counts up to it.']),
            Field::text('suffix', 'Counter Suffix', ['help' => 'e.g. k, +, k+, %']),
            Field::text('icon', 'Icon Class', ['help' => 'Template icon class, e.g. icon-clipboard or fas fa-check.']),
            Field::file('image', 'Image', ['accept' => 'image/*']),
            Field::url('url', 'Link URL'),
            Field::number('sort_order', 'Sort Order'),
            Field::checkbox('status', 'Visible', ['help' => 'Uncheck to hide it from the website without deleting it.']),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'section', 'label' => 'Section', 'type' => 'select', 'options' => ContentBlock::SECTIONS],
            ['key' => 'status', 'label' => 'Status', 'type' => 'boolean', 'options' => ['1' => 'Visible', '0' => 'Hidden']],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'section' => ['required', 'string', 'in:'.implode(',', array_keys(ContentBlock::SECTIONS))],
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'value' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'suffix' => ['nullable', 'string', 'max:10'],
            'icon' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
            'url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['boolean'],
        ];
    }
}
