<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Employee;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Decides who should hear about an event. Notifications are always delivered to
 * admin accounts, so employee-targeted alerts resolve through the linked login.
 */
class NotificationDispatcher
{
    /**
     * Notify the admin accounts behind the given employee ids.
     *
     * @param  array<int, int|string|null>  $employeeIds
     */
    public function toEmployees(array $employeeIds, Notification $notification, bool $skipSelf = true): void
    {
        $adminIds = Employee::query()
            ->whereIn('id', array_filter($employeeIds))
            ->whereNotNull('admin_id')
            ->pluck('admin_id');

        $this->send($this->admins($adminIds->all(), $skipSelf), $notification);
    }

    /** Notify one admin account directly. */
    public function toAdmin(?int $adminId, Notification $notification, bool $skipSelf = true): void
    {
        $this->send($this->admins([$adminId], $skipSelf), $notification);
    }

    /**
     * Notify every active admin who holds a permission, e.g. "quotations.view".
     */
    public function toPermission(string $permission, Notification $notification, bool $skipSelf = true): void
    {
        $recipients = Admin::where('status', true)
            ->get()
            ->filter(fn (Admin $admin) => $admin->hasPermission($permission));

        if ($skipSelf && $current = Auth::guard('admin')->id()) {
            $recipients = $recipients->reject(fn (Admin $admin) => $admin->getKey() === $current);
        }

        $this->send($recipients, $notification);
    }

    /**
     * @param  array<int, int|string|null>  $adminIds
     * @return Collection<int, Admin>
     */
    protected function admins(array $adminIds, bool $skipSelf): Collection
    {
        $ids = array_filter($adminIds);

        if ($skipSelf && $current = Auth::guard('admin')->id()) {
            $ids = array_filter($ids, fn ($id) => (int) $id !== (int) $current);
        }

        if ($ids === []) {
            return collect();
        }

        return Admin::whereIn('id', $ids)->where('status', true)->get();
    }

    /**
     * The in-panel record is what matters, so one recipient failing (a dead
     * SMTP server, a bad address) must never abort the request that raised
     * the notification or stop the remaining recipients being told.
     *
     * @param  Collection<int, Admin>  $recipients
     */
    protected function send(Collection $recipients, Notification $notification): void
    {
        foreach ($recipients as $recipient) {
            try {
                $recipient->notify($notification);
            } catch (\Throwable $e) {
                Log::warning('Notification delivery failed for admin #'.$recipient->getKey().': '.$e->getMessage());
            }
        }
    }
}
