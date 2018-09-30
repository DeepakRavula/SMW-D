<?php

use yii\db\Migration;
use common\models\Enrolment;

/**
 * Class m180930_145310_extra_lesson_enrolment_delete
 */
class m180930_145310_extra_lesson_enrolment_delete extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $enrolement = Enrolment::findOne(1282);
        $enrolement->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180930_145310_extra_lesson_enrolment_delete cannot be reverted.\n";

        return false;
    }
}
