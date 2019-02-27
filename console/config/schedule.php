<?php
/**
 * @var \omnilight\scheduling\Schedule $schedule
 */

// Place here all of your cron jobs

// This command will execute console command of your application every 00:01am
// $schedule->command('enrolment/auto-renewal')->dailyAt('00:01');

// This command will execute console command of your application every 11pm
$schedule->command('invoice/all-completed-lessons')->sendOutputTo('/var/log/smw.'.env('YII_ENV').'.log')->dailyAt('23:00');
$schedule->command('student/set-status-production')->sendOutputTo('/var/log/smw.'.env('YII_ENV').'.log')->dailyAt('01:00');
$schedule->command('student/set-status-non-production')->sendOutputTo('/var/log/smw.'.env('YII_ENV').'.log')->dailyAt('01:30');
$schedule->command('customer/set-status')->sendOutputTo('/var/log/smw.'.env('YII_ENV').'.log')->dailyAt('02:00');
$schedule->command('invoice/all-expired-lessons')->sendOutputTo('/var/log/smw.'.env('YII_ENV').'.log')->dailyAt('23:30');
$schedule->command('payment-request/create --locationId=4')->sendOutputTo('/var/log/smw.'.env('YII_ENV').'.log')->dailyAt('03:00');
$schedule->command('payment-request/create --locationId=9')->sendOutputTo('/var/log/smw.'.env('YII_ENV').'.log')->dailyAt('03:00');
$schedule->command('payment-request/create --locationId=14')->sendOutputTo('/var/log/smw.'.env('YII_ENV').'.log')->dailyAt('03:00');
$schedule->command('payment-request/create --locationId=15')->sendOutputTo('/var/log/smw.'.env('YII_ENV').'.log')->dailyAt('03:15');
$schedule->command('payment-request/create --locationId=16')->sendOutputTo('/var/log/smw.'.env('YII_ENV').'.log')->dailyAt('03:15');
$schedule->command('payment-request/create --locationId=17')->sendOutputTo('/var/log/smw.'.env('YII_ENV').'.log')->dailyAt('03:15');
$schedule->command('payment-request/create --locationId=18')->sendOutputTo('/var/log/smw.'.env('YII_ENV').'.log')->dailyAt('03:30');
$schedule->command('payment-request/create --locationId=19')->sendOutputTo('/var/log/smw.'.env('YII_ENV').'.log')->dailyAt('03:30');
$schedule->command('payment-request/create --locationId=20')->sendOutputTo('/var/log/smw.'.env('YII_ENV').'.log')->dailyAt('03:30');
$schedule->command('payment-request/create --locationId=21')->sendOutputTo('/var/log/smw.'.env('YII_ENV').'.log')->dailyAt('03:45');
$schedule->command('payment-preference/create')->sendOutputTo('/var/log/smw.'.env('YII_ENV').'.log')->dailyAt('06:00');
$schedule->command('tools/backup')->dailyAt('23:59');




