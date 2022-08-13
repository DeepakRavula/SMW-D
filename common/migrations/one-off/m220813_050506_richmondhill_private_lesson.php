<?php

use yii\db\Migration;
use common\models\Lesson;
use common\models\NotificationEmailType;
use common\models\PrivateLessonEmailStatus;

/**
 * Class m220813_050506_richmondhill_private_lesson
 */
class m220813_050506_richmondhill_private_lesson extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $lessons = Lesson::find()
                ->andWhere(['>', 'lesson.date', (new \DateTime())->format('Y-m-d H:i:s')])
                ->notCanceled()
                ->notDeleted()
                ->isConfirmed()
                ->regular()
                ->location(16)
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

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220813_050506_richmondhill_private_lesson cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220813_050506_richmondhill_private_lesson cannot be reverted.\n";

        return false;
    }
    */
}
