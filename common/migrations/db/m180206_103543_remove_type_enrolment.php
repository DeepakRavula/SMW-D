<?php

use yii\db\Migration;

/**
 * Class m180206_103543_remove_type_enrolment
 */
class m180206_103543_remove_type_enrolment extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->dropColumn('enrolment', 'type');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180206_103543_remove_type_enrolment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180206_103543_remove_type_enrolment cannot be reverted.\n";

        return false;
    }
    */
}
