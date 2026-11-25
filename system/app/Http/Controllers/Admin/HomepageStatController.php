<?php

namespace App\Http\Controllers\Admin;

use App\Models\ContentBlock;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HomepageStatController extends AdminResourceController
{
    protected string $model = ContentBlock::class;

    protected string $route = 'homepage-stats';

    protected string $permission = 'cms';

    protected string $singular = 'Homepage Stat';

    protected string $plural = 'Homepage Stats';

    protected string $description = 'Manage statistics, project counts, and achievement counters displayed on the website.';

    protected string $icon = 'fa-chart-line';

    protected array $searchable = ['title', 'description', 'value', 'suffix'];

    protected string $defaultSort = 'sort_order';

    protected string $defaultDirection = 'asc';

    protected function baseQuery(): Builder
    {
        return parent::baseQuery()->where('section', ContentBlock::SECTION_FUNFACT);
    }

    protected function columns(): array
    {
        return [
            Column::custom('value', 'Value', fn (ContentBlock $stat) => '<strong>'.e($stat->value.$stat->suffix).'</strong>'),
            Column::text('title', 'Title / Label'),
            Column::text('description', 'Description'),
            Column::number('sort_order', 'Order'),
            Column::boolean('status', 'Status', ['true' => 'Enabled', 'false' => 'Disabled']),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::text('value_combined', 'Number / Value', [
                'required' => true,
                'help' => 'e.g. 28K, 13K+, 36K, 78+, 98% (enter the number and any suffix together)',
            ]),
            Field::text('title', 'Title / Label', [
                'required' => true,
                'help' => 'e.g. Recently Projects Completed for Clients',
            ]),
            Field::textarea('description', 'Short Description', [
                'help' => 'Optional short description if needed by the frontend.',
            ]),
            Field::text('icon', 'Icon Class', [
                'help' => 'e.g. icon-clipboard, icon-team-management, icon-customer-review, icon-user-group',
            ]),
            Field::number('sort_order', 'Display Order'),
            Field::checkbox('status', 'Enabled', [
                'help' => 'Check to display this stat on the website.',
            ]),
        ];
    }

    protected function formViewData(Model $record): array
    {
        $data = parent::formViewData($record);

        if ($record->exists && ! isset($record->value_combined)) {
            $record->value_combined = $record->value.$record->suffix;
        }

        return $data;
    }

    protected function mutateData(array $data, Request $request, Model $record): array
    {
        $data['section'] = ContentBlock::SECTION_FUNFACT;

        if (isset($data['value_combined'])) {
            $raw = trim((string) $data['value_combined']);
            if (preg_match('/^([0-9.,]+)\s*(.*)$/', $raw, $matches)) {
                $data['value'] = $matches[1];
                $data['suffix'] = $matches[2];
            } else {
                $data['value'] = $raw;
                $data['suffix'] = '';
            }
            unset($data['value_combined']);
        }

        return $data;
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'value_combined' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'icon' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['boolean'],
        ];
    }

    public function toggleStatus(ContentBlock $homepageStat): RedirectResponse
    {
        $this->authorizeAction('edit');

        $homepageStat->update(['status' => ! $homepageStat->status]);

        return back()->with('success', 'Homepage stat status updated successfully.');
    }
}
