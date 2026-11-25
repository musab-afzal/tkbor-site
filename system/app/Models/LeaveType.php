<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug', 'days_allowed', 'is_paid', 'status'])]
class LeaveType extends Model
{
    protected function casts(): array
    {
        return ['is_paid' => 'boolean', 'status' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::creating(function (LeaveType $type) {
            $type->slug = $type->slug ?: Str::slug($type->name);
        });
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }
}
