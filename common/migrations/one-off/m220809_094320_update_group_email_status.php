<?php

use yii\db\Migration;
use common\models\Location;
use common\models\Lesson;
use common\models\NotificationEmailType;
use common\models\GroupLessonEmailStatus;

/**
 * Class m220809_094320_update_group_email_status
 */
class m220809_094320_update_group_email_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $locationIds = [1, 4, 9, 14, 15, 16, 17, 18, 19, 20, 21, 22];
        $locations = Location::find()->andWhere(['id' => $locationIds])->all();

        foreach($locations as $location) {
            $lessons = Lesson::find()
                    ->andWhere(['>', 'lesson.date', (new \DateTime())->format('Y-m-d H:i:s')])
                    ->notCanceled()
                    ->notDeleted()
                    ->isConfirmed()
                    ->regular()
                    ->location($location->id)
                    ->groupLessons()
                    ->all();
            $emailNotifyTypes = NotificationEmailType::find()->all();
            foreach($lessons as $lesson){
                foreach($lesson->groupStudents as $student){
                    $groupLessonEmail = GroupLessonEmailStatus::find()->andWhere(['studentId' => $student->id])->count();
                    if($groupLessonEmail == 0){
                        foreach($emailNotifyTypes as $emailNotifyType) {
                            $emailStatus = new GroupLessonEmailStatus();
                            $emailStatus->lessonId = $lesson->id;
                            $emailStatus->studentId = $student->id;
                            $emailStatus->notificationType = $emailNotifyType->id;
                            $emailStatus->status = false;
                            $emailStatus->save();
                        }
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
        echo "m220809_094320_update_group_email_status cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220809_094320_update_group_email_status cannot be reverted.\n";

        return false;
    }
    */
}
