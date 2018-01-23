<?php

use yii\db\Migration;
use common\models\Lesson;


class m180123_061124_adding_expirydate_to_private_lessons extends Migration
{
    public function up()
    {
        $lessons=Lesson::find()
		 ->isConfirmed()
                 ->notDeleted()
		 ->privateLessons()
		 ->andWhere(['NOT IN', 'lesson.status', [Lesson::STATUS_CANCELED]])
                 ->all();
       
foreach($lessons as $lesson)
{
    if(!$lesson->hasExpiryDate())
    {
    if($lesson->status=== Lesson::STATUS_UNSCHEDULED)
    {
        $rootLesson=$lesson->getRootLesson();
        $privateLessonModel = new PrivateLesson();
        $privateLessonModel->lessonId = $this->id;
        $date = new \DateTime($this->date);
        $expiryDate = $date->modify('90 days');
        $privateLessonModel->expiryDate = $expiryDate->format('Y-m-d H:i:s');
        $privateLessonModel->save();
    }
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
