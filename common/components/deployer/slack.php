<?php
/* (c) Anton Medvedev <anton@medv.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

use Deployer\Utility\Httpie;

set('slack_git_commit', 'git commit');

task('slack:notify:git', function () {
    if (!get('slack_webhook', false)) {
        return;
    }

    $attachment = [
        'text' => get('slack_git_commit'),
        'color' => get('slack_color'),
        'mrkdwn_in' => ['text'],
    ];

    Httpie::post(get('slack_webhook'))->body(['attachments' => [$attachment]])->send();
})
    ->once()
    ->shallow()
    ->setPrivate();