@php
    /**
     * Menu definition. Each group renders as a collapsible section using the
     * existing sidebar styling — only the structure changed, not the theme.
     * Items are hidden when the signed-in admin lacks the permission.
     */
    $menu = [
        [
            'label' => 'Sales & CRM',
            'icon' => 'fa-handshake-angle',
            'match' => ['admin.leads.*', 'admin.clients.*', 'admin.follow-ups.*', 'admin.meetings.*', 'admin.quotations.*', 'admin.contracts.*', 'admin.inquiries.*'],
            'items' => [
                ['label' => 'Leads', 'route' => 'admin.leads.index', 'active' => 'admin.leads.*', 'can' => 'leads.view'],
                ['label' => 'Clients', 'route' => 'admin.clients.index', 'active' => 'admin.clients.*', 'can' => 'clients.view'],
                ['label' => 'Follow Ups', 'route' => 'admin.follow-ups.index', 'active' => 'admin.follow-ups.*', 'can' => 'follow_ups.view'],
                ['label' => 'Meetings', 'route' => 'admin.meetings.index', 'active' => 'admin.meetings.*', 'can' => 'meetings.view'],
                ['label' => 'Quotations', 'route' => 'admin.quotations.index', 'active' => 'admin.quotations.*', 'can' => 'quotations.view'],
                ['label' => 'Contracts', 'route' => 'admin.contracts.index', 'active' => 'admin.contracts.*', 'can' => 'contracts.view'],
                ['label' => 'Website Inquiries', 'route' => 'admin.inquiries.index', 'active' => 'admin.inquiries.*', 'can' => 'inquiries.view'],
            ],
        ],
        [
            'label' => 'Projects',
            'icon' => 'fa-diagram-project',
            'match' => ['admin.projects.*', 'admin.milestones.*', 'admin.requirements.*', 'admin.change-requests.*', 'admin.bugs.*', 'admin.timesheets.*'],
            'items' => [
                ['label' => 'All Projects', 'route' => 'admin.projects.index', 'active' => 'admin.projects.*', 'can' => 'projects.view'],
                ['label' => 'Milestones', 'route' => 'admin.milestones.index', 'active' => 'admin.milestones.*', 'can' => 'milestones.view'],
                ['label' => 'Requirements / SRS', 'route' => 'admin.requirements.index', 'active' => 'admin.requirements.*', 'can' => 'requirements.view'],
                ['label' => 'Change Requests', 'route' => 'admin.change-requests.index', 'active' => 'admin.change-requests.*', 'can' => 'change_requests.view'],
                ['label' => 'Bugs / Issues', 'route' => 'admin.bugs.index', 'active' => 'admin.bugs.*', 'can' => 'bugs.view'],
                ['label' => 'Timesheets', 'route' => 'admin.timesheets.index', 'active' => 'admin.timesheets.*', 'can' => 'timesheets.view'],
            ],
        ],
        [
            'label' => 'Task Management',
            'icon' => 'fa-list-check',
            'match' => ['admin.tasks.*', 'admin.todos.*'],
            'items' => [
                ['label' => 'All Tasks', 'route' => 'admin.tasks.index', 'active' => 'admin.tasks.index', 'can' => 'tasks.view'],
                ['label' => 'Task Board', 'route' => 'admin.tasks.board', 'active' => 'admin.tasks.board', 'can' => 'tasks.view'],
                ['label' => 'My Tasks', 'route' => 'admin.tasks.mine', 'active' => 'admin.tasks.mine', 'can' => 'tasks.view'],
                ['label' => 'Assigned Tasks', 'route' => 'admin.tasks.assigned', 'active' => 'admin.tasks.assigned', 'can' => 'tasks.view'],
                ['label' => 'Tasks Due Today', 'route' => 'admin.tasks.due-today', 'active' => 'admin.tasks.due-today', 'can' => 'tasks.view'],
                ['label' => 'Upcoming', 'route' => 'admin.tasks.upcoming', 'active' => 'admin.tasks.upcoming', 'can' => 'tasks.view'],
                ['label' => 'Overdue', 'route' => 'admin.tasks.overdue', 'active' => 'admin.tasks.overdue', 'can' => 'tasks.view'],
                ['label' => 'Company TODO', 'route' => 'admin.todos.index', 'active' => 'admin.todos.*', 'can' => 'todos.view'],
            ],
        ],
        [
            'label' => 'Finance',
            'icon' => 'fa-sack-dollar',
            'match' => ['admin.invoices.*', 'admin.payments.*', 'admin.expenses.*', 'admin.expense-categories.*', 'admin.recurring-payments.*', 'admin.finance.*'],
            'items' => [
                ['label' => 'Invoices', 'route' => 'admin.invoices.index', 'active' => 'admin.invoices.*', 'can' => 'invoices.view'],
                ['label' => 'Payments', 'route' => 'admin.payments.index', 'active' => 'admin.payments.*', 'can' => 'payments.view'],
                ['label' => 'Expenses', 'route' => 'admin.expenses.index', 'active' => 'admin.expenses.*', 'can' => 'expenses.view'],
                ['label' => 'Expense Categories', 'route' => 'admin.expense-categories.index', 'active' => 'admin.expense-categories.*', 'can' => 'expense_categories.view'],
                ['label' => 'Recurring Payments', 'route' => 'admin.recurring-payments.index', 'active' => 'admin.recurring-payments.*', 'can' => 'recurring_payments.view'],
                ['label' => 'Financial Reports', 'route' => 'admin.finance.reports', 'active' => 'admin.finance.*', 'can' => 'finance_reports.view'],
            ],
        ],
        [
            'label' => 'Pricing',
            'icon' => 'fa-tags',
            'match' => ['admin.pricing.*', 'admin.pricing-categories.*', 'admin.packages.*'],
            'items' => [
                ['label' => 'Service Pricing', 'route' => 'admin.pricing.index', 'active' => 'admin.pricing.*', 'can' => 'pricing.view'],
                ['label' => 'Pricing Categories', 'route' => 'admin.pricing-categories.index', 'active' => 'admin.pricing-categories.*', 'can' => 'pricing_categories.view'],
                ['label' => 'Packages', 'route' => 'admin.packages.index', 'active' => 'admin.packages.*', 'can' => 'packages.view'],
            ],
        ],
        [
            'label' => 'Digital Marketing',
            'icon' => 'fa-bullhorn',
            'match' => ['admin.campaigns.*', 'admin.seo-projects.*', 'admin.keywords.*', 'admin.social-posts.*', 'admin.marketing.*'],
            'items' => [
                ['label' => 'Campaigns', 'route' => 'admin.campaigns.index', 'active' => 'admin.campaigns.*', 'can' => 'campaigns.view'],
                ['label' => 'SEO Projects', 'route' => 'admin.seo-projects.index', 'active' => 'admin.seo-projects.*', 'can' => 'seo_projects.view'],
                ['label' => 'Keywords', 'route' => 'admin.keywords.index', 'active' => 'admin.keywords.*', 'can' => 'keywords.view'],
                ['label' => 'Social Media Calendar', 'route' => 'admin.social-posts.index', 'active' => 'admin.social-posts.*', 'can' => 'social_posts.view'],
                ['label' => 'Marketing Reports', 'route' => 'admin.marketing.reports', 'active' => 'admin.marketing.*', 'can' => 'campaigns.view'],
            ],
        ],
        [
            'label' => 'Support',
            'icon' => 'fa-headset',
            'match' => ['admin.tickets.*', 'admin.maintenance-requests.*', 'admin.client-approvals.*'],
            'items' => [
                ['label' => 'Tickets', 'route' => 'admin.tickets.index', 'active' => 'admin.tickets.*', 'can' => 'tickets.view'],
                ['label' => 'Maintenance Requests', 'route' => 'admin.maintenance-requests.index', 'active' => 'admin.maintenance-requests.*', 'can' => 'maintenance_requests.view'],
                ['label' => 'Client Approvals', 'route' => 'admin.client-approvals.index', 'active' => 'admin.client-approvals.*', 'can' => 'client_approvals.view'],
            ],
        ],
        [
            'label' => 'Team',
            'icon' => 'fa-users',
            'match' => ['admin.employees.*', 'admin.departments.*', 'admin.attendance.*', 'admin.leaves.*', 'admin.workload.*'],
            'items' => [
                ['label' => 'Employees', 'route' => 'admin.employees.index', 'active' => 'admin.employees.*', 'can' => 'employees.view'],
                ['label' => 'Departments', 'route' => 'admin.departments.index', 'active' => 'admin.departments.*', 'can' => 'departments.view'],
                ['label' => 'Attendance', 'route' => 'admin.attendance.index', 'active' => 'admin.attendance.*', 'can' => 'attendance.view'],
                ['label' => 'Leave', 'route' => 'admin.leaves.index', 'active' => 'admin.leaves.*', 'can' => 'leaves.view'],
                ['label' => 'Workload', 'route' => 'admin.workload.index', 'active' => 'admin.workload.*', 'can' => 'workload.view'],
            ],
        ],
        [
            'label' => 'Website CMS',
            'icon' => 'fa-globe',
            'match' => ['admin.sliders.*', 'admin.content-blocks.*', 'admin.services.*', 'admin.packages.*', 'admin.portfolios.*', 'admin.case-studies.*', 'admin.blog.*', 'admin.faqs.*', 'admin.reviews.*', 'admin.team.*', 'admin.careers.*', 'admin.pages.*', 'admin.media.*', 'admin.menus.*', 'admin.companies.*'],
            'items' => [
                ['label' => 'Slider', 'route' => 'admin.sliders.index', 'active' => 'admin.sliders.*', 'can' => 'cms.view'],
                ['label' => 'Services', 'route' => 'admin.services.index', 'active' => 'admin.services.*', 'can' => 'cms.view'],
                ['label' => 'Packages', 'route' => 'admin.packages.index', 'active' => 'admin.packages.*', 'can' => 'packages.view'],
                ['label' => 'Portfolio', 'route' => 'admin.portfolios.index', 'active' => 'admin.portfolios.*', 'can' => 'cms.view'],
                ['label' => 'Case Studies', 'route' => 'admin.case-studies.index', 'active' => 'admin.case-studies.*', 'can' => 'cms.view'],
                ['label' => 'Blogs', 'route' => 'admin.blog.index', 'active' => 'admin.blog.*', 'can' => 'cms.view'],
                ['label' => 'FAQs', 'route' => 'admin.faqs.index', 'active' => 'admin.faqs.*', 'can' => 'cms.view'],
                ['label' => 'Testimonials', 'route' => 'admin.reviews.index', 'active' => 'admin.reviews.*', 'can' => 'cms.view'],
                ['label' => 'Team', 'route' => 'admin.team.index', 'active' => 'admin.team.*', 'can' => 'cms.view'],
                ['label' => 'Companies', 'route' => 'admin.companies.index', 'active' => 'admin.companies.*', 'can' => 'cms.view'],
                ['label' => 'Careers', 'route' => 'admin.careers.index', 'active' => 'admin.careers.*', 'can' => 'cms.view'],
                ['label' => 'Pages', 'route' => 'admin.pages.index', 'active' => 'admin.pages.*', 'can' => 'cms.view'],
                ['label' => 'Homepage Stats', 'route' => 'admin.homepage-stats.index', 'active' => 'admin.homepage-stats.*', 'can' => 'cms.view'],
                ['label' => 'Content Blocks', 'route' => 'admin.content-blocks.index', 'active' => 'admin.content-blocks.*', 'can' => 'cms.view'],
                ['label' => 'Contact Messages', 'route' => 'admin.inquiries.index', 'active' => 'admin.contact-messages.*', 'can' => 'inquiries.view'],
                ['label' => 'Media', 'route' => 'admin.media.index', 'active' => 'admin.media.*', 'can' => 'media.view'],
                ['label' => 'Menus', 'route' => 'admin.menus.index', 'active' => 'admin.menus.*', 'can' => 'menus.view'],
            ],
        ],
        [
            'label' => 'SaaS',
            'icon' => 'fa-cloud',
            'match' => ['admin.saas-products.*', 'admin.saas-customers.*', 'admin.saas-subscriptions.*'],
            'items' => [
                ['label' => 'Software Products', 'route' => 'admin.saas-products.index', 'active' => 'admin.saas-products.*', 'can' => 'saas.view'],
                ['label' => 'Subscriptions & Trials', 'route' => 'admin.saas-subscriptions.index', 'active' => 'admin.saas-subscriptions.*', 'can' => 'saas.view'],
                ['label' => 'Customers', 'route' => 'admin.saas-customers.index', 'active' => 'admin.saas-customers.*', 'can' => 'saas.view'],
            ],
        ],
        [
            'label' => 'Assets & Renewals',
            'icon' => 'fa-server',
            'match' => ['admin.assets.*'],
            'items' => [
                ['label' => 'All Assets', 'route' => 'admin.assets.index', 'active' => 'admin.assets.index', 'can' => 'assets.view'],
                ['label' => 'Domains', 'route' => 'admin.assets.type', 'params' => ['type' => 'domain'], 'active' => null, 'can' => 'assets.view'],
                ['label' => 'Hosting', 'route' => 'admin.assets.type', 'params' => ['type' => 'hosting'], 'active' => null, 'can' => 'assets.view'],
                ['label' => 'SSL', 'route' => 'admin.assets.type', 'params' => ['type' => 'ssl'], 'active' => null, 'can' => 'assets.view'],
                ['label' => 'Licenses', 'route' => 'admin.assets.type', 'params' => ['type' => 'license'], 'active' => null, 'can' => 'assets.view'],
                ['label' => 'Subscriptions', 'route' => 'admin.assets.type', 'params' => ['type' => 'subscription'], 'active' => null, 'can' => 'assets.view'],
            ],
        ],
        [
            'label' => 'Settings',
            'icon' => 'fa-gear',
            'match' => ['admin.settings.*', 'admin.users.*', 'admin.roles.*', 'admin.audit-logs.*'],
            'items' => [
                ['label' => 'General Settings', 'route' => 'admin.settings.edit', 'params' => ['group' => 'general'], 'active' => 'admin.settings.*', 'can' => 'settings.view'],
                ['label' => 'Website Settings', 'route' => 'admin.settings.edit', 'params' => ['group' => 'website'], 'active' => null, 'can' => 'settings.view'],
                ['label' => 'Document Settings', 'route' => 'admin.settings.edit', 'params' => ['group' => 'documents'], 'active' => null, 'can' => 'settings.view'],
                ['label' => 'Bank & Payment', 'route' => 'admin.settings.edit', 'params' => ['group' => 'bank'], 'active' => null, 'can' => 'settings.view'],
                ['label' => 'SEO Settings', 'route' => 'admin.settings.edit', 'params' => ['group' => 'seo'], 'active' => null, 'can' => 'settings.view'],
                ['label' => 'Users', 'route' => 'admin.users.index', 'active' => 'admin.users.*', 'can' => 'users.view'],
                ['label' => 'Roles & Permissions', 'route' => 'admin.roles.index', 'active' => 'admin.roles.*', 'can' => 'roles.view'],
                ['label' => 'Audit Logs', 'route' => 'admin.audit-logs.index', 'active' => 'admin.audit-logs.*', 'can' => 'audit_logs.view'],
            ],
        ],
    ];
