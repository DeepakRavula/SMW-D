<?php

use yii\db\Migration;
use common\models\Lesson;

class m180123_061124_adding_expirydate_to_private_lessons extends Migration
{

    public function up()
    {
        $lessons = Lesson::find()
            ->isConfirmed()
            ->notDeleted()
            ->privateLessons()
            ->andWhere(['NOT', ['lesson.status' => Lesson::STATUS_CANCELED]])
            ->all();

        foreach ($lessons as $lesson) {
            if (!$lesson->hasExpiryDate()) {
                $lesson->setExpiry();
            } else {
                if ($lesson->rootLesson) {
                    $lessonToUpdate = $lesson->rootLesson;
                } else {
                    $lessonToUpdate = $lesson;
                }
                $date = new \DateTime($lessonToUpdate->date);
                $expiryDate = $date->modify('90 days');
                $lesson->privateLesson->updateAttributes([
                    'expiryDate' => $expiryDate->format('Y-m-d H:i:s')
                ]);
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