<?php

use yii\db\Migration;
use common\models\AutoEmailStatus;
use common\models\NotificationEmailType;
use common\models\Location;
use common\models\Lesson;

/**
 * Class m220802_121807_auto_email_status_entries
 */
class m220802_121807_auto_email_status_entries extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $locations = Location::find()->all();
        foreach($locations as $location) {

            $lessons = Lesson::find()->location($location->id)->all();

            foreach ($lessons as $lesson) {
                $emailNotifyTypes = NotificationEmailType::find()->all();

                foreach($emailNotifyTypes as $emailNotifyType) {
                    $autoEmailStatus = new AutoEmailStatus();
                    $autoEmailStatus->lessonId = $lesson->id;
                    $autoEmailStatus->notificationType = $emailNotifyType->id;
                    $autoEmailStatus->status = false;
                    $autoEmailStatus->save();
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220802_121807_auto_email_status_entries cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220802_121807_auto_email_status_entries cannot be reverted.\n";

        return false;
    }
    */
}
