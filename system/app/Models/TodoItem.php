<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['todo_id', 'title', 'is_done', 'sort_order'])]
class TodoItem extends Model
{
    protected function casts(): array
    {
        return ['is_done' => 'boolean'];
    }

    public function todo(): BelongsTo
    {
        return $this->belongsTo(Todo::class);
    }
}
