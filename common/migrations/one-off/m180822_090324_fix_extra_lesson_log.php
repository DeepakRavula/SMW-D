<?php

use yii\db\Migration;
use common\models\log\Log;
use common\models\log\LogLink;
use yii\helpers\Url;
use common\models\User;
/**
 * Class m180822_090324_fix_extra_lesson_log
 */
class m180822_090324_fix_extra_lesson_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $log = Log::findOne(['id' => 5500]);
        $log_link = new LogLink();
        $log_link->logId = $log->id;
        $log_link->index = 'Paulo Trial';
        $log_link->baseUrl = 'https://smw.arcadiamusicacademy.com';
        $log_link->path = '/admin/maple/student/view?id=7351';
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
