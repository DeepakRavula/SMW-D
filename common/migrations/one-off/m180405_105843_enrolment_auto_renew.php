<?php

use yii\db\Migration;
use common\models\Lesson;
use common\models\Enrolment;

/**
 * Class m180405_105843_enrolment_auto_renew
 */
class m180405_105843_enrolment_auto_renew extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $enrolments = Enrolment::find()
            ->notDeleted()
            ->privateProgram()
            ->all();
        foreach ($enrolments as $enrolment) {
            $enrolmentEndDate = new \DateTime($enrolment->endDate);
            $enrolmentLastLesson = Lesson::find()
                ->roots()
                ->andWhere(['courseId' => $enrolment->courseId, 'isDeleted' => true])
                ->andWhere(['>', 'DATE(date)', $enrolmentEndDate->format('Y-m-d')])
                ->one();
            if ($enrolmentLastLesson) {
                $enrolment->updateAttributes(['isAutoRenew' => false]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180405_105843_enrolment_auto_renew cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180405_105843_enrolment_auto_renew cannot be reverted.\n";

        return false;
    }
    */
}
