<?php

use yii\db\Migration;
use common\models\Enrolment;
use common\models\EnrolmentProgramRate;

class m171114_095100_enrolment_program_rate_update extends Migration
{
    public function up()
    {
        $enrolments = Enrolment::find()->all();
        foreach ($enrolments as $enrolment) {
            $enrolmentProgramRate = new EnrolmentProgramRate();
            $enrolmentProgramRate->enrolmentId = $enrolment->id;
            $enrolmentProgramRate->startDate = $enrolment->course->startDate;
            $enrolmentProgramRate->endDate = $enrolment->course->endDate;
            $enrolmentProgramRate->programRate = $enrolment->programRate;
            $enrolmentProgramRate->save();
        }
        $this->dropColumn('enrolment', 'programRate');
    }

    public function down()
    {
        echo "m171114_095100_enrolment_program_rate_update cannot be reverted.\n";

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
