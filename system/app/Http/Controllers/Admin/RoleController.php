<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $this->authorize('roles.view');

        return view('admin.roles.index', [
            'roles' => Role::withCount(['permissions', 'admins'])->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('roles.create');

        return view('admin.roles.create', [
            'role' => new Role,
            'groups' => config('permissions'),
            'assigned' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('roles.create');

        $data = $this->validateRole($request);

        $role = Role::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
        ]);

        $role->permissions()->sync($this->permissionIds($data['permissions'] ?? []));

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role): View
    {
        $this->authorize('roles.edit');

        return view('admin.roles.edit', [
            'role' => $role,
            'groups' => config('permissions'),
            'assigned' => $role->permissions()->pluck('slug')->all(),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $this->authorize('roles.edit');

        $data = $this->validateRole($request, $role);

        $role->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        // The super admin role always keeps every permission.
        if (! $role->isSuperAdmin()) {
            $role->permissions()->sync($this->permissionIds($data['permissions'] ?? []));
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('roles.delete');

        if ($role->is_locked || $role->isSuperAdmin()) {
            return back()->with('error', 'The Super Admin role cannot be deleted.');
        }

        if ($role->admins()->exists()) {
            return back()->with('error', 'Reassign the users on this role before deleting it.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }

    /** @return array<string, mixed> */
    protected function validateRole(Request $request, ?Role $role = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('roles', 'name')->ignore($role?->getKey())->whereNull('deleted_at')],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,slug'],
        ]);
    }

    /**
     * @param  array<int, string>  $slugs
     * @return array<int, int>
     */
    protected function permissionIds(array $slugs): array
    {
        return Permission::whereIn('slug', $slugs)->pluck('id')->all();
    }
}
