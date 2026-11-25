<?php

namespace App\Services;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Single place that writes uploads to the public disk. Names are randomised so
 * a client supplied filename can never define the stored path.
 */
class FileUploader
{
    public function store(UploadedFile $file, string $directory): string
    {
        $extension = strtolower($file->guessExtension() ?: 'bin');
        $filename = Str::random(24).'-'.time().'.'.$extension;

        $path = $file->storeAs($directory, $filename, 'public');
        if ($path === false) {
            throw new \RuntimeException('The uploaded file could not be stored.');
        }
        return $path;
    }

    public function replace(?UploadedFile $file, string $directory, ?string $oldPath): ?string
    {
        if (! $file) {
            return $oldPath;
        }

        $path = $this->store($file, $directory);
        $this->delete($oldPath);
        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Attach one or more uploads to any model that uses HasAttachments.
     *
     * @param  array<int, UploadedFile>|UploadedFile|null  $files
     */
    public function attachTo(Model $model, array|UploadedFile|null $files, string $directory): void
    {
        foreach (array_filter(is_array($files) ? $files : [$files]) as $file) {
            $model->attachments()->create([
                'path' => $this->store($file, $directory),
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'uploaded_by' => Auth::guard('admin')->id(),
            ]);
        }
    }

    public function deleteAttachment(Attachment $attachment): void
    {
        $this->delete($attachment->path);
        $attachment->delete();
    }
}