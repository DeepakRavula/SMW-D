<?php

use yii\db\Migration;
use common\models\Location;
use common\models\Lesson;
use common\models\NotificationEmailType;
use common\models\PrivateLessonEmailStatus;

/**
 * Class m220809_071952_update_private_email_status
 */
class m220809_071952_update_private_email_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $locations = Location::find()->all();

        foreach($locations as $location) {
            $lessons = Lesson::find()
            ->andWhere(['>', 'lesson.date', (new \DateTime())->format('Y-m-d H:i:s')])
            ->location($location->id)
            ->all();
            $emailNotifyTypes = NotificationEmailType::find()->all();
            foreach($lessons as $lesson){
                foreach($emailNotifyTypes as $emailNotifyType) {
                    $emailStatus = new PrivateLessonEmailStatus();
                    $emailStatus->lessonId = $lesson->id;
                    $emailStatus->notificationType = $emailNotifyType->id;
                    $emailStatus->status = false;
                    $emailStatus->save();
                }
            }
        }    
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220809_071952_update_private_email_status cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220809_071952_update_private_email_status cannot be reverted.\n";

        return false;
    }
    */
}
