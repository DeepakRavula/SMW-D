<?php

use yii\db\Migration;
use common\models\Location;
use common\models\Lesson;
use common\models\NotificationEmailType;
use common\models\PrivateLessonEmailStatus;

/**
 * Class m220816_060242_private_lesson_email_update6
 */
class m220816_060242_private_lesson_email_update6 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $locationIds = [21, 22];
        $locations = Location::find()->andWhere(['id'=> $locationIds])->all();

        foreach($locations as $location) {
            $lessons = Lesson::find()
                    ->andWhere(['>', 'lesson.date', (new \DateTime())->format('Y-m-d H:i:s')])
                    ->notCanceled()
                    ->notDeleted()
                    ->isConfirmed()
                    ->regular()
                    ->location($location->id)
                    ->privateLessons()
                    ->all();
            $emailNotifyTypes = NotificationEmailType::find()->all();
            
            foreach($lessons as $lesson){
                $privateLessonEmail = PrivateLessonEmailStatus::find()->andWhere(['lessonId' => $lesson->id])->count();
                if($privateLessonEmail == 0 ){
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
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220816_060242_private_lesson_email_update6 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220816_060242_private_lesson_email_update6 cannot be reverted.\n";

        return false;
    }
    */
}