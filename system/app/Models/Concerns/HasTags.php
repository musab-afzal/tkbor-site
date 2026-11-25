<?php

namespace App\Models\Concerns;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasTags
{
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * Replace the tag set from a comma separated string, creating any tag that
     * does not exist yet.
     */
    public function syncTagsFromString(?string $value): void
    {
        $names = collect(explode(',', (string) $value))
            ->map(fn ($name) => trim($name))
            ->filter()
            ->unique();

        $ids = $names->map(fn (string $name) => Tag::findOrCreateByName($name)->id)->all();

        $this->tags()->sync($ids);
    }
}
