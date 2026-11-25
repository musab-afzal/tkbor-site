<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\BugController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\CareerController;
use App\Http\Controllers\Admin\CaseStudyController;
use App\Http\Controllers\Admin\ChangeRequestController;
use App\Http\Controllers\Admin\ClientApprovalController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\ContentBlockController;
use App\Http\Controllers\Admin\HomepageStatController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ExpenseCategoryController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\FinanceReportController;
use App\Http\Controllers\Admin\FollowUpController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\KeywordController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\LeaveController;
use App\Http\Controllers\Admin\MaintenanceRequestController;
use App\Http\Controllers\Admin\MarketingReportController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MeetingController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PasswordResetController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PortfolioController;
use App\Http\Controllers\Admin\PricingCategoryController;
use App\Http\Controllers\Admin\PricingItemController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ProjectMilestoneController;
use App\Http\Controllers\Admin\ProjectRequirementController;
use App\Http\Controllers\Admin\QuotationController;
use App\Http\Controllers\Admin\RecurringPaymentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SaasCustomerController;
use App\Http\Controllers\Admin\SaasProductController;
use App\Http\Controllers\Admin\SaasSubscriptionController;
use App\Http\Controllers\Admin\SeoProjectController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\SocialPostController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\TeamMemberController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\TimesheetController;
use App\Http\Controllers\Admin\TodoController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WebsiteInquiryController;
use App\Http\Controllers\Admin\WorkloadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
| Authorisation is handled inside the controllers through the permission gate
| ("module.action"), so every route here only needs the admin guard.
*/

