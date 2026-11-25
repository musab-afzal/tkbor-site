@extends('admin.layouts.admin')

@section('title', 'Menus')
@section('page-title', 'Menus')

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>Menus</h1>
            <p>Navigation menus used across the public website.</p>
        </div>
    </div>

    @can('menus.create')
        <div class="admin-card">
            <div class="admin-card__header"><h2>Add Menu</h2></div>
            <div class="admin-card__body">
                @include('admin.partials.validation-summary')

                <form action="{{ route('admin.menus.store') }}" method="POST">
                    @csrf
                    <div class="admin-form-grid">
                        <div class="admin-form-group">
                            <label class="admin-label" for="name">Menu Name</label>
                            <input type="text" id="name" name="name" class="admin-input" required placeholder="Main Navigation">
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-label" for="location">Location</label>
                            <select id="location" name="location" class="admin-select" required>
                                @foreach (\App\Models\Menu::LOCATIONS as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="admin-form-actions">
                        <button type="submit" class="admin-btn admin-btn--primary"><i class="fas fa-plus"></i> Create Menu</button>
                    </div>
                </form>
            </div>
        </div>
    @endcan

    <div class="admin-card">
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead><tr><th>Menu</th><th>Location</th><th>Items</th><th>Status</th><th class="admin-table__actions-head">Actions</th></tr></thead>
                <tbody>
                    @forelse ($menus as $menu)
                        <tr>
                            <td><strong>{{ $menu->name }}</strong></td>
                            <td>{{ \App\Models\Menu::LOCATIONS[$menu->location] ?? $menu->location }}</td>
                            <td>{{ $menu->items_count }}</td>
                            <td>
                                <span class="admin-badge {{ $menu->status ? 'admin-badge--active' : 'admin-badge--inactive' }}">
                                    {{ $menu->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="admin-row-actions">
                                    <a href="{{ route('admin.menus.show', $menu) }}" class="admin-icon-btn" title="Manage items"><i class="fas fa-list"></i></a>
                                    @can('menus.delete')
                                        <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" class="js-delete-form" data-confirm="Delete the {{ $menu->name }} menu and all its items?">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="admin-icon-btn admin-icon-btn--danger" title="Delete"><i class="fas fa-trash"></i></button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="admin-empty-state"><i class="fas fa-bars"></i>No menus created yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
