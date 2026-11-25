<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily reminder digest: tasks due, overdue work, follow-ups, invoice arrears
// and upcoming domain / hosting / licence renewals.
Schedule::command('agency:reminders')->dailyAt('08:00');

// Close SaaS trials / paid periods whose end date has passed.
Schedule::command('saas:expire')->everyFifteenMinutes();
