<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\FileUploader;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function __construct(protected FileUploader $uploader) {}

    public function index(Request $request): View
    {
        $this->authorize('media.view');

        $media = Media::query()
            ->with('uploader')
            ->when($request->filled('folder'), fn (Builder $q) => $q->where('folder', $request->input('folder')))
            ->when($request->filled('search'), fn (Builder $q) => $q->where('name', 'like', '%'.$request->string('search').'%'))
            ->latest('id')
            ->paginate(30)
            ->withQueryString();

        return view('admin.media.index', compact('media'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('media.create');

        $request->validate([
            'files' => ['required', 'array', 'max:20'],
            'files.*' => ['file', 'mimes:jpg,jpeg,png,webp,gif,svg,pdf,doc,docx,xls,xlsx,zip,mp4', 'max:20480'],
            'folder' => ['required', Rule::in(array_keys(Media::FOLDERS))],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($request->file('files') as $file) {
            Media::create([
                'name' => $file->getClientOriginalName(),
                'path' => $this->uploader->store($file, 'media/'.$request->input('folder')),
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'folder' => $request->input('folder'),
                'alt_text' => $request->input('alt_text'),
                'uploaded_by' => Auth::guard('admin')->id(),
            ]);
        }

        return back()->with('success', 'Files uploaded to the media library.');
    }

    public function destroy(Media $medium): RedirectResponse
    {
        $this->authorize('media.delete');

        $this->uploader->delete($medium->path);
        $medium->delete();

        return back()->with('success', 'File deleted from the media library.');
    }
}
