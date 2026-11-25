@extends('admin.layouts.admin')

@section('title', $menu->name)
@section('page-title', 'Menu: '.$menu->name)

@section('content')

    <div class="admin-page-header">
        <div>
            <h1>{{ $menu->name }}</h1>
            <p>{{ \App\Models\Menu::LOCATIONS[$menu->location] ?? $menu->location }} menu</p>
        </div>
        <a href="{{ route('admin.menus.index') }}" class="admin-btn admin-btn--ghost"><i class="fas fa-arrow-left"></i> Back</a>
    </div>

    @include('admin.partials.validation-summary')

    <div class="admin-detail-grid">
        <div>
            <div class="admin-card">
                <div class="admin-card__header"><h2>Menu Items</h2></div>
                <div class="admin-card__body">
                    @forelse ($menu->rootItems as $item)
                        @include('admin.menus._item', ['item' => $item, 'depth' => 0])

                        @foreach ($item->children as $child)
                            @include('admin.menus._item', ['item' => $child, 'depth' => 1])
                        @endforeach
                    @empty
                        <div class="admin-empty-state"><i class="fas fa-link"></i>No items in this menu yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div>
            @can('menus.edit')
                <div class="admin-card">
                    <div class="admin-card__header"><h2>Add Item</h2></div>
                    <div class="admin-card__body">
                        <form action="{{ route('admin.menus.items.store', $menu) }}" method="POST">
                            @csrf
                            <div class="admin-form-grid">
                                <div class="admin-form-group admin-form-group--full">
                                    <label class="admin-label" for="label">Label</label>
                                    <input type="text" id="label" name="label" class="admin-input" required>
                                </div>
                                <div class="admin-form-group admin-form-group--full">
                                    <label class="admin-label" for="url">URL</label>
                                    <input type="text" id="url" name="url" class="admin-input" placeholder="/about.html">
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-label" for="parent_id">Parent</label>
                                    <select id="parent_id" name="parent_id" class="admin-select">
                                        <option value="">Top level</option>
                                        @foreach ($parentOptions as $id => $label)
                                            <option value="{{ $id }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-label" for="target">Opens In</label>
                                    <select id="target" name="target" class="admin-select">
                                        <option value="_self">Same tab</option>
                                        <option value="_blank">New tab</option>
                                    </select>
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-label" for="icon">Icon Class</label>
                                    <input type="text" id="icon" name="icon" class="admin-input" placeholder="fa-home">
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-label" for="sort_order">Sort Order</label>
                                    <input type="number" id="sort_order" name="sort_order" class="admin-input" value="0" min="0">
                                </div>
                            </div>
                            <div class="admin-form-actions">
                                <button type="submit" class="admin-btn admin-btn--primary admin-btn--sm"><i class="fas fa-plus"></i> Add Item</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="admin-card">
                    <div class="admin-card__header"><h2>Menu Settings</h2></div>
                    <div class="admin-card__body">
                        <form action="{{ route('admin.menus.update', $menu) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="admin-form-grid">
                                <div class="admin-form-group admin-form-group--full">
                                    <label class="admin-label" for="menu_name">Name</label>
                                    <input type="text" id="menu_name" name="name" class="admin-input" value="{{ $menu->name }}" required>
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-label" for="menu_location">Location</label>
                                    <select id="menu_location" name="location" class="admin-select" required>
                                        @foreach (\App\Models\Menu::LOCATIONS as $value => $label)
                                            <option value="{{ $value }}" @selected($menu->location === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-label">Status</label>
                                    <div class="admin-checkbox-row">
                                        <input type="checkbox" id="menu_status" name="status" value="1" @checked($menu->status)>
                                        <label for="menu_status" class="admin-checkbox-label">Active</label>
                                    </div>
                                </div>
                            </div>
                            <div class="admin-form-actions">
                                <button type="submit" class="admin-btn admin-btn--ghost admin-btn--sm"><i class="fas fa-check"></i> Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endcan
        </div>
    </div>

@endsection
