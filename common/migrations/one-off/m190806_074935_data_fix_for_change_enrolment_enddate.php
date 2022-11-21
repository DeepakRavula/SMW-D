<?php

use yii\db\Migration;
use common\models\Enrolment;
use yii\helpers\Console;

/**
 * Class m190806_074935_data_fix_for_change_enrolment_enddate
 */
class m190806_074935_data_fix_for_change_enrolment_enddate extends Migration
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
                Console::output("Affected Private Enrolment: " . $enrolment->id, Console::FG_GREEN, Console::BOLD);
                $enrolment->updateAttributes(['endDateTime' => $enrolment->course->endDate]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190806_074935_data_fix_for_change_enrolment_enddate cannot be reverted.\n";

        return false;
    }
}
