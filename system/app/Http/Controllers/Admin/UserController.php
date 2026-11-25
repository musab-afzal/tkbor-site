<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\Role;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends AdminResourceController
{
    protected string $model = Admin::class;

    protected string $route = 'users';

    protected string $permission = 'users';

    protected string $singular = 'User';

    protected string $plural = 'Admin Users';

    protected string $description = 'Who can sign in to this panel and what they are allowed to do.';

    protected string $icon = 'fa-user-shield';

    protected string $uploadDirectory = 'avatars';

    protected array $searchable = ['name', 'email'];

    protected array $with = ['role', 'employee'];

    protected function columns(): array
    {
        return [
            Column::image('avatar', 'Avatar'),
            Column::text('name', 'Name'),
            Column::text('email', 'Email'),
            Column::relation('role.name', 'Role'),
            Column::relation('employee.name', 'Employee Record'),
            Column::datetime('last_login_at', 'Last Login'),
            Column::boolean('status', 'Status', ['true' => 'Active', 'false' => 'Disabled']),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::text('name', 'Full Name', ['required' => true]),
            Field::email('email', 'Email', ['required' => true]),
            Field::select('role_id', 'Role', $this->roleOptions(), ['required' => true]),
            Field::tel('phone', 'Phone'),
            Field::file('avatar', 'Avatar', ['accept' => 'image/*']),
            Field::password('password', 'Password', [
                'help' => $this->currentRecord?->exists
                    ? 'Leave blank to keep the current password.'
                    : 'Minimum 8 characters.',
            ]),
            Field::password('password_confirmation', 'Confirm Password'),
            Field::checkbox('status', 'Active', ['help' => 'Disabled users cannot sign in.']),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'role_id', 'label' => 'Role', 'options' => $this->roleOptions()],
            ['key' => 'status', 'label' => 'Status', 'type' => 'boolean', 'options' => ['1' => 'Active', '0' => 'Disabled']],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('admins', 'email')->ignore($record?->getKey())->whereNull('deleted_at')],
            'role_id' => ['required', 'exists:roles,id'],
            'phone' => ['nullable', 'string', 'max:50'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'password' => [$record?->exists ? 'nullable' : 'required', 'confirmed', Password::min(8)],
            'password_confirmation' => ['nullable', 'string'],
            'status' => ['boolean'],
        ];
    }

    /** An empty password on edit must not blank out the stored hash. */
    protected function mutateData(array $data, Request $request, Model $record): array
    {
        unset($data['password_confirmation']);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        return $data;
    }

    public function destroy(): RedirectResponse
    {
        $this->authorizeAction('delete');

        $user = $this->resolveRecord();

        if ($user->getKey() === auth('admin')->id()) {
            return back()->with('error', 'You cannot delete the account you are signed in with.');
        }

        return $this->deleteRecord($user);
    }

    /** @return array<int, string> */
    protected function roleOptions(): array
    {
        return Role::orderBy('name')->pluck('name', 'id')->all();
    }
}
