<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Models\FollowUp;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Task;
use App\Notifications\AssetRenewalDue;
use App\Notifications\FollowUpDue;
use App\Notifications\InvoiceOverdue;
use App\Notifications\ProjectDeadlineApproaching;
use App\Notifications\TaskDueReminder;
use App\Services\NotificationDispatcher;
use App\Services\Settings;
use Illuminate\Console\Command;

/**
 * Daily digest of everything that needs attention. Run it from the scheduler
 * (or Windows Task Scheduler) once a day.
 */
class SendAgencyReminders extends Command
{
    protected $signature = 'agency:reminders';

    protected $description = 'Notify staff about tasks due, overdue work, follow-ups, invoice arrears and upcoming renewals';

    public function handle(NotificationDispatcher $dispatcher, Settings $settings): int
    {
        // Nothing here should be skipped by "don't notify me about my own action".
        $sent = 0;

        if ($settings->get('task_due_reminder', '1')) {
            foreach (Task::with('assignees')->dueToday()->get() as $task) {
                $dispatcher->toEmployees($task->assignees->pluck('id')->all(), new TaskDueReminder($task), skipSelf: false);
                $sent++;
            }

            foreach (Task::with('assignees')->overdue()->get() as $task) {
                $dispatcher->toEmployees($task->assignees->pluck('id')->all(), new TaskDueReminder($task, overdue: true), skipSelf: false);
                $sent++;
            }
        }

        if ($settings->get('follow_up_reminder', '1')) {
            foreach (FollowUp::with('assignee')->pending()->whereDate('scheduled_at', '<=', today())->get() as $followUp) {
                $dispatcher->toEmployees([$followUp->assigned_to], new FollowUpDue($followUp), skipSelf: false);
                $sent++;
            }
        }

        if ($settings->get('invoice_overdue_reminder', '1')) {
            foreach (Invoice::with('client')->unpaid()->whereDate('due_date', '<', today())->get() as $invoice) {
                $dispatcher->toPermission('invoices.view', new InvoiceOverdue($invoice), skipSelf: false);
                $sent++;
            }
        }

        foreach (Project::with('manager')->active()->whereNotNull('deadline')
            ->whereBetween('deadline', [today(), today()->addDays(7)])->get() as $project) {
            $dispatcher->toEmployees([$project->project_manager_id], new ProjectDeadlineApproaching($project), skipSelf: false);
            $sent++;
        }

        $reminderDays = (int) $settings->get('renewal_reminder_days', 30);

        foreach (Asset::with('client')->where('status', 'active')->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', today()->addDays($reminderDays))->get() as $asset) {
            $dispatcher->toPermission('assets.view', new AssetRenewalDue($asset), skipSelf: false);
            $sent++;
        }

        $this->info("Reminder run complete. {$sent} notification batches dispatched.");

        return self::SUCCESS;
    }
}
