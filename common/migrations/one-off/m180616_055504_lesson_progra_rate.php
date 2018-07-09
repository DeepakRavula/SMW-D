<?php

use yii\db\Migration;
use common\models\Lesson;
use common\models\Qualification;

/**
 * Class m180616_055504_lesson_progra_rate
 */
class m180616_055504_lesson_progra_rate extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        
        $lessons = Lesson::find()
            ->notDeleted()
            ->isConfirmed()
            ->notCanceled()
            ->location([14, 15])
            ->all();
        foreach ($lessons as $lesson) {
            $lesson->updateAttributes(['programRate' => $lesson->courseProgramRate->programRate]);
            if ($lesson->hasProformaInvoice()) {
                $teacherRate = $lesson->proformaLineItem->rate;
            } else if ($lesson->hasInvoice()) {
                $teacherRate = $lesson->invoice->lineItem->rate;
            } else {
                $qualification = Qualification::findOne(['teacher_id' => $lesson->teacherId,
                    'program_id' => $lesson->course->program->id]);
                $teacherRate = !empty($qualification->rate) ? $qualification->rate : 0;
            }
            $lesson->updateAttributes(['teacherRate' => $teacherRate]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180616_055504_lesson_progra_rate cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180616_055504_lesson_progra_rate cannot be reverted.\n";

        return false;
    }
    */
}
