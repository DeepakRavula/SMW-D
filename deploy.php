<?php
namespace Deployer;

require 'recipe/yii2-app-advanced.php';
require 'recipe/slack.php';

set('user', function () {
    return getenv('DEP_USER');
});
$user = getenv('DEP_HOST_USER');
// Hosts
host('smw')
    ->user($user)
    ->port(22)
    ->configFile('~/.ssh/config')
    ->identityFile('~/.ssh/id_rsa')
    ->forwardAgent(true)
	->set('deploy_path', '/home/arcadia/smw-dev')
	->set('branch', 'master')
	->stage('development');

set('slack_webhook', 'https://hooks.slack.com/services/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');
set('slack_title', 'Studio Manager Web');
set('slack_text', '{{user}} deploying {{branch}} to {{deploy_path}}');
set('slack_success_text', 'Deploy to {{deploy_path}} successful');
set('slack_failure_text', 'Deploy to {{deploy_path}} failure');
set('slack_color', 'blue');
set('slack_success_color', 'green');
set('slack_failure_color', 'red');

set('default_stage', 'development');

// Project name
set('application', 'Studio Manager Web');

// Project repository
set('repository', 'smw-git:kristin-green-and-associates/smw.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

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
    run("composer install");

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

before('deploy', 'slack:notify');

task('deploy', [
    'deploy:prepare',
    'deploy:latest_code',
    //'deploy:composer',
    'deploy:migration',
    'deploy:one-off',
]);

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
after('success', 'slack:notify:success');
after('deploy:failed', 'slack:notify:failure');
