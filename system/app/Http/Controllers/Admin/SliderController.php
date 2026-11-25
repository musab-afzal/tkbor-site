<?php

namespace App\Http\Controllers\Admin;

use App\Models\Slider;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;

class SliderController extends AdminResourceController
{
    protected string $model = Slider::class;

    protected string $route = 'sliders';

    protected string $permission = 'cms';

    protected string $singular = 'Slide';

    protected string $plural = 'Slider';

    protected string $description = 'Hero slides shown at the top of the homepage.';

    protected string $icon = 'fa-images';

    protected string $uploadDirectory = 'sliders';

    protected array $searchable = ['title', 'subtitle'];

    protected string $defaultSort = 'sort_order';

    protected string $defaultDirection = 'asc';

    protected function columns(): array
    {
        return [
            Column::image('image', 'Image'),
            Column::text('title', 'Title'),
            Column::text('subtitle', 'Subtitle', ['muted' => true]),
            Column::text('button_text', 'Button'),
            Column::number('sort_order', 'Order'),
            Column::boolean('status', 'Status', ['true' => 'Active', 'false' => 'Inactive']),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::text('title', 'Title', ['required' => true]),
            Field::text('subtitle', 'Subtitle'),
            Field::textarea('description', 'Description'),
            Field::file('image', 'Slide Image', ['accept' => 'image/*']),
            Field::text('button_text', 'Button Text'),
            Field::url('button_url', 'Button URL'),
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
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['boolean'],
        ];
    }
}
