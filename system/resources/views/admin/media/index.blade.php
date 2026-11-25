@extends('admin.layouts.admin')

@section('title', 'Media Library')
@section('page-title', 'Media Library')

@push('styles')
<style>
    .media-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 14px; }
    .media-tile { border: 1px solid var(--admin-border); border-radius: var(--admin-radius-sm); overflow: hidden; background: #fff; }
    .media-tile__preview { height: 120px; display: flex; align-items: center; justify-content: center; background: #f4f6fb; overflow: hidden; }
    .media-tile__preview img { width: 100%; height: 100%; object-fit: cover; }
    .media-tile__preview i { font-size: 32px; color: var(--admin-muted); }
    .media-tile__body { padding: 9px 10px; font-size: 12px; }
    .media-tile__name { font-weight: 600; word-break: break-all; margin-bottom: 3px; }
    .media-tile__meta { color: var(--admin-muted); display: flex; justify-content: space-between; align-items: center; gap: 6px; }
</style>
@endpush

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>Media Library</h1>
            <p>Shared images and documents you can reference anywhere on the site.</p>
        </div>
    </div>

    @can('media.create')
        <div class="admin-card">
            <div class="admin-card__header"><h2>Upload Files</h2></div>
            <div class="admin-card__body">
                @include('admin.partials.validation-summary')

                <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="admin-form-grid">
                        <div class="admin-form-group">
                            <label class="admin-label" for="folder">Folder</label>
                            <select id="folder" name="folder" class="admin-select" required>
                                @foreach (\App\Models\Media::FOLDERS as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-label" for="alt_text">Alt Text</label>
                            <input type="text" id="alt_text" name="alt_text" class="admin-input" placeholder="Describes the image for accessibility">
                        </div>
                        <div class="admin-form-group admin-form-group--full">
                            <label class="admin-label" for="files">Files</label>
                            <input type="file" id="files" name="files[]" class="admin-input" multiple required>
                            <div class="admin-help-text">Up to 20 files, 20 MB each.</div>
                        </div>
                    </div>
                    <div class="admin-form-actions">
                        <button type="submit" class="admin-btn admin-btn--primary"><i class="fas fa-upload"></i> Upload</button>
                    </div>
                </form>
            </div>
        </div>
    @endcan

    <div class="admin-card admin-filter-card">
        <form action="{{ route('admin.media.index') }}" method="GET" class="admin-filter-form">
            <div class="admin-filter-field admin-filter-field--search">
                <label class="admin-label" for="search">Search</label>
                <input type="text" id="search" name="search" class="admin-input" value="{{ request('search') }}" placeholder="File name...">
            </div>
            <div class="admin-filter-field">
                <label class="admin-label" for="folder_filter">Folder</label>
                <select id="folder_filter" name="folder" class="admin-select">
                    <option value="">All folders</option>
                    @foreach (\App\Models\Media::FOLDERS as $value => $label)
                        <option value="{{ $value }}" @selected(request('folder') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="admin-filter-actions">
                <button type="submit" class="admin-btn admin-btn--primary admin-btn--sm"><i class="fas fa-filter"></i> Filter</button>
                <a href="{{ route('admin.media.index') }}" class="admin-btn admin-btn--ghost admin-btn--sm"><i class="fas fa-xmark"></i> Reset</a>
            </div>
        </form>
    </div>

    <div class="admin-card">
        <div class="admin-card__body">
            @if ($media->isEmpty())
                <div class="admin-empty-state"><i class="fas fa-photo-film"></i>No files uploaded yet.</div>
            @else
                <div class="media-grid">
                    @foreach ($media as $file)
                        <div class="media-tile">
                            <a href="{{ uploaded_url($file->path) }}" target="_blank" rel="noopener" class="media-tile__preview">
                                @if ($file->isImage())
                                    <img src="{{ uploaded_url($file->path) }}" alt="{{ $file->alt_text }}">
                                @else
                                    <i class="fas fa-file"></i>
                                @endif
                            </a>
                            <div class="media-tile__body">
                                <div class="media-tile__name">{{ Str::limit($file->name, 28) }}</div>
                                <div class="media-tile__meta">
                                    <span>{{ $file->humanSize() }}</span>
                                    @can('media.delete')
                                        <form action="{{ route('admin.media.destroy', $file) }}" method="POST" class="js-delete-form" data-confirm="Delete {{ $file->name }}?">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="admin-icon-btn admin-icon-btn--danger" title="Delete"><i class="fas fa-trash"></i></button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="admin-pagination-wrapper">{{ $media->links() }}</div>
    </div>

@endsection
