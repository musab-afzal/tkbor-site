<?php

namespace App\Http\Controllers\Admin;

use App\Models\Keyword;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KeywordController extends AdminResourceController
{
    protected string $model = Keyword::class;

    protected string $route = 'keywords';

    protected string $permission = 'keywords';

    protected string $singular = 'Keyword';

    protected string $plural = 'Keywords';

    protected string $description = 'Tracked search terms and their ranking movement.';

    protected string $icon = 'fa-key';

    protected array $searchable = ['keyword', 'target_url'];

    protected array $with = ['seoProject.client'];

    protected function columns(): array
    {
        return [
            Column::text('keyword', 'Keyword'),
            Column::relation('seoProject.name', 'SEO Project'),
            Column::text('target_url', 'Target URL', ['muted' => true]),
            Column::number('search_volume', 'Volume'),
            Column::number('current_rank', 'Rank'),
            Column::custom('change', 'Change', function (Keyword $keyword) {
                $change = $keyword->rankChange();

                if ($change === null) {
                    return '<span class="admin-cell-muted">—</span>';
                }

                if ($change === 0) {
                    return '<span class="admin-badge admin-badge--neutral">No change</span>';
                }

                return $change > 0
                    ? '<span class="admin-badge admin-badge--success"><i class="fas fa-arrow-up"></i> '.$change.'</span>'
                    : '<span class="admin-badge admin-badge--danger"><i class="fas fa-arrow-down"></i> '.abs($change).'</span>';
            }),
            Column::date('last_checked_at', 'Checked'),
            Column::badge('status', 'Status', Keyword::STATUSES),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::text('keyword', 'Keyword', ['required' => true]),
            Field::select('seo_project_id', 'SEO Project', $this->options->seoProjects(), ['required' => true]),
            Field::url('target_url', 'Target URL'),
            Field::number('search_volume', 'Monthly Search Volume'),
            Field::number('current_rank', 'Current Rank'),
            Field::number('previous_rank', 'Previous Rank'),
            Field::date('last_checked_at', 'Last Checked'),
            Field::select('difficulty', 'Difficulty', Keyword::DIFFICULTIES),
            Field::select('status', 'Status', Keyword::STATUSES, ['required' => true]),
            Field::textarea('notes', 'Notes'),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'seo_project_id', 'label' => 'SEO Project', 'options' => $this->options->seoProjects()],
            ['key' => 'status', 'label' => 'Status', 'options' => Keyword::STATUSES],
            ['key' => 'difficulty', 'label' => 'Difficulty', 'options' => Keyword::DIFFICULTIES],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'keyword' => ['required', 'string', 'max:255'],
            'seo_project_id' => ['required', 'exists:seo_projects,id'],
            'target_url' => ['nullable', 'url', 'max:255'],
            'search_volume' => ['nullable', 'integer', 'min:0'],
            'current_rank' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'previous_rank' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'last_checked_at' => ['nullable', 'date'],
            'difficulty' => ['nullable', Rule::in(array_keys(Keyword::DIFFICULTIES))],
            'status' => ['required', Rule::in(array_keys(Keyword::STATUSES))],
            'notes' => ['nullable', 'string'],
        ];
    }

    /** Editing the rank shifts the old value into previous_rank automatically. */
    protected function mutateData(array $data, Request $request, Model $record): array
    {
        if ($record->exists
            && array_key_exists('current_rank', $data)
            && (int) $data['current_rank'] !== (int) $record->current_rank
            && ! $request->filled('previous_rank')) {
            $data['previous_rank'] = $record->current_rank;
            $data['last_checked_at'] = $data['last_checked_at'] ?? now()->toDateString();
        }

        return $data;
    }
}
