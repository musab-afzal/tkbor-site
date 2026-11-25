<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FileUploader;
use App\Support\Options;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Shared CRUD engine. A module declares its model, fields, columns and filters
 * and gets a full listing / create / edit / delete flow rendered by the shared
 * crud views. Modules with unusual behaviour override the hooks or skip this
 * base entirely.
 */
abstract class AdminResourceController extends Controller
{
    /** Eloquent model class this controller manages. */
    protected string $model;

    /** Route name prefix without the admin. part, e.g. "leads". */
    protected string $route;

    /** Permission prefix, e.g. "leads" gives leads.view / leads.create ... */
    protected string $permission;

    /** Human labels used in headings and flash messages. */
    protected string $singular = 'Record';

    protected string $plural = 'Records';

    protected string $description = '';

    protected string $icon = 'fa-folder';

    /** Storage sub-directory for this module's uploads. */
    protected string $uploadDirectory = 'uploads';

    /** Columns searched by the free-text search box. */
    protected array $searchable = [];

    /** Relations eager loaded on the index listing. */
    protected array $with = [];

    /** Relations counted on the index listing. */
    protected array $withCount = [];

    protected int $perPage = 15;

    protected string $defaultSort = 'id';

    protected string $defaultDirection = 'desc';

    /**
     * Form keys that map to relations rather than columns. They are stripped
     * from the payload before fill() and synced in afterSave().
     *
     * @var array<int, string>
     */
    protected array $relationFields = [];

    /**
     * The record currently being rendered or saved. Set before fields() runs so
     * a module can shape a field definition around the existing values.
     */
    protected ?Model $currentRecord = null;

    /** Show a "view" action in the row when the module has a detail page. */
    protected bool $hasShow = false;

    /** Optional blade partial rendering extra per-row actions; gets $record. */
    protected ?string $rowActionsView = null;

    /** Views can be overridden per module; they default to the shared crud set. */
    protected string $indexView = 'admin.crud.index';

    protected string $createView = 'admin.crud.create';

    protected string $editView = 'admin.crud.edit';

    public function __construct(
        protected FileUploader $uploader,
        protected Options $options,
    ) {}

    /** @return array<int, array<string, mixed>> */
    abstract protected function columns(): array;

    /** @return array<int, array<string, mixed>> */
    abstract protected function fields(): array;

    /**
     * Filter controls shown above the table.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function filters(): array
    {
        return [];
    }

    /** Validation rules for create. */
    abstract protected function rules(?Model $record = null): array;

    public function index(Request $request): View
    {
        $this->authorizeAction('view');

        $records = $this->applyFilters($this->baseQuery(), $request)
            ->orderBy($this->defaultSort, $this->defaultDirection)
            ->paginate($this->perPage)
            ->withQueryString();

        return view($this->indexView, array_merge($this->sharedViewData(), [
            'records' => $records,
            'columns' => $this->columns(),
            'filterControls' => $this->filters(),
        ], $this->indexViewData($request)));
    }

    public function create(): View
    {
        $this->authorizeAction('create');

        $record = new $this->model;
        $this->currentRecord = $record;

        return view($this->createView, array_merge($this->sharedViewData(), [
            'record' => $record,
            'fields' => $this->fields(),
        ], $this->formViewData($record)));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAction('create');

        $data = $this->validateRequest($request);

        $record = DB::transaction(function () use ($request, $data) {
            /** @var Model $record */
            $record = new $this->model;
            $this->currentRecord = $record;
            $record->fill($this->prepareData($data, $request, $record));
            $record->save();

            $this->afterSave($record, $request, true);

            return $record;
        });

        return redirect()
            ->route($this->indexRoute())
            ->with('success', $this->singular.' created successfully.');
    }

    public function edit(): View
    {
        $this->authorizeAction('edit');

        $record = $this->resolveRecord();
        $this->currentRecord = $record;

        return view($this->editView, array_merge($this->sharedViewData(), [
            'record' => $record,
            'fields' => $this->fields(),
        ], $this->formViewData($record)));
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorizeAction('edit');

        $record = $this->resolveRecord();
        $data = $this->validateRequest($request, $record);

        DB::transaction(function () use ($request, $record, $data) {
            $this->currentRecord = $record;
            $record->fill($this->prepareData($data, $request, $record));
            $record->save();

            $this->afterSave($record, $request, false);
        });

        return redirect()
            ->route($this->indexRoute())
            ->with('success', $this->singular.' updated successfully.');
    }

    public function destroy(): RedirectResponse
    {
        $this->authorizeAction('delete');

        return $this->deleteRecord($this->resolveRecord());
    }

    protected function deleteRecord(Model $record): RedirectResponse
    {
        $this->beforeDelete($record);
        $record->delete();

        return redirect()
            ->route($this->indexRoute())
            ->with('success', $this->singular.' deleted successfully.');
    }

    // ---------------------------------------------------------------- helpers