@endphp

<aside class="admin-sidebar">
    @php
        // The sidebar is dark, so prefer the light artwork when one is uploaded.
        $agencyName = settings('agency_name', config('app.name'));
        $brandLogo = settings('agency_wordmark_light') ?: settings('agency_logo_light') ?: settings('agency_logo');
    @endphp

    <a href="{{ route('admin.dashboard') }}" class="admin-sidebar__brand {{ $brandLogo ? 'admin-sidebar__brand--logo' : '' }}">
        @if ($brandLogo)
            <img src="{{ uploaded_url($brandLogo) }}" alt="{{ $agencyName }}" class="admin-sidebar__brand-image">
            <span class="admin-sidebar__brand-caption">Admin Console</span>
        @else
            <div class="admin-sidebar__brand-mark">{{ strtoupper(substr($agencyName, 0, 1)) }}</div>
            <div class="admin-sidebar__brand-text">
                <strong>{{ strtoupper($agencyName) }}</strong>
                <span>Admin Console</span>
            </div>
        @endif
    </a>

    <nav>
        <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard*') ? 'is-active' : '' }}">
            <i class="fas fa-gauge-high"></i> Dashboard
        </a>

        @foreach ($menu as $group)
            @php
                $visibleItems = collect($group['items'])->filter(fn ($item) => auth('admin')->user()?->hasPermission($item['can']));
            @endphp

            @continue($visibleItems->isEmpty())

            <div class="admin-nav-group" data-group="{{ \Illuminate\Support\Str::slug($group['label']) }}">
                <button type="button" class="admin-nav-link admin-nav-group__toggle">
                    <i class="fas {{ $group['icon'] }}"></i> {{ $group['label'] }}
                    <i class="fas fa-chevron-right admin-nav-group__caret"></i>
                </button>

                <div class="admin-nav-sub">
                    @foreach ($visibleItems as $item)
                        <a href="{{ route($item['route'], $item['params'] ?? []) }}"
                           class="admin-nav-link {{ ! empty($item['active']) && request()->routeIs($item['active']) ? 'is-active' : '' }}">
                            <i class="fas fa-circle"></i> {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach

        @can('reports.view')
            <a href="{{ route('admin.reports.index') }}" class="admin-nav-link {{ request()->routeIs('admin.reports.*') ? 'is-active' : '' }}">
                <i class="fas fa-chart-line"></i> Reports
            </a>
        @endcan

        <a href="{{ route('admin.notifications.index') }}" class="admin-nav-link {{ request()->routeIs('admin.notifications.*') ? 'is-active' : '' }}">
            <i class="fas fa-bell"></i> Notifications
            @if (($unreadNotificationCount ?? 0) > 0)
                <span class="admin-notif-badge" style="position:static; margin-left:auto;">{{ $unreadNotificationCount }}</span>
            @endif
        </a>

        <div class="admin-sidebar__section-title">Account</div>

        <a href="{{ route('admin.profile.edit') }}" class="admin-nav-link {{ request()->routeIs('admin.profile.*') ? 'is-active' : '' }}">
            <i class="fas fa-user-gear"></i> My Profile
        </a>

        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit" class="admin-nav-link" style="width:100%; text-align:left; background:none; border:none; cursor:pointer; font-family:inherit;">
                <i class="fas fa-right-from-bracket"></i> Logout
            </button>
        </form>
    </nav>
</aside>
<div class="admin-sidebar__overlay"></div>
