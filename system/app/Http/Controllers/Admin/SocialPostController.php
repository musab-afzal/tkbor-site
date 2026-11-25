<?php

namespace App\Http\Controllers\Admin;

use App\Models\Campaign;
use App\Models\SocialPost;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SocialPostController extends AdminResourceController
{
    protected string $model = SocialPost::class;

    protected string $route = 'social-posts';

    protected string $permission = 'social_posts';

    protected string $singular = 'Social Post';

    protected string $plural = 'Social Media Calendar';

    protected string $description = 'Planned and published social content across every channel.';

    protected string $icon = 'fa-calendar-days';

    protected string $uploadDirectory = 'social';

    protected array $searchable = ['title', 'content'];

    protected array $with = ['client', 'campaign', 'assignee'];

    protected string $defaultSort = 'scheduled_at';

    protected string $defaultDirection = 'asc';

    protected function columns(): array
    {
        return [
            Column::text('title', 'Title'),
            Column::badge('platform', 'Platform', SocialPost::PLATFORMS),
            Column::relation('client.company_name', 'Client'),
            Column::datetime('scheduled_at', 'Scheduled'),
            Column::relation('assignee.name', 'Owner'),
            Column::badge('status', 'Status', SocialPost::STATUSES),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::text('title', 'Title', ['required' => true]),
            Field::select('platform', 'Platform', SocialPost::PLATFORMS, ['required' => true]),
            Field::select('client_id', 'Client', $this->options->clients()),
            Field::select('campaign_id', 'Campaign', $this->campaignOptions()),
            Field::datetime('scheduled_at', 'Scheduled For', ['required' => true]),
            Field::datetime('published_at', 'Published At'),
            Field::select('assigned_to', 'Owner', $this->options->employees()),
            Field::select('status', 'Status', SocialPost::STATUSES, ['required' => true]),
            Field::url('link', 'Link'),
            Field::file('media', 'Media', ['accept' => 'image/*,video/*']),
            Field::textarea('content', 'Post Content', ['rows' => 6]),
            Field::textarea('notes', 'Notes'),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'platform', 'label' => 'Platform', 'options' => SocialPost::PLATFORMS],
            ['key' => 'status', 'label' => 'Status', 'options' => SocialPost::STATUSES],
            ['key' => 'client_id', 'label' => 'Client', 'options' => $this->options->clients()],
            ['key' => 'from', 'label' => 'From', 'input' => 'date', 'type' => 'date_from', 'column' => 'scheduled_at'],
            ['key' => 'to', 'label' => 'To', 'input' => 'date', 'type' => 'date_to', 'column' => 'scheduled_at'],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'platform' => ['required', Rule::in(array_keys(SocialPost::PLATFORMS))],
            'client_id' => ['nullable', 'exists:clients,id'],
            'campaign_id' => ['nullable', 'exists:campaigns,id'],
            'scheduled_at' => ['required', 'date'],
            'published_at' => ['nullable', 'date'],
            'assigned_to' => ['nullable', 'exists:employees,id'],
            'status' => ['required', Rule::in(array_keys(SocialPost::STATUSES))],
            'link' => ['nullable', 'url', 'max:255'],
            'media' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif,mp4', 'max:20480'],
            'content' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }

    protected function mutateData(array $data, Request $request, Model $record): array
    {
        if (($data['status'] ?? null) === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $data;
    }

    /** Month grid of scheduled posts. */
    public function calendar(Request $request): View
    {
        $this->authorizeAction('view');

        $month = $request->filled('month')
            ? Carbon::parse($request->input('month'))->startOfMonth()
            : now()->startOfMonth();

        $posts = SocialPost::with(['client', 'assignee'])
            ->whereBetween('scheduled_at', [$month->copy()->startOfWeek(), $month->copy()->endOfMonth()->endOfWeek()])
            ->orderBy('scheduled_at')
            ->get()
            ->groupBy(fn (SocialPost $post) => $post->scheduled_at->toDateString());

        return view('admin.social-posts.calendar', compact('month', 'posts'));
    }

    /** @return array<int, string> */
    protected function campaignOptions(): array
    {
        return Campaign::orderByDesc('id')->pluck('name', 'id')->all();
    }
}
