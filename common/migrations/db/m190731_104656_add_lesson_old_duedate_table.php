<?php

use yii\db\Migration;
use common\models\Lesson;
use common\models\Location;
use common\models\LessonOldDueDate;
use yii\helpers\Console;

/**
 * Class m190731_104656_add_lesson_old_duedate_table
 */
class m190731_104656_add_lesson_old_duedate_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $tableSchema = Yii::$app->db->schema->getTableSchema('lesson_old_duedate');
        if ($tableSchema == null) {
            $this->createTable(
                'lesson_old_duedate',
                [
                'id' => $this->primaryKey(),
                'lessonId' => $this->integer()->notNull(),
                'lessonOldDueDate' => $this->date(),
                ]
            );
        }
        $locationIds = [1, 4, 9, 14, 15, 16, 17, 18, 19, 20, 21];
        $locations = Location::find()->andWhere(['id' => $locationIds])->all();
        foreach ($locations as $location) {
            $lessons = Lesson::find()
                ->notDeleted()
                ->isConfirmed()
                ->notCanceled()
                ->location([$location->id])
                ->all();
            foreach ($lessons as $lesson) {
                Console::output("processing: " . $lesson->id . 'added old due date', Console::FG_GREEN, Console::BOLD);
                $lessonOldDueDate =  new LessonOldDueDate();
                $lessonOldDueDate->lessonId = $lesson->id;
                $lessonOldDueDate->lessonOldDueDate = $lesson->dueDate;
                $lessonOldDueDate->save();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190731_104656_add_lesson_old_duedate_table cannot be reverted.\n";

        return false;
    }
}
