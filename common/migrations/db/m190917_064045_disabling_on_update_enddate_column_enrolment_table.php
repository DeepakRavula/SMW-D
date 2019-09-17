<?php

use yii\db\Migration;

/**
 * Class m190917_064045_disabling_on_update_enddate_column_enrolment_table
 */
class m190917_064045_disabling_on_update_enddate_column_enrolment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('enrolment', 'endDateTime', $this->timestamp()->notNull()
        ->defaultValue('0000-00-00 00:00:00'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190917_064045_disabling_on_update_enddate_column_enrolment_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190917_064045_disabling_on_update_enddate_column_enrolment_table cannot be reverted.\n";

        return false;
    }
    */
}
