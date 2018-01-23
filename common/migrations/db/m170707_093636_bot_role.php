<?php

use yii\db\Migration;
use common\models\User;
use common\models\UserProfile;

class m170707_093636_bot_role extends Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;
        $bot = $auth->createRole('bot');
        $bot->description = 'Bot';
        $auth->add($bot);
           
        $user = new User();
        $user->email = 'bot@example.com';
        $user->status = User::STATUS_ACTIVE;
        if (!$user->save()) {
            throw new Exception('Model not saved');
        }
        $userProfile = new UserProfile();
        $userProfile->user_id = $user->id;
        $userProfile->firstname = 'Smw';
        $userProfile->lastname = 'Bot';
        if (! $userProfile->save()) {
            Yii::error('Bot user profile: ' . \yii\helpers\VarDumper::dumpAsString($userProfile->getErrors()));
        }
        $authManager = Yii::$app->authManager;
        $authManager->assign($auth->getRole(User::ROLE_BOT), $user->id);
    }

    public function down()
    {
        echo "m170707_093636_bot_role cannot be reverted.\n";

        return false;
    }
}
