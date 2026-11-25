<?php

namespace App\Http\Controllers\Admin;

use App\Models\SeoProject;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SeoProjectController extends AdminResourceController
{
    protected string $model = SeoProject::class;

    protected string $route = 'seo-projects';

    protected string $permission = 'seo_projects';

    protected string $singular = 'SEO Project';

    protected string $plural = 'SEO Projects';

    protected string $description = 'Websites you are running search campaigns for.';

    protected string $icon = 'fa-magnifying-glass-chart';

    protected array $searchable = ['name', 'website'];

    protected array $with = ['client', 'manager'];

    protected array $withCount = ['keywords'];

    protected bool $hasShow = true;

    protected function columns(): array
    {
        return [
            Column::text('name', 'Project'),
            Column::relation('client.company_name', 'Client'),
            Column::text('website', 'Website'),
            Column::text('target_country', 'Target'),
            Column::number('keywords_count', 'Keywords'),
            Column::relation('manager.name', 'Managed By'),
            Column::badge('status', 'Status', SeoProject::STATUSES),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::text('name', 'Project Name', ['required' => true]),
            Field::url('website', 'Website URL', ['required' => true]),
            Field::select('client_id', 'Client', $this->options->clients()),
            Field::select('project_id', 'Linked Project', $this->options->projects()),
            Field::text('target_country', 'Target Country / Region'),
            Field::select('managed_by', 'Managed By', $this->options->employees()),
            Field::date('start_date', 'Start Date'),
            Field::select('status', 'Status', SeoProject::STATUSES, ['required' => true]),
            Field::textarea('goals', 'Goals'),
            Field::textarea('notes', 'Notes'),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'status', 'label' => 'Status', 'options' => SeoProject::STATUSES],
            ['key' => 'client_id', 'label' => 'Client', 'options' => $this->options->clients()],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'website' => ['required', 'url', 'max:255'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'target_country' => ['nullable', 'string', 'max:120'],
            'managed_by' => ['nullable', 'exists:employees,id'],
            'start_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(array_keys(SeoProject::STATUSES))],
            'goals' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function show(SeoProject $seoProject): View
    {
        $this->authorizeAction('view');

        $seoProject->load(['client', 'manager', 'keywords' => fn ($query) => $query->orderBy('current_rank')]);

        return view('admin.seo-projects.show', compact('seoProject'));
    }
}