Route::prefix('admin')->name('admin.')->group(function () {

    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])
            ->middleware('throttle:10,1')
            ->name('login.submit');

        Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
            ->middleware('throttle:6,1')
            ->name('password.email');
        Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
        Route::post('/reset-password', [PasswordResetController::class, 'reset'])
            ->middleware('throttle:6,1')
            ->name('password.update');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.alias');

        /* ---------------------------------------------------------- Sales & CRM */

        Route::get('leads/{lead}', [LeadController::class, 'show'])->whereNumber('lead')->name('leads.show');
        Route::post('leads/{lead}/activities', [LeadController::class, 'addActivity'])->name('leads.activities.store');
        Route::post('leads/{lead}/convert-client', [LeadController::class, 'convertToClient'])->name('leads.convert-client');
        Route::post('leads/{lead}/convert-project', [LeadController::class, 'convertToProject'])->name('leads.convert-project');
        Route::resource('leads', LeadController::class)->except('show');

        Route::get('clients/{client}', [ClientController::class, 'show'])->whereNumber('client')->name('clients.show');
        Route::patch('clients/{client}/toggle-status', [ClientController::class, 'toggleStatus'])->name('clients.toggle-status');
        Route::resource('clients', ClientController::class)->except('show');

        Route::patch('follow-ups/{followUp}/complete', [FollowUpController::class, 'complete'])->name('follow-ups.complete');
        Route::resource('follow-ups', FollowUpController::class)->except('show')->parameters(['follow-ups' => 'followUp']);

        Route::resource('meetings', MeetingController::class)->except('show');
        Route::resource('contracts', ContractController::class)->except('show');

        Route::get('inquiries/export', [WebsiteInquiryController::class, 'export'])->name('inquiries.export');
        Route::get('inquiries/{inquiry}', [WebsiteInquiryController::class, 'show'])->whereNumber('inquiry')->name('inquiries.show');
        Route::patch('inquiries/{inquiry}/read', [WebsiteInquiryController::class, 'markRead'])->name('inquiries.read');
        Route::patch('inquiries/{inquiry}/assign', [WebsiteInquiryController::class, 'assign'])->name('inquiries.assign');
        Route::post('inquiries/{inquiry}/convert-lead', [WebsiteInquiryController::class, 'convertToLead'])->name('inquiries.convert-lead');
        Route::post('inquiries/{inquiry}/convert-client', [WebsiteInquiryController::class, 'convertToClient'])->name('inquiries.convert-client');
        Route::post('inquiries/{inquiry}/quotation', [WebsiteInquiryController::class, 'createQuotation'])->name('inquiries.quotation');
        Route::resource('inquiries', WebsiteInquiryController::class)->except('show')->parameters(['inquiries' => 'inquiry']);

        /* ----------------------------------------------------------- Quotations */

        Route::get('quotations/{quotation}/print', [QuotationController::class, 'print'])->name('quotations.print');
        Route::post('quotations/{quotation}/send', [QuotationController::class, 'send'])->name('quotations.send');
        Route::post('quotations/{quotation}/approve', [QuotationController::class, 'approve'])->name('quotations.approve');
        Route::post('quotations/{quotation}/reject', [QuotationController::class, 'reject'])->name('quotations.reject');
        Route::post('quotations/{quotation}/duplicate', [QuotationController::class, 'duplicate'])->name('quotations.duplicate');
        Route::post('quotations/{quotation}/revise', [QuotationController::class, 'revise'])->name('quotations.revise');
        Route::post('quotations/{quotation}/convert', [QuotationController::class, 'convertToProject'])->name('quotations.convert');
        Route::resource('quotations', QuotationController::class);

        /* -------------------------------------------------------------- Pricing */

        Route::resource('pricing', PricingItemController::class)->except('show')->parameters(['pricing' => 'pricing']);
        Route::resource('pricing-categories', PricingCategoryController::class)->except('show')->parameters(['pricing-categories' => 'pricingCategory']);
        Route::resource('packages', PackageController::class)->except('show');

        /* ------------------------------------------------------------- Projects */

        Route::get('projects/{project}', [ProjectController::class, 'show'])->whereNumber('project')->name('projects.show');
        Route::patch('projects/{project}/sync-progress', [ProjectController::class, 'syncProgress'])->name('projects.sync-progress');
        Route::resource('projects', ProjectController::class)->except('show');

        Route::resource('milestones', ProjectMilestoneController::class)->except('show')->parameters(['milestones' => 'projectMilestone']);
        Route::resource('requirements', ProjectRequirementController::class)->except('show')->parameters(['requirements' => 'projectRequirement']);
        Route::resource('change-requests', ChangeRequestController::class)->except('show')->parameters(['change-requests' => 'changeRequest']);
        Route::resource('bugs', BugController::class)->except('show');

        Route::patch('timesheets/{timesheet}/approve', [TimesheetController::class, 'approve'])->name('timesheets.approve');
        Route::patch('timesheets/{timesheet}/reject', [TimesheetController::class, 'reject'])->name('timesheets.reject');
        Route::resource('timesheets', TimesheetController::class)->except('show');

        /* ---------------------------------------------------------------- Tasks */

        Route::get('tasks/board', [TaskController::class, 'board'])->name('tasks.board');
        Route::get('tasks/due-today', [TaskController::class, 'dueToday'])->name('tasks.due-today');
        Route::get('tasks/upcoming', [TaskController::class, 'upcoming'])->name('tasks.upcoming');
        Route::get('tasks/overdue', [TaskController::class, 'overdue'])->name('tasks.overdue');
        Route::get('tasks/mine', [TaskController::class, 'mine'])->name('tasks.mine');
        Route::get('tasks/assigned', [TaskController::class, 'assigned'])->name('tasks.assigned');
        Route::get('tasks/{task}', [TaskController::class, 'show'])->whereNumber('task')->name('tasks.show');
        Route::patch('tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
        Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.status');
        Route::post('tasks/{task}/comments', [TaskController::class, 'addComment'])->name('tasks.comments.store');
        Route::post('tasks/{task}/checklist', [TaskController::class, 'addChecklistItem'])->name('tasks.checklist.store');
        Route::patch('tasks/{task}/checklist/{item}', [TaskController::class, 'toggleChecklistItem'])->name('tasks.checklist.toggle');
        Route::delete('tasks/{task}/checklist/{item}', [TaskController::class, 'deleteChecklistItem'])->name('tasks.checklist.destroy');
        Route::resource('tasks', TaskController::class)->except('show');

        Route::get('todos/{todo}', [TodoController::class, 'show'])->whereNumber('todo')->name('todos.show');
        Route::patch('todos/{todo}/complete', [TodoController::class, 'complete'])->name('todos.complete');
        Route::patch('todos/{todo}/items/{item}', [TodoController::class, 'toggleItem'])->name('todos.items.toggle');
        Route::resource('todos', TodoController::class)->except('show');

        /* -------------------------------------------------------------- Finance */

        Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
        Route::post('invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send');
        Route::resource('invoices', InvoiceController::class);

        Route::get('payments/{payment}', [PaymentController::class, 'show'])->whereNumber('payment')->name('payments.show');
        Route::resource('payments', PaymentController::class)->except('show');

        Route::resource('expenses', ExpenseController::class)->except('show');
        Route::resource('expense-categories', ExpenseCategoryController::class)->except('show')->parameters(['expense-categories' => 'expenseCategory']);

        Route::patch('recurring-payments/{recurringPayment}/paid', [RecurringPaymentController::class, 'markPaid'])->name('recurring-payments.paid');
        Route::resource('recurring-payments', RecurringPaymentController::class)->except('show')->parameters(['recurring-payments' => 'recurringPayment']);

        Route::get('finance/reports', [FinanceReportController::class, 'index'])->name('finance.reports');

        /* ---------------------------------------------------- Digital Marketing */

        Route::resource('campaigns', CampaignController::class)->except('show');

        Route::get('seo-projects/{seoProject}', [SeoProjectController::class, 'show'])->whereNumber('seoProject')->name('seo-projects.show');
        Route::resource('seo-projects', SeoProjectController::class)->except('show')->parameters(['seo-projects' => 'seoProject']);

        Route::resource('keywords', KeywordController::class)->except('show');

        Route::get('social-posts/calendar', [SocialPostController::class, 'calendar'])->name('social-posts.calendar');
        Route::resource('social-posts', SocialPostController::class)->except('show')->parameters(['social-posts' => 'socialPost']);

        Route::get('marketing/reports', [MarketingReportController::class, 'index'])->name('marketing.reports');

        /* -------------------------------------------------------------- Support */

        Route::get('tickets/{ticket}', [TicketController::class, 'show'])->whereNumber('ticket')->name('tickets.show');
        Route::post('tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');
        Route::patch('tickets/{ticket}/assign', [TicketController::class, 'assign'])->name('tickets.assign');
        Route::resource('tickets', TicketController::class)->except('show');

        Route::resource('maintenance-requests', MaintenanceRequestController::class)->except('show')->parameters(['maintenance-requests' => 'maintenanceRequest']);

        Route::patch('client-approvals/{clientApproval}/decide', [ClientApprovalController::class, 'decide'])->name('client-approvals.decide');
        Route::resource('client-approvals', ClientApprovalController::class)->except('show')->parameters(['client-approvals' => 'clientApproval']);

        /* ----------------------------------------------------------------- Team */

        Route::get('employees/{employee}', [EmployeeController::class, 'show'])->whereNumber('employee')->name('employees.show');
        Route::resource('employees', EmployeeController::class)->except('show');

        Route::resource('departments', DepartmentController::class)->except('show');
        Route::resource('attendance', AttendanceController::class)->except('show')->parameters(['attendance' => 'attendance']);

        Route::patch('leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
        Route::patch('leaves/{leave}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');
        Route::resource('leaves', LeaveController::class)->except('show');

        Route::get('workload', [WorkloadController::class, 'index'])->name('workload.index');

        /* ---------------------------------------------------------- Website CMS */

        Route::resource('sliders', SliderController::class)->except('show');

        Route::resource('content-blocks', ContentBlockController::class)->except('show')->parameters(['content-blocks' => 'contentBlock']);

        Route::patch('homepage-stats/{homepageStat}/toggle-status', [HomepageStatController::class, 'toggleStatus'])->name('homepage-stats.toggle-status');
        Route::resource('homepage-stats', HomepageStatController::class)->except('show')->parameters(['homepage-stats' => 'homepageStat']);

        Route::patch('services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle-status');
        Route::resource('services', ServiceController::class)->except('show');

        Route::patch('companies/{company}/toggle-status', [CompanyController::class, 'toggleStatus'])->name('companies.toggle-status');
        Route::resource('companies', CompanyController::class)->except('show');

        Route::patch('portfolios/{portfolio}/toggle-status', [PortfolioController::class, 'toggleStatus'])->name('portfolios.toggle-status');
        Route::patch('portfolios/{portfolio}/toggle-featured', [PortfolioController::class, 'toggleFeatured'])->name('portfolios.toggle-featured');
        Route::resource('portfolios', PortfolioController::class)->except('show');

        Route::resource('case-studies', CaseStudyController::class)->except('show')->parameters(['case-studies' => 'caseStudy']);

        Route::patch('reviews/{review}/toggle-status', [ReviewController::class, 'toggleStatus'])->name('reviews.toggle-status');
        Route::resource('reviews', ReviewController::class)->except('show');

        Route::patch('faqs/{faq}/toggle-status', [FaqController::class, 'toggleStatus'])->name('faqs.toggle-status');
        Route::resource('faqs', FaqController::class)->except('show');

        Route::patch('team/{teamMember}/toggle-status', [TeamMemberController::class, 'toggleStatus'])->name('team.toggle-status');
        Route::resource('team', TeamMemberController::class)->except('show')->parameters(['team' => 'teamMember']);

        Route::resource('careers', CareerController::class)->except('show');
        Route::resource('pages', PageController::class)->except('show');
        Route::resource('blog', BlogController::class)->except('show');

        Route::get('media', [MediaController::class, 'index'])->name('media.index');
        Route::post('media', [MediaController::class, 'store'])->name('media.store');
        Route::delete('media/{medium}', [MediaController::class, 'destroy'])->name('media.destroy');

        Route::get('menus', [MenuController::class, 'index'])->name('menus.index');
        Route::post('menus', [MenuController::class, 'store'])->name('menus.store');
        Route::get('menus/{menu}', [MenuController::class, 'show'])->name('menus.show');
        Route::put('menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
        Route::delete('menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');
        Route::post('menus/{menu}/items', [MenuController::class, 'storeItem'])->name('menus.items.store');
        Route::put('menus/{menu}/items/{item}', [MenuController::class, 'updateItem'])->name('menus.items.update');
        Route::delete('menus/{menu}/items/{item}', [MenuController::class, 'destroyItem'])->name('menus.items.destroy');

        /* ----------------------------------------------------------------- SaaS */

        Route::resource('saas-products', SaasProductController::class)->except('show')->parameters(['saas-products' => 'saasProduct']);
        Route::resource('saas-customers', SaasCustomerController::class)->except('show')->parameters(['saas-customers' => 'customer']);

        Route::patch('saas-subscriptions/{subscription}/approve', [SaasSubscriptionController::class, 'approvePayment'])->name('saas-subscriptions.approve');
        Route::patch('saas-subscriptions/{subscription}/reject', [SaasSubscriptionController::class, 'rejectPayment'])->name('saas-subscriptions.reject');
        Route::patch('saas-subscriptions/{subscription}/expire', [SaasSubscriptionController::class, 'expireNow'])->name('saas-subscriptions.expire');
        Route::resource('saas-subscriptions', SaasSubscriptionController::class)->except('show')->parameters(['saas-subscriptions' => 'subscription']);

        /* --------------------------------------------------- Assets & Renewals */

        Route::get('assets/type/{type}', [AssetController::class, 'ofType'])->name('assets.type');
        Route::patch('assets/{asset}/renew', [AssetController::class, 'renew'])->name('assets.renew');
        Route::resource('assets', AssetController::class)->except('show');

        /* -------------------------------------------------------------- Reports */

        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/{report}', [ReportController::class, 'show'])->name('reports.show');

        /* -------------------------------------------------------- Notifications */

        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
        Route::get('notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
        Route::delete('notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

        /* ------------------------------------------------------------- Settings */

        Route::get('settings/{group?}', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings/{group}', [SettingController::class, 'update'])->name('settings.update');
        Route::post('settings/test-email', [SettingController::class, 'sendTestEmail'])->name('settings.test-email');

        Route::resource('users', UserController::class)->except('show')->parameters(['users' => 'admin']);
        Route::resource('roles', RoleController::class)->except('show');

        Route::get('audit-logs', [ActivityLogController::class, 'index'])->name('audit-logs.index');

        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });
});
