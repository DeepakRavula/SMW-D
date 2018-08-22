<?php

use yii\db\Migration;
use common\models\log\Log;
use common\models\log\LogLink;
use yii\helpers\Json;
use yii\helpers\Url;
use common\models\User;
/**
 * Class m180822_090324_fix_extra_lesson_log
 */
class m180822_090324_fix_extra_lesson_log extends Migration
{
    public function init() 
    {
        parent::init();
        $user = User::findByRole(User::ROLE_BOT);
        $botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));
    }
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $log = Log::findOne(['id' => 5500]);
        $log_link = new LogLink();
        $log_link->logId = $log->id;
        $log_link->index = 'Paulo Trial';
        $log_link->baseUrl = Yii::$app->request->hostInfo;
        $log_link->path = Url::to(['/student/view', 'id' => 7351]);
        $log_link->save();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180822_090324_fix_extra_lesson_log cannot be reverted.\n";

        return false;
    }
}
