<?php
/**
 * @var \omnilight\scheduling\Schedule $schedule
 */

// Place here all of your cron jobs

// This command will execute console command of your application every 00:01am
$schedule->command('invoice/generate-invoice')->dailyAt('00:01');

// This command will execute console command of your application every 11pm
$schedule->command('invoice/all-completed-lessons')->dailyAt('23:00');
$schedule->command('invoice/all-expired-lessons')->dailyAt('23:00');
$schedule->command('invoice/payment-preference-invoice')->dailyAt('23:00');
$schedule->command('tools/backup')->dailyAt('23:00');




