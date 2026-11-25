<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug', 'description', 'is_locked'])]
class Role extends Model
{
    use LogsActivity, SoftDeletes;

    public const SUPER_ADMIN = 'super-admin';

    protected function casts(): array
    {
        return ['is_locked' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::creating(function (Role $role) {
            $role->slug = $role->slug ?: Str::slug($role->name);
        });
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function admins(): HasMany
    {
        return $this->hasMany(Admin::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->slug === self::SUPER_ADMIN;
    }
}
