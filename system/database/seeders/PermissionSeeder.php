<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Builds the permission rows from config/permissions.php and the default role
 * set. Safe to re-run: everything is updateOrCreate.
 */
class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $slugs = $this->syncPermissions();

        $superAdmin = Role::updateOrCreate(
            ['slug' => Role::SUPER_ADMIN],
            ['name' => 'Super Admin', 'description' => 'Unrestricted access to every module.', 'is_locked' => true],
        );

        // Super admin passes the gate implicitly, but syncing keeps the UI honest.
        $superAdmin->permissions()->sync(Permission::pluck('id'));

        foreach ($this->defaultRoles() as $slug => $definition) {
            $role = Role::updateOrCreate(
                ['slug' => $slug],
                ['name' => $definition['name'], 'description' => $definition['description']],
            );

            $granted = $this->expand($definition['grants'], $slugs);

            $role->permissions()->sync(Permission::whereIn('slug', $granted)->pluck('id'));
        }
    }

    /**
     * @return array<int, string> every permission slug that exists
     */
    protected function syncPermissions(): array
    {
        $slugs = [];

        foreach (config('permissions') as $group => $modules) {
            foreach ($modules as $module => $definition) {
                foreach ($definition['actions'] as $action) {
                    $slug = $module.'.'.$action;
                    $slugs[] = $slug;

                    Permission::updateOrCreate(
                        ['slug' => $slug],
                        [
                            'name' => $definition['label'].' — '.Str::headline($action),
                            'group' => $group,
                        ],
                    );
                }
            }
        }

        return $slugs;
    }

    /**
     * Expands shorthand grants. "leads.*" means every action on that module.
     *
     * @param  array<int, string>  $grants
     * @param  array<int, string>  $allSlugs
     * @return array<int, string>
     */
    protected function expand(array $grants, array $allSlugs): array
    {
        $expanded = [];

        foreach ($grants as $grant) {
            if (! str_ends_with($grant, '.*')) {
                $expanded[] = $grant;

                continue;
            }

            $prefix = substr($grant, 0, -1);

            foreach ($allSlugs as $slug) {
                if (str_starts_with($slug, $prefix)) {
                    $expanded[] = $slug;
                }
            }
        }

        return array_unique($expanded);
    }

    /**
     * @return array<string, array{name: string, description: string, grants: array<int, string>}>
     */
    protected function defaultRoles(): array
    {
        return [
            'manager' => [
                'name' => 'Manager',
                'description' => 'Runs delivery and sales. No settings, users or salary access.',
                'grants' => [
                    'leads.*', 'clients.*', 'follow_ups.*', 'meetings.*', 'quotations.*',
                    'contracts.*', 'inquiries.*', 'projects.*', 'milestones.*',
                    'requirements.*', 'change_requests.*', 'bugs.*', 'timesheets.*',
                    'tasks.*', 'todos.*', 'pricing.*', 'pricing_categories.*', 'packages.*',
                    'invoices.view', 'payments.view', 'finance_reports.view',
                    'campaigns.*', 'seo_projects.*', 'keywords.*', 'social_posts.*',
                    'tickets.*', 'maintenance_requests.*', 'client_approvals.*',
                    'employees.view', 'departments.view', 'attendance.*', 'leaves.*',
                    'workload.view', 'cms.*', 'media.*', 'menus.*', 'assets.*',
                    'reports.*', 'notifications.view',
                ],
            ],

            'sales' => [
                'name' => 'Sales Executive',
                'description' => 'Leads, clients, quotations and everything that feeds them.',
                'grants' => [
                    'leads.*', 'clients.*', 'follow_ups.*', 'meetings.*', 'inquiries.*',
                    'quotations.view', 'quotations.create', 'quotations.edit',
                    'contracts.view', 'contracts.create', 'contracts.edit',
                    'pricing.view', 'packages.view', 'projects.view',
                    'tasks.view', 'todos.*', 'notifications.view', 'reports.view',
                ],
            ],

            'developer' => [
                'name' => 'Developer',
                'description' => 'Project delivery: tasks, bugs, requirements and timesheets.',
                'grants' => [
                    'projects.view', 'milestones.view', 'requirements.view',
                    'change_requests.view', 'bugs.*',
                    'timesheets.view', 'timesheets.create', 'timesheets.edit',
                    'tasks.view', 'tasks.edit', 'todos.*',
                    'tickets.view', 'tickets.edit', 'tickets.reply',
                    'clients.view', 'notifications.view',
                ],
            ],

            'accountant' => [
                'name' => 'Accountant',
                'description' => 'Invoicing, payments, expenses and financial reporting.',
                'grants' => [
                    'invoices.*', 'payments.*', 'expenses.*', 'expense_categories.*',
                    'recurring_payments.*', 'finance_reports.*',
                    'clients.view', 'projects.view', 'quotations.view',
                    'employees.view', 'employees.view_salary',
                    'assets.view', 'reports.*', 'notifications.view',
                ],
            ],

            'marketing' => [
                'name' => 'Marketing Executive',
                'description' => 'Campaigns, SEO, social calendar and website content.',
                'grants' => [
                    'campaigns.*', 'seo_projects.*', 'keywords.*', 'social_posts.*',
                    'cms.*', 'media.*', 'menus.*',
                    'clients.view', 'leads.view', 'inquiries.view',
                    'todos.*', 'reports.view', 'notifications.view',
                ],
            ],

            'support' => [
                'name' => 'Support Agent',
                'description' => 'Tickets, maintenance requests and client approvals.',
                'grants' => [
                    'tickets.*', 'maintenance_requests.*', 'client_approvals.*',
                    'clients.view', 'projects.view', 'bugs.view', 'bugs.create',
                    'assets.view', 'todos.*', 'notifications.view',
                ],
            ],
        ];
    }
}
