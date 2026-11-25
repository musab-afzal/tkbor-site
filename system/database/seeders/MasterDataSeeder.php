<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Department;
use App\Models\Employee;
use App\Models\ExpenseCategory;
use App\Models\LeaveType;
use App\Models\PricingCategory;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Reference data the panel needs before it is usable: departments, expense and
 * pricing categories, leave types, and a super-admin account linked to the
 * existing admin login.
 */
class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->attachExistingAdminsToSuperAdmin();
        $this->seedDepartments();
        $this->seedExpenseCategories();
        $this->seedPricingCategories();
        $this->seedLeaveTypes();
        $this->ensureEmployeeForAdmins();
    }

    /** Existing admins predate roles, so give them full access rather than none. */
    protected function attachExistingAdminsToSuperAdmin(): void
    {
        $superAdmin = Role::where('slug', Role::SUPER_ADMIN)->first();

        if (! $superAdmin) {
            return;
        }

        Admin::whereNull('role_id')->update(['role_id' => $superAdmin->id]);
    }

    protected function seedDepartments(): void
    {
        $departments = [
            'Management' => 'Company leadership and operations.',
            'Development' => 'Web, mobile and backend engineering.',
            'Design' => 'UI, UX, branding and visual design.',
            'Digital Marketing' => 'SEO, ads, social and content.',
            'Sales' => 'Lead generation, quotations and client relations.',
            'Support' => 'Client support, maintenance and hosting.',
            'Accounts' => 'Invoicing, payments and bookkeeping.',
        ];

        foreach ($departments as $name => $description) {
            Department::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'description' => $description],
            );
        }
    }

    protected function seedExpenseCategories(): void
    {
        $categories = [
            'Salaries', 'Hosting & Servers', 'Domains', 'Software & Licenses',
            'Subscriptions', 'Marketing & Ads', 'Office Rent', 'Utilities',
            'Equipment', 'Travel', 'Outsourcing', 'Bank Charges', 'Miscellaneous',
        ];

        foreach ($categories as $name) {
            ExpenseCategory::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name],
            );
        }
    }

    protected function seedPricingCategories(): void
    {
        $categories = [
            'Web Development', 'Mobile Development', 'UI/UX Design',
            'Branding', 'Digital Marketing', 'SEO', 'Hosting & Maintenance',
            'Content & Copywriting', 'E-Commerce',
        ];

        foreach ($categories as $index => $name) {
            PricingCategory::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'sort_order' => $index],
            );
        }
    }

    protected function seedLeaveTypes(): void
    {
        $types = [
            ['name' => 'Annual Leave', 'days_allowed' => 20, 'is_paid' => true],
            ['name' => 'Sick Leave', 'days_allowed' => 10, 'is_paid' => true],
            ['name' => 'Casual Leave', 'days_allowed' => 8, 'is_paid' => true],
            ['name' => 'Unpaid Leave', 'days_allowed' => 0, 'is_paid' => false],
            ['name' => 'Maternity / Paternity', 'days_allowed' => 30, 'is_paid' => true],
        ];

        foreach ($types as $type) {
            LeaveType::firstOrCreate(
                ['slug' => Str::slug($type['name'])],
                $type,
            );
        }
    }

    /**
     * Every admin needs an employee record so tasks, leads and timesheets can be
     * assigned to them and "My Tasks" resolves.
     */
    protected function ensureEmployeeForAdmins(): void
    {
        $management = Department::where('slug', 'management')->first();

        foreach (Admin::whereDoesntHave('employee')->get() as $admin) {
            Employee::create([
                'employee_code' => $this->nextEmployeeCode(),
                'admin_id' => $admin->id,
                'department_id' => $management?->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'designation' => 'Administrator',
                'joining_date' => $admin->created_at?->toDateString() ?? now()->toDateString(),
                'status' => Employee::STATUS_ACTIVE,
            ]);
        }
    }

    protected function nextEmployeeCode(): string
    {
        $last = Employee::withTrashed()->orderByDesc('id')->value('employee_code');
        $sequence = $last ? (int) Str::afterLast($last, '-') : 0;

        return 'EMP-'.str_pad((string) ($sequence + 1), 4, '0', STR_PAD_LEFT);
    }
}
