<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Public website accounts that log in to the SaaS portal. Customers can also
 * register themselves; this screen lets staff create or fix an account.
 */
class SaasCustomerController extends AdminResourceController
{
    protected string $model = User::class;

    protected string $route = 'saas-customers';

    protected string $permission = 'saas';

    protected string $singular = 'SaaS Customer';

    protected string $plural = 'SaaS Customers';

    protected string $description = 'Website accounts that log in to start trials and use your software.';

    protected string $icon = 'fa-user-group';

    protected array $searchable = ['name', 'email', 'phone'];

    protected array $withCount = ['saasSubscriptions'];

    protected function columns(): array
    {
        return [
            Column::text('name', 'Name'),
            Column::text('email', 'Email'),
            Column::text('phone', 'Phone', ['muted' => true]),
            Column::number('saas_subscriptions_count', 'Software'),
            Column::datetime('created_at', 'Registered'),
        ];
    }

    protected function fields(): array
    {
        $editing = $this->currentRecord?->exists;

        return [
            Field::text('name', 'Name', ['required' => true]),
            Field::email('email', 'Email (login)', ['required' => true]),
            Field::tel('phone', 'Phone'),
            Field::password('password', 'Password', [
                'required' => ! $editing,
                'help' => $editing ? 'Leave blank to keep the current password.' : 'At least 8 characters.',
            ]),
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($record?->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => [$record ? 'nullable' : 'required', 'string', Password::min(8)],
        ];
    }

    protected function mutateData(array $data, Request $request, Model $record): array
    {
        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        return $data;
    }
}
