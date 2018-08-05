<?php
namespace Deployer;

require 'recipe/yii2-app-advanced.php';
require 'recipe/slack.php';
require __DIR__.'/common/components/deployer/slack.php';
require __DIR__.'/common/env.php';

// Hosts
host(getenv('DEP_HOST_NAME'))
    ->user(getenv('DEP_HOST_USER'))
    ->port(22)
    ->configFile(getenv('DEP_IDENTITY_FILE'))
    ->identityFile(getenv('DEP_IDENTITY_KEY'))
    ->forwardAgent(true)
	->set('branch', getenv('DEP_DEPLOY_BRANCH'))
	->set('instance', 'Dev');

set('user', getenv('DEP_USER'));

set('slack_webhook', getenv('DEP_SLACK_HOOK'));
set('slack_title', 'SMW');


// Project name
set('application', 'SMW');

// Project repository
set('repository', getenv('DEP_REPO'));

// Writable dirs by web server 
add('writable_dirs', []);


// Tasks
task('deploy:set-dev', function() {
	set('deploy_path', getenv('DEP_DEV_PATH'));
});

task('deploy:set-prod', function() {
	set('deploy_path', getenv('DEP_PROD_PATH'));
	set('instance', 'Prod');
});

task('deploy:latest_code', function() {
    writeln('<info>Pulling code....</info>');
    $deployPath = get('deploy_path');

    cd($deployPath);
    run("git pull origin master");

    writeln('<info>Pulling code is done.</info>');
});
task('deploy:composer', function() {
    writeln('<info>Install composer...</info>');
    $deployPath = get('deploy_path');

    cd($deployPath);
    run("/opt/cpanel/composer/bin/composer install");

    writeln('<info>Install composer is done.</info>');
});
task('deploy:migration', function() {
    writeln('<info>Run Migration...</info>');
    $deployPath = get('deploy_path');
    cd($deployPath);
    run('php console/yii migrate/up --interactive=0');

    writeln('<info>Data migration is done.</info>');
});
task('deploy:one-off', function() {
    writeln('<info>Run one-off migration...</info>');
    $deployPath = get('deploy_path');

    cd($deployPath);
    run("php console/yii one-off --interactive=0");

    writeln('<info>One off migration is done.</info>');
});

task('deploy:git', function() {
    writeln('<info>Show git commit...</info>');
    $deployPath = get('deploy_path');

    cd($deployPath);
    $last_commit = run('git log -1 --pretty=format:"%H"');
    $command = "git rev-list " .$last_commit . "..HEAD --pretty=format:'%h - %aD (%ar)%d%n  %s - %an'"; 
    $commit = run($command);

    writeln($commit);

    set('slack_git_commit', $commit);
});

task('deploy:dev', [
	'deploy:set-dev',
    'deploy:prepare',
    'deploy:git',
    'deploy:latest_code',
    'deploy:composer',
    'deploy:migration',
    'deploy:one-off',
]);

task('deploy:prod', [
	'deploy:set-prod',
    'deploy:prepare',
    'deploy:git',
    'deploy:latest_code',
    'deploy:composer',
    'deploy:migration',
    'deploy:one-off',
]);

set('slack_success_text', '{{user}} deployed to {{instance}} instance.');
set('slack_failure_text', '{{user}} deployed to {{instance}} instance.');

after('deploy:dev', 'success');

after('deploy:prod', 'success');

after('success', 'slack:notify:success');

after('success', 'slack:notify:git');

after('deploy:failed', 'slack:notify:failure');

after('deploy:failed', 'deploy:unlock');
