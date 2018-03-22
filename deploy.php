<?php
namespace Deployer;

require 'recipe/yii2-app-advanced.php';
require 'recipe/slack.php';

// Hosts
host('pt')
    ->user('poonkodi')
    ->port(22)
    ->configFile('~/.ssh/config')
    ->identityFile('~/.ssh/github')
    ->forwardAgent(true)
	->set('deploy_path', '/srv/smw')
	->set('branch', 'master')
	->set('user', 'Seng')
	->stage('development');

set('default_stage', 'development');

// Project name
set('application', 'Studio Manager Web');

// Project repository
set('repository', 'git@github.com:senguttuvang/smw.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// Shared files/dirs between deploys 
add('shared_files', []);
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

task('deploy', [
    'deploy:prepare',
    'deploy:latest_code',
    'deploy:composer',
    'deploy:migration',
    'deploy:one-off',
]);

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

