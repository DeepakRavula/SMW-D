<?php
namespace Deployer;

require 'recipe/yii2-app-advanced.php';
require 'recipe/slack.php';

$user = getenv('DEP_HOST_USER');
$configFilePath = getenv('DEP_HOST_CONFIG');
$hostKey = getenv('DEP_HOST_KEY');
$branch = getenv('DEP_DEPLOY_BRANCH');

$repo = getenv('DEP_REPO');

// Hosts
host('smw')
    ->user($user)
    ->port(22)
    ->configFile($configFilePath)
    ->identityFile($hostKey)
    ->forwardAgent(true)
	->set('deploy_path', '/home/arcadia/smw-dev')
	->set('branch', $branch)
	->set('instance', 'Dev');

set('user', function () {
    return runLocally('git config --get user.name');
});

set('slack_webhook', 'https://hooks.slack.com/services/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');
set('slack_title', 'SMW');
set('slack_text', 'Smw deployed to {{instance}} by {{user}}');
set('slack_success_text', 'Deploy to {{instance}} instance successful');
set('slack_failure_text', 'Deploy to {{instance}} instance failure');

// Project name
set('application', 'SMW');

// Project repository
set('repository', 'git@github.com:kristin-green-and-associates/smw.git');

// [Optional] Allocate tty for git clone. Default value is false.
//set('git_tty', true);

// Shared files/dirs between deploys 
add('shared_files', ['.env']);
add('shared_dirs', []);

// Writable dirs by web server 
add('writable_dirs', []);


// Tasks
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
    run("php {{deploy_path}} console/yii migrate/up");

    writeln('<info>Data migration is done.</info>');
});
task('deploy:one-off', function() {
    writeln('<info>Run one-off migration...</info>');
    $deployPath = get('deploy_path');

    cd($deployPath);
    run("php {{deploy_path}} console/yii one-off");

    writeln('<info>One off migration is done.</info>');
});

task('deploy', [
    'deploy:prepare',
	'deploy:release',
	'deploy:update_code',
    'deploy:latest_code',
    'deploy:composer',
    'deploy:migration',
    'deploy:one-off',
]);


before('deploy', 'slack:notify');
// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
after('success', 'slack:notify:success');
after('deploy:failed', 'slack:notify:failure');
