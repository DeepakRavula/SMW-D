<?php

use yii\db\Migration;
use common\models\Location;
use common\models\LessonOwing;
use common\models\Lesson;

/**
 * Class m181008_155327_listing_lessons_with_owing_amount
 */
class m181008_155327_listing_lessons_with_owing_amount extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $tableSchema = Yii::$app->db->schema->getTableSchema('lesson_owing');
        if ($tableSchema == null) {
            $this->createTable('lesson_owing', [
                'lessonId' => $this->integer()->notNull(), 
            ]);
        }
        else {
            $this->truncateTable('lesson_owing');
        }
        
        $lessonIds = [];
        $lessons = Lesson::find()
        ->notDeleted()
        ->isconfirmed()
        ->notCanceled()
        ->regular()
        ->location(['14','15'])
        ->activePrivateLessons()
        ->all();
        foreach ($lessons as $lesson)  {
            if ($lesson->enrolment){
                $owingAmount = $lesson->getOwingAmount($lesson->enrolment->id);
                if ($owingAmount>=1 && $owingAmount<=10){
                    $lessonOwing = new LessonOwing();
                    $lessonOwing->lessonId = $lesson->id;
                    $lessonOwing->save();
                } 
            }
    }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181008_155327_listing_lessons_with_owing_amount cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181008_155327_listing_lessons_with_owing_amount cannot be reverted.\n";

        return false;
    }
    */
}
