<div style="display:flex; gap:8px; align-items:center; padding: 10px 0 10px {{ $depth * 26 }}px; border-bottom: 1px solid var(--admin-border);">
    <form action="{{ route('admin.menus.items.update', [$menu, $item->id]) }}" method="POST" style="flex:1; min-width:0;">
        @csrf @method('PUT')

        <div style="display:grid; grid-template-columns: minmax(0,1.4fr) minmax(0,1.6fr) 130px 90px 46px 40px; gap:8px; align-items:center;">
            <input type="text" name="label" class="admin-input" value="{{ $item->label }}" required placeholder="Label">

            <input type="text" name="url" class="admin-input" value="{{ $item->url }}" placeholder="/page.html">

            <select name="parent_id" class="admin-select">
                <option value="">Top level</option>
                @foreach ($parentOptions as $id => $label)
                    @continue($id === $item->id)
                    <option value="{{ $id }}" @selected($item->parent_id === $id)>{{ $label }}</option>
                @endforeach
            </select>

            <input type="number" name="sort_order" class="admin-input" value="{{ $item->sort_order }}" min="0" title="Sort order">

            <label class="admin-checkbox-row" style="margin:0" title="Visible on the website">
                <input type="checkbox" name="status" value="1" @checked($item->status)>
            </label>

            <input type="hidden" name="target" value="{{ $item->target }}">
            <input type="hidden" name="icon" value="{{ $item->icon }}">

            <button type="submit" class="admin-icon-btn" title="Save"><i class="fas fa-check"></i></button>
        </div>
    </form>

    @can('menus.delete')
        <form action="{{ route('admin.menus.items.destroy', [$menu, $item->id]) }}" method="POST"
              class="js-delete-form" data-confirm="Delete the {{ $item->label }} menu item?">
            @csrf @method('DELETE')
            <button type="submit" class="admin-icon-btn admin-icon-btn--danger" title="Delete"><i class="fas fa-trash"></i></button>
        </form>
    @endcan
</div>
