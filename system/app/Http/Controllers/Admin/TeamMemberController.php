<?php

namespace App\Http\Controllers\Admin;

use App\Models\TeamMember;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;

/**
 * Public-facing team profiles. Internal HR records live in EmployeeController.
 */
class TeamMemberController extends AdminResourceController
{
    protected string $model = TeamMember::class;

    protected string $route = 'team';

    protected string $permission = 'cms';

    protected string $singular = 'Team Member';

    protected string $plural = 'Website Team';

    protected string $description = 'Team profiles shown on the public team pages.';

    protected string $icon = 'fa-people-group';

    protected string $uploadDirectory = 'team';

    protected array $searchable = ['name', 'designation'];

    protected string $defaultSort = 'sort_order';

    protected string $defaultDirection = 'asc';

    protected function columns(): array
    {
        return [
            Column::image('image', 'Photo'),
            Column::text('name', 'Name'),
            Column::text('designation', 'Designation'),
            Column::text('email', 'Email', ['muted' => true]),
            Column::number('sort_order', 'Order'),
            Column::boolean('status', 'Status', ['true' => 'Active', 'false' => 'Inactive']),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::heading('Profile'),
            Field::text('name', 'Name', ['required' => true]),
            Field::text('designation', 'Designation', ['required' => true]),
            Field::email('email', 'Email'),
            Field::tel('phone', 'Phone'),
            Field::file('image', 'Photo', ['accept' => 'image/*']),
            Field::textarea('bio', 'Biography', ['rows' => 5]),

            Field::heading('Social Links'),
            Field::url('linkedin_url', 'LinkedIn'),
            Field::url('facebook_url', 'Facebook'),
            Field::url('instagram_url', 'Instagram'),
            Field::url('github_url', 'GitHub'),

            Field::heading('Display'),
            Field::number('sort_order', 'Sort Order'),
            Field::checkbox('status', 'Active', ['help' => 'Visible on the public website.']),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'status', 'label' => 'Status', 'type' => 'boolean', 'options' => ['1' => 'Active', '0' => 'Inactive']],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'status' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function toggleStatus(TeamMember $teamMember): RedirectResponse
    {
        $this->authorizeAction('edit');

        $teamMember->update(['status' => ! $teamMember->status]);

        return back()->with('success', 'Team member status updated successfully.');
    }
}
