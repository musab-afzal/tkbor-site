<?php

/*
|--------------------------------------------------------------------------
| Admin Permission Map
|--------------------------------------------------------------------------
| Single source of truth for the granular permissions available in the panel.
| The seeder builds `permissions` rows from this map and the roles screen
| renders its checkbox groups from it, so adding a permission here is all that
| is required to expose it everywhere.
|
| Format: group label => [ module key => [label, actions] ]
| Permission slug is "{module}.{action}", e.g. leads.create.
*/

return [

    'Sales & CRM' => [
        'leads' => ['label' => 'Leads', 'actions' => ['view', 'create', 'edit', 'delete', 'convert']],
        'clients' => ['label' => 'Clients', 'actions' => ['view', 'create', 'edit', 'delete']],
        'follow_ups' => ['label' => 'Follow Ups', 'actions' => ['view', 'create', 'edit', 'delete']],
        'meetings' => ['label' => 'Meetings', 'actions' => ['view', 'create', 'edit', 'delete']],
        'quotations' => ['label' => 'Quotations', 'actions' => ['view', 'create', 'edit', 'delete', 'approve', 'view_cost']],
        'contracts' => ['label' => 'Contracts', 'actions' => ['view', 'create', 'edit', 'delete']],
        'inquiries' => ['label' => 'Website Inquiries', 'actions' => ['view', 'create', 'edit', 'delete', 'convert']],
    ],

    'Projects' => [
        'projects' => ['label' => 'Projects', 'actions' => ['view', 'create', 'edit', 'delete']],
        'milestones' => ['label' => 'Milestones', 'actions' => ['view', 'create', 'edit', 'delete']],
        'requirements' => ['label' => 'Requirements / SRS', 'actions' => ['view', 'create', 'edit', 'delete']],
        'change_requests' => ['label' => 'Change Requests', 'actions' => ['view', 'create', 'edit', 'delete']],
        'bugs' => ['label' => 'Bugs / Issues', 'actions' => ['view', 'create', 'edit', 'delete']],
        'timesheets' => ['label' => 'Timesheets', 'actions' => ['view', 'create', 'edit', 'delete', 'approve']],
    ],

    'Tasks' => [
        'tasks' => ['label' => 'Tasks', 'actions' => ['view', 'create', 'edit', 'delete', 'assign']],
        'todos' => ['label' => 'Company TODO', 'actions' => ['view', 'create', 'edit', 'delete']],
    ],

    'Pricing' => [
        'pricing' => ['label' => 'Service Pricing', 'actions' => ['view', 'create', 'edit', 'delete', 'view_cost']],
        'pricing_categories' => ['label' => 'Pricing Categories', 'actions' => ['view', 'create', 'edit', 'delete']],
        'packages' => ['label' => 'Packages', 'actions' => ['view', 'create', 'edit', 'delete']],
    ],

    'Finance' => [
        'invoices' => ['label' => 'Invoices', 'actions' => ['view', 'create', 'edit', 'delete']],
        'payments' => ['label' => 'Payments', 'actions' => ['view', 'create', 'edit', 'delete']],
        'expenses' => ['label' => 'Expenses', 'actions' => ['view', 'create', 'edit', 'delete']],
        'expense_categories' => ['label' => 'Expense Categories', 'actions' => ['view', 'create', 'edit', 'delete']],
        'recurring_payments' => ['label' => 'Recurring Payments', 'actions' => ['view', 'create', 'edit', 'delete']],
        'finance_reports' => ['label' => 'Financial Reports', 'actions' => ['view', 'export']],
    ],

    'Digital Marketing' => [
        'campaigns' => ['label' => 'Campaigns', 'actions' => ['view', 'create', 'edit', 'delete']],
        'seo_projects' => ['label' => 'SEO Projects', 'actions' => ['view', 'create', 'edit', 'delete']],
        'keywords' => ['label' => 'Keywords', 'actions' => ['view', 'create', 'edit', 'delete']],
        'social_posts' => ['label' => 'Social Media Calendar', 'actions' => ['view', 'create', 'edit', 'delete']],
    ],

    'Support' => [
        'tickets' => ['label' => 'Tickets', 'actions' => ['view', 'create', 'edit', 'delete', 'reply']],
        'maintenance_requests' => ['label' => 'Maintenance Requests', 'actions' => ['view', 'create', 'edit', 'delete']],
        'client_approvals' => ['label' => 'Client Approvals', 'actions' => ['view', 'create', 'edit', 'delete']],
    ],

    'Team' => [
        'employees' => ['label' => 'Employees', 'actions' => ['view', 'create', 'edit', 'delete', 'view_salary']],
        'departments' => ['label' => 'Departments', 'actions' => ['view', 'create', 'edit', 'delete']],
        'attendance' => ['label' => 'Attendance', 'actions' => ['view', 'create', 'edit', 'delete']],
        'leaves' => ['label' => 'Leave', 'actions' => ['view', 'create', 'edit', 'delete', 'approve']],
        'workload' => ['label' => 'Workload', 'actions' => ['view']],
    ],

    'Website CMS' => [
        'cms' => ['label' => 'CMS Content', 'actions' => ['view', 'create', 'edit', 'delete']],
        'media' => ['label' => 'Media Library', 'actions' => ['view', 'create', 'delete']],
        'menus' => ['label' => 'Menus', 'actions' => ['view', 'create', 'edit', 'delete']],
    ],

    'SaaS' => [
        'saas' => ['label' => 'SaaS Products, Customers & Subscriptions', 'actions' => ['view', 'create', 'edit', 'delete', 'approve']],
    ],

    'Assets & Renewals' => [
        'assets' => ['label' => 'Domains, Hosting, SSL, Licenses', 'actions' => ['view', 'create', 'edit', 'delete', 'view_credentials']],
    ],

    'System' => [
        'reports' => ['label' => 'Reports', 'actions' => ['view', 'export']],
        'notifications' => ['label' => 'Notifications', 'actions' => ['view']],
        'settings' => ['label' => 'Settings', 'actions' => ['view', 'edit']],
        'users' => ['label' => 'Admin Users', 'actions' => ['view', 'create', 'edit', 'delete']],
        'roles' => ['label' => 'Roles & Permissions', 'actions' => ['view', 'create', 'edit', 'delete']],
        'audit_logs' => ['label' => 'Audit Logs', 'actions' => ['view']],
    ],

];
