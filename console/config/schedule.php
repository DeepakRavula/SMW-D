<?php
/**
 * @var \omnilight\scheduling\Schedule $schedule
 */

// Place here all of your cron jobs

// This command will execute console command of your application every 00:01am
// $schedule->command('enrolment/auto-renewal')->dailyAt('00:01');

// This command will execute console command of your application every 11pm
$schedule->command('invoice/all-completed-lessons')->dailyAt('23:00');
$schedule->command('student/set-status')->dailyAt('02:00');
$schedule->command('customer/set-status')->dailyAt('02:30');
$schedule->command('invoice/all-expired-lessons')->dailyAt('23:00');
$schedule->command('payment-request/create')->dailyAt('03:00');
$schedule->command('payment-preference/create')->dailyAt('05:00');
$schedule->command('tools/backup')->dailyAt('23:00');




