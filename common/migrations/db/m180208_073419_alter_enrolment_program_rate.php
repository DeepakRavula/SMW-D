<?php

use yii\db\Migration;
use common\models\CourseProgramRate;
use common\models\Enrolment;

/**
 * Class m180208_073419_alter_enrolment_program_rate
 */
class m180208_073419_alter_enrolment_program_rate extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->renameTable('enrolment_program_rate', 'course_program_rate');
        $this->renameColumn('course_program_rate', 'enrolmentId', 'courseId');
        $this->alterColumn('invoice_line_item', 'amount', $this->decimal(10, 4));
        $courseProgramRates = CourseProgramRate::find()->all();
        foreach ($courseProgramRates as $courseProgramRate) {
            $enrolment = Enrolment::findOne($courseProgramRate->courseId);
            $course = $enrolment->course;
            $courseProgramRate->updateAttributes(['courseId' => $course->id]);
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180208_073419_alter_enrolment_program_rate cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180208_073419_alter_enrolment_program_rate cannot be reverted.\n";

        return false;
    }
    */
}
