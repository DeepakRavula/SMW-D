<?php

use yii\db\Migration;
use common\models\Enrolment;

/**
 * Class m180408_174323_add_enrolment_created_date
 */
class m180408_174323_add_enrolment_created_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('enrolment', 'createdAt', $this->timestamp()->null());
        $this->addColumn('enrolment', 'updatedAt', $this->timestamp()->null());
        $enrolments = Enrolment::find()
            ->all();
        foreach ($enrolments as $enrolment) {
            if ($enrolment->course) {
                $enrolment->updateAttributes([
                    'createdAt' => $enrolment->course->startDate,
                    'updatedAt' => $enrolment->course->startDate
                ]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180408_174323_add_enrolment_created_date cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180408_174323_add_enrolment_created_date cannot be reverted.\n";

        return false;
    }
    */
}
