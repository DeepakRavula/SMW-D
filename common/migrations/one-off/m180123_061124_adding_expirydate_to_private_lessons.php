<?php

use yii\db\Migration;
use common\models\Lesson;

class m180123_061124_adding_expirydate_to_private_lessons extends Migration
{

    public function up()
    {
        $lessons = Lesson::find()
            ->isConfirmed()
            ->privateLessons()
            ->notDeleted()
            ->all();

        foreach ($lessons as $lesson) {
            if (!$lesson->privateLesson) {
                $lesson->setExpiry();
            } else {
                if ($lesson->rootLesson) {
                    $expiryDate = new \DateTime($lesson->rootLesson->privateLesson->expiryDate);
                } else {
                    $date = new \DateTime($lesson->date);
                    $expiryDate = $date->modify('90 days');
                }
                $lesson->privateLesson->updateAttributes([
                    'expiryDate' => $expiryDate->format('Y-m-d H:i:s')
                ]);
            }
        }
        
        $groupLessons = Lesson::find()
            ->isConfirmed()
            ->groupLessons()
            ->notDeleted()
            ->all();
        
        foreach ($groupLessons as $groupLesson) {
            if ($groupLesson->privateLesson) {
                $groupLesson->privateLesson->delete();
            }
        }
    }

    public function down()
    {
        echo "m180123_061124_adding_expirydate_to_private_lessons cannot be reverted.\n";

        return false;
    }
    /*
      // Use safeUp/safeDown to run migration code within a transaction
      public function safeUp()
      {
      }

      public function safeDown()
      {
      }
     */
}