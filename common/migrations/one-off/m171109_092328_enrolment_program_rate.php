<?php

use yii\db\Migration;

class m171109_092328_enrolment_program_rate extends Migration
{
    public function up()
    {
        $enrolments = common\models\Enrolment::find()
                ->where(['programRate' => 0])
                ->all();
        foreach ($enrolments as $enrolment) {
            $enrolment->updateAttributes(['programRate' => $enrolment->course->program->rate]);
        }
    }

    public function down()
    {
        echo "m171109_092328_enrolment_program_rate cannot be reverted.\n";

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
