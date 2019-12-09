<?php

use yii\db\Migration;
use yii\helpers\Console;
use common\models\Location;
use common\models\Lesson;
/**
 * Class m191209_112656_adding_original_date_old_lessons
 */
class m191209_112656_adding_original_date_old_lessons extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $locationIds = [1, 4, 9, 14, 15, 16, 17, 18, 19, 20, 21];
        Console::startProgress(0, 100, 'Cancelling lessons');
        $lessonCount = 0;
        $locations = Location::find()->andWhere(['id' => $locationIds])->all();
        foreach ($locations as $location) {
            $lessons = Lesson::find()
            ->location([$location->id])
            ->andwhere(['id' <= 512026])
            ->all();
            foreach ($lessons as $lesson) {
                $lesson->updateAttributes(['originalDate' => $lesson->getOriginalDate()]);
    
           }
        }
        foreach ($locations as $location) {
            $lessons = Lesson::find()
            ->location([$location->id])
            ->andwhere(['id' > 512026])
            ->all();
            foreach ($lessons as $lesson) {
                $lesson->updateAttributes(['originalDate' => $lesson->getOriginalDate()]);
    
           }
        }
        Console::output("Affected Lessons Count".$lessonCount, Console::FG_GREEN, Console::BOLD);
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191209_112656_adding_original_date_old_lessons cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191209_112656_adding_original_date_old_lessons cannot be reverted.\n";

        return false;
    }
    */
}
