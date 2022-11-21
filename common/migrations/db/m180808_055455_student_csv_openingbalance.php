<?php

use yii\db\Migration;

/**
 * Class m180808_055455_student_csv_openingbalance
 */
class m180808_055455_student_csv_openingbalance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('student_csv', 'openingBalance', 'varchar(255)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180808_055455_student_csv_openingbalance cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180808_055455_student_csv_openingbalance cannot be reverted.\n";

        return false;
    }
    */
}
