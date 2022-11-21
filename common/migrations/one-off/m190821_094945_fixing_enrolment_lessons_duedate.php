<?php

use yii\db\Migration;
use common\models\Enrolment;

/**
 * Class m190821_094945_fixing_enrolment_lessons_duedate
 */
class m190821_094945_fixing_enrolment_lessons_duedate extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $enrolment = Enrolment::findOne('3582');
        $enrolment->setDueDate();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190821_094945_fixing_enrolment_lessons_duedate cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190821_094945_fixing_enrolment_lessons_duedate cannot be reverted.\n";

        return false;
    }
    */
}
