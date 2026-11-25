<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'path', 'mime_type', 'size', 'folder', 'alt_text', 'uploaded_by'])]
class Media extends Model
{
    protected $table = 'media';

    public const FOLDERS = [
        'general' => 'General',
        'services' => 'Services',
        'portfolio' => 'Portfolio',
        'blog' => 'Blog',
        'team' => 'Team',
        'clients' => 'Clients',
        'sliders' => 'Sliders',
        'documents' => 'Documents',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'uploaded_by');
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }

    public function humanSize(): string
    {
        $bytes = (int) $this->size;

        foreach (['B', 'KB', 'MB', 'GB'] as $unit) {
            if ($bytes < 1024) {
                return round($bytes, 1).' '.$unit;
            }
            $bytes /= 1024;
        }

        return round($bytes, 1).' TB';
    }
}
