<?php
/**
 * @var \omnilight\scheduling\Schedule $schedule
 */
 $environment = 'dev';
// Place here all of your cron jobs

// This command will execute console command of your application every 00:01am
// $schedule->command('enrolment/auto-renewal')->dailyAt('00:01');

// This command will execute console command of your application every 11pm
if (env('YII_ENV') === 'prod') { 
    $environment = 'pro';
}
$schedule->command('customer/update-balance')->sendOutputTo('/var/log/smw.'.$environment.'.log')->dailyAt('08:00');
$schedule->command('enrolment/auto-renewal')->sendOutputTo('/var/log/smw.'.$environment.'.log')->dailyAt('03:00');
$schedule->command('invoice/all-completed-lessons')->sendOutputTo('/var/log/smw.'.$environment.'.log')->dailyAt('23:00');
$schedule->command('student/set-status-production')->sendOutputTo('/var/log/smw.'.$environment.'.log')->dailyAt('01:00');
$schedule->command('student/set-status-non-production')->sendOutputTo('/var/log/smw.'.$environment.'.log')->dailyAt('01:30');
$schedule->command('customer/set-status')->sendOutputTo('/var/log/smw.'.$environment.'.log')->dailyAt('02:00');
$schedule->command('invoice/all-expired-lessons')->sendOutputTo('/var/log/smw.'.$environment.'.log')->dailyAt('23:20');
$schedule->command('recurring-payment/update-recurring-payments')->sendOutputTo('/var/log/smw.'.$environment.'.log')->dailyAt('23:10');
$schedule->command('recurring-payment/create')->sendOutputTo('/var/log/smw.'.$environment.'.log')->dailyAt('23:40');
$schedule->command('tools/backup')->dailyAt('23:59');




