<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Repeating website copy grouped by the section it appears in. Frontend views
 * pull a section through ContentBlock::section('funfact') rather than holding
 * the text themselves.
 */
#[Fillable([
    'section', 'title', 'subtitle', 'description', 'icon', 'image', 'url',
    'value', 'suffix', 'sort_order', 'status',
])]
class ContentBlock extends Model
{
    use LogsActivity, SoftDeletes;

    public const SECTION_FUNFACT = 'funfact';

    public const SECTION_WHY_CHOOSE = 'why_choose';

    public const SECTION_ABOUT_HIGHLIGHT = 'about_highlight';

    public const SECTION_MARQUEE = 'marquee';

    public const SECTION_WORK_PROCESS = 'work_process';

    public const SECTION_AWARD = 'award';

    /** @var array<string, string> */
    public const SECTIONS = [
        'funfact' => 'Counter (Fun Fact)',
        'why_choose' => 'Why Choose Us',
        'about_highlight' => 'About Highlight',
        'marquee' => 'Scrolling Marquee',
        'work_process' => 'Work Process Step',
        'award' => 'Award / Achievement',
    ];

    protected function casts(): array
    {
        return ['status' => 'boolean', 'sort_order' => 'integer'];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function scopeSection(Builder $query, string $section): Builder
    {
        return $query->where('section', $section);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * The blocks of one section, ready for a view.
     *
     * @return Collection<int, static>
     */
    public static function forSection(string $section, int $limit = 20): Collection
    {
        return static::query()->active()->section($section)->ordered()->take($limit)->get();
    }

    public function sectionLabel(): string
    {
        return self::SECTIONS[$this->section] ?? ucfirst(str_replace('_', ' ', (string) $this->section));
    }

    public function activityLabel(): string
    {
        return $this->sectionLabel().' — '.$this->title;
    }
}
