<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug', 'location', 'status'])]
class Menu extends Model
{
    use LogsActivity;

    public const LOCATIONS = [
        'header' => 'Header',
        'footer' => 'Footer',
        'sidebar' => 'Sidebar',
        'mobile' => 'Mobile',
    ];

    protected function casts(): array
    {
        return ['status' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::creating(function (Menu $menu) {
            $menu->slug = $menu->slug ?: Str::slug($menu->name);
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort_order')->orderBy('id');
    }

    /** Only the roots — children come through the nested relation. */
    public function rootItems(): HasMany
    {
        return $this->items()->whereNull('parent_id')->with('children');
    }
}
