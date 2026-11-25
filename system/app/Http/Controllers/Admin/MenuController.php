<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(): View
    {
        $this->authorize('menus.view');

        return view('admin.menus.index', [
            'menus' => Menu::withCount('items')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('menus.create');

        Menu::create($request->validate([
            'name' => ['required', 'string', 'max:150'],
            'location' => ['required', Rule::in(array_keys(Menu::LOCATIONS))],
        ]) + ['status' => true]);

        return back()->with('success', 'Menu created successfully.');
    }

    public function show(Menu $menu): View
    {
        $this->authorize('menus.view');

        $menu->load('rootItems.children');

        return view('admin.menus.show', [
            'menu' => $menu,
            'parentOptions' => $menu->items()->whereNull('parent_id')->pluck('label', 'id'),
        ]);
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $this->authorize('menus.edit');

        $menu->update($request->validate([
            'name' => ['required', 'string', 'max:150'],
            'location' => ['required', Rule::in(array_keys(Menu::LOCATIONS))],
            'status' => ['boolean'],
        ]) + ['status' => $request->boolean('status')]);

        return back()->with('success', 'Menu updated successfully.');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $this->authorize('menus.delete');

        $menu->delete();

        return redirect()->route('admin.menus.index')->with('success', 'Menu deleted successfully.');
    }

    public function storeItem(Request $request, Menu $menu): RedirectResponse
    {
        $this->authorize('menus.edit');

        $data = $request->validate([
            'label' => ['required', 'string', 'max:150'],
            'url' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:menu_items,id'],
            'target' => ['required', Rule::in(['_self', '_blank'])],
            'icon' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $menu->items()->create($data + ['status' => true]);

        return back()->with('success', 'Menu item added.');
    }

    public function updateItem(Request $request, Menu $menu, MenuItem $item): RedirectResponse
    {
        $this->authorize('menus.edit');

        abort_unless($item->menu_id === $menu->id, 404);

        $data = $request->validate([
            'label' => ['required', 'string', 'max:150'],
            'url' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:menu_items,id'],
            'target' => ['required', Rule::in(['_self', '_blank'])],
            'icon' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['boolean'],
        ]);

        // An item cannot be its own parent.
        if ((int) ($data['parent_id'] ?? 0) === $item->id) {
            $data['parent_id'] = null;
        }

        $item->update($data + ['status' => $request->boolean('status')]);

        return back()->with('success', 'Menu item updated.');
    }

    public function destroyItem(Menu $menu, MenuItem $item): RedirectResponse
    {
        $this->authorize('menus.delete');

        abort_unless($item->menu_id === $menu->id, 404);

        $item->delete();

        return back()->with('success', 'Menu item deleted.');
    }
}
