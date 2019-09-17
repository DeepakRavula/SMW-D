<?php

use yii\db\Migration;
use common\models\Enrolment;
use yii\helpers\Console;
use Carbon\Carbon;
/**
 * Class m190917_071839_correcting_enrolments_enddate_respective_lastlesson
 */
class m190917_071839_correcting_enrolments_enddate_respective_lastlesson extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $enrolments = Enrolment::find()
            ->notDeleted()
            ->isConfirmed()
            ->isRegular()
            ->privateProgram()
            ->all();
        foreach ($enrolments as $enrolment) {
            if ($enrolment->endDateTime != $enrolment->course->endDate) {
                Console::output("Affected Private Enrolment: Enrolment Id:  " .$enrolment->id."   ". Carbon::parse($enrolment->endDateTime)->format('M d, Y')."   Course end date   ".Carbon::parse($enrolment->course->endDate)->format('M, d, Y')." Location: ".$enrolment->course->locationId." Last Lesson Date: ".Carbon::parse($enrolment->course->lastLesson->date)->format('Y-m-d'), Console::FG_GREEN, Console::BOLD);
                $enrolment->updateAttributes(['endDateTime' => $enrolment->course->endDate]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190917_071839_correcting_enrolments_enddate_respective_lastlesson cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190917_071839_correcting_enrolments_enddate_respective_lastlesson cannot be reverted.\n";

        return false;
    }
    */
}
