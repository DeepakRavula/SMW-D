<?php

use yii\db\Migration;
use common\models\Lesson;
use common\models\Holiday;
use common\models\Location;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

/**
 * Class m190710_061653_data_fix_for_holiday_unscheduled_lesson
 */
class m190710_061653_data_fix_for_holiday_unscheduled_lesson extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $holidays = Holiday::find()
                ->notDeleted()
                ->all();

        $holidayDates = ArrayHelper::getColumn($holidays, function ($element) {
                return (new \DateTime($element->date))->format('Y-m-d');
            });

        $locationIds = [1, 4, 9, 13, 14, 15, 16, 17, 18, 19, 20, 21];
        $locations = Location::find()->andWhere(['id' => $locationIds])->all();
        Console::startProgress(0, 100, 'Change lesson status unscheduled to scheduled');
        foreach ($locations as $location) {
            Console::output("Processing Location  " . $location->name, Console::FG_GREEN, Console::BOLD);
            $lessons = Lesson::find()
                    ->notDeleted()
                    ->isConfirmed()
                    ->unscheduled()
                    ->location($location->id)
                    ->notCanceled()
                    ->activePrivateLessons()
                    ->andWhere(['>', 'lesson.date', (new \DateTime())->format('Y-m-d')])
                    ->all();
            foreach ($lessons as $lesson) {
                $lessonDate = (new \DateTime($lesson->date))->format('Y-m-d');
                if (in_array($lessonDate, $holidayDates)) {
                    Console::output("Affected Private Lesson: " . $lesson->id, Console::FG_GREEN, Console::BOLD);
                    $lesson->updateAttributes(['status' => Lesson::STATUS_SCHEDULED]);
                }
            }
            $groupLessons = Lesson::find()
                    ->notDeleted()
                    ->isConfirmed()
                    ->unscheduled()
                    ->location($location->id)
                    ->notCanceled()
                    ->groupLessons()
                    ->andWhere(['>', 'lesson.date', (new \DateTime())->format('Y-m-d')])
                    ->all();
            foreach ($groupLessons as $lesson) {
                $lessonDate = (new \DateTime($lesson->date))->format('Y-m-d');
                if (in_array($lessonDate, $holidayDates)) {
                    Console::output("Affected Group Lesson: " . $lesson->id, Console::FG_GREEN, Console::BOLD);
                    $lesson->updateAttributes(['status' => Lesson::STATUS_SCHEDULED]);
                }
            }
        }
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190710_061653_data_fix_for_holiday_unscheduled_lesson cannot be reverted.\n";

        return false;
    }
}
