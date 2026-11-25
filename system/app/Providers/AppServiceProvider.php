<?php

namespace App\Providers;

use App\Models\Admin;
use App\Services\MailConfigurator;
use App\Services\Settings;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Settings::class);
    }

    public function boot(): void
    {
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        } elseif (! $this->app->runningInConsole()) {
            if (request()->isSecure() || request()->header('x-forwarded-proto') === 'https') {
                URL::forceScheme('https');
            }
        }

        $this->registerPermissionGate();
        $this->shareAdminViewData();
        $this->applyMailSettings();
    }

    /**
     * The SMTP account is managed from Settings → Email, so it has to be pushed
     * into the mail config before anything is sent. Wrapped defensively: a
     * missing settings table (fresh install, migrations not yet run) must never
     * stop the application from booting.
     */
    protected function applyMailSettings(): void
    {
        if ($this->app->runningInConsole() && ! $this->app->environment('testing')) {
            // Artisan commands that send mail resolve this themselves; skipping
            // here keeps `migrate` usable before the settings table exists.
            if (in_array($_SERVER['argv'][1] ?? '', ['migrate', 'migrate:fresh', 'migrate:install', 'db:seed'], true)) {
                return;
            }
        }

        try {
            $this->app->make(MailConfigurator::class)->apply();
        } catch (\Throwable) {
            // Settings unavailable — fall back to the .env mail configuration.
        }
    }

    /**
     * A single before-callback resolves every "module.action" ability from the
     * signed-in admin's role. Returning null (rather than false) for a miss
     * leaves any explicitly defined gate free to answer, and unknown abilities
     * stay denied by default.
     */
    protected function registerPermissionGate(): void
    {
        Gate::before(function ($user, string $ability) {
            if (! $user instanceof Admin) {
                return null;
            }

            if ($user->isSuperAdmin()) {
                return true;
            }

            return $user->hasPermission($ability) ? true : null;
        });
    }

    /**
     * The sidebar and topbar need the unread notification count and the agency
     * name on every admin screen.
     */
    protected function shareAdminViewData(): void
    {
        View::composer(['admin.layouts.admin', 'admin.partials.*'], function ($view) {
            $admin = auth('admin')->user();

            $view->with([
                'agencySettings' => app(Settings::class),
                'unreadNotificationCount' => $admin ? $admin->unreadNotifications()->count() : 0,
                'recentNotifications' => $admin
                    ? $admin->notifications()->latest()->take(8)->get()
                    : collect(),
            ]);
        });
    }
}
