<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug', 'color'])]
class Tag extends Model
{
    public static function findOrCreateByName(string $name): self
    {
        return static::firstOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name],
        );
    }
}