    /**
     * Resolves the record from the current route. The base controller is
     * generic, so it cannot type-hint a concrete model for implicit binding —
     * the resource route always carries exactly one parameter, which is the id.
     */
    protected function resolveRecord(): Model
    {
        $parameters = request()->route()?->parameters() ?? [];
        $id = end($parameters);

        abort_if($id === false || $id === null, 404);

        return $this->model::query()->findOrFail($id);
    }

    protected function baseQuery(): Builder
    {
        $query = $this->model::query();

        if ($this->with !== []) {
            $query->with($this->with);
        }

        if ($this->withCount !== []) {
            $query->withCount($this->withCount);
        }

        return $query;
    }

    /**
     * Applies the free-text search and every declared filter. Filters map a
     * request key to a column and an operator.
     */
    protected function applyFilters(Builder $query, Request $request): Builder
    {
        if ($request->filled('search') && $this->searchable !== []) {
            $term = '%'.$request->string('search').'%';

            $query->where(function (Builder $inner) use ($term) {
                foreach ($this->searchable as $column) {
                    if (str_contains($column, '.')) {
                        [$relation, $field] = explode('.', $column, 2);
                        $inner->orWhereHas($relation, fn (Builder $q) => $q->where($field, 'like', $term));

                        continue;
                    }

                    $inner->orWhere($column, 'like', $term);
                }
            });
        }

        foreach ($this->filters() as $filter) {
            $key = $filter['key'];

            if (! $request->filled($key)) {
                continue;
            }

            $value = $request->input($key);
            $column = $filter['column'] ?? $key;

            match ($filter['type'] ?? 'equals') {
                'date_from' => $query->whereDate($column, '>=', $value),
                'date_to' => $query->whereDate($column, '<=', $value),
                'like' => $query->where($column, 'like', '%'.$value.'%'),
                'boolean' => $query->where($column, (bool) $value),
                'relation' => $query->whereHas($filter['relation'], fn (Builder $q) => $q->where($filter['relation_column'] ?? 'id', $value)),
                default => $query->where($column, $value),
            };
        }

        return $this->scopeQuery($query, $request);
    }

    /** Extra constraints a module wants on every listing. */
    protected function scopeQuery(Builder $query, Request $request): Builder
    {
        return $query;
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateRequest(Request $request, ?Model $record = null): array
    {
        return $request->validate($this->rules($record));
    }

    /**
     * Last chance to shape the validated payload: cast checkboxes, store
     * uploads, split comma separated lists.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function prepareData(array $data, Request $request, Model $record): array
    {
        foreach ($this->fields() as $field) {
            $key = $field['key'];

            if ($field['type'] === 'file') {
                // Fields flagged as attachments are handled in afterSave() so
                // the upload lands in the attachments table, not a column.
                if ($field['attachment'] ?? false) {
                    unset($data[$key]);

                    continue;
                }

                $data[$key] = $this->uploader->replace(
                    $request->file($key),
                    $field['directory'] ?? $this->uploadDirectory,
                    $record->{$key} ?? null,
                );

                continue;
            }

            match ($field['type']) {
                'checkbox' => $data[$key] = $request->boolean($key),
                'list' => $data[$key] = $this->splitList($data[$key] ?? null),
                'multiselect' => $data[$key] = array_values(array_filter((array) ($data[$key] ?? []))),
                default => null,
            };
        }

        $data = $this->mutateData($data, $request, $record);

        return array_diff_key($data, array_flip($this->relationFields));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateData(array $data, Request $request, Model $record): array
    {
        return $data;
    }

    protected function afterSave(Model $record, Request $request, bool $created): void
    {
        //
    }

    protected function beforeDelete(Model $record): void
    {
        foreach ($this->fields() as $field) {
            if ($field['type'] === 'file' && ! ($field['attachment'] ?? false)) {
                $this->uploader->delete($record->{$field['key']} ?? null);
            }
        }
    }

    /** @return array<int, string>|null */
    protected function splitList(?string $value): ?array
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        return array_values(array_filter(array_map('trim', explode(',', $value))));
    }

    /** @return array<string, mixed> */
    protected function sharedViewData(): array
    {
        return [
            'route' => $this->route,
            'singular' => $this->singular,
            'plural' => $this->plural,
            'moduleDescription' => $this->description,
            'moduleIcon' => $this->icon,
            'permission' => $this->permission,
            'searchable' => $this->searchable !== [],
            'hasShow' => $this->hasShow,
            'rowActionsView' => $this->rowActionsView,
            // Filtered sub-views (due today, overdue...) post back to themselves
            // so the sub-view is not lost when a filter is applied.
            'filterAction' => url()->current(),
        ];
    }

    /** @return array<string, mixed> */
    protected function indexViewData(Request $request): array
    {
        return [];
    }

    /** @return array<string, mixed> */
    protected function formViewData(Model $record): array
    {
        return [];
    }

    protected function indexRoute(): string
    {
        return 'admin.'.$this->route.'.index';
    }

    /** Gate check using the module's permission prefix. */
    protected function authorizeAction(string $action): void
    {
        if (isset($this->permission)) {
            $this->authorize($this->permission.'.'.$action);
        }
    }
}
