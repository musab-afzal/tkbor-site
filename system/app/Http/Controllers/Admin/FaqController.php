<?php

namespace App\Http\Controllers\Admin;

use App\Models\Faq;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;

class FaqController extends AdminResourceController
{
    protected string $model = Faq::class;

    protected string $route = 'faqs';

    protected string $permission = 'cms';

    protected string $singular = 'FAQ';

    protected string $plural = 'FAQs';

    protected string $description = 'Questions and answers shown on the public FAQ page.';

    protected string $icon = 'fa-circle-question';

    protected array $searchable = ['question', 'answer'];

    protected string $defaultSort = 'sort_order';

    protected string $defaultDirection = 'asc';

    protected function columns(): array
    {
        return [
            Column::text('question', 'Question'),
            Column::number('sort_order', 'Order'),
            Column::boolean('status', 'Status', ['true' => 'Active', 'false' => 'Inactive']),
            Column::date('created_at', 'Created'),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::text('question', 'Question', ['required' => true, 'full' => true]),
            Field::textarea('answer', 'Answer', ['required' => true, 'rows' => 5]),
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
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'status' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function toggleStatus(Faq $faq): RedirectResponse
    {
        $this->authorizeAction('edit');

        $faq->update(['status' => ! $faq->status]);

        return back()->with('success', 'FAQ status updated successfully.');
    }
}
